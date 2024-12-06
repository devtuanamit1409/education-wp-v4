<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}
delete_option('foxtool_settings');
delete_option('foxtool_code_settings');
delete_option('foxtool_extend_settings');
delete_option('foxtool_fontset_settings');
delete_option('foxtool_redirects_settings');
delete_option('foxtool_gindex_settings');
delete_option('foxtool_toc_settings');
delete_option('foxtool_ads_settings');
delete_option('foxtool_notify_settings');
delete_option('foxtool_shortcode_settings');
delete_option('foxtool_search_settings');
delete_option('foxtool_debug_settings');
