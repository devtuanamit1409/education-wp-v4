<?php

namespace WCBT\Controllers;

class WooCommerceController
{
    public function __construct()
    {
        add_filter('woocommerce_locate_template', array($this, 'woocommerce_locate_template'), 10, 3);
    }

    public function woocommerce_locate_template($template, $template_name, $template_path)
    {

        global $woocommerce;
        $_template = $template;
        if (! $template_path) {
            $template_path = $woocommerce->template_url;
        }

        $plugin_path  = WCBT_DIR . 'woocommerce/';

        // Look within passed path within the theme - this is priority
        $template = locate_template(
            array(
                $template_path . $template_name,
                $template_name
            )
        );

        if (! $template && file_exists($plugin_path . $template_name)) {
            $template = $plugin_path . $template_name;
        }

        if (! $template) {
            $template = $_template;
        }


        return $template;
    }
}
