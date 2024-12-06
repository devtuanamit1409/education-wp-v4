<?php
/**
 * Template for displaying form action of assignment.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/addons/h5p/content-h5p/buttons.php.
 *
 * @author   ThimPress
 * @package  Learnpress/H5p/Templates
 * @version  3.0.0
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit(); ?>

<div class="lp-h5p-buttons">

	<?php do_action( 'learn-press/before-h5p-buttons' ); ?>

	<?php
	/**
	 * @see learn_press_h5p_nav_buttons - 5
	 * @see learn_press_h5p_start_button - 10
	 * @see learn_press_h5p_after_sent - 15
	 * @see learn_press_h5p_result - 15
	 * @see learn_press_h5p_retake - 20
	 */
	do_action( 'learn-press/h5p-buttons' );
	?>

	<?php do_action( 'learn-press/after-h5p-buttons' ); ?>

</div>
