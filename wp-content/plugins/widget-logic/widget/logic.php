<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly


function widget_logic_check_logic($logic)
{
    $logic = @trim((string) $logic);
    $logic = apply_filters("widget_logic_eval_override", $logic);

    if (is_bool($logic)) {
        return $logic;
    }

    if ($logic === '') {
        return true;
    }

    if (stristr($logic, 'return') === false) {
        $logic = 'return (' . html_entity_decode($logic, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES) . ');';
    }

    set_error_handler('widget_logic_error_handler');

    try {
        $show_widget = eval ($logic); // @codingStandardsIgnoreLine - widget can't work without eval
    } catch (Error $e) {
        trigger_error($e->getMessage(), E_USER_WARNING); // @codingStandardsIgnoreLine - message is not dependent on user input

        $show_widget = false;
    }

    restore_error_handler();

    return $show_widget;
}

function widget_logic_error_handler($errno, $errstr)
{
    global $wl_options;

    $show_errors = !empty($wl_options['widget_logic-options-show_errors']) && current_user_can('manage_options');

    if ($show_errors) {
        echo 'Invalid Widget Logic: ' . esc_html($errstr);
    }

    return true;
}

function widget_logic_by_id($widget_id)
{
    global $wl_options;

    if (preg_match('/^(.+)-(\d+)$/', $widget_id, $m)) {
        $widget_class = $m[1];
        $widget_i     = $m[2];

        $info = get_option('widget_' . $widget_class);
        if (empty($info[$widget_i])) {
            return '';
        }

        $info = $info[$widget_i];
    } else {
        $info = (array) get_option('widget_' . $widget_id, array());
    }

    if (isset($info['widget_logic'])) {
        $logic = $info['widget_logic'];
    } elseif (isset($wl_options[$widget_id])) {
        $logic = stripslashes($wl_options[$widget_id]);
        widget_logic_save($widget_id, $logic);

        unset($wl_options[$widget_id]);
        update_option('widget_logic', $wl_options);
    } else {
        $logic = '';
    }

    return $logic;
}

function widget_logic_save($widget_id, $logic)
{
    global $wl_options;

    if (preg_match('/^(.+)-(\d+)$/', $widget_id, $m)) {
        $widget_class = $m[1];
        $widget_i     = $m[2];

        $info = get_option('widget_' . $widget_class);
        if (!is_array($info[$widget_i])) {
            $info[$widget_i] = array();
        }

        $info[$widget_i]['widget_logic'] = $logic;
        update_option('widget_' . $widget_class, $info);
    } elseif (
        isset($_POST['widget_logic_nonce'])
        && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['widget_logic_nonce'])), 'widget_logic_save')
    ) {
        $info                 = (array) get_option('widget_' . $widget_id, array());
        $info['widget_logic'] = $logic;
        update_option('widget_' . $widget_id, $info);
    }
}
