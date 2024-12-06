<?php

namespace WCBT\Register;

use WCBT\Helpers\Settings;
use WCBT\Helpers\Config;
use WCBT\Helpers\Template;
use WCBT\Helpers\Validation;

/**
 * Class Setting
 * @package WCBT\Register
 */
class Setting
{
    /**
     * @var array|mixed
     */
    public $config = array();
    /**
     * @var array|false|mixed|void|null
     */
    public $data = array();

    public function __construct()
    {
        $this->config = Config::instance()->get('wcbt-setting');
        $this->data   = Settings::get_all_settings();
        add_action('admin_init', array( $this, 'register_settings' ));
        add_action('admin_menu', array( $this, 'register' ));
        add_action('admin_init', array( $this, 'save_settings' ));
    }

    public function register_settings()
    {
    }

    /**
     * @return void
     */
    public function register()
    {
        add_menu_page(
            $this->config['setting']['title'],
            $this->config['setting']['title'],
            'manage_options',
            $this->config['setting']['slug'],
            array( $this, 'show_settings' )
        );
//      remove_submenu_page( 'wcbt-setting', 'wcbt-setting' );
    }


    /**
     * @return void
     */
    public function show_settings()
    {
        $config = $this->config;
        $data   = $this->data;
        Template::instance()->get_admin_template('settings', compact('config', 'data'));
    }

    /**
     * Save config
     *
     * @return void
     */
    public function save_settings()
    {
        $nonce = Validation::sanitize_params_submitted($_POST['wcbt-option-setting-name'] ?? '');
        if (! wp_verify_nonce($nonce, 'wcbt-option-setting-action')) {
            return;
        }

        $data = $this->data;

        foreach ($data as $name => $value) {
            $field = Config::instance()->get('wcbt-setting:' . $name);
            $key   = Validation::sanitize_params_submitted(isset($_POST[ WCBT_OPTION_KEY ][ $name ]));
            if ($key) {
                $sanitize      = $field['sanitize'] ?? 'text';
                $data[ $name ] = Validation::sanitize_params_submitted($_POST[ WCBT_OPTION_KEY ][ $name ], $sanitize);
            }
        }

        update_option(WCBT_OPTION_KEY, $data);

		do_action('wcbt/option-setting/update/after', $data);
        wp_redirect(Validation::sanitize_params_submitted($_SERVER['HTTP_REFERER']));
    }
}
