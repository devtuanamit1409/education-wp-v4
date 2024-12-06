<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
# tai font len wp
// dương dan
function foxtool_font_upload_dir($dir){
    return array(
        'path' => $dir['basedir'] . '/foxtool-fonts',
        'url' => $dir['baseurl'] . '/foxtool-fonts',
        'subdir' => '/foxtool-fonts',
    ) + $dir;
}
// cho phep tai file font
function foxtool_engine_font_filetypes( $data, $file, $filename, $mimes, $real_mime ) {
	if ( ! empty( $data['ext'] ) && ! empty( $data['type'] ) ) {
	return $data;
	}
	$wp_file_type = wp_check_filetype( $filename, $mimes );
	if ( 'woff2' === $wp_file_type['ext'] ) {
	$data['ext'] = 'woff2';
	$data['type'] = 'font/woff2';
	}
	if ( 'ttf' === $wp_file_type['ext'] ) {
	$data['ext'] = 'ttf';
	$data['type'] = 'font/ttf';
	}
	if ( 'otf' === $wp_file_type['ext'] ) {
	$data['ext'] = 'otf';
	$data['type'] = 'font/otf';
	}
	if ( 'off' === $wp_file_type['ext'] ) {
	$data['ext'] = 'off';
	$data['type'] = 'font/off';
	}
	return $data;
	}
add_filter( 'wp_check_filetype_and_ext', 'foxtool_engine_font_filetypes', 10, 5 );
function foxtool_allow_custom_mime_types( $mimes ) {
    $mimes['ttf'] = 'font/ttf';   
    $mimes['woff2'] = 'font/woff2'; 
    $mimes['otf'] = 'font/otf';   
    $mimes['off'] = 'font/off';    
    return $mimes;
}
add_filter( 'upload_mimes', 'foxtool_allow_custom_mime_types' );
// Upload Font
function foxtool_upload_font($file){
    if (!isset($_POST['foxtool_font_name'])) {
        return;
    }
	// Kiểm tra loại file được tải lên
    $allowed_formats = array('woff2', 'ttf', 'otf', 'off');
    $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    if (!in_array(strtolower($file_ext), $allowed_formats)) {
        return;
    }
    $font_name = $_POST['foxtool_font_name'];
    $upload_dir = wp_upload_dir();
    $custom_dir = $upload_dir['basedir'] . '/foxtool-fonts';
    if (!file_exists($custom_dir)) {
        wp_mkdir_p($custom_dir);
    }
    if (!function_exists('wp_handle_upload')) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
    }
    add_filter('upload_dir', 'foxtool_font_upload_dir');
    $upload_overrides = array('test_form' => false);
    $movefile = wp_handle_upload($file, $upload_overrides, $custom_dir);
    remove_filter('upload_dir', 'foxtool_font_upload_dir');
    if ($movefile && !isset($movefile['error'])) {
        foxtool_save_font_entry_to_db($font_name, $movefile['url'], $movefile['file']);
        foxtool_overwrite_font_style($custom_dir);
        echo "1";
    } else {
        echo $movefile['error'];
    }
}
// Overwrite Font File
function foxtool_overwrite_font_style($upload_dir){
    ob_start();
    $fontsData = foxtool_get_uploaded_font_data();
    if (!empty($fontsData)):
        foreach ($fontsData as $key => $fontData): ?>
	@font-face {
	    font-family: '<?php echo esc_html($fontData['font_name']) ?>';
	    src: <?php if (file_exists($fontData['font_url'])) {?>url('<?php echo wp_make_link_relative($fontData['font_url']) ?>'),<?php }?>
	    url('<?php echo wp_make_link_relative($fontData['font_url']) ?>') format("truetype");
	}
	.font-demo.<?php echo esc_html($fontData['font_name']) ?>{
		font-family: '<?php echo esc_html($fontData['font_name']) ?>', sans-serif  !important;
	}
	<?php endforeach;
    endif;
    $content = ob_get_contents();
    file_put_contents($upload_dir . '/foxtool-fonts.css', $content);
    ob_end_clean();
}
// get font data
function foxtool_get_uploaded_font_data(){
    $fontsRawData = get_option('foxtool_font_settings');
    return json_decode($fontsRawData, true);
}
// get ver data
function foxtool_fver(){
    $fontsData = foxtool_get_uploaded_font_data();
    $latestKey = null;
    if (!empty($fontsData)){
        foreach ($fontsData as $key => $fontData){
            $latestKey = $key;
        }
    }
    return $latestKey;
}
// sever link font data
function foxtool_save_font_entry_to_db($font_name, $font_url, $font_path){
    $fontsData = foxtool_get_uploaded_font_data();
    if (empty($fontsData)):
        $fontsData = array();
    endif;
    $fontArrayKey = date('ymdhis');
    $fontsData[$fontArrayKey] = array(
        'font_name' => sanitize_title($font_name),
        'font_url' => $font_url,
        'font_path' => $font_path,
    );
    $updateFontData = json_encode($fontsData);
    update_option('foxtool_font_settings', $updateFontData);
}
// delete font
function foxtool_delete_font() {
    $upload_dir = wp_upload_dir();
    $custom_dir = $upload_dir['basedir'] . '/foxtool-fonts';
    $fontsData = foxtool_get_uploaded_font_data();
    $key_to_delete = sanitize_key($_GET['delete_font_key']);
    if (isset($fontsData[$key_to_delete])) {
        $font_path = realpath($fontsData[$key_to_delete]['font_path']);
        if ($font_path && is_file($font_path)) {
            if (@unlink($font_path)) {
                $return['status'] = __('Deleted successfully', 'foxtool');
            } else {
                $return['status'] = __('Failed to delete the file', 'foxtool');
            }
        } else {
            $return['status'] = __('The file path is invalid or not a file', 'foxtool');
        }
        unset($fontsData[$key_to_delete]);
        $updateFontData = json_encode($fontsData);
        update_option('foxtool_font_settings', $updateFontData);
    } else {
        $return['status'] = __('Font does not exist', 'foxtool');
    }
    foxtool_overwrite_font_style($custom_dir);
    return $return;
}
// ajax upload file font
function foxtool_upload_fonts(){
	check_ajax_referer('foxtool_font_nonce', 'security');
	if (!current_user_can('manage_options')){
        wp_die(__('Insufficient permissions', 'foxtool'));
    }
    $file = $_FILES["foxtool_upload_file"];
    $result = foxtool_upload_font($file);
    echo $result;
    die();
}
add_action('wp_ajax_foxtool_upload_fonts', 'foxtool_upload_fonts');
add_action('wp_ajax_nopriv_foxtool_upload_fonts', 'foxtool_upload_fonts');
// add css admin
function foxtool_font_customize_enqueue() {
	$upload_dir = wp_get_upload_dir();
    $font_css_file = $upload_dir['basedir'] . '/foxtool-fonts/foxtool-fonts.css';
    if (file_exists($font_css_file) && filesize($font_css_file) > 0) {
	wp_enqueue_style('foxtool-fonts', wp_make_link_relative(wp_get_upload_dir()['baseurl']. '/foxtool-fonts/foxtool-fonts.css'), array(), foxtool_fver());
	}
}
add_action( 'admin_head', 'foxtool_font_customize_enqueue' );
add_action('wp_enqueue_scripts', 'foxtool_font_customize_enqueue', 101);
# set font tren web
function foxtool_font_sethead() {
	global $foxtool_fontset_options;
	$fontsData = foxtool_get_uploaded_font_data();
	if (!empty($fontsData)){
	$p_contents = array(
	'body div',
	'body p',
	'body a',
	'body span',
	'body button',
	'body input',
	'body textarea',
	'body select',
	'body h1',
	'body h2',
	'body h3',
	'body h4',
	'body h5',
	'bdy h6',
	);
	echo '<style>body #wpadminbar a, body #wpadminbar{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI,Roboto,Oxygen-Sans,Ubuntu,Cantarell, Helvetica Neue",sans-serif !important}';
	for ($i = 1; $i <= 14; $i++) { 
		if(isset($foxtool_fontset_options['font' . $i]) && $foxtool_fontset_options['font' . $i] !== 'Default'){
			echo $p_contents[$i - 1] ."{ font-family: 'Dashicons', ". $foxtool_fontset_options['font' . $i] ." !important;}";
		}
	}
	echo '</style>';
	}
}
add_action( 'wp_head', 'foxtool_font_sethead' );
// add font to editor
function foxtool_custom_fonts($init) {
    $fontsData = foxtool_get_uploaded_font_data();
    $custom_fonts = '';
    if (!empty($fontsData)) {
        foreach ($fontsData as $fontData) {
            $custom_fonts .= $fontData['font_name'] . '=' . $fontData['font_name'] . ', sans-serif;';
        }
    }
    $theme_advanced_fonts = "Andale Mono=andale mono,times;" .
                            "Arial=arial,helvetica,sans-serif;" .
                            "Arial Black=arial black,avant garde;" .
                            "Book Antiqua=book antiqua,palatino;" .
                            "Comic Sans MS=comic sans ms,sans-serif;" .
                            "Courier New=courier new,courier;" .
                            "Georgia=georgia,palatino;" .
                            "Helvetica=helvetica;" .
                            "Impact=impact,chicago;" .
                            "Symbol=symbol;" .
                            "Tahoma=tahoma,arial,helvetica,sans-serif;" .
                            "Terminal=terminal,monaco;" .
                            "Times New Roman=times new roman,times;" .
                            "Trebuchet MS=trebuchet ms,geneva;" .
                            "Verdana=verdana,geneva;" .
                            "Webdings=webdings;" .
                            "Wingdings=wingdings,zapf dingbats";
    if (!empty($custom_fonts)) {
        $theme_advanced_fonts .= ';' . $custom_fonts;
    }
    $init['font_formats'] = $theme_advanced_fonts;
    return $init;
}
add_filter('tiny_mce_before_init', 'foxtool_custom_fonts');
