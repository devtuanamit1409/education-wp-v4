<?php

namespace WCBT\Helpers;

class Media
{
    /**
     * @param $attachment_id
     *
     * @return string
     */
    public static function get_image_alt($attachment_id)
    {
        if (! $attachment_id) {
            return '';
        }

        $attachment = get_post($attachment_id);
        if (! $attachment) {
            return '';
        }

        $alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
        if (! $alt) {
            $alt = $attachment->post_excerpt;
            if (! $alt) {
                $alt = $attachment->post_title;
            }
        }

        return trim(strip_tags($alt));
    }
}
