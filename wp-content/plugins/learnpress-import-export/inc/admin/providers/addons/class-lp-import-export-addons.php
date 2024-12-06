<?php
/**
 * Import Export Addon class.
 *
 * @author   ThimPress
 * @package  LearnPress/Import-Export/Classes
 * @version  4.0.0
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LP_Import_Export_Addons' ) ) {
	/**
	 * Class LP_Import_Export_Addons.
	 */
	class LP_Import_Export_Addons {

		public function __construct() {

			// addon assignment
			if ( is_plugin_active( 'learnpress-assignments/learnpress-assignments.php' ) ) {
				include_once LP_ADDON_IMPORT_EXPORT_INC . 'admin/providers/addons/assignment/class-lp-assignment-import-export.php';
			}
			// addon h5p
			if ( is_plugin_active( 'learnpress-h5p/learnpress-h5p.php' ) ) {
				//include_once LP_ADDON_IMPORT_EXPORT_INC . 'admin/providers/addons/h5p/class-lp-h5p-import-export.php';
			}
		}
	}
}

return new LP_Import_Export_Addons();
