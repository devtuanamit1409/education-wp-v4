<?php
/**
 * Stripe payment gateway class.
 *
 * @author   ThimPress
 * @package  LearnPress/Stripe/Classes
 * @version  4.0.1
 * @since    3.0.0
 */

use LearnPress\Helpers\Singleton;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LP_Gateway_Stripe' ) ) {
	/**
	 * Class LP_Gateway_Stripe
	 */
	class LP_Gateway_Stripe extends LP_Gateway_Abstract {
		use Singleton;

		/**
		 * @var string Payment method ID.
		 */
		public $id = 'stripe';
		/**
		 * @var object|null
		 */
		protected $settings = null;
		/**
		 * @var null
		 */
		public $test_mode;
		/**
		 * @var null
		 */
		public $publish_key;
		/**
		 * @var null
		 */
		protected $secret_key;
		/**
		 * @var string|null
		 */
		public $test_publish_key;
		/**
		 * @return string|null
		 */
		public $test_secret_key;
		/**
		 * @var null
		 */
		protected $client_secret;

		public function init() {
			// TODO: Implement init() method.
		}

		/**
		 * LP_Gateway_Stripe constructor.
		 */
		public function __construct() {
			$this->method_title       = 'Stripe';
			$this->method_description = esc_html__( 'Make a payment with Stripe.', 'learnpress-stripe' );
			$this->icon               = LP_ADDON_STRIPE_PAYMENT_URL . 'assets/images/stripe.svg';

			parent::__construct();

			// Get settings.
			$this->title       = $this->settings->get( 'title' ) ?? $this->method_title;
			$this->description = $this->settings->get( 'description' );

			// Add default values for fresh installs.
			if ( $this->is_enabled() ) {
				$this->test_mode        = $this->settings->get( 'test_mode', 'no' );
				$this->test_publish_key = $this->settings->get( 'test_publish_key', '' );
				$this->test_secret_key  = $this->settings->get( 'test_secret_key', '' );
				$this->publish_key      = $this->settings->get( 'live_publish_key', '' );
				if ( $this->is_test_mode() ) {
					$this->publish_key = $this->test_publish_key;
				}
				$this->secret_key = $this->settings->get( 'live_secret_key', '' );
				if ( $this->is_test_mode() ) {
					$this->secret_key = $this->test_secret_key;
				}
			}

			// check payment gateway enable.
			add_filter( 'learn-press/payment-gateway/' . $this->id . '/available', array(
				$this,
				'stripe_available'
			), 10, 2 );
		}

		/**
		 * Admin payment settings.
		 *
		 * @return array
		 */
		public function get_settings() {
			return include_once LP_ADDON_STRIPE_PAYMENT_PATH . '/config/settings.php';
		}

		/**
		 * Check is enable option Direct payment on Stripe
		 *
		 * @return bool
		 * @since 4.0.2
		 * @version 1.0.0
		 */
		public function is_direct_pay_on_stripe_page(): bool {
			return $this->settings->get( 'direct_payment_on_stripe_page' ) === 'yes';
		}

		/**
		 * Payment form.
		 */
		public function get_payment_form() {
			ob_start();
			$data = [
				'description'                  => $this->get_description(),
				'mode'                         => $this->is_test_mode() ? 'test' : 'live',
				'is_direct_pay_on_stripe_page' => $this->is_direct_pay_on_stripe_page(),
				'is_test_mode'                 => $this->is_test_mode(),
			];
			LP_Addon_Stripe_Payment_Preload::$addon->get_template( 'form.php', $data );

			return ob_get_clean();
		}

		/**
		 * Check gateway available.
		 *
		 * @return bool
		 */
		public function stripe_available() {
			if ( ! $this->is_enabled() ) {
				return false;
			}

			if ( $this->is_test_mode() ) {
				if ( empty( $this->test_publish_key ) || empty( $this->test_secret_key ) ) {
					return false;
				}
			} else {
				if ( empty( $this->publish_key ) || empty( $this->secret_key ) ) {
					return false;
				}
			}

			return true;
		}

		/**
		 * @return bool
		 */
		public function is_test_mode(): bool {
			return $this->test_mode === 'yes';
		}

		/**
		 * Stripe payment process.
		 *
		 * @param int $order_id
		 *
		 * @return array
		 * @throws string
		 * @throws ApiErrorException
		 * @throws Exception
		 */
		public function process_payment( $order_id ) {
			$result = array(
				'result'   => 'fail',
				'message'  => '',
				'redirect' => '',
			);

			$lp_order = learn_press_get_order( $order_id );
			if ( ! $lp_order ) {
				throw new Exception( __( 'Order not found!', 'learnpress-stripe' ) );
			}

			if ( LP_Gateway_Stripe::instance()->is_direct_pay_on_stripe_page() ) {
				$stripe_checkout_url = $this->get_url_payment_on_stripe_page( $lp_order );

				$result = array_merge(
					$result,
					[
						'result'   => 'success',
						'message'  => esc_html__( 'Redirecting to Stripe.', 'learnpress-stripe' ),
						'redirect' => esc_url( $stripe_checkout_url ),
					]
				);
			} else {
				$stripe_pi = LearnPress::instance()->session->get( 'stripe_awaiting_payment_intent', 0 );
				$pi_id     = $stripe_pi->id;
				$this->update_payment_intent( $pi_id, $order_id );

				$result = array_merge(
					$result,
					[
						/**
						 * Don't set success on here,
						 * because one step left confirm payment intent status,
						 * if status is succeeded, then set success on method stripe_retrieve_payment_intent.
						 */
						'result'   => LP_ORDER_PROCESSING,
						'message'  => esc_html__( 'The payment is processing.', 'learnpress-stripe' ),
						'redirect' => add_query_arg( 'lp-stripe-confirm-payment', 1, $this->get_return_url( $lp_order ) ),
					]
				);
			}

			return $result;
		}

		/**
		 * Get stripe checkout url.
		 * via create a checkout Session
		 * https://stripe.com/docs/api/checkout/sessions/create?lang=php
		 *
		 * @param LP_Order $order
		 *
		 * @return string|null
		 * @throws ApiErrorException
		 * @throws Exception
		 * @version 1.0.0
		 * @since 4.0.2
		 */
		public function get_url_payment_on_stripe_page( LP_Order $order ) {
			$stripe                  = new StripeClient( $this->secret_key );
			$success_url             = $this->get_return_url( $order );
			$cancel_url              = learn_press_get_page_link( 'checkout' ); //$order->get_cancel_order_url();
			$stripe_checkout_session = $stripe->checkout->sessions->create(
				array(
					'line_items'  => [
						[
							'price_data' => array(
								'currency'     => strtolower( learn_press_get_currency() ),
								'product_data' => array(
									'name' => sprintf( __( 'Order %s', 'learnpress-stripe' ), $order->get_order_number() ),
								),
								'unit_amount'  => $this->calculate_order_amount( $order->get_total() ),
							),
							'quantity'   => 1,
						],
					],
					'mode'        => 'payment',
					'success_url' => add_query_arg( 'lp_stripe_session_id', '{CHECKOUT_SESSION_ID}', $success_url ),
					'cancel_url'  => $cancel_url,
					'metadata'    => [ 'lp_order_id' => $order->get_id() ],
				)
			);

			return $stripe_checkout_session->url;
		}

		/**
		 * Retrieve stripe session.
		 * Check status payment by checkout session id.
		 *
		 * @throws ApiErrorException
		 * @version 1.0.0
		 * @since 4.0.2
		 */
		public function retrieve_stripe_session( string $checkout_session_id ) {
			$stripe   = new StripeClient( $this->secret_key );
			$retrieve = $stripe->checkout->sessions->retrieve( $checkout_session_id );
			if ( $retrieve->payment_status === Session::PAYMENT_STATUS_PAID ) {
				$lp_order_id = $retrieve->metadata->lp_order_id ?? 0;
				$lp_order    = learn_press_get_order( $lp_order_id );
				if ( $lp_order->is_completed() ) {
					return;
				}
				$lp_order->payment_complete();
			}
		}

		/**
		 * Create Stripe payment intent.
		 *
		 * @return Stripe\PaymentIntent|null
		 * @version 1.0.1
		 * @since 4.0.2
		 */
		public function create_payment_intent() {
			$cart                  = LearnPress::instance()->cart;
			$stripe_payment_intent = null;

			try {
				if ( ! $cart || $cart->is_empty() ) {
					throw new Exception( __( 'Cart is empty.', 'learnpress-stripe' ) );
				}

				$cart_total = $cart->calculate_totals();
				if ( $cart_total->total <= 0 ) {
					throw new Exception( __( 'Total amount must be greater than 0.', 'learnpress-stripe' ) );
				}

				$payment_intent_exist = LearnPress::instance()->session->get( 'stripe_awaiting_payment_intent', '' );
				//if ( empty( $payment_intent_exist ) ) {
				$stripe                = new StripeClient( $this->secret_key );
				$stripe_payment_intent = $stripe->paymentIntents->create(
					array(
						'amount'                    => $this->calculate_order_amount( $cart_total->total ),
						'currency'                  => strtolower( learn_press_get_currency() ),
						'automatic_payment_methods' => [ 'enabled' => true ],
					)
				);
				LearnPress::instance()->session->set( 'stripe_awaiting_payment_intent', $stripe_payment_intent, true );
				/*} else {
					$stripe_payment_intent = $payment_intent_exist;
				}*/
			} catch ( Throwable $e ) {
				$stripe_payment_intent = new WP_Error( 'stripe_payment_intent_error', $e->getMessage() );
				//error_log( __METHOD__ . $e->getMessage() );
			}

			return $stripe_payment_intent;
		}

		/**
		 * Update payment intent Stripe
		 *
		 * @throws ApiErrorException
		 * @version 1.0.0
		 * @since 4.0.2
		 */
		public function update_payment_intent( string $pi, int $order_id ) {
			$stripe     = new StripeClient( $this->secret_key );
			$cart       = LearnPress::instance()->cart;
			$cart_total = $cart->calculate_totals();
			$amount     = $this->calculate_order_amount( $cart_total->total );
			$stripe->paymentIntents->update(
				$pi,
				array(
					'amount'   => $amount,
					'metadata' => array(
						'lp_order_id' => $order_id,
					),
				)
			);
		}

		/**
		 * Check Stripe payment intent.
		 *
		 * @throws Exception
		 * @version 1.0.0
		 * @since 4.0.2
		 */
		public function stripe_retrieve_payment_intent( $payment_intent ) {
			$stripe                  = new StripeClient( $this->secret_key );
			$payment_intent_retrieve = $stripe->paymentIntents->retrieve( $payment_intent, array() );
			if ( $payment_intent_retrieve->status === 'succeeded' ) {
				$order_id = $payment_intent_retrieve->metadata->lp_order_id ?? 0;
				$lp_order = learn_press_get_order( $order_id );
				if ( $lp_order->is_completed() ) {
					return;
				}
				$lp_order->set_data( 'payment_method', $this->id );
				$lp_order->set_data( 'payment_method_title', $this->method_title );
				$lp_order->payment_complete();

				LearnPress::instance()->cart->empty_cart();
				LearnPress::instance()->session->remove( 'stripe_awaiting_payment_intent', true );
			} else {
				throw new Exception( $payment_intent_retrieve->status );
			}
		}

		/**
		 * Get stripe currency rules.
		 * For calculate order amount with currency.
		 * @docs https://docs.stripe.com/currencies#zero-decimal
		 *
		 * @since 4.0.3
		 * @return array stripe currency list
		 */
		public function get_stripe_currency_rules(): array {
			return include_once LP_ADDON_STRIPE_PAYMENT_PATH . '/config/stripe-currency-rules.php';
		}

		/**
		 * Calculate order amount with currency rule.
		 *
		 * @param float $amount
		 *
		 * @since 4.0.3
		 * @version 1.0.0
		 * @return int|float
		 */
		public function calculate_order_amount( float $amount = 0 ) {
			$stripe_support_rules = $this->get_stripe_currency_rules();
			$currency                = learn_press_get_currency();
			if ( in_array( $currency, $stripe_support_rules['zero-decimal'] ) ) {
				$order_amount = (int) $amount;
			} elseif ( in_array( $currency, $stripe_support_rules['three-decimal'] ) ) {
				$order_amount = round( $amount, 2 ) * 1000;
			} elseif ( in_array( $currency, $stripe_support_rules['special-case'] ) ) {
				$order_amount = (int) $amount * 100;
			} else {
				$order_amount = $amount * 100;
			}
			return $order_amount;
		}
	}
}
