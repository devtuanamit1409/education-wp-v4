<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) exit;

// Register block scripts and styles.
function widget_logic_widget_enqueue_block_editor_assets()
{
    if (is_admin()) {
        $isWidgetScreen = 'widgets' === get_current_screen()->id;
        wp_enqueue_script(
            'block-widget',
            esc_url(plugins_url('/js/widget.js', __FILE__)),
            ['wp-blocks', 'wp-i18n', 'wp-element', $isWidgetScreen ? 'wp-edit-widgets' : 'wp-editor'],
            filemtime(plugin_dir_path(__FILE__) . '/js/widget.js'),
            false // Do not enqueue the script in the footer.
        );
    }
    wp_enqueue_style(
        'block-widget',
        esc_url(plugins_url('/css/widget.css', __FILE__)),
        array(),
        filemtime(plugin_dir_path(__FILE__) . '/css/widget.css')
    );
    wp_enqueue_script('widget-logic_live_match_widget');
}
add_action('enqueue_block_assets', 'widget_logic_widget_enqueue_block_editor_assets');
