<?php
/**
 * Template meta-box random questions.
 *
 * @since 4.0.2
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $data ) || ! isset( $data['question_random_meta_box'] ) ||
	! $data['question_random_meta_box'] instanceof LP_Meta_Box_Random_Questions ) {
	return;
}

wp_enqueue_script( 'lp-random-quiz' );
wp_enqueue_style( 'lp-admin-random-quiz' );

$question_random_meta_box = $data['question_random_meta_box'];
$value                    = esc_attr( $data['value'] ?? 0 );
$total                    = esc_attr( $data['total'] ?? 0 );
?>
<div class="form-field lp-quiz-random-questions-meta-box" <?php echo $question_random_meta_box->condition; ?>>
	<label for="<?php echo esc_attr( $question_random_meta_box->id ); ?>">
		<?php echo wp_kses_post( $question_random_meta_box->label ); ?>
	</label>
	<div class="lp-quiz-random-questions-meta-box-fields">
		<div class="content">
			<label>
				<?php esc_html_e( 'Use', 'learnpress-random-quiz' ); ?>
			</label>
			<input type="number" name="<?php echo $question_random_meta_box->id; ?>"
				id="<?php echo $question_random_meta_box->id; ?>"
				value="<?php echo $value; ?>"
				max="<?php echo $total; ?>" min="0">
			<div>
				<span><?php echo __( 'to', 'learnpress-random-quiz' ); ?></span>
				<span class="total_questions"><strong><?php echo $total; ?></strong></span>
			</div>
		</div>
		<?php
		if ( ! empty( $question_random_meta_box->description ) ) {
			echo '<span class="description">' . wp_kses_post( $question_random_meta_box->description ) . '</span>';
		}
		?>
	</div>
</div>
