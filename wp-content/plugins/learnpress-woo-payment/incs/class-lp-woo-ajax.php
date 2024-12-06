<?php

use LearnPress\Upsell\Package\Package;

/**
 * Class LP_Woo_Ajax
 *
 * Handle ajax for certificates
 *
 * @since 3.1.4
 */
class LP_Woo_Ajax {
	protected static $_instance;
	/** @see lpWooAddItemToCart */
	protected $_hook_arr = array( 'lpWooAddItemToCart' );

	protected function __construct() {
		foreach ( $this->_hook_arr as $hook ) {
			add_action( 'wp_ajax_' . $hook, array( $this, $hook ) );
			add_action( 'wp_ajax_nopriv_' . $hook, array( $this, $hook ) );
		}
	}

	public static function getInstance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Add course to cart Woo
	 */
	public function lpWooAddItemToCart() {
		$response = new LP_REST_Response();

		try {
			if ( ! wp_verify_nonce( $_POST['nonce'] ?? '', 'lp-woo-payment-nonce' ) ) {
				throw new Exception( esc_html__( 'Request is invalid.', 'learnpress-woo-payment' ) );
			}

			$item_id   = LP_Request::get_param( 'item-id', 0, 'int' );
			$item_type = LP_Request::get_param( 'item-type', '', 'key' );
			if ( empty( $item_id ) || empty( $item_type ) ) {
				throw new Exception( __( 'Params invalid!', 'learnpress-woo-payment' ) );
			}

			$can_add_to_cart = false;
			switch ( $item_type ) {
				case LP_COURSE_CPT:
					$course = learn_press_get_course( $item_id );
					if ( ! $course ) {
						throw new Exception( __( 'Course is invalid!', 'learnpress-woo-payment' ) );
					}

					$can_add_to_cart = true;
					break;
				case LP_PACKAGE_CPT:
					if ( get_post_type( $item_id ) !== LP_PACKAGE_CPT ) {
						throw new Exception( __( 'Package is invalid!', 'learnpress-woo-payment' ) );
					}

					$can_add_to_cart = true;
					break;
				case $item_type:
					do_action( 'learnpress/woo-payment/add-item-to-cart', $item_id, $item_type );
					break;
			}

			$can_add_to_cart = apply_filters( 'learn-press/woo/add-item-to-cart', $can_add_to_cart, $item_id, $item_type );
			if ( ! $can_add_to_cart ) {
				throw new Exception( __( 'Can not add to cart!', 'learnpress-woo-payment' ) );
			}

			$wc_cart       = WC()->cart;
			$cart_item_key = $wc_cart->add_to_cart( $item_id );

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
					throw new Exception( __( 'Course is only added one time.', 'learnpress-woo-payment' ) );
				}
			}
		} catch ( Throwable $e ) {
			$response->message = $e->getMessage();
		}

		$response = apply_filters( 'learn-press/woo/add-course-to-cart', $response );

		wp_send_json( $response );
	}
}

LP_Woo_Ajax::getInstance();
