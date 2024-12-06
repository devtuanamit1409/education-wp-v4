<?php
/**
 * Template for displaying content of h5p item.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/addons/h5p/single-h5p/content.php.
 *
 * @author   ThimPress
 * @package  Learnpress/H5p/Templates
 * @version  3.0.0
 */

defined( 'ABSPATH' ) || exit();

$current_h5p = LP_Global::course_item();

$conditional_h5p = get_post_meta( $current_h5p->get_id(), '_lp_h5p_interact', true );
$plugin          = H5P_Plugin::get_instance();
$content         = $plugin->get_content( $conditional_h5p );
$library         = ! empty( $content['library']['name'] ) ? $content['library']['name'] : '';
?>

<div class="learn_press_h5p_content <?php echo esc_attr( strtolower( str_replace( 'H5P.', '', $library ) ) ); ?>">
	<?php echo $current_h5p->get_content(); ?>
</div>
