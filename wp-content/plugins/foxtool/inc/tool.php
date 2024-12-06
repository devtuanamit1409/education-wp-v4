<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $foxtool_options;
# bật editor classic
if (isset($foxtool_options['tool-edit1'])){
add_filter('use_block_editor_for_post', '__return_false');
}
# them chuc nang cho classic
if (isset($foxtool_options['tool-edit11'])){
// them menu hang 1
function foxtool_mce_editor_buttons_mot( $buttons ) {
	if (is_admin()) {
		$alignright_index = array_search('alignright', $buttons);
		if ($alignright_index !== false) {
			array_splice($buttons, $alignright_index + 1, 0, 'unlink');
			array_splice($buttons, $alignright_index + 1, 0, 'alignjustify');
		} else {
			$buttons[] = 'alignjustify';
			$buttons[] = 'unlink';
		}
	}
    return $buttons;
}
add_filter( 'mce_buttons', 'foxtool_mce_editor_buttons_mot' );
// them menu hang 2
function foxtool_tinymce_plugin( $plugin_array ) {
	if (is_admin()) {
		$table_plugin_url = FOXTOOL_URL . 'link/tinyMCE/table/plugin.min.js';
		$plugin_array['table'] = $table_plugin_url;
		$shortcode_plugin_url = FOXTOOL_URL . 'link/tinyMCE/shortcode/plugin.min.js';
		$plugin_array['foxtool_dropbutton'] = $shortcode_plugin_url;
		$search_plugin_url = FOXTOOL_URL . 'link/tinyMCE/searchreplace/plugin.min.js';
		$plugin_array['searchreplace'] = $search_plugin_url;
		$print_plugin_url = FOXTOOL_URL . 'link/tinyMCE/print/plugin.min.js';
		$plugin_array['print'] = $print_plugin_url;
	}
    return $plugin_array;
}
add_filter( 'mce_external_plugins', 'foxtool_tinymce_plugin' );
// add
function foxtool_mce_editor_buttons_hai( $buttons ) {
	if (is_admin()) {
		$current_screen = get_current_screen();
		array_unshift( $buttons, 'fontselect' );
		array_unshift( $buttons, 'fontsizeselect' );
		if ( $current_screen->post_type == 'post' || $current_screen->post_type == 'page' ) {
		array_push( $buttons, 'foxtool_dropbutton' );
		}
		array_push( $buttons, 'separator', 'table' );
	}
    return $buttons;
}
add_filter( 'mce_buttons_2', 'foxtool_mce_editor_buttons_hai' );
// them menu hang 3
function foxtool_mce_editor_buttons_ba( $buttons ) {
	if (is_admin()) {
		$buttons[] = 'superscript';
		$buttons[] = 'subscript';
		$buttons[] = 'cut';
		$buttons[] = 'copy';
		$buttons[] = 'paste';
		$buttons[] = 'newdocument';
		$buttons[] = 'searchreplace';
		$buttons[] = 'print';
	}
	return $buttons;
}
add_filter( 'mce_buttons_3', 'foxtool_mce_editor_buttons_ba' );
// chuyen pt sang size
if ( ! function_exists( 'foxtool_mce_text_sizes' ) ) {
    function foxtool_mce_text_sizes( $initArray ){
        $initArray['fontsize_formats'] = "8px 10px 12px 14px 16px 20px 24px 28px 32px 36px 48px 60px 72px 96px";
        return $initArray;
    }
}
add_filter( 'tiny_mce_before_init', 'foxtool_mce_text_sizes', 99);
}
# them nut add classic vao phan quan lý bài viết và trang
if (isset($foxtool_options['tool-edit12']) && !isset($foxtool_options['tool-edit1'])){
function foxtool_add_classic_editor( $actions, $post){
	if ( 'trash' === $post->post_status || ! post_type_supports( $post->post_type, 'editor' ) ) {
		return $actions;
	}
	$edit_url = get_edit_post_link( $post->ID, 'raw' );
	if ( ! $edit_url ) {
		return $actions;
	}
	if ($post->post_type == 'page' || $post->post_type == 'post') {
		$edit_url = add_query_arg( 'open-classic', '', $edit_url );
		$title       = _draft_or_post_title( $post->ID );
		$edit_action = array(
			'classic' => sprintf(
				'<a href="%s" aria-label="%s">%s</a>',
				esc_url( $edit_url ),
				esc_attr( sprintf(__('Classic editing', 'foxtool'), $title) ),
				sprintf(__('Editor classic', 'foxtool')),
			),
		);
		$edit_offset = array_search( 'edit', array_keys( $actions ), true );
		array_splice( $actions, $edit_offset + 1, 0, $edit_action );
	}
	return $actions;
}
add_filter( 'page_row_actions', 'foxtool_add_classic_editor', 15, 2 );
add_filter( 'post_row_actions', 'foxtool_add_classic_editor', 15, 2 );
// nut classic o quan ly bai viet trang
function foxtool_addbutton_classic() {
    global $pagenow;
    if (($pagenow === 'edit.php' && !isset($_GET['post_type'])) || ($pagenow === 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'page') || ($pagenow === 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'post')){
		if($pagenow === 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'page'){
			$new_post_url = admin_url('post-new.php?post_type=page&open-classic');
		} else {
		$new_post_url = admin_url('post-new.php?open-classic');
		}
        echo '<script>
            jQuery(document).ready(function($) {
                var newButton = \'<a href="'. $new_post_url .'" class="page-title-action" style="margin-left:10px;">'. __('Classic Editor', 'foxtool') .'</a>\';
                $(".wrap h1").append(newButton);
            });
        </script>';
    }
}
add_action('admin_footer', 'foxtool_addbutton_classic');
// chuyen qua classic
if ( isset( $_GET['open-classic'] )) {
	add_filter( 'use_block_editor_for_post_type', '__return_false', 100 );
}
// thêm open-class sau khi luu
function foxtool_add_open_classic_query_arg() {
	if (defined('DOING_AJAX') && DOING_AJAX) {
        return;
    }
    $post_id = isset($_POST['post_ID']) ? $_POST['post_ID'] : false;
    if ($post_id) {
        $post_type = get_post_type($post_id);
        if ($post_type === 'post' || $post_type === 'page') {
			$edit_post_url = admin_url("post.php?post=$post_id&action=edit");
            $redirect_url = add_query_arg('open-classic', '1', $edit_post_url);
            wp_redirect($redirect_url);
            exit;
        }
    }
}
add_action('save_post', 'foxtool_add_open_classic_query_arg');
}
# bật widget classic
if (isset($foxtool_options['tool-widget1'])){
add_filter( 'gutenberg_use_widgets_block_editor', '__return_false' );
add_filter( 'use_widgets_block_editor', '__return_false' );
}
# chăn copy nội dung khoa tat ca cac phim
function foxtool_lockcop_scripts() {
  global $foxtool_options;
  if (isset($foxtool_options['tool-mana2']) && !current_user_can('administrator')){
  wp_enqueue_script( 'lockcop', FOXTOOL_URL . 'link/lockcop.js', array(), FOXTOOL_VERSION);
  wp_enqueue_style( 'lockcop', FOXTOOL_URL . 'link/lockcop.css', array(), FOXTOOL_VERSION);
  }
}
add_action( 'wp_enqueue_scripts', 'foxtool_lockcop_scripts' );
# copy noi dung da dat truoc
function foxtool_copyset_scripts() { 
	global $foxtool_options; 
	$text = !empty($foxtool_options['tool-mana22']) ? $foxtool_options['tool-mana22'] : __('You have successfully copied', 'foxtool');
	if (isset($foxtool_options['tool-mana21']) && !current_user_can('administrator')){
	?>
	<script>
	jQuery(document).ready(function($) {
	<?php if (isset($foxtool_options['tool-mana23'])){ ?>
		$(document).on('copy', function(e){
            var newText = "\n<?php echo $text; ?>";
            var clipboardData = e.originalEvent.clipboardData || window.clipboardData || e.clipboardData;
            var originalText = window.getSelection().toString(); 
            clipboardData.setData('text', originalText + newText);
            e.preventDefault(); 
        });
	<?php } else { ?>
		$(document).on('copy', function(e){
			e.preventDefault();
			var newText = "<?php echo $text; ?>";
			var clipboardData = e.originalEvent.clipboardData || window.clipboardData || e.clipboardData;
			clipboardData.setData('text', newText);
		});
	<?php } ?>
	});
	</script>
	<?php
	}
}
add_action( 'wp_footer', 'foxtool_copyset_scripts' );
# tắt những công cụ không cần thiết
function foxtool_remove_appwp_admin(){
	global $foxtool_options, $menu;
	if (is_array($menu)) {
		echo '<style>';
		foreach ($menu as $index => $item) {
			if (isset($foxtool_options['tool-hiden'. $index])){
				echo '#'. $item[5] .'{display:none;}';
			}
		}
		echo '</style>';
	}
}
add_action( 'admin_head', 'foxtool_remove_appwp_admin');
# tắt tự động cập nhật
function fox_dis_update_full() {
	global $foxtool_options;
	
	// Chặn cập nhật core WP
	if (isset($foxtool_options['tool-upload1'])){
		add_filter('auto_update_core', '__return_false');
		add_filter( 'pre_option_update_core', '__return_null' );
		remove_action( 'wp_version_check', 'wp_version_check' );
		remove_action( 'admin_init', '_maybe_update_core' );
		wp_clear_scheduled_hook( 'wp_version_check' );
	}

	// Chặn cập nhật gói ngôn ngữ
	if (isset($foxtool_options['tool-upload2'])){
		add_filter('auto_update_translation', '__return_false');
		add_filter( 'pre_site_transient_update_languages', '__return_null' );
		add_filter( 'site_transient_update_languages', '__return_null' );
		remove_action('load-update-core.php', 'wp_update_languages');
		remove_action('admin_init', '_maybe_update_languages');
		remove_action('wp_update_languages', 'wp_update_languages');
		wp_clear_scheduled_hook('wp_update_languages');
	}

    // Chặn cập nhật theme
	if (isset($foxtool_options['tool-upload3'])){
		add_filter('auto_update_theme', '__return_false');
		add_filter( 'pre_site_transient_update_themes', '__return_null' );
		remove_action('load-themes.php', 'wp_update_themes');
		remove_action('load-update.php', 'wp_update_themes');
		remove_action('admin_init', '_maybe_update_themes');
		remove_action('wp_update_themes', 'wp_update_themes');
		remove_action('load-update-core.php', 'wp_update_themes');
		wp_clear_scheduled_hook('wp_update_themes');
	}

    // Chặn cập nhật plugin
	if (isset($foxtool_options['tool-upload4'])){
		add_filter('auto_update_plugin', '__return_false');
		add_filter( 'pre_site_transient_update_plugins', '__return_null' );
		remove_action( 'load-plugins.php', 'wp_update_plugins' );
		remove_action( 'load-update.php', 'wp_update_plugins' );
		remove_action( 'admin_init', '_maybe_update_plugins' );
		remove_action( 'wp_update_plugins', 'wp_update_plugins' );
		remove_action( 'load-update-core.php', 'wp_update_plugins' );
		wp_clear_scheduled_hook( 'wp_update_plugins' );
	}
	
	// Loại bỏ thông báo cập nhật & bảo trì
	if (isset($foxtool_options['tool-upload5'])){
		remove_action( 'admin_notices', 'update_nag', 3 );
		remove_action( 'network_admin_notices', 'update_nag', 3 );
		remove_action( 'admin_notices', 'maintenance_nag' );
		remove_action( 'network_admin_notices', 'maintenance_nag' );
	}

    // Chặn cập nhật qua API
	if (isset($foxtool_options['tool-upload6'])){
		remove_action( 'wp_maybe_auto_update', 'wp_maybe_auto_update' );
		remove_action( 'admin_init', 'wp_maybe_auto_update' );
		remove_action( 'admin_init', 'wp_auto_update_core' );
		wp_clear_scheduled_hook( 'wp_maybe_auto_update' );
		remove_all_filters( 'plugins_api' );
	}
}
add_action('init', 'fox_dis_update_full');

# thêm tiny editor vao description
if ( isset($foxtool_options['tool-mana3'])){
function foxtool_tiny_description($tag){
    ?>
    <table class="form-table">
        <tr class="form-field">
            <th scope="row" valign="top"><label for="description"></label></th>
            <td>
                <?php
                    $settings = array('wpautop' => true, 'media_buttons' => true, 'quicktags' => true, 'textarea_rows' => '10', 'textarea_name' => 'description' );
                    wp_editor(html_entity_decode($tag->description), 'foxtool_tiny_description', $settings);
                ?>
                <br />
                <span class="description"></span>
            </td>
        </tr>
    </table>
    <?php
}
add_filter('category_edit_form_fields', 'foxtool_tiny_description');
add_filter('product_cat_edit_form_fields', 'foxtool_tiny_description');
// xoa mac dinh
function foxtool_remove_default_category_description(){
    global $current_screen;
    if ($current_screen->taxonomy == 'category' || $current_screen->taxonomy == 'product_cat') {
    echo '<style>textarea#description{display:none}</style>';
    }
}
add_action('admin_head', 'foxtool_remove_default_category_description');
// xoa loc html khi luu
remove_filter('pre_term_description', 'wp_filter_kses');
remove_filter('term_description', 'wp_kses_data');
}
