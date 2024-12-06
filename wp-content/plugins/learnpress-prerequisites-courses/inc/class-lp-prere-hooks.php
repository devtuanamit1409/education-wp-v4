<?php
/**
 * Class LP_Addon_WPML_Hooks
 *
 * @since 4.0.6
 * @version  1.0.0
 * @author thimpress
 */

use LearnPress\Models\CourseModel;
use LearnPress\Models\UserItems\UserCourseModel;
use LearnPress\Models\UserModel;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class LP_Prere_Course_Hooks {
	/**
	 * @var bool|LP_User|LP_User_Guest
	 */
	protected $user_current;
	public $addon;

	/**
	 * @return LP_Prere_Course_Hooks
	 */
	public static function get_instance(): LP_Prere_Course_Hooks {
		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * LP_Prere_Hooks constructor.
	 */
	protected function __construct() {
		$this->addon        = LP_Addon_Prerequisites_Courses_Preload::$addon;
		$this->user_current = UserModel::find( get_current_user_id(), true );
		$this->init();
	}

	private function init() {
		// Hook add styles
		add_action( 'learn-press/frontend-default-styles', array( $this, 'enqueue_styles' ) );
		// Hook meta box
		add_filter( 'learnpress/course/metabox/tabs', array( $this, 'add_course_metabox' ), 10, 2 );
		// Hook check condition show message.
		//add_action( 'learn-press/course-buttons', array( $this, 'check_condition' ), 1 );
		// Hook check can enroll course
		add_filter( 'learn-press/user/can-enroll/course', array( $this, 'can_enroll_course' ), 10, 3 );
		// Hook check can buy course
		add_filter( 'learn-press/user/can-purchase/course', array( $this, 'can_purchase_course' ), 10, 3 );
		// Hook check can view content course
		add_filter( 'learnpress/course/can-view-content', array( $this, 'can_view_content_course' ), 99, 3 );
		// Hook accept message show message prerequisites course.
		add_filter(
			'learn-press/course/html-button-enroll/show-messages',
			function ( $error_code_show ) {
				$error_code_show[] = 'lp_prerequisites_course_can_not_enroll';

				return $error_code_show;
			}
		);
	}

	/**
	 * Equeue assets.
	 *
	 * @param array $styles
	 *
	 * @return array
	 */
	public function enqueue_styles( array $styles = array() ): array {

		$url = $this->addon->get_plugin_url( 'assets/css/lp-prerequisite-course.min.css' );
		if ( LP_Debug::is_debug() ) {
			$url = $this->addon->get_plugin_url( 'assets/css/lp-prerequisite-course.css' );
		}

		$styles['lp-prerequisites-courses'] = new LP_Asset_Key( $url );

		return $styles;
	}

	/**
	 * Show tab and field config for course.
	 *
	 * @param $tabs
	 * @param $post_id
	 *
	 * @return array|mixed
	 * @since 3.0.0
	 * @version 1.0.1
	 */
	public function add_course_metabox( $tabs, $post_id ) {
		$course = CourseModel::find( $post_id );
		if ( ! $course ) {
			return $tabs;
		}

		if ( $course->is_offline() ) {
			return $tabs;
		}

		$data_struct = [
			'urlApi'      => get_rest_url( null, 'lp/v1/admin/tools/search-course' ),
			'dataSendApi' => [ 'id_not_in' => [ $post_id ] ],
			'dataType'    => 'courses',
			'keyGetValue' => [
				'value'      => 'ID',
				'text'       => '{{post_title}}(#{{ID}})',
				'key_render' => [
					'post_title' => 'post_title',
					'ID'         => 'ID',
				],
			],
			'setting'     => [
				'placeholder' => esc_html__( 'Choose Course', 'learnpress' ),
			],
		];

		$tabs['prerequisites'] = array(
			'label'    => esc_html__( 'Prerequisites Course', 'learnpress-prerequisites-courses' ),
			'target'   => 'prerequisites_course_data',
			'icon'     => 'dashicons-excerpt-view',
			'priority' => 80,
			'content'  => [
				'_lp_prerequisite_allow_purchase' => new LP_Meta_Box_Checkbox_Field(
					esc_html__( 'Allow Purchase', 'learnpress-prerequisites-courses' ),
					esc_html__( 'Allow purchase course without finish prerequisites.', 'learnpress-prerequisites-courses' ),
					'no'
				),
				'_lp_course_prerequisite'         => new LP_Meta_Box_Select_Field(
					esc_html__( 'Select Courses', 'learnpress' ),
					'',
					[],
					array(
						'options'           => [],
						'style'             => 'min-width:200px;',
						'tom_select'        => true,
						'multiple'          => true,
						'custom_attributes' => [ 'data-struct' => htmlentities2( json_encode( $data_struct ) ) ],
					)
				),
			],
		);

		return $tabs;
	}

	/**`
	 * Show notice required pass prerequisites courses.
	 *
	 * @since 3.0.0
	 * @version 3.0.3
	 * @deprecated 4.0.8
	 */
	/*public function check_condition() {
		global $post;

		$user   = $this->user_current;
		$course = CourseModel::find( $post->ID, true );
		if ( ! $course ) {
			return;
		}

		$can_enroll = $this->addon->check_condition( $course, $user );
		if ( ! $can_enroll instanceof WP_Error ) {
			return;
		}

		// Remove buttons course.
		remove_all_actions( 'learn-press/course-buttons' );
	}*/

	/**
	 * Hook check can enroll course.
	 *
	 * @param true|WP_Error $can_enroll
	 * @param CourseModel $course
	 * @param false|UserModel $user
	 *
	 * @return true|WP_Error
	 * @since 4.0.8
	 * @version 1.0.0
	 */
	public function can_enroll_course( $can_enroll, $course, $user ) {
		$user_id = 0;
		if ( $user instanceof UserModel ) {
			$user_id = $user->get_id();
		}

		// Skip check course not free and allow purchase course without finish prerequisites.
		$userCourse = UserCourseModel::find( $user_id, $course->get_id(), true );
		if ( ! $course->is_free() && $this->addon->is_allow_purchase_course( $course )
		&& ! $userCourse ) {
			return $can_enroll;
		}

		if ( $course->has_no_enroll_requirement() ) {
			return $can_enroll;
		}

		$pass_condition = $this->addon->check_condition( $course, $user );
		if ( ! $pass_condition ) {
			$html_message = $this->addon->html_prerequisite_courses( $course, $user );

			$can_enroll = new WP_Error( 'lp_prerequisites_course_can_not_enroll', $html_message );
		}

		return $can_enroll;
	}

	/**
	 * Hook check can buy course.
	 *
	 * @param $can_purchase
	 * @param CourseModel $course
	 * @param UserModel|false $user
	 *
	 * @return true|WP_Error
	 * @since 4.0.8
	 * @version 1.0.0
	 */
	public function can_purchase_course( $can_purchase, $course, $user ) {
		if ( ! $can_purchase ) {
			return $can_purchase;
		}

		try {
			$user_id = 0;
			if ( $user instanceof UserModel ) {
				$user_id = $user->get_id();
			}

			// Get option allow purchase course without prerequisite
			$allow_purchase   = $this->addon->is_allow_purchase_course( $course );
			$allow_repurchase = $course->enable_allow_repurchase();

			// For case repurchase course
			if ( $allow_purchase ) {
				$userCourse = UserCourseModel::find( $user_id, $course->get_id(), true );
				if ( $userCourse && $userCourse->has_purchased() && $allow_repurchase ) {
					if ( $userCourse->is_passed() ) {
						$can_purchase = true;
					}
				}
			} elseif ( ! $this->addon->check_condition( $course, $user ) ) {
				$can_purchase = new WP_Error( '', '' );
			}
		} catch ( Throwable $e ) {
			error_log( $e->getMessage() );
		}

		return $can_purchase;
	}

	/**
	 * Filer user can view content course condition.
	 *
	 * @param LP_Model_User_Can_View_Course_Item $view
	 * @param int $user_id
	 * @param LP_Course $course
	 *
	 * @return LP_Model_User_Can_View_Course_Item
	 * @since 4.0.0
	 * @version 1.0.2
	 */
	public function can_view_content_course( LP_Model_User_Can_View_Course_Item $view, int $user_id, LP_Course $course ) {
		$user = UserModel::find( $user_id );
		if ( ! $user ) {
			return $view;
		}

		$course_id           = $course->get_id();
		$required_course_ids = LP_Addon_Prerequisites_Courses::get_prerequisite_courses( $course_id );

		foreach ( $required_course_ids as $required_course_id ) {
			$course_data = UserCourseModel::find( $user_id, $required_course_id, true );
			if ( ! $course_data || ! $course_data->is_passed() ) {
				$view->flag    = false;
				$view->message = __(
					'This content is protected, please pass the prerequisites course(s) to view this content!',
					'learnpress-prerequisites-courses'
				);
				break;
			}
		}

		return $view;
	}
}
