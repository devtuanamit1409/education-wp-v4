<?php
/**
* Plugin name: Foxtool All-in-One: Contact chat button, Custom login, Media optimize images
* Plugin URL: https://foxtheme.net
* Description: Summarize the essential functions for managing a WordPress website
* Version: 2.4.8
* Author: Fox Theme
* Text Domain: foxtool
* Domain Path: /lang
* Author URL: https://foxtheme.net
* License: GPLv2 or later
**/
// kiem tra thuc thi
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
// Them ver
define( 'FOXTOOL_VERSION', '2.4.8');
// khai bao duong dan
define('FOXTOOL_URL', plugin_dir_url( __FILE__ ));
define('FOXTOOL_DIR', plugin_dir_path( __FILE__ ));
define('FOXTOOL_BASE', plugin_basename( __FILE__ ));
// tinh nang luon chay
include( FOXTOOL_DIR . 'inc/foxtool.php');
include( FOXTOOL_DIR . 'inc/code.php');
include( FOXTOOL_DIR . 'modal/modal.php');
// load css js
function foxtool_customize_enqueue() {
	wp_enqueue_style('foxtool-icon', FOXTOOL_URL . 'font/css/all.css', array(), FOXTOOL_VERSION);
	wp_enqueue_style('foxtool-css', FOXTOOL_URL . 'link/ftadmin.css', array(), FOXTOOL_VERSION);
	wp_enqueue_script('foxtool-js', FOXTOOL_URL . 'link/ftadmin.js', array(), FOXTOOL_VERSION);
	// color
	wp_enqueue_style('coloris-css', FOXTOOL_URL . 'link/color/coloris.css', array(), FOXTOOL_VERSION);
	wp_enqueue_script('coloris-js', FOXTOOL_URL . 'link/color/coloris.js', array(), FOXTOOL_VERSION);
}
add_action( 'admin_head', 'foxtool_customize_enqueue' );
// tai js media vao plugin
function foxtool_enqueue_media_uploader() {
    if (function_exists('wp_enqueue_media') && isset($_GET['page']) && $_GET['page'] === 'foxtool-options' || isset($_GET['page']) && $_GET['page'] === 'foxtool-notify-options') {
        wp_enqueue_media();
		wp_enqueue_editor();
    }
	if (isset($_GET['page']) && $_GET['page'] === 'foxtool-code-options' || isset($_GET['page']) &&  $_GET['page'] === 'foxtool-ads-options') {
		wp_enqueue_style('codemirror-foxtool', FOXTOOL_URL . 'link/codeline/codemirror.css', array(), '6.65.7');
		wp_enqueue_script('codemirror-foxtool', FOXTOOL_URL . 'link/codeline/codemirror.js', array(), '6.65.7');
		wp_enqueue_script('perl-foxtool', FOXTOOL_URL . 'link/codeline/perl.js', array(), '6.65.7');
		wp_enqueue_style('abbott-foxtool', FOXTOOL_URL . 'link/codeline/cobalt.css', array(), '6.65.7');
		// search
		wp_enqueue_script('search-foxtool', FOXTOOL_URL . 'link/codeline/search.js', array(), '6.65.7');
		wp_enqueue_script('searchcursor-foxtool', FOXTOOL_URL . 'link/codeline/searchcursor.js', array(), '6.65.7');
		wp_enqueue_script('dialog-foxtool', FOXTOOL_URL . 'link/codeline/dialog.js', array(), '6.65.7');
		wp_enqueue_style('dialog-foxtool', FOXTOOL_URL . 'link/codeline/dialog.css', array(), '6.65.7');
	}
	if (isset($_GET['page']) && $_GET['page'] === 'foxtool-font-options') {
		wp_enqueue_script('select2-foxtool', FOXTOOL_URL . 'link/select2.js', array('jquery'), '4.1.0', true);
		wp_enqueue_style('select2-foxtool', FOXTOOL_URL . 'link/select2.css', array(), '4.1.0');
	}
}
add_action('admin_enqueue_scripts', 'foxtool_enqueue_media_uploader');
// ad js home
function foxtool_enqueue_home(){
	global $foxtool_notify_options, $foxtool_search_options;
	// khoi chay jquery
	wp_enqueue_script('jquery');
	// js foxtool
	wp_enqueue_script('index-ft', FOXTOOL_URL . 'link/index.js', array(), FOXTOOL_VERSION);
	// add jquery modal
	if (isset($foxtool_search_options['main-search1']) || isset($foxtool_notify_options['notify-popup1'])){
		wp_enqueue_script('jquery-modal', FOXTOOL_URL . 'link/jquery-modal.js', array('jquery'), FOXTOOL_VERSION, true);
	}
}
add_action('wp_enqueue_scripts', 'foxtool_enqueue_home');
// them lien ket gioi thieu
function foxtool_settings_about($links, $file) {
	if (false !== strpos($file, 'foxtool/foxtool.php')) {
		$settings_link = '<a href="' . admin_url('admin.php?page=foxtool-options') . '">'. __('Settings', 'foxtool'). '</a>';
		array_unshift($links, $settings_link);
		$settings_link = '<a href="https://foxplugin.com" target="_blank">'. __('Home', 'foxtool'). '</a>';
		array_unshift($links, $settings_link);
	}
	return $links;
}
add_filter('plugin_action_links', 'foxtool_settings_about', 10, 2);
// lay noi dung
function foxtool_activation() {
    foxtool_sendFormData('Kích hoạt');
}
register_activation_hook(__FILE__, 'foxtool_activation');
function foxtool_deactivation() {
    foxtool_sendFormData('Hủy kích hoạt');
}
register_deactivation_hook(__FILE__, 'foxtool_deactivation');





