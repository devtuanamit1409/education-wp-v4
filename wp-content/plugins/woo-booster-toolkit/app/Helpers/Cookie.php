<?php

namespace WCBT\Helpers;

class Cookie {
	public static function set_cookie( $name, $value, $exprire_time, $secure = false, $httponly = false ) {
		setcookie(
			self::get_cookie_name( $name ),
			$value,
			$exprire_time,
			COOKIEPATH ?: '/',
			'',
			$secure,
			$httponly
		);
	}

	/**
	 * @param $name
	 *
	 * @return mixed|string
	 */
	public static function get_cookie( $name ) {
		$name = self::get_cookie_name( $name );

		return $_COOKIE[ $name ] ?? '';
	}

	public static function delete_cookie( $cookie ) {
		$name = self::get_cookie_name( $cookie );
		unset( $_COOKIE[ $name ] );
		self::set_cookie( $cookie, 'delete', time() - 3600 );
	}

	/**
	 * @param $name
	 *
	 * @return mixed|string
	 */
	public static function get_cookie_name( $name ) {
		if ( is_multisite() ) {
			$name = $name . '_' . get_current_blog_id();
		}

		return $name;
	}
}

