<?php

namespace WCBT\Controllers;

use WCBT\Helpers\Template;
use WCBT\Helpers\SaleNotice;

class SaleNoticeController
{
    private $data;

    public function __construct()
    {
        add_action('init', array($this, 'set_data'));
        add_action('wp_footer', array( $this, 'add_sale_notice' ));
        add_action('wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ));
    }

    public function set_data()
    {
        $this->data = SaleNotice::get_data();
    }

    public function add_sale_notice()
    {
        $data = $this->data;

        if ($data['mode'] !== 'on') {
            return;
        }

        Template::instance()->get_frontend_template_type_classic('sale-notice/popup.php', compact('data'));
    }

    /**
     * @return void
     */
    public function wp_enqueue_scripts()
    {
        $data = $this->data;
        if ($data['mode'] !== 'on') {
            return;
        }

        $custom_css = sprintf(
            '
	       body #toast-container > .toast-success{
	            background-color:%s;
	       }
	    ',
            $data["background_color"]
        );

        wp_add_inline_style('wcbt-global', $custom_css);
        wp_localize_script('wcbt-global', 'WCBT_SALE_NOTICE_OBJECT', $data);
    }
}
