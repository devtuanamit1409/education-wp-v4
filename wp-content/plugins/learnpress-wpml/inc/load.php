<?php

/**
 * Class LP_Addon_WPML
 *
 * @since 4.0.0
 * @author Minhpd
 */
class LP_Addon_WPML extends LP_Addon {
	/**
	 * Version
	 *
	 * @var string
	 */
	public $version = LP_ADDON_WPML_VER;

	/**
	 * LP require version
	 *
	 * @var null|string
	 */
	public $require_version = LP_ADDON_WPML_REQUIRE_VER;

	/**
	 * Path file addon
	 *
	 * @var null|string
	 */
	public $plugin_file = LP_ADDON_WPML_PLUGIN_FILE;

	/**
	 * LP_Addon_WPML constructor.
	 */
	public function __construct() {
		parent::__construct();
	}

	protected function _includes() {
		$default_lang = apply_filters( 'wpml_default_language', null );
		// If not setup wizard WPML return
		if ( empty ( $default_lang ) ) {
			return;
		}

		if ( is_admin() ) {
			require_once LP_ADDON_WPML_PLUGIN_PATH . '/inc/admin/class-lp-wpml-settings-general.php';
		}

		require_once 'background-process/class-lp-wpml-bg-translate.php';

		require_once LP_ADDON_WPML_PLUGIN_PATH . '/inc/filters/class-lp-wpml-filter.php';
		require_once LP_ADDON_WPML_PLUGIN_PATH . '/inc/databases/class-lp-wpml-db.php';
		require_once LP_ADDON_WPML_PLUGIN_PATH . '/inc/hooks/class-lp-wpml-hooks.php';
	}
}
