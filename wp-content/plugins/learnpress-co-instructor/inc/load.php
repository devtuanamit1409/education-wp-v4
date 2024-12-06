<?php
/**
 * Plugin load class.
 *
 * @author   ThimPress
 * @package  LearnPress/Co-Instructor/Classes
 * @version  3.0.1
 */

use LearnPress\Models\CourseModel;
use LearnPress\Models\UserModel;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LP_Addon_Co_Instructor' ) ) {
	/**
	 * Class LP_Addon_Co_Instructor
	 */
	class LP_Addon_Co_Instructor extends LP_Addon {

		/**
		 * @var string
		 */
		public $version = LP_ADDON_CO_INSTRUCTOR_VER;

		/**
		 * @var string
		 */
		public $require_version = LP_ADDON_CO_INSTRUCTOR_REQUIRE_VER;

		/**
		 * Path file addon.
		 *
		 * @var string
		 */
		public $plugin_file = LP_ADDON_CO_INSTRUCTOR_FILE;

		/**
		 * LP_Addon_Co_Instructor constructor.
		 */
		public function __construct() {
			parent::__construct();
		}

		/**
		 * Define Learnpress Co-Instructor constants.
		 *
		 * @since 3.0.0
		 */
		protected function _define_constants() {
			define( 'LP_ADDON_CO_INSTRUCTOR_INC', LP_ADDON_CO_INSTRUCTOR_PATH . '/inc/' );
			define( 'LP_ADDON_CO_INSTRUCTOR_TEMPLATE', LP_ADDON_CO_INSTRUCTOR_PATH . '/templates/' );
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 *
		 * @since 3.0.0
		 */
		protected function _includes() {
			include_once LP_ADDON_CO_INSTRUCTOR_INC . 'functions.php';
			include_once LP_ADDON_CO_INSTRUCTOR_INC . 'class-lp-co-instructor-database.php';
			include_once LP_ADDON_CO_INSTRUCTOR_INC . 'CourseCoInstructorTemplate.php';
			include_once LP_ADDON_CO_INSTRUCTOR_INC . 'Hook.php';
		}

		/**
		 * Hook into actions and filters.
		 */
		protected function _init_hooks() {
			if ( current_user_can( ADMIN_ROLE ) || current_user_can( LP_TEACHER_ROLE ) ) {
				add_filter( 'learn-press/profile-tabs', array( $this, 'add_profile_instructor_tab' ) );
				add_filter(
					'learnpress/get-post-type-lp-on-backend',
					array( $this, 'get_items_of_co_instructor' ),
					11
				);
			}

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			//add_filter( 'learn-press/edit-admin-bar-button', array( $this, 'before_admin_bar_course_item' ), 10, 2 );

			// add co-instructor settings in admin settings page
			add_filter(
				'learn-press/profile-settings-fields/sub-tabs',
				array( $this, 'co_instructor_settings' ),
				10,
				2
			);

			// update post author for items in course, quiz
			//add_filter( 'learnpress_course_insert_item_args', array( $this, 'course_insert_item_args' ) );
			//add_filter( 'learnpress_quiz_insert_item_args', array( $this, 'quiz_insert_question_args' ), 10, 2 );

			/*add_filter(
				'learn_press_excerpt_duplicate_post_meta',
				array( $this, 'excerpt_duplicate_post_meta' ),
				10,
				3
			);*/

			//add_action( 'learn-press/after-single-course-instructor', array( $this, 'single_course_instructors' ) );

			// Add field Co-Instructors
			add_filter( 'learnpress/course/metabox/tabs', [ $this, 'field_choice_co_instructors' ], 10, 2 );
		}

		/**
		 * @param array $tabs
		 * @param int $post_id
		 *
		 * @return array
		 */
		public function field_choice_co_instructors( array $tabs, int $post_id ): array {
			$users = array();

			$post_author = get_post( $post_id )->post_author ?? '';
			if ( empty( $post_author ) ) {
				return $tabs;
			}

			$data_struct = [
				'urlApi'      => get_rest_url( null, 'lp/v1/admin/tools/search-user' ),
				'dataSendApi' => [
					'role_in'   => ADMIN_ROLE . ',' . LP_TEACHER_ROLE,
					'id_not_in' => $post_author,
				],
				'dataType'    => 'users',
				'keyGetValue' => [
					'value'      => 'ID',
					'text'       => '{{display_name}}(#{{ID}}) - {{user_email}}',
					'key_render' => [
						'display_name' => 'display_name',
						'user_email'   => 'user_email',
						'ID'           => 'ID',
					],
				],
				'setting'     => [
					'placeholder' => esc_html__( 'Choose User', 'learnpress' ),
				],
			];

			$tabs['author']['content']['_lp_co_teacher'] = new LP_Meta_Box_Select_Field(
				esc_html__( 'Co-Instructors', 'learnpress-co-instructor' ),
				__( 'Colleagues will work with you.', 'learnpress-co-instructor' ),
				[],
				[
					'options'           => $users,
					'style'             => 'min-width:200px;',
					'tom_select'        => true,
					'multiple'          => true,
					'multil_meta'       => true,
					'custom_attributes' => [ 'data-struct' => htmlentities2( json_encode( $data_struct ) ) ],
				]
			);

			return $tabs;
		}

		/**
		 * Assets.
		 */
		public function enqueue_scripts() {
			$min = '.min';
			if ( LP_Debug::is_debug() ) {
				$ver = time();
				$min = '';
			}
			wp_register_style( 'lp-co-intructor', $this->get_plugin_url( "assets/css/co-instructor{$min}.css" ) );

			if ( LP_PAGE_SINGLE_COURSE === LP_Page_Controller::page_current() ) {
				wp_enqueue_style( 'lp-co-intructor' );
			}
		}

		/**
		 * Remove edit lesson, quiz, question in admin bar for unauthorized user.
		 *
		 * @param $can_edit
		 * @param $course_item
		 *
		 * @return bool
		 * @deprecated 4.0.4
		 */
		/*public function before_admin_bar_course_item( $can_edit, $course_item ) {
			if ( ! $course_item ) {
				return false;
			}

			if ( current_user_can( 'administrator' ) ) {
				return true;
			}

			$item_id = $course_item->get_id();
			$type    = get_post_type( $item_id );
			if ( $type == LP_LESSON_CPT ) {
				if ( in_array( $item_id, $this->co_instructor_valid_lessons() ) ) {
					return false;
				}
			} elseif ( $type == LP_QUIZ_CPT ) {
				if ( in_array( $item_id, $this->co_instructor_valid_quizzes() ) ) {
					return false;
				}
			} elseif ( $type == LP_QUESTION_CPT ) {
				if ( in_array( $item_id, $this->co_instructor_valid_questions() ) ) {
					return false;
				}
			}

			return apply_filters( 'learn-press/co-instructor/edit-admin-bar', $can_edit, $item_id );
		}*/


		/**
		 * Pre query items for co-instructor.
		 *
		 * @param WP_Query $query
		 *
		 * @return WP_Query
		 * @version 1.0.1
		 */
		public function get_items_of_co_instructor( WP_Query $query ) {
			$current_user = wp_get_current_user();
			if ( ! $current_user ) {
				return $query;
			}

			if ( user_can( $current_user, ADMIN_ROLE ) ) {
				return $query;
			}

			if ( ! user_can( $current_user, LP_TEACHER_ROLE ) || ! is_admin()
				|| ! function_exists( 'get_current_screen' ) ) {
				return $query;
			}

			$current_screen   = get_current_screen();
			$screen_check_arr = array( 'edit-' . LP_COURSE_CPT );

			if ( ! in_array( $current_screen->id, $screen_check_arr ) ) {
				return $query;
			}

			$query->set(
				'meta_query',
				array(
					'relation' => 'OR',
					array(
						'key'     => '_lp_co_teacher',
						'value'   => $current_user->ID,
						'compare' => '=',
					),
					array(
						'key'     => 'post_author',
						'value'   => $current_user->ID,
						'compare' => '=',
					),
				)
			);

			return $query;
		}

		/**
		 * Restrict co-instructor items.
		 *
		 * @param $views
		 *
		 * @return mixed
		 * @deprecated 4.0.4
		 */
		/*public function restrict_co_instructor_items( $views ) {
			$post_type = get_query_var( 'post_type' );
			$author    = get_current_user_id();

			$new_views = array(
				'all'        => __( 'All', 'learnpress-co-instructor' ),
				'mine'       => __( 'Mine', 'learnpress-co-instructor' ),
				'publish'    => __( 'Published', 'learnpress-co-instructor' ),
				'private'    => __( 'Private', 'learnpress-co-instructor' ),
				'pending'    => __( 'Pending Review', 'learnpress-co-instructor' ),
				'future'     => __( 'Scheduled', 'learnpress-co-instructor' ),
				'draft'      => __( 'Draft', 'learnpress-co-instructor' ),
				'trash'      => __( 'Trash', 'learnpress-co-instructor' ),
				'co_teacher' => __( 'Co-instructor', 'learnpress-co-instructor' ),
			);

			$url = 'edit.php';

			foreach ( $new_views as $view => $name ) {

				$query = array(
					'post_type' => $post_type,
				);

				if ( $view == 'all' ) {
					$query['all_posts'] = 1;
					$class              = ( get_query_var( 'all_posts' ) == 1 || ( get_query_var( 'post_status' ) == '' && get_query_var( 'author' ) == '' ) ) ? ' class="current"' : '';
				} elseif ( $view == 'mine' ) {
					$query['author'] = $author;
					$class           = ( get_query_var( 'author' ) == $author ) ? ' class="current"' : '';
				} elseif ( $view == 'co_teacher' ) {
					$query['author'] = - $author;
					$class           = ( get_query_var( 'author' ) == - $author ) ? ' class="current"' : '';
				} else {
					$query['post_status'] = $view;
					$class                = ( get_query_var( 'post_status' ) == $view ) ? ' class="current"' : '';
				}

				$result = new WP_Query( $query );

				if ( $result->found_posts > 0 ) {
					$views[ $view ] = sprintf(
						'<a href="%s" ' . $class . '>' . $name . ' <span class="count">(%d)</span></a>',
						esc_url( add_query_arg( $query, $url ) ),
						$result->found_posts
					);
				} else {
					unset( $views[ $view ] );
				}
			}

			return $views;
		}*/

		/**
		 * Get all editable courses of current user.
		 *
		 * @return array
		 * @deprecated 4.0.4
		 */
//		public function get_available_courses(): array {
//			$user = learn_press_get_current_user();
//
//			if ( ! $user->is_admin() && ! $user->is_instructor() ) {
//				return array();
//			}
//
//			$courses = LP_CO_Instructor_DB::getInstance()->get_post_of_instructor( $user->get_id() );
//
//			/*$course_factory = new LP_Course_CURD();
//			$course_factory->read_course_sections( $courses );*/
//
//			return $courses;
//		}

		/**
		 * Get all editable lessons of current user, return array lessons id.
		 *
		 * @param $courses
		 *
		 * @return array
		 * @since 3.0.0
		 * @deprecated 4.0.4
		 */
//		public function get_available_lessons( $courses ) {
//			$user_id = get_current_user_id();
//
//			/**
//			 * Cache available lessons for instructor
//			 *
//			 * @since 3.0.0
//			 */
//			$lessons = wp_cache_get( 'user-' . $user_id, 'co-instructor-lessons' );
//			if ( false === $lessons ) {
//				global $wpdb;
//
//				$query = $wpdb->prepare(
//					"
//					SELECT ID FROM $wpdb->posts
//					WHERE ( post_type = %s OR post_type = %s )
//					AND post_author = %d
//				",
//					'lpr_lesson',
//					'lp_lesson',
//					get_current_user_id()
//				);
//
//				$lessons = $wpdb->get_col( $query );
//				if ( $courses ) {
//					foreach ( $courses as $course_id ) {
//						$temp    = $this->get_available_lesson_from_course( $course_id );
//						$lessons = array_unique( array_merge( $lessons, $temp ) );
//					}
//				}
//
//				wp_cache_set( 'user-' . $user_id, $lessons, 'co-instructor-lessons' );
//			}
//
//			return $lessons;
//		}

		/**
		 * Get all editable quizzes of current user, return array quizzes id.
		 *
		 * @param $courses
		 *
		 * @return array
		 * @since 3.0.0
		 * @deprecated 4.0.4
		 */
//		public function get_available_quizzes( $courses ) {
//			$user_id = get_current_user_id();
//
//			/**
//			 * Cache quizzes for instructor
//			 *
//			 * @since 3.0.0
//			 */
//			$quizzes = wp_cache_get( 'user-' . $user_id, 'co-instructor-quizzes' );
//			if ( false === $quizzes ) {
//				global $wpdb;
//				$query = $wpdb->prepare(
//					"
//					SELECT ID FROM $wpdb->posts
//					WHERE ( post_type = %s OR post_type = %s )
//					AND post_author = %d
//				",
//					'lpr_quiz',
//					'lp_quiz',
//					get_current_user_id()
//				);
//
//				// get quizzes of self co-instructor.
//				$quizzes = $wpdb->get_col( $query );
//				if ( $courses ) {
//					foreach ( $courses as $course ) {
//						$temp    = $this->get_available_quizzes_from_course( $course );
//						$quizzes = array_unique( array_merge( $quizzes, $temp ) );
//					}
//				}
//
//				wp_cache_set( 'user-' . $user_id, $quizzes, 'co-instructor-quizzes' );
//			}
//
//			return $quizzes;
//		}

		/**
		 * @deprecated 4.0.4
		 */
		/*public function get_available_questions( $quizzes ) {
			global $wpdb;

			$query = $wpdb->prepare(
				"
				SELECT ID FROM $wpdb->posts
				WHERE  post_type = %s
				AND post_author = %d",
				'lp_question',
				get_current_user_id()
			);

			$questions = $wpdb->get_col( $query );

			if ( $quizzes ) {
				foreach ( $quizzes as $quiz ) {
					$temp      = $this->get_available_question_from_quiz( $quiz );
					$questions = array_unique( array_merge( $questions, $temp ) );
				}
			}

			return $questions;
		}*/

		/**
		 * Get all lessons from course.
		 *
		 * @param null $course_id
		 *
		 * @return array
		 * @since 3.0.0
		 * @deprecated 4.0.4
		 */
		/*public function get_available_lesson_from_course( $course_id = null ) {
			if ( empty( $course_id ) ) {
				return array();
			}

			$course  = learn_press_get_course( $course_id );
			$lessons = $course->get_items( LP_LESSON_CPT );

			$available = array();

			if ( $lessons ) {
				foreach ( $lessons as $lesson_id ) {
					$available[ $lesson_id ] = absint( $lesson_id );
				}
			}

			return $available;
		}*/

		/**
		 * Get all quizzes from course, return array quizzes ids.
		 *
		 * @param null $course_id
		 *
		 * @return array
		 * @since 3.0.0
		 * @deprecated 4.0.4
		 */
		/*public function get_available_quizzes_from_course( $course_id = null ) {
			if ( empty( $course_id ) ) {
				return array();
			}

			$course  = learn_press_get_course( $course_id );
			$quizzes = $course->get_items( LP_QUIZ_CPT );

			$available = array();

			if ( $quizzes ) {
				foreach ( $quizzes as $quiz_id ) {
					$available[ $quiz_id ] = absint( $quiz_id );
				}
			}

			return $available;
		}*/

		/**
		 * Get all questions form quiz, return array questions ids.
		 *
		 * @param null $quiz_id
		 *
		 * @return array
		 * @deprecated 4.0.4
		 */
		/*public function get_available_question_from_quiz( $quiz_id = null ) {
			if ( empty( $quiz_id ) ) {
				return array();
			}

			$quiz      = learn_press_get_quiz( $quiz_id );
			$questions = $quiz->get_question_ids();

			$available = array();
			foreach ( $questions as $question_id ) {
				$available[] = absint( $question_id );
			}

			return $available;
		}*/

		/**
		 * Valid lessons.
		 *
		 * @return array
		 * @deprecated 4.0.4
		 */
		/*public function co_instructor_valid_lessons() {
			$courses = $this->get_available_courses();

			return $this->get_available_lessons( $courses );
		}*/

		/**
		 * Valid quizzes.
		 *
		 * @return array
		 * @deprecated 4.0.4
		 */
		/*public function co_instructor_valid_quizzes() {
			$courses = $this->get_available_courses();

			return $this->get_available_quizzes( $courses );
		}*/

		/**
		 * Valid questions.
		 *
		 * @return array
		 * @deprecated 4.0.4
		 */
		/*public function co_instructor_valid_questions() {
			$quizzes = $this->co_instructor_valid_quizzes();

			return $this->get_available_questions( $quizzes );
		}*/

		/**
		 * Add co-instructor settings in admin settings.
		 *
		 * @param $settings
		 * @param $object
		 *
		 * @return array
		 */
		public function co_instructor_settings( $settings, $object ) {
			$instructor_setting = array(
				'title'       => esc_html__( 'Instructor', 'learnpress-co-instructor' ),
				'id'          => 'profile_endpoints[profile-instructor]',
				'default'     => 'instructor',
				'type'        => 'text',
				'placeholder' => '',
				'desc'        =>
					__(
						'This is a slug and should be unique.',
						'learnpress-co-instructor'
					) . sprintf(
						' %s <code>[profile/admin/instructor]</code>',
						__( 'Example link is', 'learnpress-co-instructor' )
					),
			);

			$instructor_setting = apply_filters(
				'learn_press_page_settings_item_instructor',
				$instructor_setting,
				$settings,
				$object
			);

			$new_settings = array();

			foreach ( $settings as $index => $setting ) {
				$new_settings[] = $setting;

				if ( isset( $setting['id'] ) && $setting['id'] === 'profile_endpoints[profile-order-details]' ) {
					$new_settings[]     = $instructor_setting;
					$instructor_setting = false;
				}
			}

			if ( $instructor_setting ) {
				$new_settings[] = $instructor_setting;
			}

			return $new_settings;
		}

		/**
		 * Insert post author of items in course.
		 *
		 * @param $args
		 *
		 * @return mixed
		 * @deprecated 4.0.4
		 */
		/*public function course_insert_item_args( $args ) {
			$owner               = $this->get_own_user_of_post();
			$args['post_author'] = $owner;

			return $args;
		}*/

		/**
		 * Insert post author of items in quiz.
		 *
		 * @param $args
		 * @param $quiz_id
		 *
		 * @return mixed
		 * @deprecated 4.0.4
		 */
		/*public function quiz_insert_question_args( $args, $quiz_id ) {
			$author = get_current_user_id();

			if ( ! empty( $quiz_id ) ) {
				$post   = get_post( $quiz_id );
				$author = $post->post_author;
			}

			if ( ! empty( $author ) ) {
				$args['post_author'] = $author;
			}

			return $args;
		}*/

		/**
		 * Get own user.
		 *
		 * @return int
		 * @deprecated 4.0.4
		 */
		/*public function get_own_user_of_post() {
			global $post;

			if ( current_user_can( 'administrator' ) && isset( $_REQUEST['_lp_course_author'] ) && ! empty( $_REQUEST['_lp_course_author'] ) ) {
				$user = $_REQUEST['_lp_course_author'];
			} else {
				$user = $post->post_author;
			}

			return absint( $user );
		}*/

		/**
		 * Add instructor tab in profile page.
		 *
		 * @param $tabs
		 *
		 * @return array
		 */
		public function add_profile_instructor_tab( $tabs ) {
			$tab = apply_filters(
				'learn-press-co-instructor/profile-tab',
				array(
					'title'    => esc_html__( 'Co-Instructor', 'learnpress-co-instructor' ),
					'icon'     => '<i class="fas fa-user-edit"></i>',
					'callback' => array( $this, 'profile_instructor_tab_content' ),
				),
				$tabs
			);

			$instructor_endpoint = LearnPress::instance()->settings()->get( 'profile_endpoints.profile-instructor', 'instructor' );

			if ( empty( $instructor_endpoint ) || empty( $tab ) ) {
				return $tabs;
			}

			if ( in_array( $instructor_endpoint, array_keys( $tabs ) ) ) {
				return $tabs;
			}

			$instructor = array( $instructor_endpoint => $tab );

			$course_endpoint = LearnPress::instance()->settings()->get( 'profile_endpoints.profile-courses' );

			if ( ! empty( $course_endpoint ) ) {
				$pos  = array_search( $course_endpoint, array_keys( $tabs ) ) + 1;
				$tabs = array_slice( $tabs, 0, $pos, true ) + $instructor + array_slice(
					$tabs,
					$pos,
					count( $tabs ) - 1,
					true
				);
			} else {
				$tabs = $tabs + $instructor;
			}

			return $tabs;
		}

		/**
		 * Get instructor tab content in profile page.
		 *
		 * @param $current
		 * @param $tab
		 * @param $user
		 */
		public function profile_instructor_tab_content( $current, $tab, $user ) {
			learn_press_get_template(
				'profile-tab.php',
				array(
					'user'    => $user,
					'current' => $current,
					'tab'     => $tab,
				),
				learn_press_template_path() . '/addons/co-instructors/',
				LP_ADDON_CO_INSTRUCTOR_PATH . '/templates/'
			);
		}

		/**
		 * Show list instructors in single course page.
		 * @deprecated 4.0.4
		 */
		/*public function single_course_instructors() {
			$course = LP_Global::course();

			$course_id   = $course->get_id();
			$instructors = $this->get_instructors( $course_id );

			learn_press_get_template(
				'single-course-tab.php',
				array( 'instructors' => $instructors ),
				learn_press_template_path() . '/addons/co-instructors/',
				LP_ADDON_CO_INSTRUCTOR_TEMPLATE
			);
		}*/

		/**
		 * Get all course instructors.
		 *
		 * @param CourseModel $course
		 *
		 * @return mixed
		 */
		public function get_instructors( CourseModel $course ) {
			return get_post_meta( $course->get_id(), '_lp_co_teacher' );
		}

		/**
		 * Excerpt duplicate post meta.
		 *
		 * @param $excerpt
		 * @param $old_post_id
		 * @param $new_post_id
		 *
		 * @return array
		 * @deprecated 4.0.4
		 */
		/*public function excerpt_duplicate_post_meta( $excerpt, $old_post_id, $new_post_id ) {
			if ( ! in_array( '_lp_co_teacher', $excerpt ) ) {
				$excerpt[] = '_lp_co_teacher';
			}

			return $excerpt;
		}*/

		/**
		 * Check condition user can edit course (has is co-instructor).
		 *
		 * @param int $user_id
		 * @param CourseModel $course
		 *
		 * @return bool
		 * @since 4.0.4
		 * @version 1.0.0
		 */
		public function is_co_in_course( int $user_id, CourseModel $course ): bool {
			if ( ! user_can( $user_id, LP_TEACHER_ROLE ) ) {
				return false;
			}

			$instructors = $this->get_instructors( $course );
			if ( ! $instructors ) {
				return false;
			}

			if ( in_array( $user_id, $instructors ) ) {
				return true;
			}

			return false;
		}

		public function get_courses_by_co_instructor( $user_id ) {
			return [];
		}
	}
}
