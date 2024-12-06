<?php
/**
 * Plugin Name: LearnPress - Content Drip
 * Plugin URI: http://thimpress.com/learnpress
 * Description: Drip content of course.
 * Author: ThimPress
 * Version: 4.0.7
 * Author URI: http://thimpress.com
 * Tags: learnpress, lms, drip, filter
 * Text Domain: learnpress-content-drip
 * Domain Path: /languages/
 * Require_LP_Version: 4.2.7-beta.0
 *
 * @package learnpress-content-drip
 */

defined( 'ABSPATH' ) || exit;

const LP_ADDON_CONTENT_DRIP_FILE = __FILE__;
define( 'LP_ADDON_CONTENT_DRIP_PATH', dirname( __FILE__ ) );
const LP_ADDON_CONTENT_DRIP_INC_PATH = LP_ADDON_CONTENT_DRIP_PATH . '/inc/';

/**
 * Class LP_Addon_Content_Drip_Preload
 */
class LP_Addon_Content_Drip_Preload {
	/**
	 * @var array
	 */
	public static $addon_info = array();
	/**
	 * @var LP_Addon_Content_Drip $addon
	 */
	public static $addon;

	/**
	 * Singleton.
	 *
	 * @return LP_Addon_Content_Drip_Preload|mixed
	 */
	public static function instance() {
		static $instance;
		if ( is_null( $instance ) ) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * LP_Addon_Content_Drip_Preload constructor.
	 */
	protected function __construct() {
		$can_load = true;
		// Set Base name plugin.
		define( 'LP_ADDON_CONTENT_DRIP_BASENAME', plugin_basename( LP_ADDON_CONTENT_DRIP_FILE ) );

		// Set version addon for LP check .
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		self::$addon_info = get_file_data(
			LP_ADDON_CONTENT_DRIP_FILE,
			array(
				'Name'               => 'Plugin Name',
				'Require_LP_Version' => 'Require_LP_Version',
				'Version'            => 'Version',
			)
		);

		define( 'LP_ADDON_CONTENT_DRIP_VER', self::$addon_info['Version'] );
		define( 'LP_ADDON_CONTENT_DRIP_REQUIRE_VER', self::$addon_info['Require_LP_Version'] );

		// Check LP activated .
		if ( ! is_plugin_active( 'learnpress/learnpress.php' ) ) {
			$can_load = false;
		} elseif ( version_compare( LP_ADDON_CONTENT_DRIP_REQUIRE_VER, get_option( 'learnpress_version', '3.0.0' ), '>' ) ) {
			$can_load = false;
		}

		if ( ! $can_load ) {
			add_action( 'admin_notices', array( $this, 'show_note_errors_require_lp' ) );
			deactivate_plugins( LP_ADDON_CONTENT_DRIP_BASENAME );

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
		self::$addon = LP_Addon::load( 'LP_Addon_Content_Drip', 'inc/load.php', __FILE__ );
	}

	public function show_note_errors_require_lp() {
		?>
		<div class="notice notice-error">
			<p><?php echo( 'Please active <strong>LP version ' . LP_ADDON_CONTENT_DRIP_REQUIRE_VER . ' or later</strong> before active <strong>' . self::$addon_info['Name'] . '</strong>' ); ?></p>
		</div>
		<?php
	}
}

LP_Addon_Content_Drip_Preload::instance();
