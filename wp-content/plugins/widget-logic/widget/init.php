<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

function widget_logic_init()
{
    load_plugin_textdomain('widget-logic', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('init', 'widget_logic_init');


function widget_logic_customizer_dynamic_sidebar_callback($widget)
{
    widget_logic_customizer_display($widget['id']);
}

include_once 'logic.php';

function widget_logic_customizer_display($widget_id)
{
    global $wl_options;

    if (!preg_match('/^(.+)-(\d+)$/', $widget_id)) {
        return;
    }

    $logic = widget_logic_by_id($widget_id);

    $show_errors = !empty($wl_options['widget_logic-options-show_errors']) && current_user_can('manage_options');

    ob_start();
    $show_widget = widget_logic_check_logic($logic);
    $error       = ob_get_clean();

    // Register a custom script handle
    wp_register_script('widget-logic-customizer-display-script', false, array('jquery'), '6.0.1', true);
    // Enqueue the custom script
    wp_enqueue_script('widget-logic-customizer-display-script');

    // Prepare the inline script
    $inline_script = '';

    if ($show_errors && $error) {
        $inline_script .= "
            jQuery(function ($) {
                $('#" . esc_attr($widget_id) . "')
                .append($('<p class=\"widget-logic-error\">')
                .html(" . wp_json_encode($error) . "));
            });
        ";
    }

    if (!$show_widget) {
        $inline_script .= "
            jQuery(function ($) {
                $('#" . esc_attr($widget_id) . "')
                .children()
                .not('.widget-logic-error')
                .css('opacity', '0.2');
            });
        ";
    }

    // Add the inline script
    wp_add_inline_script('widget-logic-customizer-display-script', $inline_script);
}

function widget_logic_in_customizer()
{
    global $wl_in_customizer;
    $wl_in_customizer = true;

    //add_filter( 'widget_display_callback', 'widget_logic_customizer_display_callback', 10, 3 );
    add_action('dynamic_sidebar', 'widget_logic_customizer_dynamic_sidebar_callback');
}
// This action hook allows you to enqueue assets (such as javascript files) directly in the Theme Customizer only.
add_action('customize_preview_init', 'widget_logic_in_customizer');
