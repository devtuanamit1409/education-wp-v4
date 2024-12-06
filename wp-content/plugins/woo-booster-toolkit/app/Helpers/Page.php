<?php

namespace WCBT\Helpers;

/**
 * Class Page
 * @package WCBT\Helpers
 */
class Page
{
    /**
     * @return mixed|string|void
     */
    public static function get_current_page()
    {
        if (Page::is_wishlist_wc_product_page()) {
            return WCBT_WISHLIST_PAGE;
        } elseif (Page::is_compare_wc_product_page()) {
            return WCBT_COMPARE_PAGE;
        } elseif (Page::is_setting_page()) {
            return WCBT_SETTING_PAGE;
        } elseif (Page::is_single_product_page()) {
            return WCBT_SINGLE_PRODUCT_PAGE;
        } else {
            return apply_filters('wcbt/filter/page/current', '');
        }
    }

    /**
     * @return bool
     */
    public static function is_wishlist_wc_product_page()
    {
        if (! is_singular('page')) {
            return false;
        }
        global $post;
        $wish_list_page_id = Settings::get_setting_detail('wishlist:fields:page');

        return intval($wish_list_page_id) === $post->ID;
    }

    /**
     * @return bool
     */
    public static function is_compare_wc_product_page()
    {
        if (! is_page()) {
            return false;
        }
        global $post;
        $compare_product_page_id = Settings::get_setting_detail('compare:fields:page');

        return intval($compare_product_page_id) === $post->ID;
    }

    /**
     * @return bool
     */
    public static function is_setting_page()
    {
        if (! is_admin() || ! isset($_GET['page'])) {
            return false;
        }

        return in_array($_GET['page'], array(
            'wcbt-setting',
            'wcbt-wishlist',
            'wcbt-compare',
            'wcbt-quick-view',
            'wcbt-variation'
        ));
    }

    public static function is_single_product_page()
    {
        return is_product();
    }
}
