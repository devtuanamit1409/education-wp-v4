<?php
/**
 * Plugin load class.
 *
 * @author   ThimPress
 * @package  LearnPress/myCRED/Classes
 * @version  3.0.1
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LP_Addon_myCRED' ) ) {
	/**
	 * Class LP_Addon_MyCred.
	 */
	class LP_Addon_myCRED extends LP_Addon {

		/**
		 * @var string
		 */
		public $version = LP_ADDON_MYCRED_VER;

		/**
		 * @var string
		 */
		public $require_version = LP_ADDON_MYCRED_REQUIRE_VER;

		/**
		 * Path file addon
		 *
		 * @var string
		 */
		public $plugin_file = LP_ADDON_MYCRED_FILE;

		/**
		 * @var null
		 */
		protected static $_instance = null;

		/**
		 * LP_Addon_Students_List constructor.
		 */
		public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
		}

		public function plugins_loaded() {
			if ( ! $this->mycred_is_active() ) {
				add_action( 'admin_notices', array( $this, 'admin_notices' ) );
			} else {
				parent::__construct();
			}
		}

		/**
		 * Define Learnpress myCRED constants.
		 *
		 * @since 3.0.0
		 */
		protected function _define_constants() {
			define( 'LP_ADDON_MYCRED_PATH', dirname( LP_ADDON_MYCRED_FILE ) );
			define( 'LP_ADDON_MYCRED_INC', LP_ADDON_MYCRED_PATH . '/inc/' );
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 *
		 * @since 3.0.0
		 */
		protected function _includes() {
			include_once LP_ADDON_MYCRED_PATH . '/inc/hooks/mycred-hook-learnpress-learner.php';
			include_once LP_ADDON_MYCRED_PATH . '/inc/hooks/mycred-hook-learnpress-instructor.php';
		}

		/**
		 * Init hooks.
		 */
		protected function _init_hooks() {
			if ( did_action( 'learn-press/mycred-addon-loaded' ) ) {
				return;
			}
			add_filter( 'mycred_setup_addons', array( $this, 'register_mycred_addon' ), 10, 1 );
			add_filter( 'mycred_load_modules', array( $this, 'load_learnpress_cred_addon' ), 10, 2 );
			add_filter( 'mycred_setup_hooks', array( $this, 'register_hook_instructor' ) );
			add_filter( 'mycred_setup_hooks', array( $this, 'register_hook_learner' ) );

			do_action( 'learn-press/mycred-addon-loaded' );
		}

		/**
		 * Assets.
		 */
		protected function _enqueue_assets() {
			wp_enqueue_style( 'lp-mycred', $this->get_plugin_url( 'assets/css/styles.css' ) );
		}

		/**
		 * Register Learnpress addon for myCRED.
		 *
		 * @return mixed
		 */
		public function register_mycred_addon($installed) {
			$installed['learnpress'] = array(
				'name'        => 'LearnPress',
				'description' => __( 'Integrating with learning management system provided by LearnPress.', 'learnpress-mycred' ),
				'addon_url'   => 'https://thimpress.com/product/mycred-add-on-for-learnpress/',
				'version'     => '3.0.0',
				'author'      => 'ThimPress',
				'author_url'  => 'http://thimpress.com',
				'screenshot'  => 'https://thimpress.com/wp-content/uploads/2015/07/myCRED.jpg'
			);

			return $installed;
		}

		/**
		 * @param $modules
		 * @param $point_types
		 *
		 * @return mixed
		 */
		public function load_learnpress_cred_addon( $modules, $point_types ) {
			$file = LP_ADDON_MYCRED_PATH . '/inc/addon/mycred-addon-learnpress.php';
			if ( file_exists( $file ) ) {
				require_once $file;
				$modules['solo']['learnpress'] = new myCRED_LearnPress_Module();
				$modules['solo']['learnpress']->load();
			}

			return $modules;
		}

		/**
		 * Register hook LearnPress for instructor.
		 *
		 * @param $installed
		 *
		 * @return mixed
		 */
		public function register_hook_instructor( $installed ) {
			$installed['learnpress_instructor'] = array(
				'title'       => __( 'LearnPress: for instructors', 'learnpress-mycred' ),
				'description' => __( 'Award %_plural% to users who are teaching in LearnPress courses system.', 'learnpress-mycred' ),
				'callback'    => array( 'myCred_LearnPress_Instructor' )
			);

			return $installed;
		}

		/**
		 * Register hook LearnPress for learner.
		 *
		 * @param $installed
		 *
		 * @return mixed
		 */
		public function register_hook_learner( $installed ) {
			$installed['learnpress_learner'] = array(
				'title'       => __( 'LearnPress: for students', 'learnpress-mycred' ),
				'description' => __( 'Award %_plural% to users who are learning in LearnPress courses system.', 'learnpress-mycred' ),
				'callback'    => array( 'myCred_LearnPress_Learner' )
			);

			return $installed;
		}

		/**
		 * Check myCRED active.
		 *
		 * @return bool
		 */
		public function mycred_is_active() {
			return class_exists( 'myCRED_Core' );
		}

		/**
		 * Show admin notice when inactive myCRED.
		 */
		public function admin_notices() {
			?>
            <div class="notice notice-error">
                <p>
					<?php echo wp_kses(
						sprintf(
							__( '<strong>myCRED</strong> addon for <strong>LearnPress</strong> requires <a href="%s" target="_blank">myCRED</a> plugin is <strong>installed</strong> and <strong>activated</strong>.', 'learnpress-bbpress' ),
							admin_url( 'plugin-install.php?tab=search&type=term&s=myCRED' )
						), array(
							'a'      => array(
								'href'   => array(),
								'target' => array(),
							),
							'strong' => array()
						)
					); ?>
                </p>
            </div>
		<?php }
	}
}
