<?php
/**
 * Plugin Name: Woo Booster Toolkit
 * Description: Add some features to WooCommerce plugin.
 * Version: 1.0.1
 * Author: ThimPress
 * Author URI: https://thimpress.com/
 * Requires at least: 6.x
 * Requires PHP: 7.4
 * Text Domain: wcbt
 * Domain Path: /languages
 */

namespace WCBT;

use WCBT\Controllers\EnqueueScriptsController;
use WCBT\Controllers\CompareProductController;
use WCBT\Controllers\QuickViewController;
use WCBT\Controllers\SnackBarController;
use WCBT\Controllers\WishListController;
use WCBT\Controllers\VariationController;
use WCBT\Controllers\MaxSalePopUpController;
use WCBT\Controllers\TemplateController;
use WCBT\Controllers\PageController;
use WCBT\Controllers\WooCommerceController;
use WCBT\Controllers\ProductFilterController;
use WCBT\Helpers\TemplateHooks\QuickView;
use WCBT\Register\Setting;
use WCBT\Register\Widgets;
use WCBT\Metaboxes\VariationTermMeta;

//Shortcode
use WCBT\Shortcodes\WishList as WishListShortcode;
use WCBT\Shortcodes\Compare as CompareShortcode;

use WCBT\Helpers\TemplateHooks\WishList;
use WCBT\Helpers\TemplateHooks\Compare;


//Elementor
use WCBT\Elementor\ElementorWCBT;

/**
 * Class WCBT
 */
class WCBT {
	/**
	 * @var
	 */
	protected static $instance;
	/**
	 * @var array
	 */
	public static $plugin_info;

	/**
	 * Instance
	 *
	 * @return self
	 */
	public static function instance(): self {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 */
	protected function __construct() {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		self::$plugin_info = get_plugin_data( __FILE__ );

		if ( ! $this->woocommerce_is_actived() ) {
			add_action( 'admin_notices', array( $this, 'required_plugins_notice' ) );

			return;
		}

		$this->set_constant();
		$this->include();
		$this->hooks();
	}

	/**
	 * Set constant variable
	 *
	 * @return void
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	protected function set_constant() {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		define( 'WCBT_VERSION', self::$plugin_info['Version'] );
		define( 'WCBT_DB_VERSION', '1.0.2' );
		define( 'WCBT_PREFIX', 'wcbt' );
		define( 'WCBT_EL_PREFIX', 'WCBT' );

		define( 'WCBT_REST_VERSION', 'v1' );

		//Dirs and Urls
		define( 'WCBT_URL', plugin_dir_url( __FILE__ ) );
		define( 'WCBT_DIR', plugin_dir_path( __FILE__ ) );
		define( 'WCBT_CONFIG_DIR', WCBT_DIR . 'config/' );
		define( 'WCBT_VIEWS', WCBT_DIR . 'views/' );
		define( 'WCBT_ASSETS_URL', WCBT_URL . 'assets/' );
		define( 'WCBT_ELEMENT', WCBT_DIR . 'app/Elementor/Templates' );
		define(
			'WCBT_FOLDER_ROOT_NAME',
			str_replace(
				array( '/', basename( __FILE__ ) ),
				'',
				plugin_basename( __FILE__ )
			)
		);

		//option key
		define( 'WCBT_OPTION_KEY', 'wcbt_option' );
		define( 'WCBT_TERM_META_KEY', 'wcbt_term_meta_key' );

		//Debug
		define( 'WCBT_DEBUG', true );

		//Page
		define( 'WCBT_COMPARE_PAGE', 'wcbt_compare_page' );
		define( 'WCBT_WISHLIST_PAGE', 'wcbt_wishlist_page' );
		define( 'WCBT_SINGLE_PRODUCT_PAGE', 'wcbt_single_product_page' );
		define( 'WCBT_SETTING_PAGE', 'wcbt_setting_page' );
	}

	/**
	 * Include files
	 *
	 * @return void
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	protected function include() {
		require_once WCBT_DIR . 'vendor/autoload.php';

		if ( is_admin() ) {
			new Setting();
		} else {
		}

		//Register
		new Widgets();

		new EnqueueScriptsController();
		new SnackBarController();
		new TemplateController();
		new PageController();
		new VariationController();
		new WooCommerceController();
		new VariationTermMeta();
		new MaxSalePopUpController();
		new ProductFilterController();
//        new SaleNoticeController();

		//Elementor
		new ElementorWCBT();

		WishListController::instance();
		CompareProductController::instance();
		QuickViewController::instance();
		WishList::instance();
		Compare::instance();
		QuickView::instance();

		//Shortcode
		new WishListShortcode();
		new CompareShortcode();
	}

	/**
	 * Hooks to WP
	 *
	 * @return void
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	protected function hooks() {
		add_action( 'plugins_loaded', array( $this, 'load_text_domain' ) );
		register_activation_hook( __FILE__, array( $this, 'on_activate' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
	}

	public function on_activate() {
	}

	/**
	 * Load text domain
	 *
	 * @return void
	 */
	public function load_text_domain() {
		load_plugin_textdomain( 'wcbt', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}

	public function admin_notices() {
		if ( ! current_user_can( 'administrator' ) ) {
			return;
		}
	}

	public function woocommerce_is_actived() {
		return in_array(
			'woocommerce/woocommerce.php',
			get_option( 'active_plugins' )
		);
	}

	public function woocommerce_is_installed() {
		return isset( get_plugins()['woocommerce/woocommerce.php'] );
	}

	/**
	 * @return void
	 */
	public function required_plugins_notice() {
		$screen = get_current_screen();

		if ( isset( $screen->parent_file ) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id ) {
			return;
		}

		$plugin = 'woocommerce/woocommerce.php';

		if ( $this->woocommerce_is_installed() ) {
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return;
			}

			$activation_url = wp_nonce_url(
				'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s',
				'activate-plugin_' . $plugin
			);
			$message        = sprintf(
				'<p>%s</p>',
				esc_html__( 'Woo Booster Toolkit requires WooCommerce to be activated.', 'wcbt' )
			);
			$message        .= sprintf(
				'<p><a href="%s" class="button-primary">%s</a></p>',
				$activation_url,
				esc_html__( 'Activate WooCommerce Now', 'wcbt' )
			);
		} else {
			if ( ! current_user_can( 'install_plugins' ) ) {
				return;
			}

			$install_url = wp_nonce_url(
				self_admin_url( 'update.php?action=install-plugin&plugin=woocommerce' ),
				'install-plugin_woocommerce'
			);
			$message     = sprintf(
				'<p>%s</p>',
				esc_html__( 'Woo Booster Toolkit requires WooCommerce to be installed.', 'wcbt' )
			);
			$message     .= sprintf(
				'<p><a href="%s" class="button-primary">%s</a></p>',
				$install_url,
				esc_html__( 'Install WooCommerce Now', 'wcbt' )
			);
		}

		printf( '<div class="notice notice-error is-dismissible"><p>%s</p></div>', wp_kses_post( $message ) );
	}
}

WCBT::instance();

