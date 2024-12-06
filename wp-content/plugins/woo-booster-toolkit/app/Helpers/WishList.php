<?php

namespace WCBT\Helpers;

class WishList {
	/**
	 * @param $product_id
	 * @param string $user_id
	 *
	 * @return bool
	 */
	public static function is_my_wishlist( $product_id, string $user_id = '' ) {
		if ( ! is_user_logged_in() ) {
			return false;
		}

		if ( empty( $user_id ) && is_user_logged_in() ) {
			$user_id = get_current_user_id();
		}

		if ( empty( $user_id ) ) {
			$favorites = Cookie::get_cookie( 'wcbt_wishlist_product' );
		} else {
			$favorites = get_user_meta( $user_id, WCBT_PREFIX . '_my_wishlist', true );
		}


		if ( empty( $favorites ) ) {
			return false;
		}

		if ( is_string( $favorites ) ) {
			$favorites = explode( ',', $favorites );
		}

		return in_array( $product_id, $favorites );
	}

	/**
	 * @return int|null
	 */
	public static function get_count() {
		if ( is_user_logged_in() ) {
			$wishlist = get_user_meta( get_current_user_id(), WCBT_PREFIX . '_my_wishlist', true );
		} else {
			return '';
		}

		if ( empty( $wishlist ) ) {
			return 0;
		}

		if ( is_string( $wishlist ) ) {
			$wishlist = explode( ',', $wishlist );
		}

		return count( $wishlist );
	}

	/**
	 * @return array|false|\stdClass|string
	 */
	public static function get_wishlist_tooltip_text() {
		$enable = Settings::get_setting_detail( 'wishlist:fields:tooltip_enable' );

		if ( $enable === 'on' && Settings::get_setting_detail( 'wishlist:fields:tooltip_text' ) ) {
			return Settings::get_setting_detail( 'wishlist:fields:tooltip_text' );
		}

		return '';
	}

	/**
	 * @return array|false|\stdClass|string
	 */
	public static function get_wishlist_remove_tooltip_text() {
		$enable = Settings::get_setting_detail( 'wishlist:fields:tooltip_enable' );

		if ( $enable === 'on' && Settings::get_setting_detail( 'wishlist:fields:remove_tooltip_text' ) ) {
			return Settings::get_setting_detail( 'wishlist:fields:remove_tooltip_text' );
		}

		return '';
	}
}
