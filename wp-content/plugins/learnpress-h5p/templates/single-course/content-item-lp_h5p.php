<?php
/**
 * Template for displaying h5p item content in single course.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/addons/h5p/single-course/content-item-lp_h5p.php.
 *
 * @author   ThimPress
 * @package  Learnpress/Assignments/Templates
 * @version  3.0.0
 */

defined( 'ABSPATH' ) || exit();

$h5p = LP_Global::course_item();
?>

<div id="content-item-h5p" class="content-item-summary">

	<?php
	/**
	 * @see learn_press_content_item_summary_title()
	 * @see learn_press_content_item_summary_content()
	 */
	do_action( 'learn-press/before-content-item-summary/' . $h5p->get_item_type() );
	?>

	<?php
	/**
	 * @see learn_press_content_item_summary_question()
	 */
	do_action( 'learn-press/content-item-summary/' . $h5p->get_item_type() );
	?>

	<?php
	/**
	 * @see learn_press_content_item_summary_question_numbers()
	 */
	do_action( 'learn-press/after-content-item-summary/' . $h5p->get_item_type() );
	?>

</div>
