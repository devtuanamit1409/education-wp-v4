<?php
function learn_press_gradebook_nonce_url( $args = array(), $field = 'gradebook-nonce' ) {
	$args = wp_parse_args( $args, array( 'course_id' => get_the_ID() ) );

	return wp_nonce_url( add_query_arg( $args, 'admin.php?page=course-gradebook' ), 'learn-press-gradebook-' . $args['course_id'], $field );
}

