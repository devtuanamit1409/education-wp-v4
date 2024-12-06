<?php
/**
 * Plugin load class.
 *
 * @author   ThimPress
 * @package  LearnPress/Instamojo/Classes
 * @version  4.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LP_Addon_Instamojo_Payment' ) ) {
	/**
	 * Class LP_Addon_Instamojo_Payment
	 */
	class LP_Addon_Instamojo_Payment extends LP_Addon {

		/**
		 * @var string
		 */
		public $version = LP_ADDON_INSTAMOJO_PAYMENT_VER;

		/**
		 * @var string
		 */
		public $require_version = LP_ADDON_INSTAMOJO_PAYMENT_REQUIRE_VER;

		/**
		 * @var string
		 */
		public $plugin_file = LP_ADDON_INSTAMOJO_PAYMENT_FILE;

		public $id = 'instamojo';

		/**
		 * LP_Addon_Instamojo_Payment constructor.
		 */
		public function __construct() {
			parent::__construct();
		}

		/**
		 * Define Learnpress Instamojo payment constants.
		 *
		 * @since 3.0.0
		 */
		protected function _define_constants() {
			define( 'LP_ADDON_INSTAMOJO_PAYMENT_PATH', dirname( LP_ADDON_INSTAMOJO_PAYMENT_FILE ) );
			define( 'LP_ADDON_INSTAMOJO_PAYMENT_INC', LP_ADDON_INSTAMOJO_PAYMENT_PATH . '/inc/' );
			define( 'LP_ADDON_INSTAMOJO_PAYMENT_URL', plugin_dir_url( LP_ADDON_INSTAMOJO_PAYMENT_FILE ) );
			define( 'LP_ADDON_INSTAMOJO_PAYMENT_TEMPLATE', LP_ADDON_INSTAMOJO_PAYMENT_PATH . '/templates/' );
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 *
		 * @since 3.0.0
		 */
		protected function _includes() {
			include_once LP_ADDON_INSTAMOJO_PAYMENT_INC . 'class-lp-gateway-instamojo.php';
			//api
			include_once LP_ADDON_INSTAMOJO_PAYMENT_INC . 'class-lp-instamojo-api.php';

			// Hooks
			require_once LP_ADDON_INSTAMOJO_PAYMENT_INC . 'class-lp-instamojo-hooks.php';
			LP_Instamojo_Hooks::instance();
		}

		/**
		 * Init hooks.
		 */
		protected function _init_hooks() {
			//add payment gateway class
			add_filter( 'learn_press_payment_method', array( $this, 'add_payment' ) );
			add_filter( 'learn-press/payment-methods', array( $this, 'add_payment' ) );
		}

		/**
		 * Add Instamojo to payment system.
		 *
		 * @param $methods
		 *
		 * @return mixed
		 */
		public function add_payment( $methods ) {
			$methods['instamojo'] = 'LP_Gateway_Instamojo';

			return $methods;
		}

		/**
		 * Plugin links.
		 *
		 * @return array
		 */
		public function plugin_links() {
			$links[] = '<a href="' . admin_url( 'admin.php?page=learn-press-settings&tab=payments&section=instamojo' ) . '">' . esc_html__( 'Settings', 'learnpress-instamojo-payment' ) . '</a>';

			return $links;
		}
	}
}
