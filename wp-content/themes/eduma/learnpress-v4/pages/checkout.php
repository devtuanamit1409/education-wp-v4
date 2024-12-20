<?php
/**
 * Template for displaying content of page for processing checkout feature.
 *
 * @author   ThimPress
 * @package  LearnPress/Templates
 * @version  4.0.2
 */

defined( 'ABSPATH' ) or die;

get_header();


//do_action( 'learn-press/before-main-content' );
do_action( 'learnpress/template/pages/checkout/before-content' );

/**
 * LP Hook
 *
 * @since 4.0.0
 */
do_action( 'learn-press/before-checkout-page' );

// Shortcode for displaying checkout form
echo do_shortcode( '[learn_press_checkout]' );

/**
 * LP Hook
 *
 * @since 4.0.0
 */
do_action( 'learn-press/after-checkout-page' );

do_action( 'learnpress/template/pages/checkout/after-content' );
//do_action( 'learn-press/after-main-content' );

get_footer();
