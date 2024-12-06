<?php
/**
 * Template for displaying H5p answers list after completed.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/addons/h5p/content-h5p/summary/singlechoiceset.php.
 *
 * @author  ThimPress
 * @package  Learnpress/H5p/Templates
 * @version  3.0.0
 */

defined( 'ABSPATH' ) || exit();

if ( is_string( $h5p_content['params'] ) ) {
	$params = json_decode( $h5p_content['params'] );
} else {
	$params = $h5p_content['params'];
}

$choices = $params->choices;

if ( count( $choices ) > 0 ) : ?>

	<?php if ( ! isset( $not_show_head_text ) ) : ?>
		<p><strong><?php echo esc_html_e( 'Solutions:', 'learnpress-h5p' ); ?></strong></p>
	<?php endif; ?>

	<ol class="lp_h5p_singlechoiceset_solutions">
		<?php foreach ( $choices as $choice ) : ?>
			<?php
			if ( ! isset( $choice->question ) ) {
				continue;
			}
			?>

			<li class="answer"><?php echo $choice->question; ?></li>

			<?php
				$answer = $choice->answers;
				echo $answer[0];
			?>
		<?php endforeach; ?>
	</ol>

	<?php
endif;
