<?php

/**
 * Class LP_Addon_Woo_Payment
 */
class LP_Addon_Woo_Payment extends LP_Addon {
	/**
	 * @var string
	 */
	public $version = LP_ADDON_WOO_PAYMENT_VER;

	/**
	 * @var string
	 */
	public $require_version = LP_ADDON_WOO_PAYMENT_REQUIRE_VER;

	/**
	 * @var string
	 */
	public $plugin_file = LP_ADDON_WOO_PAYMENT_FILE;

	/**
	 * @var LP_Addon_Woo_Payment|null
	 *
	 * Hold the singleton of LP_Woo_Payment_Preload object
	 */
	protected static $_instance = null;

	/**
	 * LP_Woo_Payment_Preload constructor.
	 */

	public function __construct() {
		parent::__construct();
		$this->includes();
	}

	/**
	 * Include files needed
	 */
	protected function includes() {
		require_once LP_ADDON_WOO_PAYMENT_PATH . '/incs/class-lp-gateway-woo.php';
		add_filter( 'learn-press/payment-methods', array( $this, 'lp_woo_settings' ) );
		add_filter( 'learn-press/frontend/localize-data-global', function ( $data ) {
			$data['lp_woo_version'] = $this->version;

			return $data;
		} );

		if ( ! LP_Gateway_Woo::is_option_enabled() ) {
			return;
		}

		include_once LP_ADDON_WOO_PAYMENT_PATH . '/incs/TemplateHooks/LPWooTemplate.php';
		LPWooTemplate::instance();
		include_once LP_ADDON_WOO_PAYMENT_PATH . '/incs/class-lp-woo-ajax.php';
		include_once LP_ADDON_WOO_PAYMENT_PATH . '/incs/class-lp-woo-order.php';
		include_once LP_ADDON_WOO_PAYMENT_PATH . '/incs/background-process/class-lp-woo-payment-background-process.php';

		if ( LP_Gateway_Woo::is_by_courses_via_product() ) {
			require_once LP_ADDON_WOO_PAYMENT_PATH . '/incs/class-lp-woo-assign-course-to-product.php';
		} else {
			// Create type WC_Order_Item_LP_Course for wc order
			include_once LP_ADDON_WOO_PAYMENT_PATH . '/incs/class-wc-order-item-course.php';

			// Create type WC_Product_LP_Course for wc product
			require_once LP_ADDON_WOO_PAYMENT_PATH . '/incs/class-wc-product-lp-course.php';
		}

		// Hooks
		require_once LP_ADDON_WOO_PAYMENT_PATH . '/incs/class-lp-wc-hooks.php';
		LP_WC_Hooks::instance();

		// Compatible with LP Upsell
		if ( class_exists( 'LP_Addon_Upsell_Preload' ) ) {
			require_once LP_ADDON_WOO_PAYMENT_PATH . '/incs/upsell/class-lp-wc-upsell.php';
			LP_Wc_Upsell::instance();

			if ( ! LP_Gateway_Woo::is_by_courses_via_product() ) {
				// Add package to cart
				include_once LP_ADDON_WOO_PAYMENT_PATH . '/incs/upsell/class-wc-add-lp-package-to-cart.php';
				// Create type WC_Product_LP_Package for wc product
				require_once LP_ADDON_WOO_PAYMENT_PATH . '/incs/upsell/class-wc-product-lp-package.php';
				// Create type WC_Order_Item_LP_Course for wc order
				include_once LP_ADDON_WOO_PAYMENT_PATH . '/incs/upsell/class-wc-order-item-package.php';
			}
		}

		// Plugin WC subscription
		if ( class_exists( 'WC_Subscriptions' ) ) {
			require_once LP_ADDON_WOO_PAYMENT_PATH . '/incs/class-lp-wc-subscription.php';
			LP_WC_Subscription::instance();
		}
	}

	/**
	 * Show lp woo settings
	 *
	 * @param array $methods
	 *
	 * @return array
	 */
	public function lp_woo_settings( array $methods ): array {
		$methods['woo-payment'] = 'LP_Gateway_Woo';

		return $methods;
	}
}
