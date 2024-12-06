<?php
/**
 * Plugin Name: LearnPress - Live Course Add-on
 * Plugin URI: http://thimpress.com/learnpress
 * Description: Manage conferences related to the course for user.
 * Author: ThimPress
 * Version: 4.0.3
 * Author URI: http://thimpress.com
 * Tags: learnpress, lms
 * Text Domain: learnpress-live
 * Domain Path: /languages/
 * Require_LP_Version: 4.2.6
 * Requires at least: 6.3
 * Requires PHP: 7.4
 *
 * @package learnpress-live
 */

defined( 'ABSPATH' ) || exit;

define( 'LP_ADDON_LIVE_PLUGIN_PATH', dirname( __FILE__ ) );
const LP_ADDON_LIVE_PLUGIN_FILE = __FILE__;
define( 'LP_ADDON_LIVE_PLUGIN_URL', plugins_url( '', LP_ADDON_LIVE_PLUGIN_FILE ) );

/**
 * Class LP_Addon_Live_Preload
 */
class LP_Addon_Live_Preload {
	/**
	 * @var LP_Addon_Live
	 */
	public static $addon;
	/**
	 * @var array|string[]
	 */
	public static $addon_info = array();

	/**
	 * @return LP_Addon_Live_Preload
	 */
	public static function instance(): LP_Addon_Live_Preload {
		static $instance;
		if ( is_null( $instance ) ) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * LP_Addon_Live_Preload constructor.
	 */
	protected function __construct() {
		$can_load = true;
		// Set Base name plugin.
		define( 'LP_ADDON_LIVE_BASENAME', plugin_basename( LP_ADDON_LIVE_PLUGIN_FILE ) );

		// Set version addon for LP check .
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		self::$addon_info = get_file_data(
			LP_ADDON_LIVE_PLUGIN_FILE,
			array(
				'Name'               => 'Plugin Name',
				'Require_LP_Version' => 'Require_LP_Version',
				'Version'            => 'Version',
			)
		);

		define( 'LP_ADDON_LIVE_VER', self::$addon_info['Version'] );
		define( 'LP_ADDON_LIVE_REQUIRE_VER', self::$addon_info['Require_LP_Version'] );
		// Check LP activated .
		if ( ! is_plugin_active( 'learnpress/learnpress.php' ) ) {
			$can_load = false;
		} elseif ( version_compare( LP_ADDON_LIVE_REQUIRE_VER, get_option( 'learnpress_version', '3.0.0' ), '>' ) ) {
			$can_load = false;
		}

		if ( ! $can_load ) {
			add_action( 'admin_notices', array( $this, 'show_note_errors_require_lp' ) );
			deactivate_plugins( LP_ADDON_LIVE_BASENAME );

			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}

			return;
		}

		// Sure LP loaded.
		add_action( 'learn-press/ready', array( $this, 'load' ) );

		// install tables
		require_once LP_ADDON_LIVE_PLUGIN_PATH . '/incs/types/zooms/class-lp-live-tables.php';
		$db_live = LP_Live_Database::instance();
		register_activation_hook( LP_ADDON_LIVE_PLUGIN_FILE, array( $db_live, 'create_tables' ) );
	}


	/**
	 * Load addon
	 */
	public function load() {
		LP_Addon_Live_Preload::$addon = LP_Addon::load( 'LP_Addon_Live', 'incs/load.php', __FILE__ );
	}

	public function show_note_errors_require_lp() {
		?>
		<div class="notice notice-error">
			<p><?php echo( 'Please active <strong>LP version ' . LP_ADDON_LIVE_REQUIRE_VER . ' or later</strong> before active <strong>' . self::$addon_info['Name'] . '</strong>' ); ?></p>
		</div>
		<?php
	}
}

LP_Addon_Live_Preload::instance();
