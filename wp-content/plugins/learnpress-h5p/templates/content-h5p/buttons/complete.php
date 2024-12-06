<?php
/**
 * Template for displaying H5p Complete Button.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/h5p/content-h5p/buttons/complete.php.
 *
 * @author  ThimPress
 * @package  Learnpress/H5p/Templates
 * @version  3.0.0
 */

defined( 'ABSPATH' ) || exit();

if ( empty( $course ) || empty( $user ) || empty( $h5p ) ) {
	return;
}

$h5p      = LP_Global::course_item();
$h5p_data = $user->get_item_data( $h5p->get_id(), $course->get_id() );

$current_user_item_id = 0;
if ( $h5p_data ) {
	$current_user_item_id = $h5p_data->get_user_item_id();
}
$class = '';
if ( $current_user_item_id ) {
	$score     = learn_press_get_user_item_meta( $current_user_item_id, 'score', true );
	$max_score = learn_press_get_user_item_meta( $current_user_item_id, 'max_score', true );
	$class     = $score && $max_score ? ( ( $score / $max_score ) * 100 >= $h5p->get_passing_grade() ? 'force_appear' : '' ) : '';
}
?>

<?php do_action( 'learn-press/before-h5p-complete-button' ); ?>

<form name="complete-h5p" class="complete-h5p <?php esc_attr( $class ); ?>" method="post" enctype="multipart/form-data">

	<?php do_action( 'learn-press/begin-h5p-complete-button' ); ?>

	<button type="submit" disabled class="button complete-h5p-button" id="complete_h5p_button" data-confirm="<?php esc_attr_e( 'Do you really want to submit the result?', 'learnpress-h5p' ); ?>"><?php _e( 'Submit', 'learnpress-h5p' ); ?></button>

	<?php do_action( 'learn-press/end-h5p-complete-button' ); ?>

	<?php lp_h5p_action( 'complete', $h5p->get_id(), $course->get_id(), true ); ?>
	<input type="hidden" name="noajax" value="yes">

</form>

<?php do_action( 'learn-press/after-h5p-complete-button' ); ?>
