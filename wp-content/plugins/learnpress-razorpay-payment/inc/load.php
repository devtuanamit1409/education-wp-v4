<?php
/**
 * Plugin load class.
 *
 * @author   ThimPress
 * @package  LearnPress/Razorpay/Classes
 * @version  4.0.1
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LP_Addon_Razorpay_Payment' ) ) {
	/**
	 * Class LP_Addon_Razorpay_Payment
	 */
	class LP_Addon_Razorpay_Payment extends LP_Addon {

		/**
		 * LP_Addon_Razorpay_Payment constructor.
		 */
		public function __construct() {
			parent::__construct();
		}

		/**
		 * Define Razorpay payment constants.
		 *
		 * @since 3.0.0
		 */
		protected function _define_constants() {
			define( 'LP_ADDON_RAZORPAY_PAYMENT_PATH', dirname( LP_ADDON_RAZORPAY_PAYMENT_FILE ) );
			define( 'LP_ADDON_RAZORPAY_PAYMENT_INC', LP_ADDON_RAZORPAY_PAYMENT_PATH . '/inc/' );
			define( 'LP_ADDON_RAZORPAY_PAYMENT_URL', plugin_dir_url( LP_ADDON_RAZORPAY_PAYMENT_FILE ) );
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 *
		 * @since 3.0.0
		 */
		protected function _includes() {
			include_once LP_ADDON_RAZORPAY_PAYMENT_INC . 'class-lp-gateway-razorpay.php';
			//api
			// include_once LP_ADDON_RAZORPAY_PAYMENT_INC . 'class-lp-razorpay-api.php';
			// Hooks
			// require_once LP_ADDON_RAZORPAY_PAYMENT_INC . 'class-lp-razorpay-hooks.php';
			// LP_Razorpay_Hooks::instance();
		}

			/**
		 * Init hooks.
		 */
		protected function _init_hooks() {
			//add payment gateway class
			add_filter( 'learn-press/payment-methods', array( $this, 'add_payment' ) );
			if ( ! is_admin() ) {
				$this->listen_callback();
			}
		}

		/**
		 * Add Razorpay to payment system.
		 *
		 * @param $methods
		 *
		 * @return mixed
		 */
		public function add_payment( $methods ) {
			$methods['razorpay'] = 'LP_Gateway_Razorpay';

			return $methods;
		}

		/**
		 * Listen to callback from Razorpay payment.
		 *
		 * @return void
		 */
		public function listen_callback() {
			try {
				$order_id_razorpay = LP_Request::get_param( 'order_id_razorpay' );
				LP_Gateway_Razorpay::instance()->check_callback_from_razorpay( $order_id_razorpay );
			} catch ( Throwable $e ) {
				error_log( __METHOD__ . ' - ' . $e->getMessage() );
			}
		}
	}
}
