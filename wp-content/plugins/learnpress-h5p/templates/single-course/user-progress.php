<?php
/**
 * Template for displaying item h5p progress of single course.
 *
 * @author   ThimPress
 * @package  Learnpress/Templates
 * @version  1.0.0
 * @sicne 4.0.2
 * @author Minhpd
 */

if ( empty( $course_results['items']['h5p'] ) || empty( $user ) || empty( $course_data ) ) {
	return;
}

if ( ! $course_results['items']['h5p']['total'] ) {
	return;
}

$total     = $course_results['items']['h5p']['total'];
$items     = $course_data->get_items();
$course_id = $course_data->get_id();
$user_id   = $user->get_id();
$evaluated = 0;

foreach ( $items as $item_course ) {

	$item_type = $item_course->get_data( 'item_type' );
	$item_id   = $item_course->get_id();

	if ( $item_type == LP_H5P_CPT ) {
		$result = learn_press_h5p_get_result( $item_id, $user_id, $course_id );

		if ( $result['grade'] == 'passed' ) {
			$evaluated++;
		}
	}
}

$label = esc_html__( 'H5P completed', 'learnpress-h5p' );
?>
<div class="items-progress">
	<h4 class="items-progress__heading">
		<?php echo wp_sprintf( '%s:', $label ); ?>
	</h4>
	<span class="number"><?php printf( '%1$d/%2$d', $evaluated, $total ); ?></span>
</div>
<?php

