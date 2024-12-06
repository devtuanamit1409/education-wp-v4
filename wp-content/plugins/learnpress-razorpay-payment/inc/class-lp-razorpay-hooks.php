<?php


require LP_ADDON_RAZORPAY_PAYMENT_PATH . '/vendor/autoload.php';

/**
 * LearnPress Razorpay Payment Hooks
 *
 * @since 4.0.0
 * @version 1.0.0
 */
defined( 'ABSPATH' ) || exit();

class LP_Razorpay_Hooks {
	private static $instance;

	protected function __construct() {
		$this->hooks();
	}

	protected function hooks() {

	}

	/**
	 * Singleton.
	 *
	 * @return LP_Razorpay_Hooks
	 */
	public static function instance(): LP_Razorpay_Hooks {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
