<?php
/**
 * Class LP_PMS_Woo
 *
 * @desription
 *
 * @version    1.0.0
 * @since      3.1.15
 * @author     tungnx
 */

class LP_PMS_Woo {
	public static $_instance;
	public $_woo_membership_product_ids;

	/**
	 * Singleton
	 *
	 * @return LP_PMS_Woo
	 */
	public static function instance(): LP_PMS_Woo {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * LP_PMS_Woo constructor.
	 */
	public function __construct() {
		if ( $this->pmpro_woo_is_active() ) {
			$this->hook();
		}
	}

	public function hook() {
		add_action( 'woocommerce_order_status_completed', array( $this, 'create_lp_order_and_add_courses' ), 9, 2 );
	}

	/**
	 * @param                                              $order_id
	 * @param Automattic\WooCommerce\Admin\Overrides\Order $order
	 */
	public function create_lp_order_and_add_courses( $order_id, $order ) {
		// get product ids has membership
		// $membership_product_ids = pmprowoo_get_membership_products_from_order( $order_id );

		if ( isset( $_SESSION['wc_order_change_completed'] ) ) {
			unset( $_SESSION['wc_order_change_completed'] );
		}

		$_SESSION['wc_order_change_completed'] = $order;
	}

	/**
	 * Check plugin pmpro-woo is active
	 * https://www.paidmembershipspro.com/add-ons/pmpro-woocommerce/
	 *
	 * @return bool
	 */
	public function pmpro_woo_is_active(): bool {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		return is_plugin_active( 'pmpro-woocommerce/pmpro-woocommerce.php' );
	}
}

LP_PMS_Woo::instance();
