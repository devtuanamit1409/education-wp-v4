<?php

namespace WCBT\Shortcodes;

use WCBT\Helpers\Template;

class WishList extends AbstractShortcode {
	protected $shortcode_name = 'wcbt_product_wishlist';

	public function __construct() {
		parent::__construct();
	}

	/**
	 * @param $attrs
	 *
	 * @return string
	 */
	public function render( $attrs ): string {
		$data = shortcode_atts(
			self::get_default(),
			$attrs
		);

		ob_start();
		Template::instance(true)->get_frontend_template_type_classic('wishlist.php');

		return ob_get_clean();
	}

	/**
	 * @return void
	 */
	public function enqueue_scripts() {
	}


	public static function get_default() {
		$default = array();

		return apply_filters( 'wcbt/filter/shortcode/product-wishlist/default', $default );
	}
}
