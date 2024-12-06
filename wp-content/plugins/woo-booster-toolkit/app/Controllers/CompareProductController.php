<?php

namespace WCBT\Controllers;

use WCBT\Helpers\Compare;
use WCBT\Helpers\RestApi;
use WCBT\Helpers\Settings;
use WCBT\Helpers\Template;
use WCBT\Models\ProductModel;
use WP_REST_Server;

class CompareProductController {
	/**
	 * CompareProductController constructor.
	 */
	public function __construct() {
		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'add_compare_button' ), 30 );
		add_action( 'woocommerce_single_product_summary', array( $this, 'add_compare_button' ), 33 );

		add_shortcode( 'wcbt_compare_btn', array( $this, 'add_compare_button' ) );
		add_shortcode( 'wcbt_compare_link', array( $this, 'wcbt_compare_link' ) );
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
	}

	/**
	 * @return void
	 */
	public function wp_enqueue_scripts() {
		wp_localize_script( 'wcbt-global', 'WCBT_COMPARE_OBJECT', array(
			'enable'                       => Settings::get_setting_detail( 'compare:fields:enable' ) === 'on',
			'tooltip_enable'               => Settings::get_setting_detail( 'compare:fields:tooltip_enable' ) === 'on',
			'add_compare_tooltip_text'    => Compare::get_compare_tooltip_text(),
			'remove_compare_tooltip_text' => Compare::get_compare_remove_tooltip_text(),
		) );
	}

	/**
	 * @return CompareProductController|null
	 */
	public static function instance() {
		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self();
		}

		return $instance;
	}

	public function register_rest_routes() {
		register_rest_route(
			RestApi::generate_namespace(),
			'/compare',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_compare' ),
					'permission_callback' => '__return_true',
				),
			)
		);
	}

	public function get_compare(\WP_REST_Request $request ) {
		$params = $request->get_params();
		$product_ids = $params['post_in'] ?? array();

		ob_start();
		$data['product_ids'] = $product_ids;
		Template::instance()->get_frontend_template_type_classic(
			apply_filters( 'wcbt/filter/compare/compare-list', 'compare-product/compare-list.php' ),
			compact('data')
		);

		unset($data['product_ids']);
		$data['content'] = ob_get_clean();

		return RestApi::success( '', $data );
	}

	/**
	 * @param $attrs
	 *
	 * @return void
	 */
	public function add_compare_button( $attrs ) {
		if ( Settings::get_setting_detail( 'compare:fields:enable' ) !== 'on' ) {
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
		Template::instance()->get_frontend_template_type_classic( 'compare-product/compare-btn.php', compact( 'data' ) );
	}

	public function add_compare_button_single_product() {
		if ( Settings::get_setting_detail( 'compare:fields:enable' ) !== 'on' ) {
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

		$data                        = ProductModel::get_product_data( $product_id );
		$data  ['is_single_product'] = true;

		Template::instance()->get_frontend_template_type_classic( 'compare-product/compare-btn.php', compact( 'data' ) );
	}

	/**
	 * @return false|string|void
	 */
	public function wcbt_compare_link() {
		if ( Settings::get_setting_detail( 'compare:fields:enable' ) !== 'on' ) {
			return;
		}

		$compare_page_id = Settings::get_setting_detail( 'compare:fields:page' );

		if ( empty( $compare_page_id ) ) {
			return;
		}

		$data = array(
			'compare_page_url' => get_permalink( $compare_page_id ),
		);

		ob_start();

		Template::instance()->get_frontend_template_type_classic( 'compare-product/compare-link.php', compact( 'data' ) );

		return ob_get_clean();
	}
}
