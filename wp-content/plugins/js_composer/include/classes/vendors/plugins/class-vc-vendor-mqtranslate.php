<?php
/**
 * Backward compatibility with "mqtranslate" WordPress plugin.
 *
 * @since 4.4 vendors initialization moved to hooks in autoload/vendors.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once vc_path_dir( 'VENDORS_DIR', 'plugins/class-vc-vendor-qtranslate.php' );

/**
 * Class Vc_Vendor_Mqtranslate extends class Vc_Vendor_Qtranslate::__construct
 *
 * @since 4.3
 */
class Vc_Vendor_Mqtranslate extends Vc_Vendor_Qtranslate {

	/**
	 * Set languages.
	 *
	 * @since 4.3
	 */
	public function setLanguages() {
		global $q_config;
		$languages = get_option( 'mqtranslate_enabled_languages' );
		if ( ! is_array( $languages ) ) {
			$languages = $q_config['enabled_languages'];
		}
		$this->languages = $languages;
	}

	/**
	 * Set default language.
	 *
	 * @since 4.3
	 */
	public function qtransSwitch() {
		global $q_config;
		$q_config['js']['qtrans_save'] .= '
			var mqtranslate = true;
		';
	}
}
