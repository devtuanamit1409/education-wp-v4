<?php

namespace WCBT\Helpers;

class MaxSale
{
    public static function get_data()
    {
        $attachment_id = Settings::get_setting_detail('maxsale-popup:fields:image');
        $data    = array(
            'mode' => Settings::get_setting_detail('maxsale-popup:fields:enable'),
//            'goal' => Settings::get_setting_detail('maxsale-popup:fields:goal'),
            'title' => Settings::get_setting_detail('maxsale-popup:fields:title'),
            'description'=> Settings::get_setting_detail('maxsale-popup:fields:description'),
//            'button' =>Settings::get_setting_detail('maxsale-popup:fields:button'),
//            'email_placeholder'=>Settings::get_setting_detail('maxsale-popup:fields:email_placeholder'),
            'background_color'=>Settings::get_setting_detail('maxsale-popup:fields:background_color'),
            'text_color'=>Settings::get_setting_detail('maxsale-popup:fields:text_color'),
            'button_text_color'=>Settings::get_setting_detail('maxsale-popup:fields:button_text_color'),
//            'button_background_color'=>Settings::get_setting_detail('maxsale-popup:fields:button_background_color'),
            'image'=> wp_get_attachment_url($attachment_id),
            'display_on'=>Settings::get_setting_detail('maxsale-popup:fields:display_on'),
            'repeat_open'=>Settings::get_setting_detail('maxsale-popup:fields:repeat_open'),
            'repeat_open_time'=>Settings::get_setting_detail('maxsale-popup:fields:repeat_open_time'),
        );

        return apply_filters('wcbt/filter/maxsale/data', $data);
    }
}
