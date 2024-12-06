<?php

namespace WCBT\Helpers;

class ProductFilter {
	/**
	 * @return bool
	 */
	public static function is_enable() {
		return Settings::get_setting_detail( 'product-filter:fields:enable' ) === 'on';
	}

	/**
	 * @return array|\stdClass|string
	 */
	public static function get_fields() {
		if ( ! self::is_enable() ) {
			return array();
		}

		$fields = Settings::get_setting_detail( 'product-filter:fields:product-filter' );

		if ( empty( $fields ) ) {
			return array();
		}

		if ( isset( $fields['order'] ) ) {
			unset( $fields['order'] );
		}

		return $fields;
	}

	/**
	 * @return array|int|\stdClass|string
	 */
	public static function get_min_price() {
		$min_price = Settings::get_setting_detail( 'product-filter:fields:min_price' );

		if ( empty( $min_price ) ) {
			return 0;
		}

		return $min_price;
	}

	/**
	 * @return array|int|\stdClass|string
	 */
	public static function get_max_price() {
		$max_price = Settings::get_setting_detail( 'product-filter:fields:max_price' );

		if ( empty( $max_price ) ) {
			return 250;
		}

		return $max_price;
	}

	/**
	 * @return array|int|\stdClass|string
	 */
	public static function get_step_price() {
		$step_price = Settings::get_setting_detail( 'product-filter:fields:step_price' );

		if ( empty( $step_price ) ) {
			return 1;
		}

		return $step_price;
	}

	/**
	 * @return array|int|\stdClass|string
	 */
	public static function get_category_number() {
		$category_number = Settings::get_setting_detail( 'product-filter:fields:category_number' );

		if ( empty( $category_number ) ) {
			return 0;
		}

		return $category_number;
	}

	/**
	 * @return array|int|\stdClass|string
	 */
	public static function get_attribute_term_number() {
		$attribute_term_number = Settings::get_setting_detail( 'product-filter:fields:attribute_term_number' );

		if ( empty( $attribute_term_number ) ) {
			return 0;
		}

		return $attribute_term_number;
	}

	/**
	 * @return mixed|null
	 */
	public static function get_data() {
		$current_page_url = '';
		if ( is_shop() ) {
			$current_page_url = wc_get_page_permalink( 'shop' );
		} else if ( is_product_category() || is_product_tag()) {
			$term_id = get_queried_object()->term_id;
			$current_page_url = get_term_link( $term_id );
		}

		return apply_filters( 'wcbt/filter/product-filter/data', array(
			'enable'                => self::is_enable(),
			'fields'                => self::get_fields(),
			'category_number'       => self::get_category_number(),
			'attribute_term_number' => self::get_attribute_term_number(),
			'min_price'             => self::get_min_price(),
			'max_price'             => self::get_max_price(),
			'step_price'            => self::get_step_price(),
			'current_page_url'      => $current_page_url,
		) );
	}
}
