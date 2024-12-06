<?php
/**
 * Template for displaying h5p item in single course.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/addons/h5p/single-course/section/item-h5p.php.
 *
 * @author   ThimPress
 * @package  Learnpress/Assignments/Templates
 * @version  3.0.0
 */


defined( 'ABSPATH' ) || exit(); ?>

<span class="item-name"><?php echo $item->get_title( 'display' ); ?></span>
