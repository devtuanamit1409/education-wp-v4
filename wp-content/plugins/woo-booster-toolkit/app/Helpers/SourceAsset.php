<?php

namespace WCBT\Helpers;

use WCBT\Helpers\Debug;

/**
 * Class SourceAsset
 * @package WCBT\Helpers
 */
class SourceAsset
{
    protected static $instance;
    protected $min = '.min';
    protected $is_rtl;
    protected $version;

    /**
     * Constructor
     */
    protected function __construct()
    {
        if (Debug::is_debug()) {
            $this->min     = '';
            $this->version = Debug::is_debug() ? uniqid() : WCBT_VERSION;
        }

        $this->is_rtl = is_rtl() ? '-rtl' : '';
    }

    public function get_version()
    {
        return $this->version;
    }

    /**
     * @return SourceAsset
     */
    public static function getInstance(): self
    {
        if (is_null(self::$instance)) {
            self::$instance = new SourceAsset();
        }

        return self::$instance;
    }

    /**
     * @param string $src | type: js, css... (extension file)
     * @param string $file_name | only string name without extension
     * @param string $locate
     *
     * @return string
     */
    public static function get_asset_file_url(string $src = 'css', string $file_name = '', string $locate = 'admin/'): string
    {
        return WCBT_ASSETS_URL . 'dist/' . $src . '/' . $locate . $file_name . self::getInstance()->min . '.' . $src;
    }

    /**
     * Get source file url for backend
     *
     * @param string $src | type: js, css... (extension file)
     * @param string $file_name | only string name without extension
     *
     * @return string
     * @version 1.0.0
     *
     * @since 1.0.0
     */
    public function get_asset_admin_file_url(string $src = 'css', string $file_name = ''): string
    {
        if ($src == 'css') {
            $file_name .= $this->is_rtl;
        }

        return WCBT_ASSETS_URL . "dist/{$src}/admin/{$file_name}{$this->min}.{$src}";
    }

    /**
     * Get source file url for frontend
     *
     * @param string $src | type: js, css... (extension file)
     * @param string $file_name | only string name without extension
     *
     * @return string
     * @version 1.0.0
     *
     * @since 1.0.0
     */
    public function get_asset_frontend_file_url(string $src = 'css', string $file_name = ''): string
    {
        if ($src == 'css') {
            $file_name .= $this->is_rtl;
        }

        return WCBT_ASSETS_URL . "dist/{$src}/frontend/{$file_name}{$this->min}.{$src}";
    }

    /**
     * @param $
     * @param string $file_name
     *
     * @return string
     */
    public function get_asset_js_lib_file_url(string $file_name = ''): string
    {
        return WCBT_ASSETS_URL . "lib/{$file_name}.js";
    }
}
