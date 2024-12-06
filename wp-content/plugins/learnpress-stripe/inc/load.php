<?php
/**
 * Plugin load class.
 *
 * @author   ThimPress
 * @package  LearnPress/Stripe/Classes
 * @version  4.0.1
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LP_Addon_Stripe_Payment' ) ) {
	/**
	 * Class LP_Addon_Stripe_Payment
	 */
	class LP_Addon_Stripe_Payment extends LP_Addon {

		/**
		 * @var string
		 */
		public $version = LP_ADDON_STRIPE_PAYMENT_VER;

		/**
		 * @var string
		 */
		public $require_version = LP_ADDON_STRIPE_PAYMENT_REQUIRE_VER;

		/**
		 * @var string
		 */
		public $plugin_file = LP_ADDON_STRIPE_PAYMENT_FILE;

		/**
		 * LP_Addon_Stripe_Payment constructor.
		 */
		public function __construct() {
			// add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
			parent::__construct();
		}

		/**
		 * Define Learnpress Stripe payment constants.
		 *
		 * @since 3.0.0
		 */
		protected function _define_constants() {
			define( 'LP_ADDON_STRIPE_PAYMENT_PATH', dirname( LP_ADDON_STRIPE_PAYMENT_FILE ) );
			define( 'LP_ADDON_STRIPE_PAYMENT_INC', LP_ADDON_STRIPE_PAYMENT_PATH . '/inc/' );
			define( 'LP_ADDON_STRIPE_PAYMENT_URL', plugin_dir_url( LP_ADDON_STRIPE_PAYMENT_FILE ) );
			define( 'LP_ADDON_STRIPE_PAYMENT_TEMPLATE', LP_ADDON_STRIPE_PAYMENT_PATH . '/templates/' );
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 *
		 * @since 3.0.0
		 */
		protected function _includes() {
			try {
				require_once LP_ADDON_STRIPE_PAYMENT_PATH . '/vendor/autoload.php';
				include_once LP_ADDON_STRIPE_PAYMENT_INC . 'class-lp-gateway-stripe.php';
			} catch ( Throwable $e ) {
				error_log( $e->getMessage() );
			}
		}

		/**
		 * Init hooks.
		 */
		protected function _init_hooks() {
			// add payment gateway class
			add_filter( 'learn-press/payment-methods', array( $this, 'add_payment' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );

			if ( ! is_admin() ) {
				if ( LP_Gateway_Stripe::instance()->is_direct_pay_on_stripe_page() ) {
					$this->listen_callback_stripe_page();
				} else {
					$this->listen_callback_stripe_return_payment_intent();
				}
			}
		}

		/**
		 * Enqueue assets.
		 *
		 * @since 3.0.0
		 */
		public function enqueue_assets() {
			$ver = LP_ADDON_STRIPE_PAYMENT_VER;
			$min = '.min';
			if ( LP_Debug::is_debug() ) {
				$min = '';
				$ver = uniqid();
			}

			wp_register_script(
				'learn-press-stripe',
				$this->get_plugin_url( 'assets/js/dist/stripe-api' . $min . '.js' ),
				[],
				$ver,
				[ 'strategy' => 'defer' ]
			);

			if ( LP_Page_Controller::is_page_checkout() && LP_Gateway_Stripe::instance()->is_enabled() ) {
				$publish_key = LP_Gateway_Stripe::instance()->publish_key;

				$data_localize = array(
					'publish_key' => $publish_key,
					'nonce'       => wp_create_nonce( 'wp_rest' ),
				);

				if ( ! LP_Gateway_Stripe::instance()->is_direct_pay_on_stripe_page() ) {
					wp_enqueue_script( 'learn-press-stripe' );

					// Create payment intent for Stripe JS use.
					$lp_stripe             = LP_Gateway_Stripe::instance();
					$stripe_payment_intent = $lp_stripe->create_payment_intent();
					if ( $stripe_payment_intent instanceof WP_Error ) {
						$data_localize['error'] = $stripe_payment_intent->get_error_message();
					} elseif ( 'object' === gettype( $stripe_payment_intent ) ) {
						$data_localize['publishableKey'] = $stripe_payment_intent->client_secret;
					}
					$data_localize['payment_stripe_via_iframe'] = 1;
				}
				wp_localize_script( 'learn-press-stripe', 'lpStripeSetting', $data_localize );
			}
		}

		/**
		 * Add Stripe to payment system.
		 *
		 * @param $methods
		 *
		 * @return mixed
		 */
		public function add_payment( $methods ) {
			$methods['stripe'] = LP_Gateway_Stripe::class;

			return $methods;
		}

		/**
		 * Check payment intent from Stripe return.
		 *
		 * @return void
		 * @since 4.0.2
		 * @version 1.0.0
		 */
		public function listen_callback_stripe_return_payment_intent() {
			try {
				$lp_stripe_confirm = LP_Request::get_param( 'lp-stripe-confirm-payment' );
				if ( empty( $lp_stripe_confirm ) ) {
					return;
				}
				$payment_intent = LP_Request::get_param( 'payment_intent' );
				if ( empty( $payment_intent ) ) {
					return;
				}

				$lp_stripe = LP_Gateway_Stripe::instance();
				$lp_stripe->stripe_retrieve_payment_intent( $payment_intent );
			} catch ( Throwable $e ) {
				error_log( $e->getMessage() );
			}
		}

		/**
		 * List callback from stripe page return success url has lp_stripe_session_id.
		 * Check stripe session.
		 *
		 * @return void
		 * @since 4.0.2
		 * @version 1.0.0
		 */
		public function listen_callback_stripe_page() {
			try {
				$checkout_session_id = LP_Request::get_param( 'lp_stripe_session_id' );
				if ( empty( $checkout_session_id ) ) {
					return;
				}

				$lp_stripe = LP_Gateway_Stripe::instance();
				$lp_stripe->retrieve_stripe_session( $checkout_session_id );
			} catch ( Throwable $e ) {
				error_log( $e->getMessage() );
			}
		}
	}
}
