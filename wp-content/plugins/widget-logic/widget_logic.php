<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

/*
Plugin Name: Widget Logic
Description: Control widgets with WP's conditional tags is_home etc, for Enable Gutenberg widgets and new built-in widgets visit the settings page.
Version:     6.0.4
Author:      Widget Logic
Author URI:  https://widgetlogic.org
Text Domain: widget-logic
License:     GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

include_once 'WidgetLogicAdminConfig.php';
$cfg = WidgetLogicAdminConfig::getInstance();

// Add a link to the settings page in the plugin description area
add_filter('plugin_action_links_'.plugin_basename(__FILE__), array($cfg, 'addDescriptionSettingsLink'));

if ($cfg->isFullyEnabled()) {
    if (version_compare(get_bloginfo('version'), '5.0', '>=')) {
        include_once 'block/index.php';
    }

    include_once 'widget.php';
}

include_once 'widget/init.php';

global $wl_options, $wl_in_customizer;
$wl_in_customizer = false;


if ((!$wl_options = get_option('widget_logic')) || !is_array($wl_options)) {
    $wl_options = array();
}

if (is_admin()) {
    include_once 'widget/admin.php';

    add_filter('in_widget_form', 'widget_logic_in_widget_form', 10, 3);
    add_filter('widget_update_callback', 'widget_logic_update_callback', 10, 4);

    add_action('sidebar_admin_setup', 'widget_logic_expand_control');
    // before any HTML output save widget changes and add controls to each widget on the widget admin page
    add_action('sidebar_admin_page', 'widget_logic_options_control');

    add_action('widgets_init', 'widget_logic_add_controls', 999);
} else {
    include_once 'widget/config.php';

    $loadpoint = isset($wl_options['widget_logic-options-load_point'])
        ? (string) @$wl_options['widget_logic-options-load_point']
        : ''
    ;

    if ('plugins_loaded' == $loadpoint) {
        widget_logic_sidebars_widgets_filter_add();
    } else {
        if (!in_array($loadpoint, array('after_setup_theme', 'wp_loaded', 'wp_head'))) {
            $loadpoint = 'parse_query';
        }

        add_action($loadpoint, 'widget_logic_sidebars_widgets_filter_add');
    }

    if (!empty($wl_options['widget_logic-options-filter'])) {
        add_filter('dynamic_sidebar_params', 'widget_logic_widget_display_callback', 10);
        // redirect the widget callback so the output can be buffered and filtered
    }
}
