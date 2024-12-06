<?php

// use LP_Addon_Upsell_Preload;
defined( 'ABSPATH' ) || exit();

/**
 * Class LP_Wc_Upsell
 *
 * @since 4.1.4
 * @version 1.0.0
 */

use LearnPress\Helpers\Singleton;
use LearnPress\Upsell\Package\Package;

class LP_Wc_Upsell {
	use Singleton;

	/**
	 * @var string
	 */
	public static $lp_wc_package_assigned = '_lp_wc_package_assigned';

	public function init() {
		//include_once LP_ADDON_WOO_PAYMENT_PATH . '/incs/TemplateHooks/Upsell/LPWooUpsellTemplate.php';
		//LPWooUpsellTemplate::instance();
		$this->hooks();
	}

	/**
	 * add hooks here
	 */
	protected function hooks() {
		add_filter( 'lp/upsell/single-package/header/right/sections', array( $this, 'btn_package_buy_now' ) );
		add_filter( 'woocommerce_cart_item_quantity', array( $this, 'disable_quantity_box' ), 10, 3 );

		if ( LP_Gateway_Woo::is_by_courses_via_product() ) {
			add_filter( 'woocommerce_product_data_tabs', array( $this, 'lp_package_data_tabs' ), 12, 1 );
			add_action( 'woocommerce_product_data_panels', array( $this, 'lp_package_product_panels' ) );
			add_action( 'woocommerce_process_product_meta', array( $this, 'save_package_data' ), 10, 1 );
			add_filter( 'woocommerce_product_tabs', array( $this, 'view_package_by_product' ), 100, 1 );
			add_filter( 'woocommerce_add_to_cart_quantity', array( $this, 'set_quantity' ), 10, 2 );
			add_filter( 'learnpress/wc-order/buy-via-product', [ $this, 'buy_package_via_product' ], 10, 2 );
		} else {
			add_filter(
				'woocommerce_json_search_found_products',
				array(
					$this,
					'wc_json_search_found_products_and_package',
				)
			);
			add_filter( 'woocommerce_get_order_item_classname', array( $this, 'get_classname_lp_wc_order' ), 10, 3 );
			add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'order_item_line' ), 10, 4 );
			add_filter( 'woocommerce_product_class', array( $this, 'product_class' ), 10, 4 );
		}
	}

	public function btn_package_buy_now( $sections ) {
		$package_id = get_the_ID();
		if ( ! $package_id ) {
			return $sections;
		}

		$package              = new Package( absint( $package_id ) );
		$btn_html_buy_package = '';
		ob_start();
		if ( LP_Gateway_Woo::is_by_courses_via_product() ) {
			LP_Addon_Woo_Payment_Preload::$addon->get_template( 'package-notice', array( 'package_id' => $package_id ) );
		} else {
			$course_list = $package->get_course_list();
			if ( empty( $course_list ) ) {
				$btn_html_buy_package = __( 'Package is unavailable, no any courses assigned.', 'learnpress-woo-payment' );
			} else {
				$item = [
					'id'   => $package_id,
					'type' => LP_PACKAGE_CPT,
				];

				do_action( 'learnpress/woo-payment/btn-add-item-to-cart/layout', $item );
			}
		}

		$btn_html_buy_package    .= ob_get_clean();
		$sections['add-to-cart'] = array(
			'text_html' => $btn_html_buy_package,
		);

		return $sections;
	}

	public function lp_package_data_tabs( array $tabs ): array {
		$tabs['lp_package_data'] = array(
			'label'  => __( 'LP Packages', 'learnpress-woo-payment' ),
			'target' => 'lp_package_product_data',
		);

		return $tabs;
	}

	public function lp_package_product_panels() {
		global $post;

		echo '<div id="lp_package_product_data" class="panel woocommerce_options_panel hidden">';
		foreach ( $this->metabox( $post ) as $key => $object ) {
			$object->id = $key;
			$object->output( $post->ID );
		}
		echo '</div>';
	}

	public function metabox( $post ) {
		try {
			$product = wc_get_product( $post->ID );
			if ( ! $product ) {
				return [];
			}

			$lp_db                    = LP_Database::getInstance();
			$filter                   = new LP_Post_Type_Filter();
			$filter->collection       = $lp_db->tb_posts;
			$filter->collection_alias = 'p';
			$filter->only_fields      = array( 'ID', 'post_title' );
			$filter->where[]          = $lp_db->wpdb->prepare( 'AND p.post_type=%s', LP_PACKAGE_CPT );
			$filter->limit            = - 1;
			$result                   = $lp_db->execute( $filter );
			$option                   = [];
			if ( ! empty( $result ) ) {
				foreach ( $result as $row ) {
					$option[ $row->ID ] = $row->post_title;
				}
			}

			$value = $product->get_meta( LP_Wc_Upsell::$lp_wc_package_assigned ) ?? '';

			return array(
				LP_Wc_Upsell::$lp_wc_package_assigned => new LP_Meta_Box_Select_Field(
					__( 'Assign package to this product', 'learnpress-woo-payment' ),
					'',
					'',
					array(
						'options'  => $option,
						'multiple' => false,
						'value'    => $value,
					)
				),
			);
		} catch ( Throwable $e ) {
			return [];
		}
	}

	public function save_package_data( $product_id ) {
		if ( ! isset( $_POST[ LP_Wc_Upsell::$lp_wc_package_assigned ] ) ) {
			return;
		}

		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			return;
		}

		$package_assigned = LP_Request::get_param( LP_Wc_Upsell::$lp_wc_package_assigned, '', 'int' );
		$product->update_meta_data( LP_Wc_Upsell::$lp_wc_package_assigned, $package_assigned );
		$product->save_meta_data();
	}

	/**
	 * Get classname WC_Order_Item_LP_Package
	 *
	 * @throws Exception
	 */
	public function get_classname_lp_wc_order( $classname, $item_type, $id ) {
		if ( in_array( $item_type, array( 'line_item', 'product' ) ) ) {
			$lp_package_id = wc_get_order_item_meta( $id, '_lp_package_id' );
			if ( $lp_package_id && LP_PACKAGE_CPT === get_post_type( $lp_package_id ) ) {
				$classname = 'WC_Order_Item_LP_Package';
			}
		}

		return $classname;
	}

	/**
	 * For on WC Coupon data
	 *
	 * @param $products
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function wc_json_search_found_products_and_package( $products ) {

		$term = wc_clean( empty( $term ) ? stripslashes( $_GET['term'] ) : $term );

		$lp_db                    = LP_Database::getInstance();
		$filter                   = new LP_Post_Type_Filter();
		$filter->collection       = $lp_db->tb_posts;
		$filter->collection_alias = 'p';
		$filter->only_fields      = array( 'ID', 'post_title' );
		$filter->where[]          = $lp_db->wpdb->prepare( 'AND p.post_type=%s', LP_PACKAGE_CPT );
		$filter->where[]          = $lp_db->wpdb->prepare( 'AND p.post_status=%s', 'publish' );
		$filter->where[]          = $lp_db->wpdb->prepare( 'AND post_title LIKE %s', '%' . $lp_db->wpdb->esc_like( $term ) . '%' );
		$filter->limit            = - 1;
		$result                   = $lp_db->execute( $filter );
		if ( ! empty( $result ) ) {
			foreach ( $result as $row ) {
				$products[ $row->ID ] = $row->post_title . ' (' . __( 'Package', 'learnpress-woo-payment' ) . ' #' . $row->ID . ')';
			}
		}

		return $products;
	}

	public function order_item_line( $item, $cart_item_key, $values, $order ) {
		if ( LP_PACKAGE_CPT === get_post_type( $values['product_id'] ) ) {
			$item->add_meta_data( '_lp_package_id', $values['product_id'], true );
		}
	}

	/**
	 * Set the product class name.
	 *
	 * @param $classname
	 * @param $product_type
	 * @param $post_type
	 * @param $product_id
	 *
	 * @return string
	 */
	public function product_class( $classname, $product_type, $post_type, $product_id ): string {
		if ( LP_PACKAGE_CPT === get_post_type( $product_id ) ) {
			$classname = 'WC_Product_LP_Package';
		}

		return $classname;
	}

	/**
	 * Disable select quantity product which has package
	 *
	 * @param string $product_quantity
	 * @param string $cart_item_key
	 * @param array $cart_item
	 *
	 * @return string
	 */
	public function disable_quantity_box( string $product_quantity, string $cart_item_key, array $cart_item ): string {
		$product_id       = $cart_item['product_id'] ?? 0;
		$quantity_disable = false;
		if ( get_class( $cart_item['data'] ) === 'WC_Product_LP_Package' ) {
			$quantity_disable = true;
		} elseif ( LP_Gateway_Woo::is_by_courses_via_product() ) {
			$product    = wc_get_product( $product_id );
			$package_id = $product->get_meta( LP_Wc_Upsell::$lp_wc_package_assigned ) ?? '';
			if ( ! empty( $package_id ) ) {
				$quantity_disable = true;
			}
		}

		if ( $quantity_disable ) {
			$product_quantity = sprintf(
				'<span style="text-align: center; display: block">%s</span>',
				$cart_item['quantity']
			);
		}

		return $product_quantity;
	}

	/**
	 * Add tabs show package in product page
	 */
	public function view_package_by_product( $tabs ) {
		global $post;

		$product = wc_get_product( $post->ID );
		if ( ! $product ) {
			return $tabs;
		}

		$package = $product->get_meta( LP_Wc_Upsell::$lp_wc_package_assigned ) ?? '';
		if ( ! empty( $package ) ) {
			$tabs['_lp_package_data'] = array(
				'title'    => __( 'Package', 'learnpress-woo-payment' ),
				'priority' => 100,
				'callback' => array( $this, 'lp_package_content_tabs' ),
			);
		}

		return $tabs;
	}

	/**
	 * view package by product callback
	 */
	public function lp_package_content_tabs() {
		global $post;
		wp_enqueue_style( 'lp-woo-css' );
		$product = wc_get_product( $post->ID );
		if ( ! $product ) {
			return;
		}

		$package_id = $product->get_meta( LP_Wc_Upsell::$lp_wc_package_assigned ) ?? '';
		if ( $package_id ) {
			echo '<ul class="list-courses-assign-product">';
			echo '<li> <a href=' . get_permalink( $package_id ) . '>' . get_the_title( $package_id ) . '</a></li>';
			echo '</ul>';
		}
	}

	/**
	 * Product has package only add one time to cart
	 *
	 * @param int $quantity
	 * @param int $product_id
	 *
	 * @return int
	 * @since 4.1.4
	 */
	public function set_quantity( int $quantity, int $product_id ): int {
		$product    = wc_get_product( $product_id );
		$package_id = $product->get_meta( LP_Wc_Upsell::$lp_wc_package_assigned ) ?? '';

		if ( ! empty( $package_id ) ) {
			$message  = __( 'Product which has package is only added one time.', 'learnpress-woo-payment' );
			$cart     = WC()->cart;
			$cart_key = $cart->generate_cart_id( $product_id );

			if ( array_key_exists( $cart_key, $cart->cart_contents ) ) {
				$quantity = 0;

				wc_add_notice( $message );
			} elseif ( $quantity > 1 ) {
				wc_add_notice( $message );

				$quantity = 1;
			}
		}

		return $quantity;
	}

	/**
	 * Add package to LP Order when buy via product
	 *
	 * @var array $lp_order_items
	 * @var WC_Order_Item_Product $item
	 * @since 4.1.4
	 * @version 1.0.0
	 */
	public function buy_package_via_product( $lp_order_items, $item ) {
		$product    = $item->get_product();
		$package_id = $product->get_meta( LP_Wc_Upsell::$lp_wc_package_assigned );
		if ( ! $package_id ) {
			return $lp_order_items;
		}

		$package                       = new Package( absint( $package_id ) );
		$lp_order_items[ $package_id ] = array(
			'item_type'      => LP_PACKAGE_CPT,
			'item_id'        => $package_id,
			'order_subtotal' => $package->get_price(),
			'order_total'    => $package->get_price(),
		);

		return $lp_order_items;
	}
}
