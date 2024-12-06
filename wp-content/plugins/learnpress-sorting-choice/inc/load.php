<?php
/**
 * Plugin load class.
 *
 * @author   ThimPress
 * @package  LearnPress/Sorting-Choice/Classes
 * @version  3.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LP_Addon_Sorting_Choice' ) ) {

	/**
	 * Class LP_Addon_Sorting_Choice
	 */
	class LP_Addon_Sorting_Choice extends LP_Addon {

		/**
		 * @var string
		 */
		public $version = LP_ADDON_SORTING_CHOICE_VER;

		/**
		 * @var string
		 */
		public $require_version = LP_ADDON_SORTING_CHOICE_REQUIRE_VER;

		/**
		 * Path file addon
		 *
		 * @var string
		 */
		public $plugin_file = LP_ADDON_SORTING_CHOICE_FILE;

		/**
		 * LP_Addon_Sorting_Choice constructor.
		 */
		public function __construct() {
			parent::__construct();
		}

		/**
		 * Init plugin.
		 *
		 * @since 3.0.0
		 */
		public function init() {
			$this->_define_constants();
			$this->_includes();
			$this->_init_hooks();
		}

		/**
		 * Define Learnpress Sorting choice constants.
		 *
		 * @since 3.0.0
		 */
		protected function _define_constants() {
			if ( ! defined( 'LP_QUESTION_SORTING_CHOICE_PATH' ) ) {
				define( 'LP_QUESTION_SORTING_CHOICE_PATH', dirname( LP_ADDON_SORTING_CHOICE_FILE ) );
				define( 'LP_QUESTION_SORTING_CHOICE_ASSETS', LP_QUESTION_SORTING_CHOICE_PATH . '/assets/' );
				define( 'LP_QUESTION_SORTING_CHOICE_INC', LP_QUESTION_SORTING_CHOICE_PATH . '/inc/' );
			}
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 */
		protected function _includes() {
			include_once LP_QUESTION_SORTING_CHOICE_INC . 'class-lp-question-sorting-choice.php';
		}

		/**
		 * Hook into actions and filters.
		 *
		 * @since 3.0.0
		 */
		protected function _init_hooks() {
			add_filter(
				'learn-press/question-types',
				function( $types ) {
					$types['sorting_choice'] = esc_html__( 'Sorting Choice', 'learnpress-sorting-choice' );

					return $types;
				}
			);

			add_filter(
				'learn-press/default-question-types-support-answer-options',
				function( $data ) {
					$data[] = 'sorting_choice';

					return $data;
				}
			);

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 1 );
		}

		public function enqueue_scripts() {
			if ( LP_Global::course_item_quiz() ) {
				$url = '/assets/dist/min/sorting-choice-bundle.min.js';

				if ( LP_Debug::is_debug() ) {
					$url = '/assets/dist/sorting-choice-bundle.js';
				}

				wp_enqueue_script(
					'lp-sorting-choice',
					plugins_url( $url, LP_ADDON_SORTING_CHOICE_FILE ),
					array(
						'wp-i18n',
						'wp-element',
						'lp-question-types',
					),
					LP_ADDON_SORTING_CHOICE_VER,
					false // Required - Nhamdv
				);
			}
		}
	}
}
