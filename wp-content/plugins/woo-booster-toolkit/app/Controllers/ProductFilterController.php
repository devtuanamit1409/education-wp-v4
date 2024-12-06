<?php

namespace WCBT\Controllers;

use WCBT\Helpers\Template;
use WCBT\Models\ProductAttributeModel;

class ProductFilterController {
	public function __construct() {
		add_action( 'woocommerce_sidebar', array( $this, 'archive_product_sidebar' ), 9999999 );
		add_action( 'pre_get_posts', array( $this, 'filter_pre_get_products' ), 10, 1 );
	}

	public function filter_pre_get_products( $q ) {
		global $wp_query;

		if ( ! is_shop() && ! is_product_taxonomy() ) {
			return;
		}

		if ( ( ! isset( $q->query['post_type'] ) || $q->query['post_type'] !== 'product' ) &&
		     ( empty( $q->queried_object_id ) )
		) {
			return;
		}

		$tax_query = $q->get( 'tax_query' );
		if ( empty( $tax_query ) ) {
			$tax_query = array();
		}

		//Category
		if ( isset( $_GET['category'] ) ) {
			$cat_terms = json_decode( urldecode( $_GET['category'] ) );
			if ( ! empty( $cat_terms ) && is_array( $cat_terms ) ) {
				$tax_query = array_merge(
					$tax_query,
					array(
						array(
							'taxonomy' => 'product_cat',
							'field'    => 'id',
							'terms'    => $cat_terms,
							'operator' => 'IN',
						)
					),
				);
			}
		}

		//Attributes
		$attributes = ProductAttributeModel::get_attribute_taxonomies();

		foreach ( $attributes as $attribute ) {
			$attribute_name = $attribute->attribute_name;

			if ( isset( $_GET[ $attribute_name ] ) ) {
				$attr_terms = json_decode( urldecode( $_GET[ $attribute_name ] ) );
				if ( ! empty( $attr_terms ) && is_array( $attr_terms ) ) {
					$tax_query = array_merge(
						$tax_query,
						array(
							array(
								'taxonomy' => 'pa_' . $attribute_name,
								'field'    => 'id',
								'terms'    => $attr_terms,
								'operator' => 'IN',
							)
						),
					);
				}
			}
		}

		$tax_query['relation'] = 'AND';

		$q->set(
			'tax_query',
			$tax_query
		);

		//Meta query
		$meta_query = $q->get( 'meta_query' );

		if ( empty( $meta_query ) ) {
			$meta_query = array();
		}

		if ( isset( $_GET['availability'] ) ) {
			$availabilities     = json_decode( urldecode( $_GET['availability'] ) );
			$availability_value = array();
			foreach ( $availabilities as $availability ) {
				if ( $availability === 'in-stock' ) {
					$availability_value[] = 'instock';
				}

				if ( $availability === 'out-stock' ) {
					$availability_value[] = 'outofstock';
				}
			}
			if ( ! empty( $availabilities ) && is_array( $availabilities ) ) {
				$meta_query = array_merge(
					$meta_query,
					array(
						array(
							'key'     => '_stock_status',
							'value'   => $availability_value,
							'compare' => 'IN',
						)
					)
				);
			}
		}

		if ( isset( $_GET['rating'] ) ) {
			$ratings = json_decode( urldecode( $_GET['rating'] ) );
			if ( ! empty( $ratings ) && is_array( $ratings ) ) {
				$rating_query             = array();
				$rating_query['relation'] = 'OR';
				foreach ( $ratings as $rating ) {
					$rating_query[] = array(
						'key'     => '_wc_average_rating',
						'value'   => array( $rating, $rating + 1 - 1 / 100000000 ),
						'type'    => 'numeric',
						'compare' => 'BETWEEN',
					);
				}
				$meta_query = array_merge(
					$meta_query,
					array(
						$rating_query
					),
				);
			}
		}

		//Price
		if ( isset( $_GET['min_price'] ) && isset( $_GET['max_price'] ) ) {
			$min_price = $_GET['min_price'];
			$max_price = $_GET['max_price'];

			$meta_query = array_merge(
				$meta_query,
				array(
					array(
						'key'     => '_price',
						'value'   => array( $min_price, $max_price ),
						'compare' => 'BETWEEN',
						'type'    => 'DECIMAL(10,' . wc_get_price_decimals() . ')',
					)
				),
			);
		}

		$meta_query['relation'] = 'AND';

		$q->set(
			'meta_query',
			$meta_query
		);
	}

	/**
	 * @return string|void
	 */
	public function archive_product_sidebar() {
		if ( ! is_shop() && ! is_product_taxonomy() ) {
			return '';
		}

		if ( ! is_active_sidebar( 'wcbt-product-archive-sidebar' ) ) {
			return '';
		}

		dynamic_sidebar( 'wcbt-product-archive-sidebar' );
	}
}
