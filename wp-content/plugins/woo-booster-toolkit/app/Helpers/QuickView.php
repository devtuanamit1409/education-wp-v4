<?php

namespace WCBT\Helpers;

class QuickView
{
    /**
     * @return array|false|\stdClass|string
     */
    public static function get_quick_view_tooltip_text()
    {
        $enable = Settings::get_setting_detail('quick-view:fields:tooltip_enable');

        if ($enable === 'on') {
            return Settings::get_setting_detail('quick-view:fields:tooltip_text');
        }

        return '';
    }
}
