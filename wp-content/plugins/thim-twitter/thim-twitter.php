<?php
/**
 * Plugin Name: Thim Twitter
 * Plugin URI: http://thimpress.com
 * Description:  Thim Twitter plugin helps you get feed on your account easily.
 * Version: 1.0.0
 * Author: Thimpress
 * Author URI: http://thimpress.com
 * Requires at least: 4.1
 * Tested up to: 4.3
 *
 * Text Domain: thim-twitter
 *
 * @package ThimTwitter
 * @author Thimpress
 */


if ( ! defined('ABSPATH')) exit;  // if direct access 

if ( ! class_exists( 'ThimTwitter' ) ) :

/**
 * Main ThimTwitter Class
 *
 * @class ThimTwitter
 * @version	1.0.0
 */
class ThimTwitter{

	/**
	 * Constructor - get the plugin hooked in and ready
	 */
	public function __construct() {

		// Define
		define('THIM_TWITTER_DIR', plugin_dir_path( __FILE__ ) );
		// Include

		require_once( THIM_TWITTER_DIR . 'inc/option.php');
		require_once( THIM_TWITTER_DIR . 'inc/widget.php');

	}

	/**
	 * Get template part (for overridden template).
	 *
	 * @param $name
	 * @return template path
	 * @author Khoapq
	 */
	public static function getTemplate( $name ) {
		$template = THIM_TWITTER_DIR . "templates/{$name}.php";
		$overridden_template = locate_template( "thim-twitter/{$name}.php" ) ;
		if ($overridden_template){
		 	$template = $overridden_template;
		}
		return $template;
	}
	
}
endif;

$GLOBALS['ThimTwitter'] = new ThimTwitter();
