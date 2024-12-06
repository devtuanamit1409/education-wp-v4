<?php
/**
 * Template for displaying H5p answers list after completed.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/addons/h5p/content-h5p/summary/interactivevideo.php.
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

$interactions = $params->interactiveVideo->assets->interactions;
$list_actions = lp_h5p_re_sort_video_actions( $interactions );

if ( isset( $list_actions ) && count( $list_actions ) > 0 ) {
	if ( ! isset( $not_show_head_text ) ) : ?>
		<p><strong><?php echo esc_html__( 'Solutions:', 'learnpress-h5p' ); ?></strong></p>
	<?php endif; ?>

	<ol class="lp_h5p_intervideo_solutions">
		<?php foreach ( $list_actions as $action ) : ?>
			<li><?php echo $action['html']; ?></li>
		<?php endforeach; ?>
	</ol>
	<?php
}
