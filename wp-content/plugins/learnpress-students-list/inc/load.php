<?php
/**
 * Plugin load class.
 *
 * @author   ThimPress
 * @package  LearnPress/Students-List/Classes
 * @version  3.0.0
 */

use LearnPress\Helpers\Template;
use LearnPress\StudentsList\StudentsListTemplate;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LP_Addon_Students_List' ) ) {
	/**
	 * Class LP_Addon_Students_List
	 */
	class LP_Addon_Students_List extends LP_Addon {

		/**
		 * @var string
		 */
		public $version = LP_ADDON_STUDENTS_LIST_VER;

		/**
		 * @var string
		 */
		public $require_version = LP_ADDON_STUDENTS_LIST_REQUIRE_VER;

		/**
		 * Path file addon
		 *
		 * @var string
		 */
		public $plugin_file = LP_ADDON_STUDENTS_LIST_FILE;

		/**
		 * LP_Addon_Students_List constructor.
		 */
		public function __construct() {
			parent::__construct();
		}

		/**
		 * Define Learnpress Students List constants.
		 *
		 * @since 3.0.0
		 */
		protected function _define_constants() {
			define( 'LP_ADDON_STUDENTS_LIST_PATH', dirname( LP_ADDON_STUDENTS_LIST_FILE ) );
			define( 'LP_ADDON_STUDENTS_LIST_INC', LP_ADDON_STUDENTS_LIST_PATH . '/inc/' );
			define( 'LP_ADDON_STUDENTS_LIST_TEMPLATE', LP_ADDON_STUDENTS_LIST_PATH . '/templates/' );
		}

		/**
		 * Includes.
		 */
		protected function _includes() {
			include_once LP_ADDON_STUDENTS_LIST_PATH . '/inc/widgets.php';
			include_once LP_ADDON_STUDENTS_LIST_PATH . '/inc/shortcodes.php';
			require_once LP_ADDON_STUDENTS_LIST_PATH . '/inc/StudentsListTemplate.php';
			StudentsListTemplate::instance();
		}

		/**
		 * Init hooks.
		 */
		protected function _init_hooks() {
			add_filter(
				'lp/course/meta-box/fields/general',
				function ( $data ) {
					$data['_lp_hide_students_list'] = new LP_Meta_Box_Checkbox_Field(
						esc_html__( 'Students List', 'learnpress-students-list' ),
						esc_html__( 'Hide the students list in each individual course.', 'learnpress-students-list' ),
						'no'
					);

					return $data;
				}
			);

			// add student list tab in single course
			add_filter( 'learn-press/course-tabs', array( $this, 'add_single_course_students_list_tab' ), 5 );

			// Enqueue scripts
			add_filter( 'learn-press/frontend-default-scripts', array( $this, 'enqueue_js' ) );
			// Enqueue styles
			add_filter( 'learn-press/frontend-default-styles', array( $this, 'enqueue_style' ) );

			// Add settings
			add_filter( 'learn-press/courses-settings-fields', [ $this, 'settings' ], 10, 1 );
		}

		/**
		 * Register or enqueue js
		 *
		 * @param array $scripts
		 *
		 * @return array
		 * @since 4.0.1
		 * @version 1.0.1
		 */
		public function enqueue_js( array $scripts ): array {
			$min = '.min';
			if ( LP_Debug::is_debug() ) {
				$min = '';
			}
			$url = $this->get_plugin_url( "assets/js/dist/frontend/students-list{$min}.js" );

			$scripts['addon-lp-students-list'] = new LP_Asset_Key(
				$url,
				[],
				[],
				1,
				0,
				LP_ADDON_STUDENTS_LIST_VER,
				[
					'strategy' => 'async',
				]
			);

			return $scripts;
		}

		/**
		 * Register or enqueue styles
		 *
		 * @param array $styles
		 *
		 * @return array
		 * @since 4.0.2
		 * @version 1.0.0
		 */
		public function enqueue_style( array $styles ): array {
			$min    = '.min';
			$is_rtl = is_rtl() ? '-rtl' : '';
			if ( LP_Debug::is_debug() ) {
				$min = '';
			}
			$url = $this->get_plugin_url( "assets/css/students-list{$is_rtl}{$min}.css" );

			$styles['addon-lp-students-list'] = new LP_Asset_Key(
				$url,
				[],
				[],
				1,
				0,
				LP_ADDON_STUDENTS_LIST_VER
			);

			return $styles;
		}

		/**
		 * Students list tab in single course page.
		 *
		 * @param $tabs
		 *
		 * @return mixed
		 */
		public function add_single_course_students_list_tab( $tabs ) {
			$course = learn_press_get_course();
			if ( ! $course ) {
				return $tabs;
			}

			$hide_students_list = get_post_meta( $course->get_id(), '_lp_hide_students_list', true );
			if ( $hide_students_list === 'yes' ) {
				return $tabs;
			}

			$tabs['students-list'] = array(
				'title'    => __( 'Students List', 'learnpress-announcements' ),
				'priority' => 40,
				'callback' => array( $this, 'single_course_students_list_tab_content' ),
			);

			return $tabs;
		}

		/**
		 * Students list tab content in single course page.
		 *
		 * @since 4.0.0
		 * @version 1.0.1
		 */
		public function single_course_students_list_tab_content() {
			$course = learn_press_get_course();
			// Not allow override template
			Template::instance()->get_template( LP_ADDON_STUDENTS_LIST_TEMPLATE . '/students-list.php', compact( 'course' ) );
			//LP_Addon_Students_List_Preload::$addon->get_template( 'students-list.php', compact( 'course' ) );
		}

		/**
		 * Register setting fields
		 *
		 * @param array $settings LP Course Settings
		 */
		public function settings( array $settings = [] ): array {
			$setting_student_list = include_once LP_ADDON_STUDENTS_LIST_PATH . '/config/settings.php';

			return array_merge( $settings, $setting_student_list );
		}
	}
}
