<?php
/**
 * Template for displaying the conditional h5p item.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/addons/h5p/single-h5p/condition.php.
 *
 * @author   ThimPress
 * @package  Learnpress/H5p/Templates
 * @version  3.0.0
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit(); ?>

<?php
$current_h5p     = LP_Global::course_item();
$conditional_h5p = get_post_meta( $current_h5p->get_id(), '_lp_h5p_interact', true );
$course          = learn_press_get_course();
$user            = learn_press_get_current_user();

if ( empty( $course ) || empty( $user ) ) {
	return;
}

$grade        = learn_press_h5p_get_result( $current_h5p->get_id(), $user->get_id(), $course->get_id() );
$status_class = isset( $grade['status'] ) ? $grade['status'] : '';
?>

<div class="learn_press_h5p_condition <?php esc_attr( $status_class ); ?>">
	<?php echo do_shortcode( '[h5p id="' . $conditional_h5p . '"]' ); ?>
</div>
