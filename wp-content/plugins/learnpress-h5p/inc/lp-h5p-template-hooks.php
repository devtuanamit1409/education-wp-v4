<?php
/**
 * All template hooks for LearnPress H5P Content templates.
 *
 * @author  ThimPress
 * @package LearnPress/H5P/Hooks
 * @version 3.0.0
 */

defined( 'ABSPATH' ) || exit;

class LP_H5P_Template_Hook {
	public static function instance() {
		static $instance = null;
		if ( ! $instance ) {
			$instance = new self();
		}

		return $instance;
	}

	private function __construct() {
		$this->hooks();
	}

	private function hooks() {
		add_action( 'learn-press/user-item-progress', [ $this, 'user_item_progress' ], 10, 3 );
		add_action( 'learn-press/before-content-item-summary/lp_h5p', [ $this, 'learn_press_content_item_h5p_title' ], 10 );
		add_action( 'learn-press/before-content-item-summary/lp_h5p', [ $this, 'learn_press_content_item_h5p_intro' ], 15 );

		add_action( 'learn-press/after-content-item-summary/lp_h5p', [ $this, 'learn_press_content_item_h5p_buttons' ], 15 );
		add_action( 'learn-press/content-item-summary/lp_h5p', [ $this, 'learn_press_content_item_h5p_content' ], 5 );
		add_action( 'learn-press/content-item-summary/lp_h5p', [ $this, 'learn_press_content_item_h5p_condition' ], 10 );

		add_action( 'learn-press/h5p-buttons', [ $this, 'learn_press_h5p_result' ], 15 );
		add_action( 'learn-press/h5p-buttons', [ $this, 'learn_press_h5p_summary' ], 10 );
		add_action( 'learn-press/h5p-buttons', [ $this, 'learn_press_h5p_complete' ], 20 );

		add_filter( 'learn-press/can-view-h5p', [ $this, 'learn_press_h5p_filter_can_view_item' ], 10, 4 );

	}

	/**
	 * If the course is set to require submission, and the user is not logged in or has not enrolled in the
	 * course, then return 'not-logged-in' or 'not-enrolled' respectively
	 *
	 * @param view The current view status.
	 * @param h5p_id The ID of the H5P content.
	 * @param user_id The ID of the user who is trying to view the H5P content.
	 * @param course_id The ID of the course that the H5P is attached to.
	 */
	public function learn_press_h5p_filter_can_view_item( $view, $h5p_id, $user_id, $course_id ) {
		$user           = learn_press_get_user( $user_id );
		$_lp_submission = get_post_meta( $course_id, '_lp_submission', true );

		if ( $_lp_submission === 'yes' ) {
			if ( ! $user->is_logged_in() ) {
				return 'not-logged-in';
			} elseif ( ! $user->has_enrolled_course( $course_id ) ) {
				return 'not-enrolled';
			}
		}
		return $view;
	}

	/**
	 * Add item h5p progress in single course
	 */
	public function user_item_progress( $course_results, $course_data, $user ) {
		learn_press_h5p_get_template(
			'single-course/user-progress.php',
			array(
				'course_results' => $course_results,
				'course_data'    => $course_data,
				'user'           => $user,
			)
		);
	}

	/**
	 * H5p title.
	 */
	public function learn_press_content_item_h5p_title() {
		learn_press_h5p_get_template( 'content-h5p/title.php' );
	}

	/**
	 * H5p introduction.
	 */
	public function learn_press_content_item_h5p_intro() {
		$h5p = LP_Global::course_item();

		if ( ! lp_h5p_check_interacted( $h5p->get_id() ) ) {
			learn_press_h5p_get_template( 'content-h5p/intro.php' );
		}
	}

	/**
	 * H5p buttons.
	 */
	public function learn_press_content_item_h5p_buttons() {
		$h5p = LP_Global::course_item();

		if ( lp_h5p_check_interacted( $h5p->get_id() ) ) {
			learn_press_h5p_get_template( 'content-h5p/buttons.php' );
		}
	}

	/**
	 * H5p content.
	 */
	public function learn_press_content_item_h5p_content() {
		learn_press_h5p_get_template( 'content-h5p/content.php' );
	}

	/**
	 * H5p content.
	 */
	public function learn_press_content_item_h5p_condition() {
		$h5p = LP_Global::course_item();

		if ( lp_h5p_check_interacted( $h5p->get_id() ) ) {
			learn_press_h5p_get_template( 'content-h5p/condition.php' );
		}
	}

	/**
	 * Result button.
	 */
	public function learn_press_h5p_result() {
		$course = learn_press_get_course();
		$user   = learn_press_get_current_user();
		$h5p    = LP_Global::course_item();

		if ( empty( $course ) || empty( $user ) ) {
			return;
		}

		if ( ! $user->has_item_status( array( 'completed' ), $h5p->get_id(), $course->get_id() ) ) {
			return;
		}

		learn_press_h5p_get_template( 'content-h5p/buttons/result.php' );
	}

	/**
	 * H5p attachment.
	 */
	public function learn_press_h5p_summary() {
		$course = learn_press_get_course();
		$user   = learn_press_get_current_user();
		$h5p    = LP_Global::course_item();

		if ( empty( $course ) || empty( $user ) ) {
			return;
		}

		if ( $user->has_course_status( $course->get_id(), array( 'finished' ) ) || $user->has_item_status( array( 'completed' ), $h5p->get_id(), $course->get_id() ) ) {
			$conditional_h5p = get_post_meta( $h5p->get_id(), '_lp_h5p_interact', true );
			$plugin          = H5P_Plugin::get_instance();
			$content         = $plugin->get_content( $conditional_h5p );
			$library         = ! empty( $content['library']['name'] ) ? $content['library']['name'] : '';

			if ( $library != '' && in_array( $library, lp_h5p_can_summary_types_list() ) ) {
				$library_file_name = strtolower( str_replace( 'H5P.', '', $library ) ) . '.php';
				learn_press_h5p_get_template( 'content-h5p/summary/' . $library_file_name, array( 'h5p_content' => $content ) );
			}
		}
	}

	/**
	 * Retake button.
	 */
	public function learn_press_h5p_complete() {
		$course = learn_press_get_course();
		$user   = learn_press_get_current_user();
		$h5p    = LP_Global::course_item();

		if ( empty( $course ) || empty( $user ) ) {
			return;
		}

		if ( $user->has_course_status( $course->get_id(), array( 'finished' ) ) || ! $user->has_course_status( $course->get_id(), array( 'enrolled' ) ) || $user->has_item_status(
			array(
				'completed',
			),
			$h5p->get_id(),
			$course->get_id()
		)
		) {
			return;
		}
		learn_press_h5p_get_template(
			'content-h5p/buttons/complete.php',
			array(
				'course' => $course,
				'user'   => $user,
				'h5p'    => $h5p,
			)
		);
	}
}

LP_H5P_Template_Hook::instance();

/*
add_action( 'learn-press/frontend-editor/item-settings-after', 'learn_press_h5p_fe_setting' );
add_action( 'learn-press/frontend-editor/form-fields-after', 'learn_press_h5p_fe_fields' );
add_action( 'learn-press/frontend-editor/item-extra-action', 'learn_press_h5p_fe_manager_link' );
add_filter( 'the_content', 'lp_h5ps_setup_shortcode_page_content' );*/
