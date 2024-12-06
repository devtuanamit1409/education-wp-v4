<?php
require_once LP_ADDON_RAZORPAY_PAYMENT_PATH . '/inc/libraries/razorpay-php-sdk/Razorpay.php';

use Razorpay\Api\Api;
use LearnPress\Helpers\Singleton;

/**
 * Razorpay payment gateway class.
 *
 * @author   ThimPress
 * @package  LearnPress/Razorpay/Classes
 * @version  4.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LP_Gateway_Razorpay' ) ) {
	/**
	 * Class LP_Gateway_Razorpay
	 */
	class LP_Gateway_Razorpay extends LP_Gateway_Abstract {
		use Singleton;

		/**
		 * @var null
		 */
		protected $key_id = null;

		/**
		 * @var null
		 */
		protected $key_secret = null;

		/**
		 * @var null
		 */
		protected $color_theme = null;

		/**
		 * @var null
		 */
		protected $settings_gateway = array();

		/**
		 * @var string
		 */
		public $id = 'razorpay';

		public function init() {
			// TODO: Implement init() method.
		}

		public function __construct() {
			$this->settings_gateway   = require_once LP_ADDON_RAZORPAY_PAYMENT_PATH . '/inc/configs/class-lp-gateway-razorpay-settings.php';
			$this->method_title       = 'Razorpay';
			$this->method_description = esc_html__( 'Make a payment with Razorpay.', 'learnpress-razorpay-payment' );
			$this->icon               = LP_ADDON_RAZORPAY_PAYMENT_URL . 'assets/images/razorpay-icon.svg';

			$lp_settings = LP_Settings::instance();

			// Get settings.
			$this->title       = $lp_settings->get( "{$this->id}.title", $this->method_title );
			$this->description = $lp_settings->get( "{$this->id}.description", $this->method_description );

			// Add default values for fresh installs.
			if ( $lp_settings->get( "{$this->id}.enable" ) == 'yes' ) {

				$this->key_id      = $lp_settings->get( "{$this->id}.key_id" );
				$this->key_secret  = $lp_settings->get( "{$this->id}.key_secret" );
				$this->color_theme = $lp_settings->get( "{$this->id}.color_theme" );

				//enqueue scripts
				wp_register_script( 'razorpay', 'https://checkout.razorpay.com/v1/checkout.js', [], '1.0', [ 'strategy' => 'defer' ] );
				wp_enqueue_script(
					'learn-press-razorpay',
					LP_ADDON_RAZORPAY_PAYMENT_URL . 'build/razorpay.js',
					array( 'razorpay' ),
					uniqid(),
					[ 'strategy' => 'defer' ]
				);
				wp_localize_script(
					'learn-press-razorpay',
					'LearnPressRazorpay',
					array(
						'key_id'      => $this->key_id,
						'key_secret'  => $this->key_secret,
						'color_theme' => $this->color_theme,
					)
				);
			}

			// check payment gateway enable.
			add_filter(
				'learn-press/payment-gateway/' . $this->id . '/available',
				[ $this, 'razorpay_available' ],
				10, 2
			);

			parent::__construct();
		}

		/**
		 * Payment form.
		 */
		public function get_payment_form() {
			return LP()->settings->get( $this->id . '.description' );
		}

		/**
		 * Admin payment settings.
		 *
		 * @return array
		 */
		public function get_settings() {
			return $this->settings_gateway;
		}

		/**
		 * Check gateway available.
		 *
		 * @return bool
		 */
		public function razorpay_available() {
			if ( LearnPress::instance()->settings->get( "{$this->id}.enable" ) != 'yes' ) {
				return false;
			}

			if ( LearnPress::instance()->settings->get( "{$this->id}.enable" ) == 'yes' ) {
				if ( ! LearnPress::instance()->settings->get( "{$this->id}.key_id" ) || ! LearnPress::instance()->settings->get( "{$this->id}.key_secret" ) ) {
					return false;
				}
			}

			return true;
		}

		/**
		 * Check status Razorpay Order and update LP Order if Razorpay Order have just paid.
		 *
		 * @return void
		 * @throws Exception
		 */
		public function check_callback_from_razorpay( $razorpayId ) {
			if ( empty( $razorpayId ) ) {
				return;
			}
			$razorpayApi  = new Api( $this->key_id, $this->key_secret );
			$payment_data = $razorpayApi->order->fetch( $razorpayId );
			if ( ! empty( $payment_data ) ) {
				$order_id = $payment_data->notes['lp_order_id'];
				if ( ! $order_id ) {
					throw new Exception( __( 'Error: LpOrderID is invalid.', 'learnpress-razorpay-payment' ) );
				}
				$lp_order = learn_press_get_order( (int) $order_id );
				if ( ! $lp_order ) {
					throw new Exception( __( 'Error: LpOrder is invalid.', 'learnpress-razorpay-payment' ) );
				}

				if ( $lp_order->is_completed() ) {
					return;
				}

				if ( $payment_data->status == 'paid' ) {
					$lp_order->payment_complete();
				}

				// Set empty cart.
				$cart = LearnPress::instance()->cart;
				$cart->empty_cart();
			} else {
				throw new Exception( __( 'Error: razorpayOrder is invalid!', 'learnpress-razorpay-payment' ) );
			}
		}

		public function process_payment( $order_id ) {
			$result = array(
				'result'   => 'fail',
				'message'  => '',
				'redirect' => '',
			);

			$lp_order = learn_press_get_order( $order_id );
			if ( ! $lp_order ) {
				throw new Exception( __( 'Order not found!', 'learnpress-razorpay-payment' ) );
			}
			$razorpayApi   = new Api( $this->key_id, $this->key_secret );
			$orderData     = [
				'amount'   => $lp_order->get_total() * 100,
				'currency' => learn_press_get_currency(),
				'notes'    => [ 'lp_order_id' => $order_id ],
			];
			$razorpayOrder = $razorpayApi->order->create( $orderData );
			if ( empty( $razorpayOrder->id ) ) {
				throw new Exception( __( 'Razorpay Order ID is empty', 'learnpress-razorpay-payment' ) );
			}
			// $order->update_meta( '_razorpay_order_id', $razorpayOrder->id );
			$options = array(
				'key'          => $this->key_id,
				'order_id'     => $razorpayOrder->id,
				'name'         => __( 'Payment with Razorpay', 'learnpress-razorpay-payment' ),
				'image'        => '',
				'callback_url' => add_query_arg( 'order_id_razorpay', $razorpayOrder->id, $this->get_return_url( $lp_order ) ),
				'prefill'      => array(
					'name'  => $lp_order->get_user_name(),
					'email' => $lp_order->get_checkout_email(),
				),
				'theme'        => array(
					'color' => $this->color_theme,
				),
			);

			$result = array_merge(
				$result,
				[
					/**
					 * Don't set success on here,
					 * because one step left confirm payment intent status,
					 * if status is succeeded, then set success on method stripe_retrieve_payment_intent.
					 */
					'result'   => LP_ORDER_PROCESSING,
					'message'  => esc_html__( 'The payment is processing.', 'learnpress-razorpay-payment' ),
					'redirect' => '',
					'options'  => $options,
				]
			);

			return $result;
		}
	}
}
