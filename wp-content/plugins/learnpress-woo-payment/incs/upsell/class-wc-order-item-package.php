<?php

/**
 * @class WC_Order_Item_LP_Course
 */

defined( 'ABSPATH' ) || exit();

class WC_Order_Item_LP_Package extends WC_Order_Item_Product {
	/**
	 * @throws Exception
	 */
	public function set_product_id( $value ) {
		if ( $value > 0 && LP_PACKAGE_CPT !== get_post_type( absint( $value ) ) ) {
			$this->error( 'order_item_product_invalid_product_id', __( 'Invalid product ID', 'woocommerce' ) );
		}

		$lp_package_id = wc_get_order_item_meta( $this->get_id(), '_lp_package_id' );
		if ( LP_PACKAGE_CPT == get_post_type( absint( $lp_package_id ) ) ) {
			$value = $lp_package_id;
		}

		$this->set_prop( 'product_id', absint( $value ) );
	}
}
