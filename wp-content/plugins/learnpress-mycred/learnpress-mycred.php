<?php
/**
 * Plugin Name: LearnPress - myCRED Integration
 * Plugin URI: http://thimpress.com/learnpress
 * Description: Running with the point management system - myCRED.
 * Author: ThimPress
 * Version: 4.0.1
 * Author URI: http://thimpress.com
 * Tags: learnpress, lms, add-on, myCRED
 * Text Domain: learnpress-mycred
 * Domain Path: /languages/
 * Require_LP_Version: 4.1.6
 *
 * @package learnpress-mycred
 */

defined( 'ABSPATH' ) || exit;

const LP_ADDON_MYCRED_FILE = __FILE__;

/**
 * Class LP_Addon_MyCred_Preload
 */
class LP_Addon_MyCred_Preload {
	public static $addon_info = array();

	/**
	 * LP_Addon_MyCred_Preload constructor.
	 */
	public function __construct() {
		$can_load = true;
		// Set Base name plugin.
		define( 'LP_ADDON_MYCRED_BASENAME', plugin_basename( LP_ADDON_MYCRED_FILE ) );

		// Set version addon for LP check .
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		self::$addon_info = get_file_data(
			LP_ADDON_MYCRED_FILE,
			array(
				'Name'               => 'Plugin Name',
				'Require_LP_Version' => 'Require_LP_Version',
				'Version'            => 'Version',
			)
		);

		define( 'LP_ADDON_MYCRED_VER', self::$addon_info['Version'] );
		define( 'LP_ADDON_MYCRED_REQUIRE_VER', self::$addon_info['Require_LP_Version'] );

		// Check LP activated .
		if ( ! is_plugin_active( 'learnpress/learnpress.php' ) ) {
			$can_load = false;
		} elseif ( version_compare( LP_ADDON_MYCRED_REQUIRE_VER, get_option( 'learnpress_version', '3.0.0' ), '>' ) ) {
			$can_load = false;
		}

		if ( ! $can_load ) {
			add_action( 'admin_notices', array( $this, 'show_note_errors_require_lp' ) );
			deactivate_plugins( LP_ADDON_MYCRED_BASENAME );

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
		LP_Addon::load( 'LP_Addon_myCRED', 'inc/load.php', __FILE__ );
	}

	public function show_note_errors_require_lp() {
		?>
		<div class="notice notice-error">
			<p><?php echo( 'Please active <strong>LearnPress version ' . LP_ADDON_MYCRED_REQUIRE_VER . ' or later</strong> before active <strong>' . self::$addon_info['Name'] . '</strong>' ); ?></p>
		</div>
		<?php
	}
}

new LP_Addon_MyCred_Preload();
