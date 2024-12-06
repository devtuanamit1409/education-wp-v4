<?php
/**
 * Template for displaying content of landing course.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/addons/coming-soon-courses/content-single-course.php.
 *
 * @author ThimPress
 * @package LearnPress/Coming-Soon-Courses/Templates
 * @version 3.0.0
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;
?>

<?php do_action( 'learn_press_before_content_coming_soon' ); ?>

<div class="course-content_coming_soon">
	<?php do_action( 'learn_press_content_coming_soon_meta_details' ); ?>
	<?php do_action( 'learn_press_content_coming_soon_content_tabs' ); ?>
	<?php do_action( 'learn_press_content_coming_soon_message' ); ?>
	<?php do_action( 'learn_press_content_coming_soon_countdown' ); ?>
</div>

<?php do_action( 'learn_press_after_content_coming_soon' ); ?>
