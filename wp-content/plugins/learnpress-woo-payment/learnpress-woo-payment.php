<?php
/**
 * Plugin Name: LearnPress - WooCommerce Payment Methods Integration
 * Plugin URI: https://thimpress.com/product/woocommerce-add-on-for-learnpress/
 * Description: Buy courses via Woocommerce.
 * Author: ThimPress
 * Version: 4.1.4
 * Author URI: http://thimpress.com
 * Tags: learnpress, woocommerce
 * Text Domain: learnpress-woo-payment
 * Domain Path: /languages/
 * Require_LP_Version: 4.2.6.1
 * Requires at least: 6.3
 * Requires PHP: 7.4
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package learnpress-woo-payment
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
const LP_ADDON_WOO_PAYMENT_FILE = __FILE__;
const LP_ADDON_WOO_PAYMENT_PATH = __DIR__;

class LP_Addon_Woo_Payment_Preload {
	/**
	 * @var array
	 */
	public static $addon_info = array();
	/**
	 * @var LP_Addon_Woo_Payment $addon
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
	 * LP_Addon_Wishlist_Preload constructor.
	 */
	protected function __construct() {
		$can_load = true;
		// Set Base name plugin.
		define( 'LP_ADDON_WOO_PAYMENT_BASENAME', plugin_basename( LP_ADDON_WOO_PAYMENT_FILE ) );

		// Set version addon for LP check .
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		self::$addon_info = get_file_data(
			LP_ADDON_WOO_PAYMENT_FILE,
			array(
				'Name'               => 'Plugin Name',
				'Require_LP_Version' => 'Require_LP_Version',
				'Version'            => 'Version',
			)
		);

		define( 'LP_ADDON_WOO_PAYMENT_VER', self::$addon_info['Version'] );
		define( 'LP_ADDON_WOO_PAYMENT_REQUIRE_VER', self::$addon_info['Require_LP_Version'] );

		// Check LP activated .
		if ( ! is_plugin_active( 'learnpress/learnpress.php' ) ) {
			$can_load = false;
		} elseif ( version_compare( LP_ADDON_WOO_PAYMENT_REQUIRE_VER, get_option( 'learnpress_version', '3.0.0' ), '>' ) ) {
			$can_load = false;
		}

		if ( ! $can_load ) {
			add_action( 'admin_notices', array( $this, 'show_note_errors_require_lp' ) );
			deactivate_plugins( LP_ADDON_WOO_PAYMENT_BASENAME );

			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}

			return;
		}

		// Check Woo activated .
		if ( ! $this->check_woo_activated() ) {
			return;
		}

		// Disable payment LP Woo because current only is setting, not payment gateway - should rewrite of can
		add_filter(
			'learn-press/payment-gateway/woocommerce/available',
			function ( $available ) {
				return false;
			}
		);

		// Sure LP loaded.
		add_action( 'learn-press/ready', array( $this, 'load' ) );
	}

	/**
	 * Check plugin Woo activated.
	 */
	public function check_woo_activated(): bool {
		if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			add_action( 'admin_notices', array( $this, 'show_note_errors_install_plugin_woo' ) );

			deactivate_plugins( LP_ADDON_WOO_PAYMENT_BASENAME );

			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}

			return false;
		}

		return true;
	}

	/**
	 * Load addon
	 */
	public function load() {
		self::$addon = LP_Addon::load( 'LP_Addon_Woo_Payment', 'incs/load.php', __FILE__ );
	}

	public function show_note_errors_require_lp() {
		?>
		<div class="notice notice-error">
			<p><?php echo( 'Please active <strong>LP version ' . LP_ADDON_WOO_PAYMENT_REQUIRE_VER . ' or later</strong> before active <strong>' . self::$addon_info['Name'] . '</strong>' ); ?></p>
		</div>
		<?php
	}

	public function show_note_errors_install_plugin_woo() {
		?>
		<div class="notice notice-error">
			<p><?php echo 'Please active plugin <strong>Woocomerce</strong> before active plugin <strong>LearnPress - Woo payment</strong>'; ?></p>
		</div>
		<?php
	}
}

LP_Addon_Woo_Payment_Preload::instance();

