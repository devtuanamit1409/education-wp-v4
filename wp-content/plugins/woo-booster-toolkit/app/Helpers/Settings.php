<?php

namespace WCBT\Helpers;

/**
 * Class Settings
 * @package WCBT\Helpers
 */
class Settings
{
    /**
     * Get all options
     *
     * @return false|mixed|void|null
     */
    public static function get_all_settings()
    {
        static $settings = null;

        if (null === $settings) {
            $settings = get_option(WCBT_OPTION_KEY);
            $config   = Config::instance()->get('wcbt-setting');
            if (empty($settings)) {
                $settings = Config::instance()->get_default_data($config);
            } else { // Handle when add key, remove key or change key settings
                $default = Config::instance()->get_default_data($config);
                //If key exist in config, not in settings, add key into key into settings
                $settings = wp_parse_args(
                    $settings,
                    $default
                );
                //If key exist in settings, not in config, remove key in settings
                $diff_key = array_diff_key($settings, $default);
                if (! empty($diff_key)) {
                    foreach ($diff_key as $key => $value) {
                        unset($settings[ $key ]);
                    }
                }
            }
        }

        return $settings;
    }

    /**
     * @param string $group_key
     *
     * @return array|false|\stdClass|string
     */
    public static function get_setting_detail(string $group_key = '')
    {
        return self::get_all_settings()[ $group_key ] ?? '';
    }

    /**
     * @param $key
     * @param $value
     *
     * @return void
     */
    public static function update_field($key, $value)
    {
        $settings = self::get_all_settings();

        if (! isset($settings[ $key ])) {
            return;
        }

        $settings[ $key ] = $value;

        update_option(WCBT_OPTION_KEY, $settings);
    }


    /**
     * @return array
     */
    public static function get_all_products()
    {
        $results     = array();
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => - 1,
            'post_status'    => 'publish',
        );
        $products = get_posts($args);
        if ($products) {
            foreach ($products as $product) {
                $results[ $product->ID ] = $product->post_title;
            }
            wp_reset_postdata();
        }

        return $results;
    }
}
