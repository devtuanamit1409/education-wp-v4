<?php
/**
 * Template for displaying H5p answers list after completed.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/addons/h5p/content-h5p/summary/multichoice.php.
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
?>

<?php if ( isset( $params->question ) ) : ?>
	<strong><?php echo $params->question; ?></strong>
<?php endif; ?>

<span><?php echo esc_html__( 'Correct Answers:', 'learnpress-h5p' ); ?></span>

<?php
foreach ( $params->answers as $answer ) {
	if ( ! $answer->correct ) {
		continue;
	}

	echo $answer->text;
}
