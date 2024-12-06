<?php
/**
 * Plugin Name: LearnPress - Coming Soon Courses
 * Plugin URI: http://thimpress.com/learnpress
 * Description: Set a course is "Coming Soon" and schedule to public.
 * Author: ThimPress
 * Version: 4.0.6
 * Author URI: http://thimpress.com
 * Tags: learnpress
 * Text Domain: learnpress-coming-soon-courses
 * Domain Path: /languages/
 * Require_LP_Version: 4.2.5
 *
 * @package learnpress-coming-soon-courses
 **/

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

const LP_ADDON_COMING_SOON_COURSES_FILE = __FILE__;

/**
 * Class LP_Addon_Coming_Soon_Courses_Preload
 */
class LP_Addon_Coming_Soon_Courses_Preload {
	/**
	 * @var array
	 */
	public static $addon_info = array();
	/**
	 * @var LP_Addon_Coming_Soon_Courses_Preload
	 */
	protected static $instance;
	/**
	 * @var LP_Addon_Coming_Soon_Courses
	 */
	public static $addon;

	/**
	 * Instacne
	 */
	public static function instance() {
		if ( is_null( static::$instance ) ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * LP_Addon_Coming_Soon_Courses_Preload constructor.
	 */
	protected function __construct() {
		$can_load = true;
		// Set Base name plugin.
		define( 'LP_ADDON_COMING_SOON_COURSES_BASENAME', plugin_basename( LP_ADDON_COMING_SOON_COURSES_FILE ) );

		// Set version addon for LP check .
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		self::$addon_info = get_file_data(
			LP_ADDON_COMING_SOON_COURSES_FILE,
			array(
				'Name'               => 'Plugin Name',
				'Require_LP_Version' => 'Require_LP_Version',
				'Version'            => 'Version',
			)
		);

		define( 'LP_ADDON_COMING_SOON_COURSES_VER', self::$addon_info['Version'] );
		define( 'LP_ADDON_COMING_SOON_COURSES_REQUIRE_VER', self::$addon_info['Require_LP_Version'] );

		// Check LP activated .
		if ( ! is_plugin_active( 'learnpress/learnpress.php' ) ) {
			$can_load = false;
		} elseif ( version_compare( LP_ADDON_COMING_SOON_COURSES_REQUIRE_VER, get_option( 'learnpress_version', '3.0.0' ), '>' ) ) {
			$can_load = false;
		}

		if ( ! $can_load ) {
			add_action( 'admin_notices', array( $this, 'show_note_errors_require_lp' ) );
			deactivate_plugins( LP_ADDON_COMING_SOON_COURSES_BASENAME );

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
		self::$addon = LP_Addon::load( 'LP_Addon_Coming_Soon_Courses', 'inc/load.php', __FILE__ );
	}

	public function show_note_errors_require_lp() {
		?>
		<div class="notice notice-error">
			<p><?php echo( 'Please active <strong>LP version ' . LP_ADDON_COMING_SOON_COURSES_REQUIRE_VER . ' or later</strong> before active <strong>' . self::$addon_info['Name'] . '</strong>' ); ?></p>
		</div>
		<?php
	}
}

LP_Addon_Coming_Soon_Courses_Preload::instance();
