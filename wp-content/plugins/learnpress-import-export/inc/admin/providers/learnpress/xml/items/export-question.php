<?php
global $wpdb;
/**
 * Export course sections
 */
$query            = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}learnpress_question_answers WHERE question_id = %d", $post->ID );
$question_answers = $wpdb->get_results( $query );

if ( $question_answers ) {
	foreach ( $question_answers as $answer ) :?>

		<wp:answer>
			<wp:question_id><?php echo $post->ID; ?></wp:question_id>
			<wp:answer_title><?php echo wxr_cdata( $answer->title ); ?></wp:answer_title>
			<wp:answer_value><?php echo $answer->value; ?></wp:answer_value>
			<wp:answer_order><?php echo $answer->order; ?></wp:answer_order>
			<wp:answer_is_true><?php echo $answer->is_true; ?></wp:answer_is_true>
		</wp:answer>

		<?php

		if ( empty( $answer->value ) ) {

			$question_answer_id    = $answer->question_answer_id;
			$query_meta            = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}learnpress_question_answermeta WHERE learnpress_question_answer_id = %d", $question_answer_id );
			$question_answers_meta = $wpdb->get_results( $query_meta );

			if ( $question_answers_meta ) {
				foreach ( $question_answers_meta as $answer_meta ) :
					?>

					<wp:answer_meta>
						<wp:question_answer_id><?php echo $answer_meta->learnpress_question_answer_id; ?></wp:question_answer_id>
						<wp:meta_key><?php echo $answer_meta->meta_key; ?></wp:meta_key>
						<wp:meta_value><?php echo $answer_meta->meta_value; ?></wp:meta_value>
					</wp:answer_meta>

					<?php
				endforeach;
			}
		}

	endforeach;
}




