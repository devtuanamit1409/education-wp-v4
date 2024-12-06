<?php
defined( 'ABSPATH' ) || exit;
class LearnPress_PMPro_Admin {

	public static $instance = null;

	public static function getInstance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'pmpro_membership_level_after_other_settings', array( $this, 'pmpro_membership_level_after_other_settings' ) );
		add_action( 'pmpro_save_membership_level', array( $this, 'pmpro_save_membership_level' ), 10, 1 );
	}


	/**
	 * Save added/removed course to membership level
	 * Update orders of users not expire
	 *
	 * @param int $level_id
	 *
	 * @editor: tungnx
	 */
	public function pmpro_save_membership_level( $level_id = 0 ) {
		$level_course_ids = ! empty( $_POST['_lp_pmpro_courses'] ) ? $_POST['_lp_pmpro_courses'] : array();

		/***** Save courses on level */
		$courses        = LP_PMS_DB::getInstance()->getCoursesOnLevel( $level_id );
		$old_course_ids = array_keys( $courses );
		$del_course_ids = array_diff( $old_course_ids, $level_course_ids );
		$new_course_ids = array_diff( $level_course_ids, $old_course_ids );

		// Delete level on course meta
		foreach ( $del_course_ids as $course_id ) {
			delete_post_meta( $course_id, '_lp_pmpro_levels', $level_id );
		}

		// Add level on course meta
		foreach ( $new_course_ids as $course_id ) {
			add_post_meta( $course_id, '_lp_pmpro_levels', $level_id, false );
		}
		/***** End check update courses on level */

		/***** Update orders */
		$auto_update_courses_on_level = LP_Settings::get_option( 'pmpro_update_access_course', 'no' );
		if ( $auto_update_courses_on_level != 'yes' ) {
			return;
		}

		$lp_orders = LP_PMS_DB::getInstance()->getLastOrderOfUsersHasLevel( $level_id );

		if ( is_array( $lp_orders ) && count( $lp_orders ) > 0 ) {
			LP_PMS_Order::getInstance()->handleLevelChangeCourses( $lp_orders, $level_course_ids, $level_id );
		}
		/***** End update orders */
	}

	/**
	 * Add courses select box in to membership level edit pages
	 */
	public function pmpro_membership_level_after_other_settings() {
		// require template file
		require_once __DIR__ . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'edit.php';
	}

}
LearnPress_PMPro_Admin::getInstance();
