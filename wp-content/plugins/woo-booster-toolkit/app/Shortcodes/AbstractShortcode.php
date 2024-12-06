<?php

namespace WCBT\Shortcodes;

abstract class AbstractShortcode {
	protected $shortcode_name;

	/**
	 * Register shortcode.
	 */
	public function __construct() {
		add_shortcode( $this->shortcode_name, array( $this, 'render' ) );
	}

	/**
	 * Render template of shortcode.
	 * If not set any atrribute on short, $attrs is empty string.
	 *
	 * @param string|array $attrs
	 *
	 * @return string
	 */
	abstract public function render( $attrs ): string;
}
