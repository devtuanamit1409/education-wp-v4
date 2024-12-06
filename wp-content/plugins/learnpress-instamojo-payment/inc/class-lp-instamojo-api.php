<?php

require LP_ADDON_INSTAMOJO_PAYMENT_PATH . '/vendor/autoload.php';
/**
 * REST API class.
 *
 * @since  1.0.0
 * @author Minhd
 */

class LP_Instamojo_Rest_API {
	protected static $_instance = null;

	const NAMESPACE = 'lp/instamojo/v1';

	/**
	 * Constructor
	 */
	public function __construct() {
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
		$response = new stdClass();
		$params   = $request->get_params();

		$client_id     = $params['clientID'];
		$client_secret = $params['clientSecret'];
		$test_mode     = $params['testMode'];

		$instaobj = Instamojo\Instamojo::init(
			'app',
			[
				'client_id'     => $client_id,
				'client_secret' => $client_secret,
			],
			$test_mode
		);

		if ( $instaobj ) {
			$cart = LearnPress::instance()->cart;
			if ( ! $cart->is_empty() ) {
				$checkout = LP_Checkout::instance();
				$order_id = $checkout->create_order();
				$order    = learn_press_get_order( $order_id );

				$order_items = $order->get_item_ids();
				if ( ! empty( $order_items ) ) {
					$course_id    = $order_items[0];
					$course       = learn_press_get_course( $course_id );
					$course_title = $course->get_title();

					$total   = $order->get_total();
					$user_id = $order->get_user_id();
					$user    = learn_press_get_user( $user_id );
					$email   = '';
					if ( $user instanceof LP_User ) {
						$email = $user->get_email();
					} else {
						$email = $order->get_email_checkout();
					}

					$result = $instaobj->createPaymentRequest(
						array(
							'purpose'      => $course_title,
							'amount'       => $total,
							'send_email'   => true,
							'email'        => $email,
							'redirect_url' => $order->get_checkout_order_received_url(),
						)
					);

					if ( ! empty( $result ) && isset( $result['longurl'] ) ) {
						$response->success  = true;
						$response->redirect = $result['longurl'];
					}
				}
			}
		}

		return rest_ensure_response( $response );
	}
}

LP_Instamojo_Rest_API::instance();


