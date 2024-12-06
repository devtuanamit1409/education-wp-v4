<?php
/**
 * LP_Meta_Box_Random_Questions
 *
 * @author minhpd
 * @version 1.0.0
 * @since 4.0.0
 */
/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

class LP_Meta_Box_Random_Questions extends LP_Meta_Box_Field {
	/**
	 * Show field
	 *
	 * @param $thepostid
	 *
	 * @return string|void
	 */
	public function output( $thepostid ) {
		$value = $this->meta_value( $thepostid );
		$quiz  = learn_press_get_quiz( $thepostid );

		if ( ! $quiz ) {
			return;
		}

		$total = $quiz->count_questions();
		$data  = [
			'question_random_meta_box' => $this,
			'value'                    => $value,
			'total'                    => $total,
		];

		LP_Addon_Random_Quiz_Preload::$addon->get_template(
			'random-questions.php',
			compact( 'data' )
		);
	}

	/**
	 * Save meta field value
	 *
	 * @param $post_id
	 *
	 * @return void
	 */
	public function save( $post_id ) {
		$quiz = learn_press_get_quiz( $post_id );
		if ( ! $quiz ) {
			return;
		}

		$total = $quiz->count_questions();
		$value = isset( $_POST[ $this->id ] ) ? LP_Helper::sanitize_params_submitted( $_POST[ $this->id ], 'int' ) : 0;

		if ( $value > $total ) {
			$value = $total;
		}

		update_post_meta( $post_id, $this->id, $value );
	}

}
