<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

function widget_logic_sidebars_widgets_filter_add()
{
    // actually remove the widgets from the front end depending on widget logic provided
    add_filter('sidebars_widgets', 'widget_logic_filter_sidebars_widgets', 10);
}
// wp-admin/widgets.php explicitly checks current_user_can('edit_theme_options')
// which is enough security, I believe. If you think otherwise please contact me

include_once 'logic.php';

// CALLED ON 'sidebars_widgets' FILTER
function widget_logic_filter_sidebars_widgets($sidebars_widgets)
{
    global $wl_options, $wl_in_customizer;

    if ($wl_in_customizer) {
        return $sidebars_widgets;
    }

    // reset any database queries done now that we're about to make decisions based on the context given in the WP query for the page
    if (!empty($wl_options['widget_logic-options-wp_reset_query'])) {
        wp_reset_query();
    }

    // loop through every widget in every sidebar (barring 'wp_inactive_widgets') checking WL for each one
    foreach ($sidebars_widgets as $widget_area => $widget_list) {
        if ($widget_area == 'wp_inactive_widgets' || empty($widget_list)) {
            continue;
        }

        foreach ($widget_list as $pos => $widget_id) {
            $logic = widget_logic_by_id($widget_id);

            if (!widget_logic_check_logic($logic)) {
                unset($sidebars_widgets[$widget_area][$pos]);
            }
        }
    }
    return $sidebars_widgets;
}

// CALLED ON 'dynamic_sidebar_params' FILTER - this is called during 'dynamic_sidebar' just before each callback is run
// swap out the original call back and replace it with our own
function widget_logic_widget_display_callback($params)
{
    global $wp_registered_widgets;

    $id                                                 = $params[0]['widget_id'];
    $wp_registered_widgets[$id]['callback_wl_redirect'] = $wp_registered_widgets[$id]['callback'];
    $wp_registered_widgets[$id]['callback']             = 'widget_logic_redirected_callback';

    return $params;
}

// the redirection comes here
function widget_logic_redirected_callback()
{
    global $wp_registered_widgets;

    // replace the original callback data
    $params                                 = func_get_args();
    $id                                     = $params[0]['widget_id'];
    $callback                               = $wp_registered_widgets[$id]['callback_wl_redirect'];
    $wp_registered_widgets[$id]['callback'] = $callback;

    // run the callback but capture and filter the output using PHP output buffering
    if (is_callable($callback)) {
        ob_start();
        call_user_func_array($callback, $params);
        $widget_content = ob_get_contents();
        ob_end_clean();
        echo apply_filters('widget_content', $widget_content, $id); // @codingStandardsIgnoreLine - here echo content of other widget, and i don't know how to escape it
    }
}
