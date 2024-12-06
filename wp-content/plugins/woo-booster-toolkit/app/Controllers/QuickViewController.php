<?php

namespace WCBT\Controllers;

use WCBT\Helpers\RestApi;
use WCBT\Helpers\Settings;
use WCBT\Helpers\Template;
use WCBT\Models\ProductModel;
use WP_REST_Server;

/**
 * QuickViewController
 */
class QuickViewController {
	public function __construct() {
		add_action( 'wp_body_open', array( $this, 'add_quick_view_popup' ) );

		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'add_quick_view_button' ), 30 );
		add_shortcode( 'wcbt_quick_view_btn', array( $this, 'add_quick_view_button' ) );
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );

		add_filter( 'request', array( $this, 'request_product_quickview' ) );
		add_filter( 'woocommerce_is_rest_api_request', array( $this, 'woocommerce_is_rest_api_request' ) );

		// Image.
		add_action( 'wcbt_quickview_product_image', 'woocommerce_show_product_sale_flash', 10 );
		add_action( 'wcbt_quickview_product_image', array( $this, 'product_image' ) );
	}


	/**
	 * @return void
	 */
	public function product_image() {
		Template::instance()->get_frontend_template_type_classic( 'quick-view/product-image.php' );
	}

	/**
	 * @return QuickViewController|null
	 */
	public static function instance() {
		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * @param $is_rest_api_request
	 *
	 * @return false|mixed
	 */
	public function woocommerce_is_rest_api_request( $is_rest_api_request ) {
		if ( isset( $_SERVER['REQUEST_URI'] )
		     && strpos( $_SERVER['REQUEST_URI'], RestApi::generate_namespace() . '/quick-view-product/', 0 ) !== false ) {
			return false;
		}

		return $is_rest_api_request;
	}

	/**
	 * @param $query_vars
	 *
	 * @return mixed
	 */
	public function request_product_quickview( $query_vars ) {
		if ( isset( $query_vars['post_type'] ) && $query_vars['post_type'] === 'product' ) {
			if ( isset( $query_vars['rest_route'] ) &&
			     strpos(
				     $query_vars['rest_route'],
				     RestApi::generate_namespace() . '/quick-view-product/',
				     0
			     ) !== false ) {
				unset( $query_vars['rest_route'] );
			}
		}

		return $query_vars;
	}

	/**
	 * @return void
	 */
	public function register_rest_routes() {
		register_rest_route(
			RestApi::generate_namespace(),
			'/quick-view-product/(?P<product_id>\d+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_quick_view_product' ),
					'args'                => array(
						'product_id' => array(
							'required'    => false,
							'type'        => 'integer',
							'description' => 'The product id is required',
						),
					),
					'permission_callback' => '__return_true',
				),
			)
		);
	}

	/**
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function get_quick_view_product( \WP_REST_Request $request ) {
		$product_id = $request->get_param( 'product_id' );
		$data       = ProductModel::get_product_data( $product_id );

		wp( 'p=' . $product_id . '&post_type=product' );
		$template = Template::instance();
		ob_start();

		while ( have_posts() ) {
			the_post();
			$template->get_frontend_template_type_classic(
				apply_filters( 'wcbt/filter/quick-view', 'quick-view/content.php' ),
				compact( 'data' )
			);
		}

		$content = ob_get_clean();

		return RestApi::success(
			'',
			array(
				'content' => $content,
			)
		);
	}

	/**
	 * @return void
	 */
	public function add_quick_view_popup() {
		if ( Settings::get_setting_detail( 'quick-view:fields:enable' ) !== 'on' ) {
			return;
		}
		wp_enqueue_script( 'flex-slider' );
		if ( ! wp_script_is( 'wc-add-to-cart-variation' ) ) {
			wp_enqueue_script( 'wc-add-to-cart-variation' );
		}

		do_action( 'wcbt/quickview/add-quickview-popup' );
		?>
        <div id="wcbt-quick-view-popup"></div>
		<?php
	}

	/**
	 * @param $attrs
	 *
	 * @return void
	 */
	public function add_quick_view_button( $attrs ) {
		if ( Settings::get_setting_detail( 'quick-view:fields:enable' ) !== 'on' ) {
			return;
		}
		global $product;
		$product_id = $attrs['product_id'] ?? '';
		if ( empty( $product_id ) ) {
			$product_id = ! is_null( $product ) ? $product->get_id() : 0;
		}

		if ( empty( $product_id ) ) {
			return;
		}

		$data = ProductModel::get_product_data( $product_id );

		Template::instance()->get_frontend_template_type_classic( 'quick-view/quick-view-btn.php', compact( 'data' ) );
	}
}
