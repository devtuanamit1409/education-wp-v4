<?php
/**
 * Plugin Name: LearnPress - Export/Import Courses
 * Plugin URI: http://thimpress.com/learnpress
 * Description: Export and Import your courses with all lesson and quiz in easiest way.
 * Author: ThimPress
 * Version: 4.0.5
 * Author URI: http://thimpress.com
 * Tags: learnpress, lms, add-on, prerequisites courses
 * Text Domain: learnpress-import-export
 * Domain Path: /languages/
 * Require_LP_Version: 4.2.7
 *
 * @package learnpress-import-export
 */

defined( 'ABSPATH' ) || exit;

const LP_ADDON_IMPORT_EXPORT_FILE = __FILE__;

/**
 * Class LP_Addon_Import_Export_Preload
 */
class LP_Addon_Import_Export_Preload {
	/**
	 * @var string[] Addon info
	 */
	public static $addon_info = array();
	/**
	 * @var LP_Addon_Import_Export $addon
	 */
	public static $addon;

	/**
	 * Singleton.
	 *
	 * @return LP_Addon_Import_Export_Preload
	 */
	public static function instance(): LP_Addon_Import_Export_Preload {
		static $instance;
		if ( is_null( $instance ) ) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * LP_Addon_Import_Export_Preload constructor.
	 */
	protected function __construct() {
		$can_load = true;
		// Set Base name plugin.
		define( 'LP_ADDON_IMPORT_EXPORT_BASENAME', plugin_basename( LP_ADDON_IMPORT_EXPORT_FILE ) );

		// Set version addon for LP check .
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		self::$addon_info = get_file_data(
			LP_ADDON_IMPORT_EXPORT_FILE,
			array(
				'Name'               => 'Plugin Name',
				'Require_LP_Version' => 'Require_LP_Version',
				'Version'            => 'Version',
			)
		);

		define( 'LP_ADDON_IMPORT_EXPORT_VER', self::$addon_info['Version'] );
		define( 'LP_ADDON_IMPORT_EXPORT_REQUIRE_VER', self::$addon_info['Require_LP_Version'] );

		// Check LP activated .
		if ( ! is_plugin_active( 'learnpress/learnpress.php' ) ) {
			$can_load = false;
		} elseif ( version_compare( LP_ADDON_IMPORT_EXPORT_REQUIRE_VER, get_option( 'learnpress_version', '3.0.0' ), '>' ) ) {
			$can_load = false;
		}

		if ( ! $can_load ) {
			add_action( 'admin_notices', array( $this, 'show_note_errors_require_lp' ) );
			deactivate_plugins( LP_ADDON_IMPORT_EXPORT_BASENAME );

			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}

			return;
		}

		// Sure LP loaded.
		add_action( 'learn-press/ready', array( $this, 'load' ) );
	}

	/**
	 * Load addon
	 */
	public function load() {
		self::$addon = LP_Addon::load( 'LP_Addon_Import_Export', 'inc/load.php', __FILE__ );
	}

	public function show_note_errors_require_lp() {
		?>
		<div class="notice notice-error">
			<p><?php echo( 'Please active <strong>LearnPress version ' . LP_ADDON_IMPORT_EXPORT_REQUIRE_VER . ' or later</strong> before active <strong>' . self::$addon_info['Name'] . '</strong>' ); ?></p>
		</div>
		<?php
	}
}

LP_Addon_Import_Export_Preload::instance();
