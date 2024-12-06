<?php
/**
 * Template for displaying title of h5p.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/addons/h5p/single-h5p/title.php.
 *
 * @author   ThimPress
 * @package  Learnpress/H5p/Templates
 * @version  3.0.0
 */

defined( 'ABSPATH' ) || exit();

$current_h5p = LP_Global::course_item();
?>

<h2><?php echo esc_html( $current_h5p->get_title() ); ?></h2>
