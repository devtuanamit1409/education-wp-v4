<?php
/**
 * Template for displaying course content within the loop.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/addons/coming-soon-courses/content-course.php.
 *
 * @author  ThimPress
 * @package LearnPress/Coming-Soon-Courses/Templates
 * @version 3.0.0
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

$message   = '';
$course    = learn_press_get_course();
$course_id = $course->get_id();
if ( learn_press_is_coming_soon( $course_id ) && '' !== ( $message = get_post_meta( $course_id, '_lp_coming_soon_msg', true ) ) && ! learn_press_is_courses() ) {
	$message = strip_tags( $message );
} else {
	$message = '';
}
//$showtext = get_post_meta( $course_id, '_lp_coming_soon_showtext', true );
?>

<li id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <a href="<?php the_permalink(); ?>" class="course-permalink">
		<?php
		do_action( 'learn-press/courses-loop-item-title' );
		?>
    </a>
	<?php if ( $message ) { ?>
        <div class="learn-press-coming-soon-course-message"> <?php echo $message; ?></div>
	<?php } ?>
	<?php
	if ( learn_press_is_coming_soon( $course_id ) && learn_press_is_show_coming_soon_countdown( $course_id ) ) {
		$end_time = learn_press_get_coming_soon_end_time( $course_id, 'Y-m-d H:i:s' );
		$datetime = new DateTime( $end_time );
		$timezone = get_option( 'gmt_offset' );

		$end_time_timestamp = learn_press_get_coming_soon_end_time( $course_id );
		$current_time       = current_time( 'timestamp' );
		$timestamp_remain   = $end_time_timestamp - $current_time;
		$time_remain        = gmdate( "H:i:s", $timestamp_remain );
		?>
        <div class="countdown learnpress-course-coming-soon" data-time-remain="<?php echo esc_attr( $time_remain ); ?>"
             data-timestamp-remain="<?php echo esc_attr( $timestamp_remain ); ?>"
             data-time="<?php echo esc_attr( $datetime->format( DATE_ATOM ) ) ?>" data-speed="500"
             data-timezone="<?php echo $timezone; ?>"></div>
	<?php } ?>
</li>