<?php
/**
 * Plugin load class.
 *
 * @author   ThimPress
 * @package  LearnPress/Prerequisites-Courses/Classes
 * @version  3.0.1
 */

// Prevent loading this file directly
use LearnPress\Helpers\Template;
use LearnPress\Models\CourseModel;
use LearnPress\Models\UserItems\UserCourseModel;
use LearnPress\Models\UserModel;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LP_Addon_Prerequisites_Courses' ) ) {
	/**
	 * Class LP_Addon_Prerequisites_Courses
	 */
	class LP_Addon_Prerequisites_Courses extends LP_Addon {

		/**
		 * @var string
		 */
		public $version = LP_ADDON_PREREQUISITES_COURSES_VER;

		/**
		 * @var string
		 */
		public $require_version = LP_ADDON_PREREQUISITES_COURSES_REQUIRE_VER;

		/**
		 * Path file addon
		 *
		 * @var string
		 */
		public $plugin_file = LP_ADDON_PREREQUISITES_COURSES_FILE;

		const META_KEY_ALLOW_PURCHASE = '_lp_prerequisite_allow_purchase';
		const META_KEY_ENABLE         = '_lp_course_prerequisite';

		/**
		 * LP_Addon_Prerequisites_Courses constructor.
		 */
		public function __construct() {
			parent::__construct();
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 *
		 * @since 3.0.0
		 */
		protected function _includes() {
			require_once LP_ADDON_PREREQUISITES_COURSES_PATH . '/inc/class-lp-prere-hooks.php';
		}

		/**
		 * Get prerequisites of course.
		 *
		 * @param int $course_id
		 *
		 * @return array
		 */
		public static function get_prerequisite_courses( int $course_id = 0 ): array {
			$required_course_ids = get_post_meta( $course_id, '_lp_course_prerequisite', true );
			return ! empty( $required_course_ids ) ? $required_course_ids : [];
		}

		/**
		 * Check course is allow purchase.
		 *
		 * @param CourseModel $course
		 *
		 * @return bool
		 */
		public function is_allow_purchase_course( CourseModel $course ): bool {
			$is_allow = $course->get_meta_value_by_key( self::META_KEY_ALLOW_PURCHASE, 'yes' );

			return 'yes' === $is_allow;
		}

		/**
		 * Check can enroll course.
		 *
		 * @param CourseModel $course
		 * @param $user
		 *
		 * @return bool
		 * @since 4.0.8
		 * @version 1.0.0
		 */
		public function check_condition( CourseModel $course, $user ): bool {
			try {
				$required_course_ids = self::get_prerequisite_courses( $course->get_id() );
				if ( empty( $required_course_ids ) ) {
					return true;
				}

				$user_id = 0;
				if ( $user instanceof UserModel ) {
					$user_id = $user->get_id();
				}

				// Check prerequisites courses passed.
				foreach ( $required_course_ids as $required_course_id ) {
					$course = CourseModel::find( $required_course_id, true );
					if ( ! $course ) {
						continue;
					}

					$userCourseCheck = UserCourseModel::find( $user_id, $required_course_id, true );
					if ( ! $user_id || ! $userCourseCheck || ! $userCourseCheck->is_passed() ) {
						throw new Exception( esc_html__( 'Course has prerequisite courses not passed', 'learnpress-prerequisites-courses' ) );
					}
				}

				return true;
			} catch ( Throwable $e ) {
				return false;
			}
		}

		/**
		 * HTML message prerequisites courses.
		 *
		 * @param CourseModel $course
		 * @param UserModel|false $user
		 *
		 * @return string
		 * @since 4.0.8
		 */
		public function html_prerequisite_courses( CourseModel $course, $user ): string {
			$html                = '';
			$required_course_ids = self::get_prerequisite_courses( $course->get_id() );
			if ( empty( $required_course_ids ) ) {
				return $html;
			}

			wp_enqueue_style( 'lp-prerequisites-courses' );
			$user_id = 0;
			if ( $user instanceof UserModel ) {
				$user_id = $user->get_id();
			}

			$html_list = '';
			foreach ( $required_course_ids as $required_course_id ) {
				$course = CourseModel::find( $required_course_id, true );
				if ( ! $course ) {
					continue;
				}

				$graduation = '';
				if ( $user instanceof UserModel ) {
					$userCourse = UserCourseModel::find( $user_id, $course->get_id(), true );
					if ( $userCourse ) {
						$graduation = $userCourse->get_graduation();
					}
				}

				$html_list .= sprintf(
					'<li class="%s"><a href="%s">%s%s</a></li>',
					esc_attr( $graduation ),
					esc_url_raw( get_the_permalink( $course->get_id() ) ),
					$course->get_title(),
					$graduation ? sprintf(
						'<span class="lp-course-prerequisite-status"> (%s)</span>',
						learn_press_course_grade_html( $graduation, false )
					) : ''
				);
			}

			$section = apply_filters(
				'learn-press/prerequisites-course/html-message',
				[
					'wrapper'             => '<div class="lp-prerequisite">',
					'message_content'     => sprintf(
						'<div class="message-text">%s</div>',
						__(
							'NOTE: You have to pass these courses before you can enroll this course.',
							'learnpress-prerequisites-courses'
						)
					),
					'ul_list_courses'     => '<ul class="list-course-prerequisite">',
					'list_item'           => $html_list,
					'ul_list_courses_end' => '</ul>',
					'wrapper_end'         => '</div>',
				],
				$course,
				$user
			);

			return Template::combine_components( $section );
		}

		/**`
		 * Show notice required pass prerequisites courses.
		 *
		 * @since 3.0.0
		 * @version 3.0.1
		 * @editor tungnx
		 * @Todo theme coaching, eduma is using this function
		 */
		public function enroll_notice() {
		}
	}
}

add_action( 'plugins_loaded', array( 'LP_Addon_Prerequisites_Courses', 'instance' ) );
