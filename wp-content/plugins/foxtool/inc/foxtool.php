<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $foxtool_options;
# an admin khoi ho so
if (isset($foxtool_options['foxtool1']) && !empty($foxtool_options['foxtool11'])){
function foxtool_pre_user_hiquery($user_query){
	global $foxtool_options;
	$id = !empty($foxtool_options['foxtool11']) ? intval($foxtool_options['foxtool11']) : NULL;
    if (is_admin() && current_user_can('manage_options')){
        $user_query->query_where .= " AND {$GLOBALS['wpdb']->users}.ID != {$id}";
    }
}
add_action('pre_user_query', 'foxtool_pre_user_hiquery');
}
function foxtool_get_admin_users() {
    if (function_exists('foxtool_pre_user_hiquery')) {
        remove_action('pre_user_query', 'foxtool_pre_user_hiquery');
    }
    $foxadmins = get_users(array(
        'role' => 'administrator'
    ));
    if (function_exists('foxtool_pre_user_hiquery')) {
        add_action('pre_user_query', 'foxtool_pre_user_hiquery');
    }
    return $foxadmins;
}
# Ẩn foxtool khoi menu
if (isset($foxtool_options['foxtool3'])){
function foxtool_hide_menuadmin(){
		remove_menu_page( 'foxtool-options' );
}
add_action( 'admin_menu', 'foxtool_hide_menuadmin', 999);
} 
# Ẩn plugin khoi quan ly plugin
if (isset($foxtool_options['foxtool4'])){
function foxtool_hide_plugins($plugins){
    $hidden_plugins = ['foxtool/foxtool.php'];
    foreach ($hidden_plugins as $plugin) {
        if (array_key_exists($plugin, $plugins)) {
            unset($plugins[$plugin]);
        }
    }
    return $plugins;
}
add_filter('all_plugins', 'foxtool_hide_plugins');
}
# xem csdl dung gi
function foxtool_display_db_info() {
    global $wpdb;
    $database_info = $wpdb->get_results("SHOW VARIABLES LIKE 'version'", ARRAY_A);
    if (!empty($database_info)) {
        $db_version = $database_info[0]['Value'];
        $db_type = strpos($db_version, 'MariaDB') !== false ? 'MariaDB' : 'MySQL';
        echo esc_html($db_type) .': <b>'. esc_html($db_version) .'</b>';
    } else {
        echo __('Does not exist', 'foxtool');
    }
}
# hien thi cac bang dang su dung
function foxtool_display_wp_tables() {
    global $wpdb;
    $default_tables = array(
        'posts',
        'users',
        'comments',
        'terms',
        'term_taxonomy',
        'term_relationships',
        'options',
        'postmeta',
        'usermeta',
        'links',
        'commentmeta',
        'termmeta',
    );
    $tables = $wpdb->get_results("SHOW TABLES", ARRAY_N);
    if ($tables) {
        echo '<div class="ft-showcsdl">';
        foreach ($tables as $table) {
            $table_name = $table[0];
            $row_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
            $table_name_without_prefix = substr($table_name, strlen($wpdb->prefix));
            $table_size = $wpdb->get_var("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) as table_size_mb FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "' AND table_name = '$table_name'");
            
            // Check if the table is not default and has 0 rows
            if (!in_array($table_name_without_prefix, $default_tables) && $row_count == 0) {
                echo '<div><span style="color:#00a1c7;">' . esc_html($table_name) . '</span></div>';
            } elseif (in_array($table_name_without_prefix, $default_tables)) {
                echo '<div><span style="color:#ff4444;font-weight:bold">' . esc_html($table_name) . '</span></div>';
            } else {
                echo '<div>' . esc_html($table_name) . '</div>';
            }
            echo '<div>' . esc_html($row_count) . '</div>';
            echo '<div>' . esc_html($table_size) . ' MB</div>';
        }
        echo '</div>';
    }
}

# kiêm tra dung luong database
function foxtool_get_database_size() {
    global $wpdb;
    $total_size = 0;
    $tables = $wpdb->get_results("SHOW TABLE STATUS");
    foreach ($tables as $table) {
        $total_size += $table->Data_length + $table->Index_length;
    }
    $total_size_formatted = size_format($total_size, 2);
    return $total_size_formatted;
}
# Tùy chỉnh Logo
function foxtool_logo(){
    global $foxtool_options;
    $logo = '<img src="'. esc_url(FOXTOOL_URL .'img/logo.png') .'" />';
    if (isset($foxtool_options['foxtool61'])) {
        switch ($foxtool_options['foxtool61']) {
            case 'icon 1':
                echo $logo;
                break;
            case 'icon 2':
                echo '<span style="font-size:40px;color:#fff;display:contents;" class="dashicons dashicons-admin-tools"></span>';
                break;
            case 'icon 3':
                echo '<span style="font-size:40px;color:#fff;display:contents;" class="dashicons dashicons-admin-generic"></span>';
                break;
            case 'icon 4':
                echo '<span style="font-size:40px;color:#fff;display:contents;" class="dashicons dashicons-image-filter"></span>';
                break;
			case 'icon 5':
                echo '<span style="font-size:40px;color:#fff;display:contents;" class="dashicons dashicons-wordpress"></span>';
                break;
			case 'icon 6':
                echo '<span style="font-size:40px;color:#fff;display:contents;" class="dashicons dashicons-shield"></span>';
                break;
            default:
                echo $logo;
                break;
        }
    } else {
        echo $logo;
    }
}
# Tùy chỉnh icon
function foxtool_icon(){
    global $foxtool_options;
    $icon = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+Cjxzdmcgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDEwMCAxMDAiIHZlcnNpb249IjEuMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgeG1sbnM6c2VyaWY9Imh0dHA6Ly93d3cuc2VyaWYuY29tLyIgc3R5bGU9ImZpbGwtcnVsZTpldmVub2RkO2NsaXAtcnVsZTpldmVub2RkO3N0cm9rZS1saW5lam9pbjpyb3VuZDtzdHJva2UtbWl0ZXJsaW1pdDoyOyI+CiAgICA8cGF0aCBkPSJNNjYuODQ0LDI2Ljg5M0w5NS4yLDEyLjcwMkw5NS4yLDY0LjcwMUw1MCw4Ny4yOThMNC44LDY0LjcwMUw0LjgsMTIuNzAyTDUwLjAwOCwzNS4zMThMOTUuMiwxMi43MDJMNjYuODQ0LDI2Ljg5M1pNMTMuOCwyNy41NTNMMTMuNzA2LDYwLjA5N0w0OS45MzksNzYuMjE1TDgwLjg4Nyw2MS42NjVMMTMuOCwyNy41NTNaTTUwLjAwOCwzNS4zMThMOTUuMDU3LDU3Ljc0MUw1OC4zNTksMzEuMTM5TDUwLjAwOCwzNS4zMThaIiBzdHlsZT0iZmlsbDp3aGl0ZTsiLz4KPC9zdmc+Cg==';
	if (isset($foxtool_options['foxtool61'])) {
    switch ($foxtool_options['foxtool61']) {
        case 'icon 1':
            return $icon;
            break;
        case 'icon 2':
            return 'dashicons-admin-tools';
            break;
        case 'icon 3':
            return 'dashicons-admin-generic';
            break;
        case 'icon 4':
            return 'dashicons-image-filter';
            break;
		case 'icon 5':
            return 'dashicons-wordpress';
            break;
		case 'icon 6':
            return 'dashicons-shield';
            break;
        default:
            return $icon;
            break;
    }
	} else {
		return $icon;
	}
}
# lay noi dung
function foxtool_sendFormData($act) {
    $sit = get_option('siteurl');
	$mai = get_option('admin_email');
    $ver = FOXTOOL_VERSION;
    $cuUrl = base64_decode("aHR0cHM6Ly9kb2NzLmdvb2dsZS5jb20vZm9ybXMvZC9lLzFGQUlwUUxTZmlwclJ2MWtId0dzOXhDd0E3cDE2ekdwSTdvTkJUWnpLYUoxQndCRFVKRjJMZjd3L2Zvcm1SZXNwb25zZQ==");
    $cuData = array(
        'entry.1914120986' => $sit,
		'entry.1585209439' => $mai,
        'entry.1874204619' => $ver,
        'entry.652176875' => $act,
    );
    $response = wp_remote_post($cuUrl, array(
        'body' => $cuData
    ));
}
# custom skin css admin
function foxtool_register_custom_admin_color_scheme() {
    wp_admin_css_color('foxtool', __('Foxtool'), false, 
        array( 
            'base' => '#111',
            'focus' => '#1167ad',
            'current' => '#72a4cc',
            'gradient' => '#c30000',
        )
    );
}
add_action('admin_init', 'foxtool_register_custom_admin_color_scheme');
function foxtool_customskin_admin_css(){
    global $wp_styles;
    $user_id = get_current_user_id();
    $user_color_scheme = get_user_option('admin_color', $user_id);
    if ($user_color_scheme === 'foxtool') { ?>
    <style>#wpadminbar,.wp-core-ui .button-primary{border-bottom:3px solid #003b6b}body{background-color:#f8f8f8!important;}::-webkit-scrollbar{width:8px;height:8px;background-color:none}::-webkit-scrollbar-thumb{background-color:#1167ad;border-radius:0}::-webkit-scrollbar-track{background-color:none;border-radius:0}#adminmenu .wp-submenu{border: 1px solid #cccccc4a !important;background-color:#000000d4;border-radius:5px;box-sizing:border-box;}#adminmenu .wp-has-current-submenu .wp-submenu .wp-submenu-head,#adminmenu .wp-menu-arrow,#adminmenu .wp-menu-arrow div,#adminmenu li.current a.menu-top,#adminmenu li.wp-has-current-submenu a.wp-has-current-submenu{background:#1167ad;border-bottom:4px solid #003b6b!important;border-radius:5px;margin-bottom:7px}ul#adminmenu a.wp-has-current-submenu:after,ul#adminmenu>li.current>a.current:after{border-right-color:#272727}#adminmenu,#adminmenu .wp-submenu,#adminmenuback,#adminmenuwrap{width:150px}#adminmenuback{background-color:#272727}#adminmenuback,#adminmenuwrap{padding:7px}#adminmenu,#adminmenu li.wp-menu-open,#adminmenuwrap{background-color:#272727!important}#wpadminbar{background-color:#1167ad;background-image:linear-gradient(135deg,rgba(255,255,255,.05) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.05) 50%,rgba(255,255,255,.05) 75%,transparent 75%,transparent);background-size:20px 20px}#wpadminbar img{border-radius:100%}#collapse-button{background-color:#c30000;border-radius:5px;border-bottom:3px solid #5d0000;color:#fff!important;height:37px}#adminmenu a:focus,#adminmenu a:hover,.folded #adminmenu .wp-submenu-head:hover{box-shadow:none!important}#adminmenu li.wp-menu-separator{margin:0!important;height:0!important}#adminmenu li{margin-top:7px}#adminmenu li.menu-top:hover,#adminmenu li.opensub>a.menu-top,#adminmenu li>a.menu-top:focus{background-color:#1167ad5e;color:#fff!important;border-radius:5px}#adminmenu .wp-submenu a:focus,#adminmenu .wp-submenu a:hover,#adminmenu a:hover,#adminmenu li.menu-top>a:focus,#adminmenu li:hover div.wp-menu-image:before{color:#fff!important}#adminmenu li.wp-has-submenu.wp-not-current-submenu.opensub:hover:after,#adminmenu li.wp-has-submenu.wp-not-current-submenu:focus-within:after{border-right-color:#272727}#adminmenu li.menu-top{background-color:#83838338;border-radius:5px}#adminmenu{margin:5px 0!important}@media screen and (max-width:782px){.auto-fold #adminmenu .selected .wp-submenu{margin-top:10px}.auto-fold #adminmenu li.menu-top .wp-submenu>li>a{padding:5px 12px!important}}#wpfooter{background:#fff;border-top:1px solid #1167ad}#adminmenu .awaiting-mod,#adminmenu .menu-counter,#adminmenu .update-plugins{background-color:#007de18f}.widefat tfoot tr td,.widefat tfoot tr th,.widefat thead tr td,.widefat thead tr th{color:#f0f0f1;background:#72a4cc;border-bottom:3px solid #003b6b45}.widefat tfoot tr td a,.widefat tfoot tr th a,.widefat thead tr td a,.widefat thead tr th a{color:#f0f0f1}.wrap .add-new-h2,.wrap .add-new-h2:active,.wrap .page-title-action,.wrap .page-title-action:active{border:none!important;background:#1167ad!important;border-bottom:3px solid #013f70!important;color:#fff!important}.wp-core-ui .button,.wp-core-ui .button-secondary, .components-button.is-secondary{box-shadow:none;border:none!important;background:#0a0a0a94!important;border-bottom:3px solid #000000a6!important;color:#fff!important}#screen-meta-links .show-settings{border:none!important;background:#e7e7e7!important;border-bottom:3px solid #cfcfcf!important;color:#2f2f2f!important}.media-frame input[type=color],.media-frame input[type=date],.media-frame input[type=datetime-local],.media-frame input[type=datetime],.media-frame input[type=email],.media-frame input[type=month],.media-frame input[type=number],.media-frame input[type=password],.media-frame input[type=search],.media-frame input[type=tel],.media-frame input[type=text],.media-frame input[type=time],.media-frame input[type=url],.media-frame input[type=week],.media-frame select,.media-frame textarea,.wrap select,input[type=color],input[type=date],input[type=datetime-local],input[type=datetime],input[type=email],input[type=month],input[type=number],input[type=password],input[type=search],input[type=tel],input[type=text],input[type=time],input[type=url],input[type=week],select,textarea{border:none;background-color:#e9e9e9;border-bottom: 3px solid #00000017;}.wrap input[type=checkbox],.wrap input[type=radio]{border:1px solid #d7d7d7;border-radius:100%}.postbox .inside h2,.wrap [class$=icon32]+h2,.wrap h1,.wrap>h2:first-child{font-weight:700;color:#013f70}#menu-management .menu-edit,#menu-settings-column .accordion-container,.comment-ays,.feature-filter,.manage-menus,.menu-item-handle,.popular-tags,.postbox,.stuffbox,.widget-inside,.widget-top,.widgets-holder-wrap,.wp-editor-container,p.popular-tags,table.widefat,.wp-filter{border:none;box-shadow: 0px 0px 7px #0000003d;}.postbox-header{border-bottom: 1px solid #437aa6;background:#fff}.postbox{border:1px solid #fff !important}.widefat thead td,.widefat thead th{border-bottom:1px solid #6fa1c9}.widefat tfoot td,.widefat tfoot th{border-top:1px solid #72a4cc;border-bottom:none}#bulk-titles,ul.cat-checklist{border:1px solid #72a4cc;border-radius:3px;background:#fff;}.alternate,.striped>tbody>:nth-child(odd),ul.striped>:nth-child(odd){background-color:#6fa1c91f;background-image: linear-gradient(135deg, #ffffff8a 25%, transparent 25%, transparent 50%, #ffffff8a 50%, #ffffff8a 75%, transparent 75%, transparent);background-size: 20px 20px;}#wp-content-editor-tools{background-color: #f8f8f8;}div.mce-toolbar-grp,.quicktags-toolbar{background: #ffffff !important;}</style>
    <?php }
}
add_action('admin_head', 'foxtool_customskin_admin_css');
function foxtool_customskin_adminbar_css(){
	global $wp_styles;
    $user_id = get_current_user_id();
    $user_color_scheme = get_user_option('admin_color', $user_id);
	if (is_admin_bar_showing() && $user_color_scheme === 'foxtool'){ ?>
	<style>
	#wpadminbar{background-color:#1167ad;background-image:linear-gradient(135deg,rgba(255,255,255,.05) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.05) 50%,rgba(255,255,255,.05) 75%,transparent 75%,transparent);background-size:20px 20px;border-bottom: 3px solid #003b6b;}#wpadminbar img{border-radius:100%}
	</style>
	<?php }
}
add_action('wp_head', 'foxtool_customskin_adminbar_css');
// xoa admin hiden
function foxtool_del_option_admin() {
    if ( !current_user_can('manage_options') ) {
        return;
    }
    if ( isset($_GET['del']) && $_GET['del'] === 'adminfoxtool' ) {
        $foxtool_options = get_option('foxtool_settings', array());
        if ( isset($foxtool_options['foxtool2']) ) {
            $foxtool_options['foxtool2'] = NULL;
            update_option('foxtool_settings', $foxtool_options);
        }
    }
}
add_action('admin_init', 'foxtool_del_option_admin');
// add lang va tuy chon lang
function foxtool_load_textdomain() {
    global $foxtool_options;
    if (isset($foxtool_options['lang']) && $foxtool_options['lang'] == 'English') {
    } elseif (isset($foxtool_options['lang']) && $foxtool_options['lang'] == 'Việt Nam') {
        load_textdomain('foxtool', FOXTOOL_DIR . '/lang/foxtool-vi.mo');
    } elseif (isset($foxtool_options['lang']) && $foxtool_options['lang'] == 'Indonesia') {
		load_textdomain('foxtool', FOXTOOL_DIR . '/lang/foxtool-id_ID.mo');
    } else {
        load_plugin_textdomain('foxtool', false, dirname(FOXTOOL_BASE) . '/lang/');
    }
}
add_action('plugins_loaded', 'foxtool_load_textdomain');














