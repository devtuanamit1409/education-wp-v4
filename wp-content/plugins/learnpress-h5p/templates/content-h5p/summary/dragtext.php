<?php
/**
 * Template for displaying H5p answers list after completed.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/addons/h5p/content-h5p/summary/dragtext.php.
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

if ( ! isset( $not_show_head_text ) ) { ?>
	<p><strong><?php echo esc_html__( 'Solutions:', 'learnpress-h5p' ); ?></strong></p>
	<?php
}

if ( isset( $params->textField ) && $params->textField != '' ) {
	echo preg_replace( '#\*([^\*]*)\*#', '<span class="drag_text_here">$1</span>', $params->textField );
}
