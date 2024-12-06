<?php

namespace WCBT\Helpers;

/**
 * Class Validation
 * @package WCBT\Helpers
 */
class Validation {
	/**
	 * @param $value
	 * @param $cb
	 *
	 * @return array|mixed|string
	 */
	public static function sanitize_params_submitted( $value, $type_content = 'text' ) {
		$value = wp_unslash( $value );

		if ( is_string( $value ) ) {
			$value = trim( $value );
			switch ( $type_content ) {
				case 'html':
					$value = General::ksesHTML( $value );
					break;
				case 'textarea':
					$value = implode( '\n', array_map( 'sanitize_textarea_field', explode( '\n', $value ) ) );
					break;
				case 'key':
					$value = sanitize_key( $value );
					break;
				default:
					$value = sanitize_text_field( $value );
			}
		} elseif ( is_array( $value ) ) {
			return array_map( [ __CLASS__, 'sanitize_params_submitted' ], $value );
		}

		return $value;
	}

	/**
	 * @param $value
	 * @param $field
	 *
	 * @return float|int|mixed|string
	 */
	public static function validate_number( $value, $field ) {

		if ( is_numeric( $value ) ) {
			if ( isset( $field['min'] ) ) {
				if ( ! is_numeric( $field['min'] ) || $value < $field['min'] ) {
					return '';
				}
			}

			if ( isset( $field['max'] ) ) {
				if ( ! is_numeric( $field['max'] ) || $value > $field['max'] ) {
					return '';
				}
			}
		}

		return $value;
	}
}

