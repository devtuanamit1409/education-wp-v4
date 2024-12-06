<?php
/**
 * Plugin Name: LearnPress - H5P Content
 * Plugin URI: http://thimpress.com/learnpress
 * Description: H5P Content add-on for LearnPress.
 * Author: ThimPress
 * Version: 4.0.3
 * Author URI: http://thimpress.com
 * Tags: learnpress, lms, h5p
 * Text Domain: learnpress-h5p
 * Domain Path: /languages/
 * Require_LP_Version: 4.2.5.7
 *
 * @package learnpress-h5p
 */

defined( 'ABSPATH' ) || exit;

const LP_ADDON_H5P_FILE = __FILE__;
define( 'LP_ADDON_H5P_PATH', dirname( __FILE__ ) );
const LP_ADDON_H5P_INC_PATH = LP_ADDON_H5P_PATH . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR;

/**
 * Class LP_Addon_H5p_Preload
 *
 * @author Nhamdv <Update for LP4>
 */
class LP_Addon_H5p_Preload {
	/**
	 * @var array
	 */
	public static $addon_info = array();
	/**
	 * @var LP_Addon_H5p $addon
	 */
	public static $addon;

	/**
	 * Singleton.
	 *
	 * @return LP_Addon_Woo_Payment_Preload|mixed
	 */
	public static function instance() {
		static $instance;
		if ( is_null( $instance ) ) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * LP_Addon_H5p_Preload constructor.
	 */
	protected function __construct() {
		$can_load = true;
		// Set Base name plugin.
		define( 'LP_ADDON_H5P_BASENAME', plugin_basename( LP_ADDON_H5P_FILE ) );

		// Set version addon for LP check .
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		self::$addon_info = get_file_data(
			LP_ADDON_H5P_FILE,
			array(
				'Name'               => 'Plugin Name',
				'Require_LP_Version' => 'Require_LP_Version',
				'Version'            => 'Version',
			)
		);

		define( 'LP_ADDON_H5P_VER', self::$addon_info['Version'] );
		define( 'LP_ADDON_H5P_REQUIRE_VER', self::$addon_info['Require_LP_Version'] );

		// Check LP activated .
		if ( ! is_plugin_active( 'learnpress/learnpress.php' ) ) {
			$can_load = false;
		} elseif ( version_compare( LP_ADDON_H5P_REQUIRE_VER, get_option( 'learnpress_version', '3.0.0' ), '>' ) ) {
			$can_load = false;
		}

		if ( ! $can_load ) {
			add_action( 'admin_notices', array( $this, 'show_note_errors_require_lp' ) );
			deactivate_plugins( LP_ADDON_H5P_BASENAME );

			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}

			return;
		}

		// Check Woo activated .
		if ( ! $this->check_h5p_activated() ) {
			return;
		}

		// Sure LP loaded.
		add_action( 'learn-press/ready', array( $this, 'load' ) );
	}

	/**
	 * Load addon
	 */
	public function load() {
		self::$addon = LP_Addon::load( 'LP_Addon_H5p', 'inc/load.php', __FILE__ );
	}

	public function show_note_errors_require_lp() {
		?>
		<div class="notice notice-error">
			<p><?php echo( 'Please active <strong>LearnPress version ' . LP_ADDON_H5P_REQUIRE_VER . ' or later</strong> before active <strong>' . self::$addon_info['Name'] . '</strong>' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Check plugin Woo activated.
	 */
	public function check_h5p_activated(): bool {
		if ( ! is_plugin_active( 'h5p/h5p.php' ) ) {
			add_action( 'admin_notices', array( $this, 'show_note_errors_install_plugin_h5p' ) );

			deactivate_plugins( LP_ADDON_H5P_BASENAME );

			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}

			return false;
		}

		return true;
	}

	/**
	 * Show notice error install plugin H5P.
	 */
	public function show_note_errors_install_plugin_h5p() {
		?>
		<div class="notice notice-error">
			<p>
			<?php
			$url_active = wp_nonce_url( 'plugin-install.php?tab=search&s=h5p' );
			echo sprintf(
				'Please active plugin <strong><a href="%s">H5P</a></strong> before active plugin <strong>LearnPress - H5P Content</strong>',
				$url_active
			);
			?>
			</p>
		</div>
		<?php
	}
}

LP_Addon_H5p_Preload::instance();
