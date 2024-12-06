<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

include_once 'logic.php';

function widget_logic_in_widget_form($widget, $return, $instance)
{
    $logic = isset($instance['widget_logic']) ? $instance['widget_logic'] : widget_logic_by_id($widget->id);

    ?>
    <p>
        <label for="<?php echo esc_attr($widget->get_field_id('widget_logic')); ?>">
            <?php esc_html_e('Widget logic:', 'widget-logic') ?>
        </label>
        <textarea
            class="widefat"
            name="<?php echo esc_attr($widget->get_field_name('widget_logic')); ?>"
            id="<?php echo esc_attr($widget->get_field_id('widget_logic')); ?>"
        ><?php echo esc_textarea($logic) ?></textarea>
    </p>
    <?php
    return;
}


// CALLED VIA 'widget_update_callback' FILTER (ajax update of a widget)
function widget_logic_update_callback($instance, $new_instance, $old_instance, $this_widget)
{
    if (isset($new_instance['widget_logic'])) {
        $instance['widget_logic'] = $new_instance['widget_logic'];
    }

    return $instance;
}

// CALLED VIA 'sidebar_admin_setup' ACTION
// adds in the admin control per widget, but also processes import/export
function widget_logic_expand_control()
{
    global $wp_registered_widgets, $wp_registered_widget_controls, $wl_options;

    // EXPORT ALL OPTIONS
    if (
        isset($_GET['wl-options-export'])
        && isset($_GET['widget_logic_nonce'])
        && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['widget_logic_nonce'])), 'widget_logic_export')
    ) {
        header("Content-Disposition: attachment; filename=widget_logic_options.txt");
        header('Content-Type: text/plain; charset=utf-8');

        echo "[START=WIDGET LOGIC OPTIONS]\n";
        foreach ($wl_options as $id => $text) {
            echo esc_attr($id)."\t" . wp_json_encode($text) . "\n";
        }
        echo "[STOP=WIDGET LOGIC OPTIONS]";
        exit;
    }


    // IMPORT ALL OPTIONS
    if (
        isset($_POST['wl-options-import'])
        && current_user_can('administrator')
        && isset($_POST['widget_logic_nonce'])
        && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['widget_logic_nonce'])), 'widget_logic_import')
    ) {
        if (isset($_FILES['wl-options-import-file']['tmp_name'])) {
            $import = explode("\n", file_get_contents($_FILES['wl-options-import-file']['tmp_name'], false)); // @codingStandardsIgnoreLine file_get_contents used for file reading
            if (
                array_shift($import) == "[START=WIDGET LOGIC OPTIONS]"
                && array_pop($import) == "[STOP=WIDGET LOGIC OPTIONS]"
            ) {
                foreach ($import as $import_option) {
                    list($key, $value) = explode("\t", $import_option);
                    $wl_options[$key]  = json_decode($value);
                }
                $wl_options['msg'] = __('Success! Options file imported', 'widget-logic');
            } else {
                $wl_options['msg'] = __('Invalid options file', 'widget-logic');
            }

        } else {
            $wl_options['msg'] = __('No options file provided', 'widget-logic');
        }

        update_option('widget_logic', $wl_options);
        wp_redirect(admin_url('widgets.php'));
        exit;
    }

    // UPDATE OTHER WIDGET LOGIC OPTIONS
    // must update this to use http://codex.wordpress.org/Settings_API
    if (
        isset($_POST['widget_logic-options-submit'])
        && current_user_can('administrator')
        && isset($_POST['widget_logic_nonce'])
        && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['widget_logic_nonce'])), 'widget_logic_settings')
    ) {
        if (!empty($_POST['widget_logic-options-filter'])) {
            $wl_options['widget_logic-options-filter'] = true;
        } else {
            unset($wl_options['widget_logic-options-filter']);
        }

        $wl_options['widget_logic-options-wp_reset_query'] = !empty($_POST['widget_logic-options-wp_reset_query']);
        $wl_options['widget_logic-options-show_errors']    = !empty($_POST['widget_logic-options-show_errors']);
        $wl_options['widget_logic-options-load_point']     = sanitize_text_field(wp_unslash($_POST['widget_logic-options-load_point']));
    }

    update_option('widget_logic', $wl_options);
}

// CALLED VIA 'sidebar_admin_page' ACTION
// output extra HTML
// to update using http://codex.wordpress.org/Settings_API asap
function widget_logic_options_control()
{
    global $wp_registered_widget_controls, $wl_options;

    if (isset($wl_options['msg'])) {
        $isError = "OK" != substr($wl_options['msg'], 0, 2);
        $msgClass = $isError ? 'error' : 'updated';

        echo '<div id="message" class="'.esc_attr($msgClass).'"><p>Widget Logic – '.esc_html($wl_options['msg']).'</p></div>';
        unset($wl_options['msg']);
        update_option('widget_logic', $wl_options);
    }


    ?>
    <div class="wrap">
        <h2><?php esc_html_e('Widget Logic options', 'widget-logic'); ?></h2>
        <form method="POST" style="float:left; width:45%">
            <ul>
                <?php if (!empty($wl_options['widget_logic-options-filter'])): ?>
                    <li>
                        <label for="widget_logic-options-filter"
                            title="<?php esc_attr_e('Adds a new WP filter you can use in your own code. Not needed for main Widget Logic functionality.', 'widget-logic'); ?>">
                            <input
                                id="widget_logic-options-filter"
                                name="widget_logic-options-filter"
                                type="checkbox"
                                value="checked"
                                class="checkbox"
                                <?php if (!empty($wl_options['widget_logic-options-filter'])) echo "checked" ?>
                            />
                            <?php esc_html_e('Add \'widget_content\' filter', 'widget-logic'); ?>
                        </label>
                    </li>
                <?php endif ?>
                <li><label for="widget_logic-options-wp_reset_query"
                        title="<?php esc_attr_e('Resets a theme\'s custom queries before your Widget Logic is checked', 'widget-logic'); ?>">
                        <input
                            id="widget_logic-options-wp_reset_query"
                            name="widget_logic-options-wp_reset_query"
                            type="checkbox"
                            value="checked"
                            class="checkbox"
                            <?php if (!empty($wl_options['widget_logic-options-wp_reset_query'])) echo "checked" ?>
                        />
                        <?php esc_html_e('Use \'wp_reset_query\' fix', 'widget-logic'); ?>
                    </label>
                </li>
                <li><label for="widget_logic-options-load_point"
                        title="<?php esc_attr_e('Delays widget logic code being evaluated til various points in the WP loading process', 'widget-logic'); ?>"><?php esc_html_e('Load logic', 'widget-logic'); ?>
                        <select
                            id="widget_logic-options-load_point"
                            name="widget_logic-options-load_point"
                        >
                            <?php
                                $wl_load_points = array(
                                    'parse_query'       => __('after query variables set (default)', 'widget-logic'),
                                    'plugins_loaded'    => __('when plugin starts', 'widget-logic'),
                                    'after_setup_theme' => __('after theme loads', 'widget-logic'),
                                    'wp_loaded'         => __('when all PHP loaded', 'widget-logic'),
                                    'wp_head'           => __('during page header', 'widget-logic')
                                );
                                foreach ($wl_load_points as $action => $action_desc) {
                                    echo "<option value='" . esc_attr($action) . "'";
                                    if (
                                        isset($wl_options['widget_logic-options-load_point'])
                                        && $action == $wl_options['widget_logic-options-load_point']
                                    ) {
                                        echo " selected ";
                                    }
                                    echo ">" . esc_html($action_desc) . "</option>";
                                }
                            ?>
                        </select>
                    </label>
                </li>
                <li>
                    <label for="widget_logic-options-show_errors">
                        <input
                            id="widget_logic-show_errors"
                            name="widget_logic-options-show_errors"
                            type="checkbox"
                            value="1"
                            class="checkbox"
                            <?php if (!empty($wl_options['widget_logic-options-show_errors'])) echo "checked" ?>
                        />
                        <?php esc_html_e('Display logic errors to admin', 'widget-logic'); ?>
                    </label>
            </ul>

            <?php wp_nonce_field('widget_logic_settings', 'widget_logic_nonce'); ?>
            <?php submit_button(__('Save WL options', 'widget-logic'), 'button-primary', 'widget_logic-options-submit', false); ?>
        </form>
        <form method="POST" enctype="multipart/form-data" style="float:left; width:45%">
            <a
                class="submit button"
                href="<?php echo esc_url(wp_nonce_url('?wl-options-export', 'widget_logic_export', 'widget_logic_nonce')); ?>"
                title="<?php esc_attr_e('Save all WL options to a plain text config file', 'widget-logic'); ?>"
            >
                <?php esc_html_e('Export options', 'widget-logic'); ?>
            </a>
            <p>
                <?php submit_button(
                    __('Import options', 'widget-logic'),
                    'button',
                    'wl-options-import',
                    false,
                    array('title' => __('Load all WL options from a plain text config file', 'widget-logic'))
                ); ?>
                <input
                    type="file"
                    name="wl-options-import-file"
                    id="wl-options-import-file"
                    title="<?php esc_attr_e('Select file for importing', 'widget-logic'); ?>"
                />
            </p>

            <?php wp_nonce_field('widget_logic_import', 'widget_logic_nonce'); ?>
        </form>

    </div>

    <?php
}

function widget_logic_add_controls()
{
    global $wp_registered_widget_controls, $wp_registered_widgets, $wp_registered_widget_updates;

    foreach ($wp_registered_widgets as $id => $widget) {
        if (preg_match('/^(.+)-(\d+)$/', $id)) {
            continue;
        }

        if (!isset($wp_registered_widget_controls[$id])) {
            wp_register_widget_control($id, $id, 'widget_logic_extra_control', array(), $id, null);
            continue;
        }

        if (@$wp_registered_widget_controls[$id]['callback'] != 'widget_logic_extra_control') {
            $wp_registered_widget_controls[$id]['params'][] = $id;
            $wp_registered_widget_controls[$id]['params'][] = @$wp_registered_widget_controls[$id]['callback'];
            $wp_registered_widget_controls[$id]['callback'] = 'widget_logic_extra_control';

            $wp_registered_widget_updates[$id]['params'][] = $id;
            $wp_registered_widget_updates[$id]['params'][] = @$wp_registered_widget_updates[$id]['callback'];
            $wp_registered_widget_updates[$id]['callback'] = 'widget_logic_extra_control';
        }
    }
}

// added to widget functionality in 'widget_logic_expand_control' (above)
function widget_logic_extra_control()
{
    global $wp_customize;
    $args = func_get_args();

    $callback  = array_pop($args);
    $widget_id = array_pop($args);

    if (is_callable($callback)) {
        call_user_func_array($callback, $args);
    }

    if (
        isset($_POST["widget-$widget_id"]['widget_logic'])
        && current_user_can('administrator')
        && isset($_POST['widget_logic_nonce'])
        && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['widget_logic_nonce'])), 'widget_logic_save')
    ) {
        $logic = sanitize_text_field(wp_unslash($_POST["widget-$widget_id"]['widget_logic']));
        widget_logic_save($widget_id, $logic);
    } else {
        $logic = widget_logic_by_id($widget_id);
    }

    $input_id   = "widget-$widget_id-widget_logic";
    $input_name = "widget-{$widget_id}[widget_logic]";
    ?>
    <p>
        <label for="<?php echo esc_attr($input_id) ?>">
            <?php esc_html_e('Widget logic:', 'widget-logic') ?>
        </label>
        <?php if (!empty($wp_customize) && $wp_customize->is_preview()): ?>
            <textarea class="widefat" id="<?php echo esc_attr($input_id) ?>" readonly><?php echo esc_textarea($logic) ?></textarea>
            <br>
            <span class="description">
                <?php
                // Translators: %1$s is the link to widget page
                printf(esc_html__(
                        'This is a "wp register sidebar widget" and is different from regular widgets. Hence it can only be edited from the %s page.',
                        'widget-logic'
                    ),
                    sprintf(
                        '<a href="%s" target="_blank">%s</a>',
                        esc_attr(admin_url('widgets.php')),
                        esc_attr__('widgets', 'widget-logic')
                    )
                )
                ?>
            </span>
        <?php else: ?>
            <textarea
                class="widefat"
                name="<?php echo esc_attr($input_name) ?>"
                id="<?php echo esc_attr($input_id) ?>"
            ><?php echo esc_textarea($logic) ?></textarea>
        <?php endif ?>
        <?php wp_nonce_field('widget_logic_save', 'widget_logic_nonce'); ?>
    </p>
    <?php
    return true;
}
