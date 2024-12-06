<?php

namespace WCBT\Helpers\TemplateHooks;

use WCBT\Helpers\Settings;
use WCBT\Helpers\Template;
use WCBT\Models\ProductModel;

class QuickView {
	public $template;

	/**
	 * @return QuickView|null
	 */
	public static function instance()
	{
		static $instance = null;

		if (is_null($instance)) {
			$instance = new self();
		}

		return $instance;
	}

	public function __construct() {
		$this->template = Template::instance();
		add_action('wcbt/layout/quickview/add-to-cart/after', array( $this, 'add_wishlist_btn' ), 33);
		add_action('wcbt/layout/quickview/add-to-cart/after', array( $this, 'add_compare_btn' ), 33);
	}

	/**
	 * @return void
	 */
	public function add_wishlist_btn() {
		global $product;
		$product_id = ! is_null( $product ) ? $product->get_id() : 0;

		if ( empty( $product_id ) ) {
			return;
		}
		$data                      = ProductModel::get_product_data( $product_id );
		$data['is_single_product'] = true;
		if ( Settings::get_setting_detail( 'wishlist:fields:enable' ) === 'on' ) {
			$this->template->get_frontend_template_type_classic( 'wishlist/wishlist-btn.php', compact( 'data' ) );
		}
	}

	/**
	 * @return void
	 */
	public function add_compare_btn() {
		global $product;
		$product_id = ! is_null( $product ) ? $product->get_id() : 0;

		if ( empty( $product_id ) ) {
			return;
		}
		$data                      = ProductModel::get_product_data( $product_id );
		$data['is_single_product'] = true;

		if (Settings::get_setting_detail('compare:fields:enable') === 'on') {
			$this->template->get_frontend_template_type_classic('compare-product/compare-btn.php', compact('data'));
		}
	}
}
