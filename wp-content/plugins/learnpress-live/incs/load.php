<?php

/**
 * Class LP_Addon_Live
 */
class LP_Addon_Live extends LP_Addon {
	/**
	 * @var string
	 */
	public $version = LP_ADDON_LIVE_VER;

	/**
	 * @var string
	 */
	public $require_version = LP_ADDON_LIVE_REQUIRE_VER;

	/**
	 * @var string
	 */
	public $plugin_file = LP_ADDON_LIVE_PLUGIN_FILE;

	/**
	 * @var LP_Addon_Live|null
	 *
	 * Hold the singleton of LP_Addon_Live_Preload object
	 */
	protected static $_instance = null;

	/**
	 * LP_Addon_Live_Preload constructor.
	 */

	public function __construct() {
		parent::__construct();
		$this->includes();
	}

	/**
	 * Include files needed
	 */
	protected function includes() {
		/* zooms */
		//meta box
		require_once LP_PLUGIN_PATH . 'inc/admin/views/meta-boxes/fields/class-lp-meta-box-fields.php';
		require_once LP_ADDON_LIVE_PLUGIN_PATH . '/incs/types/zooms/class-lp-metabox-zooms.php';
		//shortcodes
		require_once LP_ADDON_LIVE_PLUGIN_PATH . '/incs/types/zooms/class-lp-shortcode-zoom-meeting.php';
		//api
		require_once LP_ADDON_LIVE_PLUGIN_PATH . '/incs/types/zooms/class-lp-zoom-api.php';
		//zoom auth
		require_once LP_ADDON_LIVE_PLUGIN_PATH . '/incs/types/zooms/class-lp-zoom-auth.php';

		/* google meeting */
		//api
		require_once LP_ADDON_LIVE_PLUGIN_PATH . '/incs/types/google-meet/class-lp-google-meet-api.php';
		//auth
		require_once LP_ADDON_LIVE_PLUGIN_PATH . '/incs/types/google-meet/class-lp-google-auth.php';
		//metabox
		require_once LP_ADDON_LIVE_PLUGIN_PATH . '/incs/types/google-meet/class-lp-metabox-google.php';
		//shortcodes
		require_once LP_ADDON_LIVE_PLUGIN_PATH . '/incs/types/google-meet/class-lp-shortcode-google-meeting.php';

		// Hooks
		require_once LP_ADDON_LIVE_PLUGIN_PATH . '/incs/class-lp-live-hooks.php';
		LP_Live_Hooks::instance();
	}

	/**
	 * Check is page Live setting.
	 *
	 * @return bool
	 */
	public function is_page_live_setting(): bool {
		global $wp;

		return isset( $wp->query_vars ) && array_key_exists( 'live-setting', $wp->query_vars );
	}

	/**
	 * Get link show live setting.
	 *
	 * @return string
	 */
	public function get_slug_page(): string {
		return apply_filters( 'learnpress_live_setting_get_slug', 'live-setting' );
	}

	/**
	 * Get link show live setting.
	 *
	 * @return string
	 */
	public function url_page_setting(): string {
		return trailingslashit( site_url( $this->get_slug_page() ) );
	}
}
