<?php
/**
 * Plugin load class.
 *
 * @author   ThimPress
 * @package  LearnPress/Authorizenet/Classes
 * @version  3.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LP_Addon_Authorizenet_Payment' ) ) {
	/**
	 * Class LP_Addon_Authorizenet_Payment
	 */
	class LP_Addon_Authorizenet_Payment extends LP_Addon {

		/**
		 * @var string
		 */
		public $version = LP_ADDON_AUTHORIZENET_VER;

		/**
		 * @var string
		 */
		public $require_version = LP_ADDON_AUTHORIZENET_REQUIRE_VER;

		/**
		 * Path file addon.
		 *
		 * @var string
		 */
		public $plugin_file = LP_ADDON_AUTHORIZENET_FILE;

		/**
		 * LP_Addon_Authorizenet_Payment constructor.
		 */
		public function __construct() {
			parent::__construct();
		}

		/**
		 * Define Learnpress Authorize.net payment constants.
		 *
		 * @since 3.0.0
		 */
		protected function _define_constants() {
			define( 'LP_ADDON_AUTHORIZENET_PATH', dirname( LP_ADDON_AUTHORIZENET_FILE ) );
			define( 'LP_ADDON_AUTHORIZENET_TEMPLATE', LP_ADDON_AUTHORIZENET_PATH . '/templates/' );
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 *
		 * @since 3.0.0
		 */
		protected function _includes() {
			require_once( LP_ADDON_AUTHORIZENET_PATH . '/inc/class-lp-gateway-authorizenet.php' );
		}

		/**
		 * Init hooks.
		 */
		protected function _init_hooks() {
			// add payment gateway class
			add_filter( 'learn_press_payment_method', array( $this, 'add_payment' ) );
			add_filter( 'learn-press/payment-methods', array( $this, 'add_payment' ) );
			add_filter( 'learn-press/frontend-default-scripts', array( $this, 'enqueue_script' ) );
		}

		/**
		 * Add Authorize.Net to payment system.
		 *
		 * @param $methods
		 *
		 * @return mixed
		 */
		public function add_payment( $methods ) {
			$methods['authorizenet'] = 'LP_Gateway_Authorizenet';

			return $methods;
		}

		/**
		 * Enqueue script
		 *
		 * @param $scripts
		 *
		 * @return mixed
		 */
		public function enqueue_script( $scripts ) {
			$min = LP_Assets::$_min_assets;

			$scripts['lp-authorizenet'] = new LP_Asset_Key(
				$this->get_plugin_url( "assets/authorizenet{$min}.js" ),
				array( 'jquery' ),
				array( LP_PAGE_CHECKOUT ),
				0,
				1
			);

			return $scripts;
		}
	}
}
