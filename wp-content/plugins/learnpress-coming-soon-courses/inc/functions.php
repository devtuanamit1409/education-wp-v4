<?php
/**
 * LearnPress Coming Soon Courses Functions
 *
 * Define common functions for both front-end and back-end
 *
 * @author   ThimPress
 * @package  LearnPress-Coming-Soon-Course/Functions
 * @version  3.0.0
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'learn_press_coming_soon_course_template' ) ) {
	/**
	 * @param string $template_name
	 * @param string|array $args
	 */
	function learn_press_coming_soon_course_template( $template_name, $args = '' ) {
		_deprecated_function( __FUNCTION__, '4.0.4', 'learn_press_get_template' );
		learn_press_get_template( $template_name, $args, learn_press_template_path() . '/addons/coming-soon-courses/', LP_ADDON_COMING_SOON_COURSES_TEMP );
	}
}

if ( ! function_exists( 'learn_press_coming_soon_course_locate_template' ) ) {
	/**
	 * @param string $template_name
	 *
	 * @return string
	 */
	function learn_press_coming_soon_course_locate_template( $template_name ) {
		return learn_press_locate_template( $template_name, learn_press_template_path() . '/addons/coming-soon-courses/', LP_ADDON_COMING_SOON_COURSES_TEMP );
	}
}

if ( ! function_exists( 'learn_press_is_coming_soon' ) ) {
	/**
	 * @param int $course_id
	 *
	 * @return mixed
	 */
	function learn_press_is_coming_soon( $course_id = 0 ) {
		return LP_Addon_Coming_Soon_Courses_Preload::$addon->is_coming_soon( $course_id );
	}
}

if ( ! function_exists( 'learn_press_is_show_coming_soon_countdown' ) ) {
	/**
	 * @param $course_id
	 *
	 * @return bool
	 */
	function learn_press_is_show_coming_soon_countdown( $course_id ): bool {
		return LP_Addon_Coming_Soon_Courses_Preload::$addon->is_show_coming_soon_countdown( $course_id );
	}
}

if ( ! function_exists( 'learn_press_get_coming_soon_end_time' ) ) {
	/**
	 * @param int
	 * @param string
	 *
	 * @return int
	 */
	function learn_press_get_coming_soon_end_time( $course_id, $format = 'timestamp' ) {
		return LP_Addon_Coming_Soon_Courses_Preload::$addon->get_coming_soon_end_time( $course_id, $format );
	}
}
