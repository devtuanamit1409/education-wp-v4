<?php

namespace WCBT\Helpers;

use WCBT\Models\UserModel;

class SaleNotice
{
    public static function get_data()
    {
        $product_type = Settings::get_setting_detail('sale-notice:fields:type');

        if ($product_type === 'sale-product') {
            $product_info = self:: get_sale_products();
        } else {
            $product_info = self::get_virtual_products();
        }

        $data = array(
            'mode'             => Settings::get_setting_detail('sale-notice:fields:enable'),
            'product_info'     => $product_info,
            'position'         => Settings::get_setting_detail('sale-notice:fields:position'),
            'close_button'     => Settings::get_setting_detail('sale-notice:fields:close_button'),
            'progress_bar'     => Settings::get_setting_detail('sale-notice:fields:progress_bar'),
            'text_color'       => Settings::get_setting_detail('sale-notice:fields:text_color'),
            'background_color' => Settings::get_setting_detail('sale-notice:fields:background_color'),
            'image_position'   => Settings::get_setting_detail('sale-notice:fields:image_position'),
            'show_method'      => Settings::get_setting_detail('sale-notice:fields:show_method'),
            'hide_method'      => Settings::get_setting_detail('sale-notice:fields:hide_method'),
            'show_duration'    => Settings::get_setting_detail('sale-notice:fields:show_duration'),
            'hide_duration'    => Settings::get_setting_detail('sale-notice:fields:hide_duration'),
            'timeout'          => Settings::get_setting_detail('sale-notice:fields:timeout'),
        );

        return apply_filters('wcbt/filter/sale-notice/data', $data);
    }

    /**
     * @param $product_id
     * @param $user_name
     * @param $time_ago
     *
     * @return array|false|\stdClass|string|string[]
     */
    public static function get_message($product_id, $user_name, $time_ago, $product_type)
    {
        $message           = Settings::get_setting_detail('sale-notice:fields:message');
        $text_color        = Settings::get_setting_detail('sale-notice:fields:text_color');
        $product_with_link = '<a style="color:' . $text_color . ';" href="' . get_the_permalink($product_id) .
                             '" target="_blank" rel="noopener">' . get_the_title() . '</a>';

        if ($product_type === 'virtual_product') {
            $virtual_user = self::get_virtual_user();
            if (empty($user_name) && count($virtual_user)) {
                $user_name = $virtual_user[ array_rand($virtual_user, 1) ];
            }

            if (empty($time_ago)) {
                $time_ago = self::get_virtual_time_ago();
            }
        }

        return str_replace(
            array(
                '{{user_name}}',
                '{{product}}',
                '{{product_with_link}}',
                '{{time_ago}}'
            ),
            array(
                $user_name,
                get_the_title(),
                $product_with_link,
                $time_ago
            ),
            $message
        );
    }

    /**
     * @return array|false|string[]
     */
    public static function get_virtual_user()
    {
        $virtual_user = Settings::get_setting_detail('sale-notice:fields:virtual_user');

        if (empty($virtual_user)) {
            return array();
        }

        return explode(',', $virtual_user);
    }

    public static function get_virtual_time_ago()
    {
        return rand(5, 60);
    }

    public static function get_sale_products()
    {
        $order_args = array(
            'post_type'      => wc_get_order_types(),
            'post_status'    => array( 'wc-completed', 'wc-processing' ),
            'posts_per_page' => Settings::get_setting_detail('sale-notice:fields:sale_product_number'),
        );

        if (Settings::get_setting_detail('sale-notice:fields:sale_product_order') === 'recent') {
            $order_args['orderby'] = 'date';
            $order_args['order']   = 'desc';
        } else {
            $order_args['orderby'] = 'rand';
        }

        $orders_list = get_posts($order_args);

        if (empty($orders_list)) {
            return array();
        }

        $product_info = array();

        foreach ($orders_list as $order) {
            $wc_order = wc_get_order($order->ID);
            $wc_date  = $wc_order->get_date_created();
            $time_ago = ceil(( time() - strtotime($wc_date) ) / 60);
            $user_id  = $wc_order->get_user_id();

            $products = $wc_order->get_items();

            $product_id = reset($products)->get_product_id();
            if (get_post_thumbnail_id($product_id)) {
                $thumbnail = wp_get_attachment_image_src(
                    get_post_thumbnail_id($product_id),
                    'single-post-thumbnail'
                )[0];
            } else {
                $thumbnail = General::get_default_image();
            }
            $product_info[] = array(
                'id'        => $product_id,
                'title'     => get_the_title($product_id),
                'url'       => get_permalink($product_id),
                'thumbnail' => $thumbnail,
                'price'     => get_post_meta($product_id, '_price', true),
                'time_ago'  => $time_ago,
                'message'   => self::get_message(
                    $product_id,
                    UserModel::get_field($user_id, 'display_name'),
                    $time_ago,
                    'sale_product'
                )
            );
        }

        return $product_info;
    }

    public static function get_virtual_products()
    {
        $args              = array();
        $args['post_type'] = 'product';
        $args['post__in']  = Settings::get_setting_detail('sale-notice:fields:fake_product');
        $query             = new \WP_Query($args);

        $product_info = array();

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $product_id = get_the_ID();

                if (get_post_thumbnail_id($product_id)) {
                    $thumbnail = wp_get_attachment_image_src(
                        get_post_thumbnail_id($product_id),
                        'single-post-thumbnail'
                    )[0];
                } else {
                    $thumbnail = General::get_default_image();
                }

                $virtual_time_ago = self::get_virtual_time_ago();
                $product_info[]   = array(
                    'id'        => $product_id,
                    'title'     => get_the_title(),
                    'url'       => get_permalink(),
                    'thumbnail' => $thumbnail,
                    'price'     => get_post_meta($product_id, '_price', true),
                    'time_ago'  => $virtual_time_ago,
                    'message'   => self::get_message(
                        $product_id,
                        '',
                        $virtual_time_ago,
                        'virtual_product'
                    )
                );
            }
        }

        wp_reset_postdata();

        return $product_info;
    }
}
