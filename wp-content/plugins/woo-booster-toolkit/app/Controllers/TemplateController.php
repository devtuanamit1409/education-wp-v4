<?php

namespace WCBT\Controllers;

use WCBT\Helpers\Template;
use WCBT\Helpers\Page;

/**
 * Class TemplateController
 * @package WCBT\Controllers
 */
class TemplateController {
	public function __construct() {
		add_filter( 'page_template', array( $this, 'load_wcbt_page_template' ), 10, 3 );
		add_action( 'wcbt/option-setting/update/after', array( $this, 'auto_shortcode' ), 10, 1 );
	}

	/**
	 * @param $option_name
	 * @param $old_value
	 * @param $value
	 *
	 * @return void
	 */
	public function auto_shortcode( $data ) {
		$wishlist_page_id = $data['wishlist:fields:page'] ?? '';
		if ( $wishlist_page_id ) {
			$args = array(
				'ID'           => $wishlist_page_id,
				'post_content' => '[wcbt_product_wishlist]'
			);
			wp_update_post( $args );
		}

		$compare_page_id = $data['compare:fields:page'] ?? '';
		if ( $compare_page_id ) {
			$args = array(
				'ID'           => $compare_page_id,
				'post_content' => '[wcbt_product_compare]'
			);

			wp_update_post( $args );
		}
	}

	/**
	 * @param string $template
	 * @param string $type
	 * @param array $templates
	 *
	 * @return string|null
	 */
	public function load_wcbt_page_template( string $template, string $type, array $templates ) {
		//Before render
		do_action( 'wcbt/layout/page/before-render', $template, $type, $templates );
		if ( Page::is_wishlist_wc_product_page() ) {
//			$template = Template::instance(false)->get_frontend_template_type_classic('wishlist.php');
		} elseif ( Page::is_compare_wc_product_page() ) {
//			$template = Template::instance( false )->get_frontend_template_type_classic( 'compare-product.php' );
		}

		return $template;
	}
}
