<?php
/**
 * Plugin Name: LearnPress - Paid Membership Pro Integration
 * Plugin URI: http://thimpress.com/learnpress
 * Description: Paid Membership Pro add-on for LearnPress.
 * Author: ThimPress
 * Version: 4.0.6
 * Author URI: http://thimpress.com
 * Tags: learnpress, lms
 * Text Domain: learnpress-paid-membership-pro
 * Domain Path: /languages/
 * Require_LP_Version: 4.2.7.1-beta.0
 * Requires Plugins: paid-memberships-pro
 *
 * @package learnpress-paid-membership
 */

defined( 'ABSPATH' ) || exit;

define( 'LP_ADDON_PMPRO_PATH', dirname( __FILE__ ) );
define( 'LP_ADDON_PMPRO_BASE_NAME', plugin_basename( __FILE__ ) );
const LP_ADDON_PMPRO_FILE = __FILE__;
define( 'LP_ADDON_PMPRO_URL', plugin_dir_url( __FILE__ ) );
const LP_ADDON_PMPRO_TEMP = LP_ADDON_PMPRO_PATH . DIRECTORY_SEPARATOR . 'templates';
define(
	'GMC_PHYS_PATH',
	trailingslashit( WP_PLUGIN_DIR . '/' . str_replace( basename( __FILE__ ), '', plugin_basename( __FILE__ ) ) )
);

/**
 * Class LP_Addon_Paid_Memberships_Pro_Preload
 */
class LP_Addon_Paid_Memberships_Pro_Preload {
	/**
	 * @var array
	 */
	public static $addon_info = array();
	/**
	 * @var LP_Addon_Paid_Memberships_Pro $addon
	 */
	public static $addon;

	/**
	 * Singleton.
	 *
	 * @return LP_Addon_Course_Review_Preload|mixed
	 */
	public static function instance() {
		static $instance;
		if ( is_null( $instance ) ) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * LP_Addon_Paid_Memberships_Pro_Preload constructor.
	 */
	protected function __construct() {
		$can_load = true;
		// Set version addon for LP check .
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		self::$addon_info = get_file_data(
			LP_ADDON_PMPRO_FILE,
			array(
				'Name'               => 'Plugin Name',
				'Require_LP_Version' => 'Require_LP_Version',
				'Version'            => 'Version',
			)
		);

		define( 'LP_ADDON_PMPRO_VER', self::$addon_info['Version'] );
		define( 'LP_ADDON_PMPRO_REQUIRE_VER', self::$addon_info['Require_LP_Version'] );

		// Check LP activated .
		if ( ! is_plugin_active( 'learnpress/learnpress.php' ) ) {
			$can_load = false;
		} elseif ( version_compare( LP_ADDON_PMPRO_REQUIRE_VER, get_option( 'learnpress_version', '3.0.0' ), '>' ) ) {
			$can_load = false;
		}

		if ( ! $can_load ) {
			add_action( 'admin_notices', array( $this, 'show_note_errors_require_lp' ) );
			deactivate_plugins( LP_ADDON_PMPRO_BASE_NAME );

			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}

			return;
		}

		// Check PMS activated .
		if ( ! $this->check_pms_activated() ) {
			return;
		}

		// Sure LP loaded.
		add_action( 'learn-press/ready', array( $this, 'load' ) );
	}

	/**
	 * Load addon
	 */
	public function load() {
		self::$addon = LP_Addon::load( 'LP_Addon_Paid_Memberships_Pro', 'inc/load.php', __FILE__ );

		include 'inc/classes/class-lp-pms-db.php';
		include 'inc/classes/class-lp-pms-ajax.php';
		include 'inc/classes/class-lp-pms-woo.php';
	}

	/**
	 * Check plugin Woo activated.
	 */
	public function check_pms_activated(): bool {
		if ( ! is_plugin_active( 'paid-memberships-pro/paid-memberships-pro.php' ) ) {
			add_action( 'admin_notices', array( $this, 'show_note_errors_install_plugin_pms' ) );

			deactivate_plugins( LP_ADDON_PMPRO_BASE_NAME );

			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}

			return false;
		}

		return true;
	}

	/**
	 * Show note errors must install plugin LearnPress.
	 *
	 * @return void
	 */
	public function show_note_errors_require_lp() {
		?>
		<div class="notice notice-error">
			<p><?php echo( 'Please active <strong>LP version ' . LP_ADDON_PMPRO_REQUIRE_VER . ' or later</strong> before active <strong>' . self::$addon_info['Name'] . '</strong>' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Show note errors must install plugin PMS.
	 *
	 * @return void
	 */
	public function show_note_errors_install_plugin_pms() {
		?>
		<div class="notice notice-error">
			<p><?php echo 'Please active plugin <strong>Paid Memberships pro</strong> before active plugin <strong>LearnPress - PMS</strong>'; ?></p>
		</div>
		<?php
	}
}

LP_Addon_Paid_Memberships_Pro_Preload::instance();
