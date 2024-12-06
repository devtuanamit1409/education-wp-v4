<?php
/**
 * Plugin Name: LearnPress - WPML
 * Plugin URI: http://thimpress.com/learnpress
 * Description: Support multi languages with WPML for Learnpress LMS system.
 * Author: ThimPress
 * Version: 4.0.3
 * Author URI: http://thimpress.com
 * Tags: learnpress, lms, add-on, wpml
 * Text Domain: learnpress-wpml
 * Domain Path: /languages/
 * Require_LP_Version: 4.2.7-beta.0
 * Requires at least: 6.3
 * Requires PHP: 7.4
 */

defined( 'ABSPATH' ) || exit;

define( 'LP_ADDON_WPML_PLUGIN_PATH', dirname( __FILE__ ) );
const LP_ADDON_WPML_PLUGIN_FILE = __FILE__;
const LP_WPML_COURSE_CPT        = 'post_lp_course';
/**
 * Class LP_Addon_WPML_Preload
 */
class LP_Addon_WPML_Preload {
	/**
	 * @var string[]
	 */
	public static $addon_info = array();

	public function __construct() {
		$can_load    = true;
		$miss_plugin = '';
		// Set Base name plugin.
		define( 'LP_ADDON_WPML_BASENAME', plugin_basename( LP_ADDON_WPML_PLUGIN_FILE ) );

		// Set version addon for LP check .
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		self::$addon_info = get_file_data(
			LP_ADDON_WPML_PLUGIN_FILE,
			array(
				'Name'               => 'Plugin Name',
				'Require_LP_Version' => 'Require_LP_Version',
				'Version'            => 'Version',
			)
		);

		define( 'LP_ADDON_WPML_VER', self::$addon_info['Version'] );
		define( 'LP_ADDON_WPML_REQUIRE_VER', self::$addon_info['Require_LP_Version'] );

		// Check LP activated .
		if ( ! is_plugin_active( 'learnpress/learnpress.php' ) ) {
			$can_load = false;
		} elseif ( version_compare( LP_ADDON_WPML_REQUIRE_VER, get_option( 'learnpress_version', '3.0.0' ), '>' ) ) {
			$can_load = false;
		} elseif ( ! is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
			$can_load    = false;
			$miss_plugin = 'wpml';
		}

		if ( ! $can_load ) {
			if ( 'wpml' === $miss_plugin ) {
				add_action( 'admin_notices', array( $this, 'show_note_errors_require_wpml' ) );
			} else {
				add_action( 'admin_notices', array( $this, 'show_note_errors_require_lp' ) );
			}

			deactivate_plugins( LP_ADDON_WPML_BASENAME );

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
		global $lp_addon_wpml;
		$lp_addon_wpml = LP_Addon::load( 'LP_Addon_WPML', 'inc/load.php', __FILE__ );
	}

	/**
	 * Show message must activate LP right version
	 *
	 * @return void
	 */
	public function show_note_errors_require_lp() {
		?>
		<div class="notice notice-error">
			<p><?php echo( 'Please active <strong>LP version ' . LP_ADDON_WPML_REQUIRE_VER . ' or later</strong> before active <strong>' . self::$addon_info['Name'] . '</strong>' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Show message must activate wpml
	 *
	 * @return void
	 */
	public function show_note_errors_require_wpml() {
		?>
		<div class="notice notice-error">
			<p>
				<?php
				echo sprintf(
					'<strong>LearnPress - WPML</strong> addon requires %s plugin is <strong>activated</strong>.',
					'<a href="https://wpml.org/" target="_blank">WPML Multilingual CMS</a>'
				);
				?>
			</p>
		</div>
		<?php
	}
}

new LP_Addon_WPML_Preload();
