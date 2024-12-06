<?php
/**
 * Plugin load class.
 *
 * @author   ThimPress
 * @package  LearnPress/Random-Quiz/Classes
 * @version  3.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LP_Addon_Random_Quiz' ) ) {
	/**
	 * Class LP_Addon_Random_Quiz
	 */
	class LP_Addon_Random_Quiz extends LP_Addon {
		/**
		 * @var string Key lp_user_itemmeta.
		 */
		public static $key_quiz_ids_random = 'question_ids_random';
		/**
		 * @var string Key option "Enable" random questions.
		 */
		public static $key_quiz_random_enable = 'question_random_enable';
		/**
		 * @var string Key option enable random questions.
		 */
		public static $key_number_questions_random = 'number_questions_random';
		/**
		 * @var string version.
		 */
		public $version = LP_ADDON_RANDOM_QUIZ_VER;

		/**
		 * @var string
		 */
		public $require_version = LP_ADDON_RANDOM_QUIZ_REQUIRE_VER;

		/**
		 * Path file addon
		 *
		 * @var string
		 */
		public $plugin_file = LP_ADDON_RANDOM_QUIZ_FILE;

		/**
		 * LP_Addon_Random_Quiz constructor.
		 */
		public function __construct() {
			parent::__construct();

			add_filter( 'learn-press/admin-default-scripts', [ $this, 'admin_enqueue_scripts' ] );
			add_filter( 'learn-press/admin-default-styles', [ $this, 'admin_enqueue_styles' ] );
		}

		/**
		 * Enqueue scripts
		 *
		 * It enqueues scripts.
		 */
		public function admin_enqueue_scripts( array $scripts ): array {
			$lp_admin_assets = LP_Admin_Assets::instance();

			$scripts['lp-random-quiz'] = new LP_Asset_Key(
				$this->get_plugin_url( 'assets/dist/js/admin-random-quiz' . $lp_admin_assets::$_min_assets . '.js' )
			);

			return $scripts;
		}

		/**
		 * Enqueue styles
		 *
		 * It enqueues styles.
		 */
		public function admin_enqueue_styles( array $styles ): array {
			$lp_admin_assets = LP_Admin_Assets::instance();

			$styles['lp-admin-random-quiz'] = new LP_Asset_Key(
				$this->get_plugin_url( 'assets/dist/css/admin-random-quiz' . $lp_admin_assets::$_min_assets . '.css' ),
				[],
				[],
				1,
				1,
				LP_ADDON_RANDOM_QUIZ_VER
			);

			return $styles;
		}

		/**
		 * Define Learnpress Random Quiz constants.
		 *
		 * @since 3.0.0
		 */
		public function _define_constants() {
			define( 'LP_RANDOM_QUIZ_PATH', dirname( LP_ADDON_RANDOM_QUIZ_FILE ) );
		}

		/**
		 * Include files needed
		 */
		protected function _includes() {
			//meta-box content
			require_once LP_PLUGIN_PATH . 'inc/admin/views/meta-boxes/fields/class-lp-meta-box-fields.php';
			require_once LP_RANDOM_QUIZ_PATH . '/inc/class-lp-metabox-random-questions.php';
			//hooks
			require_once LP_RANDOM_QUIZ_PATH . '/inc/class-lp-random-quiz-hooks.php';
			LP_Random_Quiz_Hooks::instance();
		}

		/**
		 * Check is enable questions random
		 *
		 * @param $quiz_id
		 *
		 * @return bool
		 */
		public function is_enable_questions_rand( $quiz_id ): bool {
			return get_post_meta( $quiz_id, self::$key_quiz_random_enable, true ) == 'yes';
		}

		/**
		 * Check is enable questions random
		 *
		 * @param $quiz_id
		 *
		 * @return int
		 */
		public function get_number_question_rand( $quiz_id ): int {
			return (int) get_post_meta( $quiz_id, self::$key_number_questions_random, true );
		}
	}
}

//add_action( 'plugins_loaded', array( 'LP_Addon_Random_Quiz', 'instance' ) );
