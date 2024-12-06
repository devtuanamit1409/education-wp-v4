<?php

/**
 * Class LP_Gateway_Woo
 *
 * @since 1.0.0
 * @version 1.0.1
 */
defined( 'ABSPATH' ) || exit();

class LP_Gateway_Woo extends LP_Gateway_Abstract {
	/**
	 * @var string
	 */
	public $id = 'woo-payment';

	/**
	 * Constructor for the gateway.
	 */
	public function __construct() {
		parent::__construct();

		$this->icon               = apply_filters( 'learn_press_woo_icon', '' );
		$this->method_title       = __( 'WooCommerce Payment', 'learnpress-woo-payment' );
		$this->title              = $this->method_title;
		$this->method_description = __( 'Make a payment with WooCommerce payment methods.', 'learnpress-woo-payment' );
	}

	public function get_settings() {
		return require_once LP_ADDON_WOO_PAYMENT_PATH . '/config/settings.php';
	}

	/**
	 * Check enable lp-woo-payment
	 *
	 * @return bool
	 */
	public static function is_option_enabled(): bool {
		return LearnPress::instance()->settings()->get( 'woo-payment.enable', 'yes' ) === 'yes';
	}

	/**
	 * Check option by courses via product enable
	 *
	 * @return bool
	 */
	public static function is_by_courses_via_product(): bool {
		$option_by_courses_via_product = LP_Settings::get_option( 'woo-payment_buy_course_via_product', 'no' );

		return 'yes' === $option_by_courses_via_product;
	}

	/**
	 * Check option by courses via product enable
	 *
	 * @return bool
	 */
	public static function is_enable_run_background(): bool {
		$option = LP_Settings::get_option( 'woo-payment_run_background', 'no' );

		return 'yes' === $option;
	}

	/**
	 * Enable Woo Payment
	 */
	public function is_enabled() {
		return self::is_option_enabled();
	}
}
