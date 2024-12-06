<?php

use WCBT\Helpers\SourceAsset;
use WCBT\Helpers\Variation;

$source_asset = SourceAsset::getInstance();
$variation_js = Variation::get_data()['mode'] === 'on';

return apply_filters(
	'wcbt/filter/config/scripts',
	array(
		'admin'    => array(
			'register'       => array(
				'wcbt-js-color' => array(
					'src' => $source_asset->get_asset_js_lib_file_url( 'jscolor/jscolor' ),
				),
				'wcbt-global'   => array(
					'src'  => $source_asset->get_asset_admin_file_url( 'js', 'wcbt-global' ),
					'deps' => array( 'wp-api-fetch' ),
				),
				'wcbt-settings' => array(
					'src'     => $source_asset->get_asset_admin_file_url( 'js', 'wcbt-settings' ),
					'screens' => array(
						WCBT_SETTING_PAGE,
					),
				),
			),
			'js-translation' => array(
				'wcbt-global' => array(
					'src' => WCBT_URL . 'languages',
				),
			),
		),
		'frontend' => array(
			'register' => array(
				'add-to-cart-variation' => array(
					'src'       => $source_asset->get_asset_js_lib_file_url( 'add-to-cart/add-to-cart-variation' ),
					'condition' => $variation_js,
				),
				'wcbt-global'           => array(
					'src'  => $source_asset->get_asset_frontend_file_url( 'js', 'wcbt-global' ),
					'deps' => array( 'wp-api-fetch' ),
				),
				'wcbt-wishlist'         => array(
					'src'     => $source_asset->get_asset_frontend_file_url( 'js', 'wcbt-wishlist' ),
					'deps'    => array( 'wp-api-fetch' ),
					'screens' => array(
						WCBT_WISHLIST_PAGE,
					),
				),
				'wcbt-compare'          => array(
					'src'     => $source_asset->get_asset_frontend_file_url( 'js', 'wcbt-compare' ),
					'screens' => array(
						WCBT_COMPARE_PAGE,
					),
				),
				'wcbt-product-filter'   => array(
					'src'     => $source_asset->get_asset_frontend_file_url( 'js', 'wcbt-product-filter' ),
					'screens' => array( 'archive-product-page' )
				),
			),
		),
	),
);
