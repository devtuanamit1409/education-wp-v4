<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
# them Global
$foxtool_options = get_option('foxtool_settings');
$foxtool_code_options = get_option('foxtool_code_settings');
$foxtool_extend_options = get_option('foxtool_extend_settings');
$foxtool_fontset_options = get_option('foxtool_fontset_settings');
$foxtool_redirects_options = get_option('foxtool_redirects_settings');
$foxtool_gindex_options = get_option('foxtool_gindex_settings');
$foxtool_toc_options = get_option('foxtool_toc_settings');
$foxtool_ads_options = get_option('foxtool_ads_settings');
$foxtool_notify_options = get_option('foxtool_notify_settings');
$foxtool_shortcode_options = get_option('foxtool_shortcode_settings');
$foxtool_search_options = get_option('foxtool_search_settings');
$foxtool_debug_options = get_option('foxtool_debug_settings');
# main
function foxtool_load_admin_files() {
    global $foxtool_options, $foxtool_extend_options;
    $optional_files = array(
        'code'      => 'main/code.php',
        'clean'     => 'main/clean.php',
        'font'      => 'main/font.php',
        'redirect'  => 'main/redirects.php',
        'index'     => 'main/gindex.php',
        'toc'       => 'main/toc.php',
        'ads'       => 'main/ads.php',
        'notify'    => 'main/notify.php',
        'shortcode' => 'main/shortcode.php',
        'search'    => 'main/search.php',
        'debug'     => 'main/debug.php',
        'export'    => 'main/export.php',
    );
    $files_to_include = array(
        'main/admin.php',
        'main/extend.php',
    );
    foreach ($optional_files as $key => $file) {
        if (isset($foxtool_extend_options[$key])) {
            $files_to_include[] = $file;
        }
    }
    $files_to_include[] = 'main/about.php';
    if (isset($foxtool_options['foxtool2']) && !empty($foxtool_options['foxtool21'])) {
        $admin_id = get_current_user_id();
        $allowed_id = $foxtool_options['foxtool21'];
        if (is_admin() && current_user_can('manage_options') && $admin_id == $allowed_id) {
            foxtool_include_files($files_to_include);
        }
    } else {
        foxtool_include_files($files_to_include);
    }
}
function foxtool_include_files($files) {
    foreach ($files as $file) {
        include(FOXTOOL_DIR . $file);
    }
}
add_action('init', 'foxtool_load_admin_files');
# nhieu tinh nang
$foxtool_extend_files = array(
    'clean'     => 'inc/clean.php',
    'font'      => 'inc/font.php',
    'redirect'  => 'inc/redirects.php',
    'ads'       => 'inc/ads.php',
    'notify'    => 'inc/notify.php',
    'shortcode' => 'inc/shortcode.php',
    'search'    => 'inc/search.php',
    'debug'     => 'inc/debug.php',
    'index'     => 'inc/gindex.php',
    'toc'       => 'inc/toc.php',
);
$foxtool_option_files = array(
    'speed'   => 'inc/speed.php',
    'scuri'   => 'inc/scuri.php',
    'tool'    => 'inc/tool.php',
    'main'    => 'inc/main.php',
    'media'   => 'inc/media.php',
    'post'    => 'inc/post.php',
    'mail'    => 'inc/mail.php',
    'woo'     => 'inc/woo.php',
    'user'    => 'inc/user.php',
    'custom'  => 'inc/custom.php',
    'goo'     => 'inc/goo.php',
    'chat'    => 'inc/chat.php',
);
if (isset($foxtool_extend_options) && is_array($foxtool_extend_options)) {
    foreach ($foxtool_extend_files as $option_key => $file_path) {
        if (isset($foxtool_extend_options[$option_key])) {
            include(FOXTOOL_DIR . $file_path);
        }
    }
}
if (isset($foxtool_options) && is_array($foxtool_options)) {
    foreach ($foxtool_option_files as $option_key => $file_path) {
        if (isset($foxtool_options[$option_key])) {
            include(FOXTOOL_DIR . $file_path);
        }
    }
}
