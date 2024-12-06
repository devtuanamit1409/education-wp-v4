<?php

namespace WCBT\Controllers;

use WCBT\Helpers\Template;
use WCBT\Helpers\MaxSale;
use WCBT\Helpers\Cookie;

class MaxSalePopUpController
{
    private $data;

    public function __construct()
    {
        add_action('init', array($this, 'set_data'));
        add_action('wp_footer', array( $this, 'add_maxsale_popup' ));
        add_action('wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ));
        add_action('init', array( $this, 'set_cookie' ));
    }

    /**
     * @return void
     */
    public function set_data()
    {
        $this->data = MaxSale::get_data();
    }

    public function set_cookie()
    {
        $data = $this->data;
        if ($data['repeat_open'] === 'on') {
            if (empty($data['repeat_open_time'])) {
                Cookie::delete_cookie('wcbt_maxsale');
            }
        }
    }

    public function add_maxsale_popup()
    {
        $data = $this->data;

        if ($data['mode'] !== 'on') {
            return;
        }

        if ($data['display_on'] === 'homepage_only' && ! is_front_page()) {
            return;
        }

        if (isset($_GET['aaa'])) {
            var_dump($_COOKIE);
        }
        if (isset($_COOKIE['wcbt_maxsale']) && $_COOKIE['wcbt_maxsale'] === 'close') {
            return;
        }

        Template::instance()->get_frontend_template_type_classic('maxsale/popup.php', compact('data'));
    }

    public function wp_enqueue_scripts()
    {
        $data = $this->data;
        if ($data['mode'] !== 'on') {
            return;
        }


        if ($data['display_on'] === 'homepage_only' && ! is_front_page()) {
            return;
        }

        wp_localize_script('wcbt-global', 'WCBT_MAXSALE_OBJECT', $data);
    }
}
