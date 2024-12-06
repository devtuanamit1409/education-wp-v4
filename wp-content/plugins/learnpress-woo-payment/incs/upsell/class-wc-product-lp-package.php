<?php
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'WC_Product' ) ) {
	return;
}
use LearnPress\Upsell\Package\Package;
/**
 * Class WC_Product_LP_Package
 */
class WC_Product_LP_Package extends WC_Product {
	/**
	 * @var array|bool|null|WP_Post
	 */
	public $post = false;

	public function __construct( $product = 0 ) {
		$this->supports[] = 'ajax_add_to_cart';
		if ( is_numeric( $product ) && $product > 0 ) {
			$this->set_id( $product );
		} elseif ( $product instanceof self ) {
			$this->set_id( absint( $product->get_id() ) );
		} elseif ( ! empty( $product->ID ) ) {
			$this->set_id( absint( $product->ID ) );
		}
		$this->post = get_post( $this->id );
	}

	public function __get( $key ) {
		if ( $key === 'id' ) {
			return $this->get_id();
		} elseif ( $key === 'post' ) {
			return get_post( $this->get_id() );
		}

		return parent::__get( $key );
	}

	/**
	 * Get internal type.
	 *
	 * @return string
	 */
	public function get_type() {
		return 'simple';
	}

	/**
	 * Get Price Description
	 *
	 * @param string $context
	 *
	 * @return float
	 */
	public function get_price( $context = 'view' ) {
		$package = new Package( (int) $this->id );

		return $package ? apply_filters( 'learn-press/wc-lp-package-price', $package->get_price(), $package ) : 0;
	}

	/**
	 * @param string $context
	 *
	 * @return string
	 */
	public function get_name( $context = 'view' ) {
		return get_the_title( $this->id );
	}

	/**
	 * @param string $context
	 *
	 * @return bool
	 */
	public function exists( $context = 'view' ) {
		return $this->post && ( get_post_type( $this->post->ID ) == LP_PACKAGE_CPT ) && ( $this->post->post_status == 'publish' );
	}

	/**
	 * Check if a product is purchasable
	 */
	public function is_purchasable() {
		$package = new Package( (int) $this->id );
		return $package;
	}

	public function is_sold_individually() {
		return true;
	}

	/**
	 *
	 * @return type
	 */
	public function is_virtual() {
		return apply_filters( 'learn_press_wc_product_lp_package_is_virtual', true, $this );
	}

	public function is_downloadable() {
		return apply_filters( 'learn_press_wc_product_lp_package_is_downloadable', true, $this );
	}

	public function get_image( $size = 'woocommerce_thumbnail', $attr = array(), $placeholder = true ) {
		$thumbnail_id = get_post_thumbnail_id( $this->post );
		if ( $thumbnail_id ) {
			$image = wp_get_attachment_image( $thumbnail_id, $size, false, $attr );
		} elseif ( $placeholder ) {
			$image = wc_placeholder_img( $size );
		} else {
			$image = '';
		}

		return apply_filters( 'woocommerce_product_get_image', $image, $this, $size, $attr, $placeholder, $image );
	}

	public function get_status( $context = 'view' ) {
		return $this->post->post_status;
	}
}
