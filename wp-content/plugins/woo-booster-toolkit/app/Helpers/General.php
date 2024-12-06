<?php

namespace WCBT\Helpers;

class General {
	/**
	 * @return string
	 */
	public static function get_default_image(): string {
		return WCBT_ASSETS_URL . 'images/no-image.jpg';
	}

	/**
	 * @param $content
	 *
	 * @return string
	 */
	public static function ksesHTML( $content ): string {
		$allowed_html = wp_kses_allowed_html( 'post' );

		$allowed_html['iframe'] = array(
			'src'         => 1,
			'width'       => 1,
			'height'      => 1,
			'style'       => 1,
			'class'       => array( 'embed-responsive-item' ),
			'frameborder' => 1,
		);

		$allowed_html['input'] = array(
			'src'         => 1,
			'width'       => 1,
			'height'      => 1,
			'type'        => array(),
			'placeholder' => 1,
			'value'       => 1,
			'class'       => array( 'embed-responsive-item' ),
			'frameborder' => 1,
			'name'        => 1,
			'min'         => 1,
			'max'         => 1,
		);

		$allowed_html['form'] = array(
			'class' => 1,
			'style' => 1,
		);

		$allowed_html['textarea'] = array(
			'placeholder' => 1,
			'cols'        => 1,
			'rows'        => 1,
			'name'        => 1,
		);

		$allowed_html['select'] = array(
			'placeholder' => 1,
			'cols'        => 1,
			'rows'        => 1,
			'name'        => 1,
			'title'       => 1,
		);

		$allowed_html['option'] = array(
			'selected' => 1,
		);

		return wp_kses( wp_unslash( $content ), $allowed_html );
	}
}
