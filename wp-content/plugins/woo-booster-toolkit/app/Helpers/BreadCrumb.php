<?php

namespace WCBT\Helpers;

class BreadCrumb
{
    /**
     * @return array
     */
    public static function get_breadcrumb()
    {
        $breadcrumb[] = array(
            'title' => esc_html_x('Home', 'breadcrumb', 'wcbt'),
            'link'  => apply_filters('wcbt/filter/breadcrumb/home-url', home_url()),
        );

        if (is_page()) {
            $breadcrumb[] = array(
                'title' => get_the_title(),
            );
        }

        return $breadcrumb;
    }
}
