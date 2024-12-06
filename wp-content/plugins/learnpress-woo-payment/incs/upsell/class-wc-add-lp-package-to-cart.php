<?php

use LearnPress\Upsell\Package\Package;

class LP_Wc_Add_Package_Ajax {

	protected static $_instance = null;
	// protected $currencies       = array();

	public $namespace = 'learpress-woo-payment/v1';

	/**
	 * Constructor
	 * @uses lp_wc_add_package_to_cart
	 */
	protected $_hook_arr = array( 'lp_wc_add_package_to_cart' );

	protected function __construct() {
		foreach ( $this->_hook_arr as $hook ) {
			add_action( 'wp_ajax_' . $hook, array( $this, $hook ) );
			add_action( 'wp_ajax_nopriv_' . $hook, array( $this, $hook ) );
		}
	}

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function lp_wc_add_package_to_cart() {
		$response = new LP_REST_Response();

		try {
			$post_data = LP_Helper::sanitize_params_submitted( $_POST );

			if ( ! wp_verify_nonce( $post_data['nonce'] ?? '', 'lp-woo-payment-nonce' )
			     || empty( $post_data['package_id'] ) ) {
				throw new Exception( esc_html__( 'Request is invalid.', 'learnpress-woo-payment' ) );
			}

			$package_id = (int) $post_data['package_id'];
			if ( get_post_type( $package_id ) !== LP_PACKAGE_CPT ) {
				throw new Exception( esc_html__( 'Package is invalid.', 'learnpress-woo-payment' ) );
			}

			$wc_cart       = WC()->cart;
			$cart_item_key = $wc_cart->add_to_cart( $package_id );
			if ( $cart_item_key ) {
				ob_start();
				LP_Addon_Woo_Payment_Preload::$addon->get_template( 'view-cart' );
				$view_cart_content = ob_get_contents();

				woocommerce_mini_cart();
				$mini_cart = ob_get_contents();
				ob_clean();
				ob_end_flush();

				if ( 'yes' == LP_Settings::get_option( 'woo-payment_redirect_to_checkout', 'no' ) ) {
					$response->data->redirect_to = wc_get_checkout_url();
				}

				$response->data->button_view_cart             = $view_cart_content;
				$response->data->widget_shopping_cart_content = $mini_cart;
				$response->data->count_items                  = WC()->cart->get_cart_contents_count();
				$response->status                             = 'success';
			} else {
				$wc_notices = wc_get_notices();

				if ( ! empty( $wc_notices['error'] ) ) {
					throw new Exception( esc_html__( 'Package is only added once.', 'learnpress-woo-payment' ) );
				}
			}
		} catch ( Throwable $e ) {
			$response->status  = 'error';
			$response->message = $e->getMessage();
		}
		wp_send_json( $response );
	}
}

LP_Wc_Add_Package_Ajax::instance();
