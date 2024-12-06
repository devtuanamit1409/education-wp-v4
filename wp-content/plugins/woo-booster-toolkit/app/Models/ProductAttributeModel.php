<?php

namespace WCBT\Models;

class ProductAttributeModel {
	public static function get_attribute_taxonomies() {
		$group = 'woocommerce-attributes';
		$prefix = wp_cache_get( 'wc_' . $group . '_cache_prefix', $group );

		if ( false === $prefix ) {
			$prefix = microtime();
			wp_cache_set( 'wc_' . $group . '_cache_prefix', $prefix, $group );
		}

		$prefix = 'wc_cache_' . $prefix . '_';

		$cache_key   = $prefix . 'attributes';
		$cache_value = wp_cache_get( $cache_key, 'woocommerce-attributes' );

		if ( false !== $cache_value ) {
			return $cache_value;
		}

		$raw_attribute_taxonomies = get_transient( 'wc_attribute_taxonomies' );

		if ( false === $raw_attribute_taxonomies ) {
			global $wpdb;

			$raw_attribute_taxonomies = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_name != '' ORDER BY attribute_name ASC;" );

			set_transient( 'wc_attribute_taxonomies', $raw_attribute_taxonomies );
		}

		$raw_attribute_taxonomies = (array) array_filter( apply_filters( 'woocommerce_attribute_taxonomies', $raw_attribute_taxonomies ) );

		// Index by ID for easier lookups.
		$attribute_taxonomies = array();

		foreach ( $raw_attribute_taxonomies as $result ) {
			$attribute_taxonomies[ 'id:' . $result->attribute_id ] = $result;
		}

		wp_cache_set( $cache_key, $attribute_taxonomies, 'woocommerce-attributes' );

		return $attribute_taxonomies;
	}
}
