<?php
/**
 * Plugin Name: LearnPress - Gradebook
 * Plugin URI: http://thimpress.com/learnpress
 * Description: Manage gradebook for user.
 * Author: ThimPress
 * Version: 4.0.7
 * Author URI: http://thimpress.com
 * Tags: learnpress, lms, gradebook
 * Text Domain: learnpress-gradebook
 * Domain Path: /languages/
 * Require_LP_Version: 4.2.6.6
 *
 * @package learnpress-gradebook
 */

defined( 'ABSPATH' ) || exit;

define( 'LP_ADDON_GRADEBOOK_PLUGIN_PATH', dirname( __FILE__ ) );
const LP_ADDON_GRADEBOOK_PLUGIN_FILE = __FILE__;

/**
 * Class LP_Addon_Gradebook_Preload
 */
class LP_Addon_Gradebook_Preload {
	/**
	 * @var array|string[]
	 */
	public static $addon_info = array();

	/**
	 * LP_Addon_Gradebook_Preload constructor.
	 */
	public function __construct() {
		$can_load = true;
		// Set Base name plugin.
		define( 'LP_ADDON_GRADEBOOK_BASENAME', plugin_basename( LP_ADDON_GRADEBOOK_PLUGIN_FILE ) );

		// Set version addon for LP check .
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		self::$addon_info = get_file_data(
			LP_ADDON_GRADEBOOK_PLUGIN_FILE,
			array(
				'Name'               => 'Plugin Name',
				'Require_LP_Version' => 'Require_LP_Version',
				'Version'            => 'Version',
			)
		);

		define( 'LP_ADDON_GRADEBOOK_VER', self::$addon_info['Version'] );
		define( 'LP_ADDON_GRADEBOOK_REQUIRE_VER', self::$addon_info['Require_LP_Version'] );

		// Check LP activated .
		if ( ! is_plugin_active( 'learnpress/learnpress.php' ) ) {
			$can_load = false;
		} elseif ( version_compare( LP_ADDON_GRADEBOOK_REQUIRE_VER, get_option( 'learnpress_version', '3.0.0' ), '>' ) ) {
			$can_load = false;
		}

		if ( ! $can_load ) {
			add_action( 'admin_notices', array( $this, 'show_note_errors_require_lp' ) );
			deactivate_plugins( LP_ADDON_GRADEBOOK_BASENAME );

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
		LP_Addon::load( 'LP_Addon_Gradebook', 'inc/load.php', __FILE__ );
	}

	public function show_note_errors_require_lp() {
		?>
		<div class="notice notice-error">
			<p><?php echo( 'Please active <strong>LP version ' . LP_ADDON_GRADEBOOK_REQUIRE_VER . ' or later</strong> before active <strong>' . self::$addon_info['Name'] . '</strong>' ); ?></p>
		</div>
		<?php
	}
}

new LP_Addon_Gradebook_Preload();
