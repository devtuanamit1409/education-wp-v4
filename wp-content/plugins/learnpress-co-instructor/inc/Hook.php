<?php
/**
 * LearnPress Co-Instructor Hook Template
 *
 * @since 4.0.4
 * @version 1.0.0
 */

namespace LP_Addon_Co_Instructor;

use LearnPress\Helpers\Singleton;
use LearnPress\Models\CourseModel;
use LP_Addon_Co_Instructor;
use LP_Co_Instructor_Preload;
use LP_Email_Enrolled_Course_Co_Instructor;
use LP_Email_Finished_Course_Co_Instructor;
use LP_Emails;
use LP_Model_User_Can_View_Course_Item;
use WP_Screen;

class Hook {
	use Singleton;

	/**
	 * @var LP_Addon_Co_Instructor $addon
	 */
	public $addon;

	public function init() {
		$this->addon = LP_Co_Instructor_Preload::$addon;

		// Set role for co-instructor
		$teacher_role = get_role( LP_TEACHER_ROLE );
		if ( $teacher_role ) {
			$teacher_role->add_cap( 'edit_others_' . LP_COURSE_CPT . 's' );
		}

		// Remove capability of instructor when deactivate plugin
		register_deactivation_hook( $this->addon->plugin_file, array( $this, 'remove_teacher_capabilities' ) );

		// Check co-instructor can view/edit post
		add_action( 'current_screen', array( $this, 'check_co_instructor_can_view_edit_post' ) );
		add_filter( 'wp_insert_post_data', array( $this, 'check_co_instructor_can_save_post' ), 10, 4 );

		// Load all items of instructor created for assign to course
		add_filter(
			'learn-press/modal-search-items/args',
			array( $this, 'load_all_items_instructor_on_course' ),
			10,
			1
		);

		// Register emails
		$this->emails_setting();
		// Email group
		add_filter( 'learn-press/emails/finished-course', [ $this, 'add_emails_group_finished_course' ] );
		add_filter( 'learn-press/emails/enrolled-course', [ $this, 'add_emails_group_enrolled_course' ] );

		add_filter( 'learnpress/course/can-view-content', [ $this, 'can_view_course_content' ], 10, 3 );
	}

	/**
	 * Plugin install, add teacher capacities.
	 *
	 * @since 3.0.0
	 */
	public function install() {
		$teacher_role = get_role( LP_TEACHER_ROLE );
		// Set capability for co-instructor
		if ( $teacher_role ) {
			// Can edit course of another instructor
			$teacher_role->add_cap( 'edit_others_' . LP_COURSE_CPT );
		}
	}

	/**
	 * Remove teacher capacities.
	 *
	 * @since 3.0.0
	 */
	public function remove_teacher_capabilities() {
		/*** Remove cab of instructor can edit post not yourself */
		$teacher_role = get_role( LP_TEACHER_ROLE );

		if ( $teacher_role ) {
			$teacher_role->remove_cap( 'edit_others_lp_courses' );
		}
		/*** End */
	}

	/**
	 * Check instructor can edit post of another instructor
	 *
	 * @param WP_Screen $current_screen Current WP_Screen object.
	 *
	 * @return void
	 */
	public function check_co_instructor_can_view_edit_post( $current_screen ) {
		$screen_check_arr = apply_filters(
			'learn-press/co-instructor/screen-check',
			[ LP_COURSE_CPT ],
			$current_screen
		);

		if ( ! $current_screen || ! in_array( $current_screen->id, $screen_check_arr )
			|| ! isset( $_GET['post'] ) ) {
			return;
		}

		$user_id = get_current_user_id();
		$post_id = absint( $_GET['post'] );
		$post    = get_post( $post_id );
		if ( ! $post ) {
			return;
		}

		if ( $user_id == $post->post_author || current_user_can( ADMIN_ROLE ) ) {
			return;
		}

		// Check post type
		switch ( $post->post_type ) {
			case LP_COURSE_CPT:
				$course = CourseModel::find( $post_id, true );
				if ( ! $course ) {
					return;
				}

				$can_edit = $this->addon->is_co_in_course( $user_id, $course );
				if ( ! $can_edit ) {
					wp_die( 'Sorry, you are not allowed to edit this post.' );
				}
				break;
			case LP_LESSON_CPT:
			case LP_QUESTION_CPT:
			case LP_QUIZ_CPT:
			default:
				do_action( 'learn-press/co-instructor/check-can-edit-post', $post, $user_id );
				break;
		}
	}

	/**
	 * Check instructor can save post of another instructor
	 *
	 * @param $data
	 * @param $postarr
	 * @param $unsanitized_postarr
	 * @param $update
	 *
	 * @return mixed
	 */
	public function check_co_instructor_can_save_post( $data, $postarr, $unsanitized_postarr, $update ) {
		if ( ! $update ) {
			return $data;
		}

		$user_id = get_current_user_id();
		$post_id = absint( $postarr['ID'] ?? 0 );
		$post    = get_post( $post_id );
		if ( ! $post ) {
			return $data;
		}

		if ( $user_id == $post->post_author || current_user_can( ADMIN_ROLE ) ) {
			return $data;
		}

		// Check post type
		switch ( $post->post_type ) {
			case LP_COURSE_CPT:
				$course = CourseModel::find( $post_id, true );
				if ( ! $course ) {
					return $data;
				}

				$can_edit = $this->addon->is_co_in_course( $user_id, $course );
				if ( ! $can_edit ) {
					wp_die( 'Sorry, you are not allowed to edit this post.' );
				}
				break;
			case LP_LESSON_CPT:
			case LP_QUESTION_CPT:
			case LP_QUIZ_CPT:
			default:
				do_action( 'learn-press/co-instructor/check-can-save-post', $post, $user_id );
				break;
		}

		return $data;
	}

	/**
	 * Load all items of instructor created for assign to course
	 * @move from file LP_Co_Instructor_Preload
	 *
	 * @param $args_query
	 *
	 * @return mixed
	 */
	public function load_all_items_instructor_on_course( $args_query ) {
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			return $args_query;
		}

		if ( ! user_can( $user_id, LP_TEACHER_ROLE ) ) {
			return $args_query;
		}

		if ( isset( $args_query['author'] ) ) {
			// Logic 1: only load items of instructor yourself
			unset( $args_query['author'] );
			$args_query['author__in'] = array( $user_id );

			// Logic 2: load items of instructor and co-instructors
		}

		return $args_query;
	}

	/**
	 * Add email settings
	 */
	public function emails_setting() {
		if ( ! class_exists( 'LP_Emails' ) ) {
			return;
		}

		$emails = LP_Emails::instance()->emails;

		$emails[ LP_Email_Finished_Course_Co_Instructor::class ] = include_once LP_ADDON_CO_INSTRUCTOR_PATH . '/inc/emails/class-lp-co-instructor-email-finished-course.php';
		$emails[ LP_Email_Enrolled_Course_Co_Instructor::class ] = include_once LP_ADDON_CO_INSTRUCTOR_PATH . '/inc/emails/class-lp-co-instructor-email-enrolled-course.php';

		LP_Emails::instance()->emails = $emails;
	}

	/**
	 * @param array $group
	 *
	 * @return array
	 */
	public function add_emails_group_finished_course( array $group ): array {
		$group[] = 'finished-course-co-instructor';

		return $group;
	}

	/**
	 * @param array $group
	 *
	 * @return array
	 */
	public function add_emails_group_enrolled_course( array $group ): array {
		$group[] = 'enrolled-course-co-instructor';

		return $group;
	}

	/**
	 * Check can view content of course
	 *
	 * @param LP_Model_User_Can_View_Course_Item $can_view_item
	 * @param int $user_id
	 * @param \LP_Course $course
	 *
	 * @return LP_Model_User_Can_View_Course_Item
	 */
	public function can_view_course_content ( $can_view_item, $user_id, $course ) {
		$courseModel = CourseModel::find( $course->get_id(), true );

		$is_con_in_course = $this->addon->is_co_in_course( $user_id, $courseModel );
		if ( ! $is_con_in_course ) {
			return $can_view_item;
		}

		$can_view_item->flag = true;
		$can_view_item->key  = 'co-instructor';

		return $can_view_item;
	}
}
