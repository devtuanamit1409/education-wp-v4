<?php
/*
 * @Author : leehld
 * @Date   : 2/9/2017
 * @Last Modified by: leehld
 * @Last Modified time: 2/9/2017
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Product' ) ) {
	return;
}
class WPEMS_WC_Product extends WC_Product {

	/*
	 * Event product data
	 */
	public $post = false;
	/**
	 * WPEMS_WC_Product constructor
	 *
	 * @param mixed $product
	 */
	public function __construct( $product = 0 ) {
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
	 * @param string $context
	 *
	 * @return int|string
	 */
	public function get_image_id( $context = 'view' ) {
		$event_id = $this->post->ID;
		if ( get_post_type( $event_id ) === 'tp_event' ) {

			return get_post_thumbnail_id( $event_id );
		}
	}

	/**
	 * Get event price
	 *
	 * @return mixed
	 */
	public function get_price( $context = 'view' ) {
		$event_id = $this->post->ID;
		$price    = floatval( get_post_meta( $event_id, 'tp_event_price', true ) ) ?? 0;

		return $price;
	}

	/**
	 * Is purchasable event
	 *
	 * @return bool
	 */
	public function is_purchasable( $content = 'view' ) {
		return true;
	}

	/**
	 * Set number event product
	 *
	 * @return mixed
	 */
	public function get_stock_quantity( $context = 'view' ) {
		$event = WPEMS_Event::instance( get_post( $this->post->ID ) );

		return $event->get_slot_available();
	}

	public function get_stock_status( $context = 'view' ) {
		return $this->get_stock_quantity( $context ) > 0 ? 'instock' : '';
	}

	/**
	 * Check only allow one of this event to be bought in a single order
	 *
	 * @return bool
	 */
	public function is_sold_individually() {
		if ( get_option( 'thimpress_events_email_register_times', true ) == 'once' && ! $this->get_price() ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @param string $context
	 *
	 * @return bool
	 */
	public function exists( $context = 'view' ) {
		return $this->post && ( get_post_type( $this->post->ID ) == 'tp_event' ) && ( ! in_array(
			get_post_status( $this->get_id() ),
			array(
				'draft',
				'auto-draft',
			)
		) );
	}

	public function is_virtual() {
		return true;
	}

	/**
	 * @param string $context
	 *
	 * @return string
	 */
	public function get_name( $context = 'view' ) {
		return get_the_title( $this->post->ID );
	}
}
