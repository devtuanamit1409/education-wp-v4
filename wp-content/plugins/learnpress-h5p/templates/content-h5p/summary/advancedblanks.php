<?php
/**
 * Template for displaying H5p answers list after completed.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/addons/h5p/content-h5p/summary/advancedblanks.php.
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

$blanklist = $params->content->blanksList;
?>

<div class="lp_h5p_summary_advancedblank">
	<?php if ( ! isset( $not_show_head_text ) ) : ?>
		<p><strong><?php echo esc_html__( 'Solutions:', 'learnpress-h5p' ); ?></strong></p>
	<?php endif; ?>

	<ol>
		<?php foreach ( $blanklist as $list ) : ?>
			<li><?php echo $list->correctAnswerText; ?></li>
		<?php endforeach; ?>
	</ol>
</div>
