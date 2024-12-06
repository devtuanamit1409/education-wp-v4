<?php

use WCBT\Helpers\SourceAsset;

$source_asset = SourceAsset::getInstance();

return apply_filters(
    'wcbt/filter/config/style',
    array(
        'admin'    => array(
            'wcbt-admin' => array(
                'src' => $source_asset->get_asset_admin_file_url(
                    'css',
                    'wcbt-admin'
                ),
            ),
        ),
        'frontend' => array(
            'wcbt-global'          => array(
                'src' => $source_asset->get_asset_frontend_file_url(
                    'css',
                    'wcbt-global'
                ),
            ),
            'wcbt-compare-product' => array(
                'src'     => $source_asset->get_asset_frontend_file_url(
                    'css',
                    'wcbt-compare-product'
                ),
                'screens' => array(
                    WCBT_COMPARE_PAGE,
                ),
            ),
            // 'wcbt-wishlist'        => array(
            //     'src'     => $source_asset->get_asset_frontend_file_url(
            //         'css',
            //         'wcbt-wishlist'
            //     ),
            //     'screens' => array(
            //         WCBT_WISHLIST_PAGE,
            //     ),
            // ),
        ),
    )
);
