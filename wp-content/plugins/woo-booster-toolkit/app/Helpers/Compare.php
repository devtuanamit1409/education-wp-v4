<?php

namespace WCBT\Helpers;

class Compare {
	/**
	 * @param $product_id
	 *
	 * @return bool
	 */
	public static function is_my_compare( $product_id ) {
		if ( ! is_user_logged_in() ) {
			return false;
		}

		$compare_products = Cookie::get_cookie( 'wcbt_compare_product' );

		if ( empty( $compare_products ) ) {
			return false;
		}

		if ( is_string( $compare_products ) ) {
			$compare_products = explode( ',', $compare_products );
		}

		return in_array( $product_id, $compare_products );
	}

	/**
	 * @return int|null
	 */
	public static function get_count() {
		$compare = Cookie::get_cookie( 'wcbt_compare_product' );

		if ( empty( $compare ) ) {
			return 0;
		}

		if ( is_string( $compare ) ) {
			$compare = explode( ',', $compare );
		}

		return count( $compare );
	}


	/**
	 * @return array|false|\stdClass|string
	 */
	public static function get_compare_tooltip_text() {
		$enable = Settings::get_setting_detail( 'compare:fields:tooltip_enable' );

		if ( $enable === 'on' && Settings::get_setting_detail( 'compare:fields:tooltip_text' ) ) {
			return Settings::get_setting_detail( 'compare:fields:tooltip_text' );
		}

		return '';
	}

	public static function get_compare_remove_tooltip_text() {
		$enable = Settings::get_setting_detail( 'compare:fields:tooltip_enable' );

		if ( $enable === 'on' && Settings::get_setting_detail( 'compare:fields:remove_tooltip_text' ) ) {
			return Settings::get_setting_detail( 'compare:fields:remove_tooltip_text' );
		}

		return '';
	}
}
