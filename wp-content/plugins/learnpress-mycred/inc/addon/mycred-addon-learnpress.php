<?php
/**
 * myCRED addon Learnpress class.
 *
 * @author   ThimPress
 * @package  LearnPress/myCRED/Classes
 * @version  3.0.1
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

! defined( 'myCRED_VERSION' ) || exit;

include_once LP_ADDON_MYCRED_PATH . '/inc/hooks/mycred-hook-learnpress-learner.php';
include_once LP_ADDON_MYCRED_PATH . '/inc/hooks/mycred-hook-learnpress-instructor.php';

if ( ! class_exists( 'myCRED_LearnPress_Modul' ) ) {
	/**
	 * Class myCRED_LearnPress_Module.
	 */
	class myCRED_LearnPress_Module extends myCRED_Module {
		/**
		 * myCRED_LearnPress_Module constructor.
		 */
		public function __construct() {
			parent::__construct( 'myCRED_LearnPress_Module', array(
				'module_name' => 'learnpress',
				'option_id'   => '',
				'defaults'    => array(),
				'labels'      => array( 'menu' => '', 'page_title' => '' ),
				'register'    => false,
				'screen_id'   => '',
				'add_to_core' => true,
				'accordion'   => false,
				'menu_pos'    => 10
			) );
		}
	}
}