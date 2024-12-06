<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// a helper function to lookup "env_FILE", "env", then fallback, standard WP function, needed for compatibility with WP 3
if (!function_exists('getenv_docker')) {
	function getenv_docker($env, $default) {
		if ($fileEnv = getenv($env . '_FILE')) {
			return rtrim(file_get_contents($fileEnv), "\r\n"); // @codingStandardsIgnoreLine
		}
		else if (($val = getenv($env)) !== false) {
			return $val;
		}
		else {
			return $default;
		}
	}
}
// a helper function for development plugin
if (!function_exists('widget_logic_getServiceVersion')) {
	function widget_logic_getServiceVersion() {
        $ver = getenv_docker('WORDPRESS_SERVICE_WGL_VER', 'v2');
        return $ver ? "/{$ver}" : $ver;
    }
}


return array(
    'ver' => widget_logic_getServiceVersion(),
    'base' => getenv_docker('WORDPRESS_PLUGIN_WGL_BASE_URL', 'https://widgetlogic.org')
);
