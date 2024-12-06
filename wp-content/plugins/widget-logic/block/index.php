<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

function widget_logic_init_block()
{
    if (function_exists('wp_set_script_translations')) {
        wp_set_script_translations('widget-logic', 'widget-logic');
    }
}
add_action('init', 'widget_logic_init_block');

function widget_logic_enqueue_block_editor_assets()
{
    if (is_admin()) {
        $isWidgetScreen = 'widgets' === get_current_screen()->id;
        wp_enqueue_script(
            'widget-logic',
            esc_url(plugins_url('/js/widget-logic.js', __FILE__)),
            ['wp-blocks', 'wp-i18n', 'wp-element', $isWidgetScreen ? 'wp-edit-widgets' : 'wp-editor'],
            filemtime(plugin_dir_path(__FILE__) . '/js/widget-logic.js'),
            false // Do not enqueue the script in the footer.
        );

        wp_enqueue_style(
            'widget-logic',
            esc_url(plugins_url('/css/widget-logic.css', __FILE__)),
            array(),
            filemtime(plugin_dir_path(__FILE__) . '/css/widget-logic.css')
        );
    }
}
add_action('enqueue_block_assets', 'widget_logic_enqueue_block_editor_assets');

/**
 * This code is necessary to fix a bug related to blocks that utilize the
 * ServerSideRender component. The bug causes an error message to be displayed
 * when the attributes are only registered in JavaScript. By registering the
 * attributes in PHP as well, the bug is resolved. Hopefully, this issue will
 * be addressed and fixed in future updates.
 *
 * (https://github.com/WordPress/gutenberg/issues/16850)
 */
function widget_logic_add()
{
    if (class_exists('WP_Block_Type_Registry')) {
        $reg_blocks = WP_Block_Type_Registry::get_instance()->get_all_registered();

        foreach ($reg_blocks as $name => $block) {
            $block->attributes['widgetLogic'] = ['type' => 'string'];
        }
    }
}
add_action('wp_loaded', 'widget_logic_add', 999);

/**
 * Improve REST API compatibility for server-side rendered blocks.
 * This fix ensures that server-side rendered blocks with visibility controls
 * will load properly in the block editor.
 *
 * (https://github.com/phpbits/block-options/blob/f741344033a2c9455828d039881616f77ef109fe/includes/class-editorskit-post-meta.php#L82-L112)
 *
 * @param mixed $result The response to replace the requested version with.
 * @param object $server The server instance.
 * @param object $request The request used to generate the response.
 *
 * @return array The modified response.
 */
function widget_logic_clearing($result, $server, $request)
{
    if (
        false !== strpos($request->get_route(), '/wp/v2/block-renderer')
        && isset($request['attributes']) && isset($request['attributes']['widgetLogic'])
    ) {
        $attributes = $request['attributes'];
        unset($attributes['widgetLogic']);
        $request['attributes'] = $attributes;
    }

    return $result;
}
add_filter('rest_pre_dispatch', 'widget_logic_clearing', 10, 3);

include_once __DIR__ . '/../widget/logic.php';

function widget_logic_block_render($block_content, $block)
{
    if (!isset($block['attrs']['widgetLogic'])) {
        return $block_content;
    }

    return widget_logic_check_logic($block['attrs']['widgetLogic']) ? $block_content : '';
}
add_filter('render_block', 'widget_logic_block_render', 10, 2);
