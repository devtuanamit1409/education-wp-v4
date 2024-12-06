<?php

require LP_ADDON_RAZORPAY_PAYMENT_PATH . '/vendor/autoload.php';
/**
 * REST API class.
 *
 * @since  1.0.0
 * @author Minhd
 * @deprecated 4.0.1 - not use
 */

class LP_Razorpay_Rest_API {
	protected static $_instance = null;
	protected $currencies       = array();

	const NAMESPACE = 'lp/razorpay/v1';

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->currencies = require_once LP_ADDON_RAZORPAY_PAYMENT_PATH . '/inc/configs/currencies.php';
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register router
	 */
	public function register_routes() {
		register_rest_route(
			self::NAMESPACE,
			'/create-payment',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'create_payment' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function create_payment( $request ) {
		$response          = new stdClass();
		$response->success = false;
		$params            = $request->get_params();

		$lp_settings = LearnPress::instance()->settings();
		$key_id      = $lp_settings->get( 'razorpay.key_id' );
		$key_secret  = $lp_settings->get( 'razorpay.key_secret' );
		$color_theme = $params['colorTheme'] ?? '';
		$lp_cart     = LearnPress::instance()->get_cart();
		$lp_cart->calculate_totals();

		$razorpayApi = new Razorpay\Api\Api( $key_id, $key_secret );

		try {
			$cart = LearnPress::instance()->cart;
			if ( ! $cart->is_empty() ) {
				$checkout                 = LP_Checkout::instance();
				$checkout->payment_method = new LP_Gateway_Razorpay();
				$lp_session               = LearnPress::instance()->session;

				$order_id = $lp_session->get( 'order_awaiting_payment', 0 );
				if ( ! $order_id ) {
					$order_id = $checkout->create_order();
					$lp_session->set( 'order_awaiting_payment', $order_id, true );

					/*$nonce       = $params['learn-press-checkout-nonce'] ?? '';
					$_POST       = array(
						'learn-press-checkout-nonce' => $nonce,
					);
					$_REQUEST    = array(
						'learn-press-checkout-nonce' => $nonce,
					);
					$checkout->process_checkout();*/
				}

				// Certificate use this hook for add to table lp_user_items.
				do_action( 'learn-press/checkout-order-processed', $order_id, null );

				/**
				 * @var LP_Order $order
				 */
				$order = learn_press_get_order( $order_id );
				if ( ! $order ) {
					throw new Exception( __( 'Order invalid', 'learnpress-razorpay-payment' ) );
				}

				$user_id = $order->get_user_id();
				$user    = learn_press_get_user( $user_id );
				if ( $user instanceof LP_User ) {
					$email = $user->get_email();
				} else {
					$email = $order->get_checkout_email();
				}

				//check currency and convert to paisa
				$lp_currency = learn_press_get_currency();
				if ( array_key_exists( $lp_currency, $this->currencies ) ) {
					$total = $lp_cart->total;
					$total = $total * 100;
				}

				$orderData = [
					'amount'   => $total,
					'currency' => $lp_currency,
				];

				$razorpayOrder = $razorpayApi->order->create( $orderData );
				if ( empty( $razorpayOrder->id ) ) {
					throw new Exception( __( 'Razorpay Order ID is empty', 'learnpress-razorpay-payment' ) );
				}
				error_log('razorpay-'.json_encode($razorpayOrder));

				// Update razorpay_order_id to metadata for callback detected.
				$order->update_meta( '_razorpay_order_id', $razorpayOrder->id );

				$response->success = true;
				$response->options = array(
					'key'          => $key_id,
					'order_id'     => $razorpayOrder->id,
					'name'         => __( 'Payment with Razorpay', 'learnpress-razorpay-payment' ),
					'image'        => '',
					'callback_url' => $order->get_checkout_order_received_url() . '&order_id_razorpay=' . $razorpayOrder->id,
					'prefill'      => array(
						'name'  => $order->get_user_name(),
						'email' => $email,
					),
					'theme'        => array(
						'color' => $color_theme,
					),
				);
			}
		} catch ( Throwable $e ) {
			$response->message = $e->getMessage();
		}

		return rest_ensure_response( $response );
	}
}

LP_Razorpay_Rest_API::instance();


