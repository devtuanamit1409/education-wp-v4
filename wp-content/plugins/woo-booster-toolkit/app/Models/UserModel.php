<?php

namespace WCBT\Models;


/**
 * UserModel
 */
class UserModel
{
    private static $user_data;

    /**
     * @param $user_id
     * @param $field
     *
     * @return int|mixed|string
     */
    public static function get_field($user_id, $field)
    {
        $user_data = self::get_user_data($user_id);

        return $user_data->{$field} ?? '';
    }

    /**
     * @param $user_id
     *
     * @return false|mixed|\WP_User
     */
    public static function get_user_data($user_id)
    {
        if (isset(self::$user_data[ $user_id ])) {
            return self::$user_data[ $user_id ];
        }

        self::$user_data[ $user_id ] = get_userdata($user_id);

        return self::$user_data[ $user_id ];
    }
}
