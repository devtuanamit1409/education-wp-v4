<?php
/**
 * All functions for LearnPress H5P Content templates.
 *
 * @author  ThimPress
 * @package LearnPress/H5P/Functions
 * @version 3.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'learn_press_content_item_h5p_duration' ) ) {
	/**
	 * @depecated 4.0.1
	 */
	/*function learn_press_content_item_h5p_duration() {
		$course = learn_press_get_course();
		if ( empty( $course ) ) {
			return;
		}
		$user          = learn_press_get_current_user();
		$h5p           = LP_Global::course_item();
		$h5p_data      = $user->get_item_data( $h5p->get_id(), $course->get_id() );
		$status        = $user->get_item_status( $h5p->get_id(), $course->get_id() );
		$duration      = learn_press_h5p_get_time_remaining( $h5p_data );
		$duration_time = get_post_meta( $h5p->get_id(), '_lp_duration', true );

		if ( in_array( $status, array( 'started', 'h5p_doing', 'completed' ) ) ) {
			learn_press_h5p_get_template(
				'content-h5p/duration.php',
				array(
					'duration'      => $duration,
					'duration_time' => $duration_time,
				)
			);
		}
	}*/
}

if ( ! function_exists( 'learn_press_h5p_start_button' ) ) {
	/**
	 * Start button.
	 * @depecated 4.0.1
	 */
	/*function learn_press_h5p_start_button() {
		$course = learn_press_get_course();
		$user   = learn_press_get_current_user();
		$h5p    = LP_Global::course_item();

		if ( empty( $course ) || empty( $user ) ) {
			return;
		}

		if ( $user->has_course_status( $course->get_id(), array( 'finished' ) ) || ! $user->has_course_status( $course->get_id(), array( 'enrolled' ) ) || $user->has_item_status(
			array(
				'started',
				'h5p_doing',
				'completed',
				'evaluated',
			),
			$h5p->get_id(),
			$course->get_id()
		)
		) {
			return;
		}

		learn_press_h5p_get_template( 'content-h5p/buttons/start.php' );
	}*/
}


if ( ! function_exists( 'learn_press_h5p_nav_buttons' ) ) {
	/**
	 * Nav button.
	 * @depecated 4.0.1
	 */
	/*function learn_press_h5p_nav_buttons() {
		$course = learn_press_get_course();
		$user   = learn_press_get_current_user();
		$h5p    = LP_Global::course_item();

		if ( empty( $course ) || empty( $user ) ) {
			return;
		}

		if ( ! $user->has_item_status( array( 'started', 'h5p_doing' ), $h5p->get_id(), $course->get_id() ) ) {
			return;
		}

		learn_press_h5p_get_template( 'content-h5p/buttons/controls.php' );
	}*/
}


if ( ! function_exists( 'learn_press_h5p_after_sent' ) ) {
	/**
	 * Sent button.
	 * @depecated 4.0.1
	 */
	/*function learn_press_h5p_after_sent() {
		$course = learn_press_get_course();
		$user   = learn_press_get_current_user();
		$h5p    = LP_Global::course_item();

		if ( empty( $course ) || empty( $user ) ) {
			return;
		}

		if ( ! $user->has_item_status( array( 'completed' ), $h5p->get_id(), $course->get_id() ) ) {
			return;
		}

		learn_press_h5p_get_template( 'content-h5p/buttons/sent.php' );
	}*/
}

/**
 * @depecated 4.0.1
 */
/*if ( ! function_exists( 'learn_press_h5p_fe_setting' ) ) {
	function learn_press_h5p_fe_setting() {
		learn_press_h5p_get_template( 'frontend-editor/item-settings.php' );
	}
}*/

if ( ! function_exists( 'learn_press_h5p_fe_fields' ) ) {
	/**
	 * @depecated 4.0.1
	 */
	/*function learn_press_h5p_fe_fields() {
		learn_press_h5p_get_template( 'frontend-editor/form-fields.php' );
	}*/
}

if ( ! function_exists( 'learn_press_h5p_fe_manager_link' ) ) {
	/**
	 * @depecated 4.0.1
	 */
	/*function learn_press_h5p_fe_manager_link() {
		$manager_page = get_option( 'h5p_students_man_page_id' );

		if ( $manager_page ) {
			$url  = get_page_link( $manager_page );
			$args = array(
				'manager_page' => $manager_page,
				'page_url'     => $url,
			);

			learn_press_h5p_get_template( 'frontend-editor/manager-link.php', $args );
		} else {
			return;
		}
	}*/
}

if ( ! function_exists( 'lp_h5ps_setup_shortcode_page_content' ) ) {

	/**
	 * @depecated 4.0.1
	 */
	/*function lp_h5ps_setup_shortcode_page_content( $content ) {
		global $post;

		$page_id = $post->ID;

		if ( ! $page_id ) {
			return $content;
		}

		if ( get_option( 'h5p_students_man_page_id' ) == $page_id ) {
			$current_content = get_post( $page_id )->post_content;
			if ( strpos( $current_content, '[h5p_students_manager' ) === false ) {
				$content = '[' . apply_filters( 'h5p_students_manager_shortcode_tag', 'h5p_students_manager' ) . ']';
			}
		} elseif ( get_option( 'h5p_evaluate_page_id' ) == $page_id ) {
			$current_content = get_post( $page_id )->post_content;
			if ( strpos( $current_content, '[h5p_evaluate_form' ) === false ) {
				$content = '[' . apply_filters( 'h5p_students_evaluate_shortcode_tag', 'h5p_evaluate_form' ) . ']';
			}
		}

		return do_shortcode( $content );
	}*/
}
