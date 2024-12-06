<?php

namespace WCBT\Models;

use WCBT\Helpers\Compare;
use WCBT\Helpers\QuickView;
use WCBT\Helpers\Settings;
use WCBT\Helpers\WishList;
use WCBT\Helpers\General;

class ProductModel {
	/**
	 * @param $product_id
	 * @param string $size
	 *
	 * @return false|string
	 */
	public static function get_post_thumbnail_url( $product_id, string $size = 'post-thumbnail' ) {
		$thumbnail_url = get_the_post_thumbnail_url( $product_id, $size );
		if ( empty( $thumbnail_url ) ) {
			$thumbnail_url = General::get_default_image();
		}

		return $thumbnail_url;
	}

	/**
	 * @param $product_id
	 *
	 * @return mixed|void
	 */
	public static function get_product_data( $product_id ) {
		$product = wc_get_product( $product_id );
		$data    = [
			'product_id'                   => $product_id,
			'title'                        => get_the_title( $product_id ),
			'permalink'                    => get_permalink( $product_id ),
			'thumbnail_url'                => self::get_post_thumbnail_url( $product_id ),
			'is_my_wishlist'               => WishList::is_my_wishlist( $product_id ),
			'is_my_compare'                => Compare::is_my_compare( $product_id ),
			'wishlist_tooltip_text'        => WishList::get_wishlist_tooltip_text(),
			'wishlist_remove_tooltip_text' => WishList::get_wishlist_remove_tooltip_text(),
			'wishlist_type'                => Settings::get_setting_detail( 'wishlist:fields:type' ),
			'compare_tooltip_text'         => Compare::get_compare_tooltip_text(),
			'compare_remove_tooltip_text'  => Compare::get_compare_remove_tooltip_text(),
			'compare_type'                 => Settings::get_setting_detail( 'compare:fields:type' ),
			'quick_view_tooltip_text'      => QuickView::get_quick_view_tooltip_text(),
			'quick_view_type'              => Settings::get_setting_detail( 'quick-view:fields:type' ),
			'short_description'            => apply_filters(
				'woocommerce_short_description',
				$product->get_short_description()
			),
//            'is_single_product_page'  => is_product()
			'price_html'                   => $product->get_price_html()
		];

		return apply_filters( 'wcbt/filter/product-list-item/data', $data, $product_id );
	}


	/**
	 * @param $rating
	 *
	 * @return float|int
	 */
	public static function get_product_total_by_rating( $rating ) {
		global $wpdb;
		$post_tbl      = $wpdb->posts;
		$post_meta_tbl = $wpdb->postmeta;

		$total = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT($post_tbl.ID) FROM $post_tbl LEFT JOIN $post_meta_tbl ON $post_tbl.ID=$post_meta_tbl.post_id
                       WHERE $post_tbl.post_status = 'publish' AND $post_tbl.post_type = 'product'
					AND $post_meta_tbl.meta_key ='_wc_average_rating' AND $post_meta_tbl.meta_value >= %f AND $post_meta_tbl.meta_value < %f",
				$rating,
				$rating + 1
			)
		);

		return empty( $total ) ? 0 : abs( $total );
	}
}
