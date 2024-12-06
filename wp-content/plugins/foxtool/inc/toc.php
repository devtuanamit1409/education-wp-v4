<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $foxtool_toc_options;
if (isset($foxtool_toc_options['toc1'])){
# add css js chat web
function foxtool_enqueue_toc(){
	global $foxtool_toc_options;
	if (isset($foxtool_toc_options['posttype']) && !empty($foxtool_toc_options['posttype'])) {
		$current_post_type = get_post_type();
        if (in_array($current_post_type, $foxtool_toc_options['posttype'])) {
			wp_enqueue_style('ftoc-css', FOXTOOL_URL . 'link/toc/foxtoc.css', array(), FOXTOOL_VERSION);
			wp_enqueue_script('ftoc-js', FOXTOOL_URL . 'link/toc/foxtoc.js', array(), FOXTOOL_VERSION, true);
		}
			
	}
}
add_action('wp_enqueue_scripts', 'foxtool_enqueue_toc');
# functions add
function foxtool_toc_fun(){
	global $foxtool_toc_options;
	$tags = []; 
	$all_tags = ['h2', 'h3', 'h4', 'h5', 'h6']; 
	$has_checked_tags = false; 
	for ($i = 1; $i <= 6; $i++) {
		if (isset($foxtool_toc_options['tit_h' . $i]) && 1 == $foxtool_toc_options['tit_h' . $i]) {
			$tags[] = 'h' . $i; 
			$has_checked_tags = true; 
		}
	}
	if (!$has_checked_tags) {
		$tagh = implode(', ', $all_tags); 
	} else {
		$tagh = implode(', ', $tags); 
	}
	$title = !empty($foxtool_toc_options['tit-c1']) ? $foxtool_toc_options['tit-c1'] : __('Table of Contents', 'foxtool');
	$show = !empty($foxtool_toc_options['tit-c3']) ? 'ft-toc-main-open' : NULL;
	$onnumber = !isset($foxtool_toc_options['tit-c4']) ? 'data-on="on"' : NULL;
	$hiddenlist = isset($foxtool_toc_options['tit-c5']) ? 'style="display:none"' : NULL;
	$hiddenicon = isset($foxtool_toc_options['tit-c6']) ? 'data-ico="off"' : NULL;
	$dtocicon = '<svg width="100%" height="100%" viewBox="0 0 100 100" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2"><path d="M88.96,62.672l-38.96,19.477l-38.96,-19.477l-0,-44.821l38.967,19.494l38.953,-19.494l0,44.821Zm-70.163,-32.02l-0.081,28.051l31.231,13.893l26.676,-12.541l-57.826,-29.403Zm31.21,6.693l38.83,19.327l-31.632,-22.929l-7.198,3.602Z"/></svg>';
	if (isset($foxtool_toc_options['main-ico'])) {
		$tocico_option = $foxtool_toc_options['main-ico'];
		switch ($tocico_option) {
			case 'Icon2':
				$tocicoset = '<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" fill="currentColor" class="bi bi-list-task" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M2 2.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5V3a.5.5 0 0 0-.5-.5zM3 3H2v1h1z"/><path d="M5 3.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5M5.5 7a.5.5 0 0 0 0 1h9a.5.5 0 0 0 0-1zm0 4a.5.5 0 0 0 0 1h9a.5.5 0 0 0 0-1z"/><path fill-rule="evenodd" d="M1.5 7a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H2a.5.5 0 0 1-.5-.5zM2 7h1v1H2zm0 3.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm1 .5H2v1h1z"/></svg>';
				break;
			case 'Icon3':
				$tocicoset = '<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" fill="currentColor" class="bi bi-list-ol" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5"/><path d="M1.713 11.865v-.474H2c.217 0 .363-.137.363-.317 0-.185-.158-.31-.361-.31-.223 0-.367.152-.373.31h-.59c.016-.467.373-.787.986-.787.588-.002.954.291.957.703a.595.595 0 0 1-.492.594v.033a.615.615 0 0 1 .569.631c.003.533-.502.8-1.051.8-.656 0-1-.37-1.008-.794h.582c.008.178.186.306.422.309.254 0 .424-.145.422-.35-.002-.195-.155-.348-.414-.348h-.3zm-.004-4.699h-.604v-.035c0-.408.295-.844.958-.844.583 0 .96.326.96.756 0 .389-.257.617-.476.848l-.537.572v.03h1.054V9H1.143v-.395l.957-.99c.138-.142.293-.304.293-.508 0-.18-.147-.32-.342-.32a.33.33 0 0 0-.342.338zM2.564 5h-.635V2.924h-.031l-.598.42v-.567l.629-.443h.635z"/></svg>';
				break;
			case 'Icon4':
				$tocicoset = '<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" fill="currentColor" class="bi bi-list-nested" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M4.5 11.5A.5.5 0 0 1 5 11h10a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5m-2-4A.5.5 0 0 1 3 7h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m-2-4A.5.5 0 0 1 1 3h10a.5.5 0 0 1 0 1H1a.5.5 0 0 1-.5-.5"/></svg>';
				break;
			case 'Icon5':
				$tocicoset = '<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/></svg>';
				break;
			case 'Icon6':
				$tocicoset = '<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/></svg>';
				break;
			default:
				$tocicoset = $dtocicon;
		}
	} else {
		$tocicoset = $dtocicon;
	}
	
	$iconcl = '<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 1024 1024"><path fill="currentColor" d="M195.2 195.2a64 64 0 0 1 90.496 0L512 421.504L738.304 195.2a64 64 0 0 1 90.496 90.496L602.496 512L828.8 738.304a64 64 0 0 1-90.496 90.496L512 602.496L285.696 828.8a64 64 0 0 1-90.496-90.496L421.504 512L195.2 285.696a64 64 0 0 1 0-90.496"/></svg>';
	$iconhi = '<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 1024 1024"><path fill="currentColor" d="M104.704 685.248a64 64 0 0 0 90.496 0l316.8-316.8l316.8 316.8a64 64 0 0 0 90.496-90.496L557.248 232.704a64 64 0 0 0-90.496 0L104.704 594.752a64 64 0 0 0 0 90.496"/></svg>';
	$toc_html = '<div class="ft-toc-placeholder" data-h="'. $tagh .'" '. $onnumber .' '. $hiddenicon .'><div class="ft-toc-main '. $show .' ">
		<div class="ft-toc-close" onclick="tocclose();" style="display:none">'. $tocicoset .'</div>
		<div class="ft-toc-tit"><span class="ft-toc-tit-sp"><span class="ft-toc-tit-svg">'. $tocicoset .'</span><span class="ft-toc-close2" onclick="tocclose();">'. $iconcl .'</span>'. $title .'</span><span class="ft-toc-tit-hi">'. $iconhi .'</span></div>
		<div class="ft-toc-scrol">
		<ol id="ft-toc-list" '. $hiddenlist .'>
		</ol>
		</div>
	</div></div>';
	return $toc_html;	
}
# add content
function foxtool_toc( $content ) {
	global $foxtool_toc_options;
	$pages_list = explode("\n", str_replace("\r", "",  $foxtool_toc_options['toc-page-hi'] ?? ''));
	$toc_status = get_post_meta(get_the_ID(), 'toc_status', true);
	if (isset($foxtool_toc_options['posttype']) && !isset($foxtool_toc_options['tag']) && !empty($foxtool_toc_options['posttype']) && !is_page($pages_list) && $toc_status !== 'disabled') {
		$current_post_type = get_post_type();
        if (in_array($current_post_type, $foxtool_toc_options['posttype'])) {
			$settoc = !isset($foxtool_toc_options['shortcode']) ? foxtool_toc_fun() : NULL;
			return $settoc .'<div id="ft-toc">'. $content . '</div>';
		}		
	}
	return $content;
}
add_filter( 'the_content', 'foxtool_toc' );
# shortcode
function foxtool_toc_shortcode($atts) {
	global $foxtool_toc_options;
	$pages_list = explode("\n", str_replace("\r", "",  $foxtool_toc_options['toc-page-hi'] ?? ''));
	$toc_status = get_post_meta(get_the_ID(), 'toc_status', true);
	if (isset($foxtool_toc_options['posttype']) && !empty($foxtool_toc_options['posttype']) && !is_page($pages_list) && $toc_status !== 'disabled') {
		$current_post_type = get_post_type();
        if (in_array($current_post_type, $foxtool_toc_options['posttype'])) {
		$settoc = isset($foxtool_toc_options['shortcode']) ? foxtool_toc_fun() : NULL;
		return $settoc;
		}
	}
	return;
}
add_shortcode('foxtoc', 'foxtool_toc_shortcode');
# vi tri tuy bien theo the h
function foxtool_toc_after( $insertion, $paragraph_id, $content ) {
	global $foxtool_toc_options;
	$tag = !empty($foxtool_toc_options['tag1']) ? $foxtool_toc_options['tag1'] : 'h2';
    $pattern = "/(<$tag\b[^>]*>)(.*?)(<\/$tag>)/is";
    $matches = array();
    preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE);
    $new_content = '';
    $last_pos = 0;
    foreach ($matches[0] as $index => $match) {
        $pos = $match[1];
        $length = strlen($match[0]);
        if ( $paragraph_id == ($index + 1) ) {
            $new_content .= substr($content, $last_pos, $pos - $last_pos) . $insertion . $match[0];
        } else {
            $new_content .= substr($content, $last_pos, $pos - $last_pos) . $match[0];
        }
        $last_pos = $pos + $length;
    }
    $new_content .= substr($content, $last_pos);
    return $new_content;
}
// add vao content
function foxtool_add_content_toc($content) {
	global $foxtool_toc_options;
	$pages_list = explode("\n", str_replace("\r", "",  $foxtool_toc_options['toc-page-hi'] ?? ''));
	$toc_status = get_post_meta(get_the_ID(), 'toc_status', true);
	if (isset($foxtool_toc_options['posttype']) && isset($foxtool_toc_options['tag']) && !empty($foxtool_toc_options['posttype']) && !is_page($pages_list) && $toc_status !== 'disabled'){
		$current_post_type = get_post_type();
        if (in_array($current_post_type, $foxtool_toc_options['posttype'])) {
		$settoc = !isset($foxtool_toc_options['shortcode']) ? foxtool_toc_fun() : NULL;
		$pin = !empty($foxtool_toc_options['tag2']) ? $foxtool_toc_options['tag2'] : '1';
		return '<div id="ft-toc">'. foxtool_toc_after($settoc, $pin, $content) .'</div>';
		}
	}
	return $content;
}
add_filter('the_content', 'foxtool_add_content_toc');
# color
function foxtool_color_toc(){
	global $foxtool_toc_options;
	$pages_list = explode("\n", str_replace("\r", "",  $foxtool_toc_options['toc-page-hi'] ?? ''));
	$toc_status = get_post_meta(get_the_ID(), 'toc_status', true);
	if (isset($foxtool_toc_options['posttype']) && !empty($foxtool_toc_options['posttype']) && !is_page($pages_list) && $toc_status !== 'disabled') {
		$current_post_type = get_post_type();
		if (in_array($current_post_type, $foxtool_toc_options['posttype'])) {
				echo '<style>';
				if (isset($foxtool_toc_options['main-color'])){
				$bgr = !empty($foxtool_toc_options['main-c1']) ? '--tocbgr:'. $foxtool_toc_options['main-c1'] .';' : NULL;
				$bor = !empty($foxtool_toc_options['main-c2']) ? '--tocbor:'. $foxtool_toc_options['main-c2'] .';' : NULL;
				$lin = !empty($foxtool_toc_options['main-c4']) ? '--toclin:'. $foxtool_toc_options['main-c4'] .';' : NULL;
				$sec = !empty($foxtool_toc_options['main-c5']) ? '--tocsec:'. $foxtool_toc_options['main-c5'] .';' : NULL;
				
				$titback = !empty($foxtool_toc_options['main-t1']) ? '--toctitback:'. $foxtool_toc_options['main-t1'] .';' : NULL;
				$tittext = !empty($foxtool_toc_options['main-t2']) ? '--toctit:'. $foxtool_toc_options['main-t2'] .';' : NULL;
				
				$scr = !empty($foxtool_toc_options['main-c6']) ? '.ft-toc-scrol *{scrollbar-color: '. $foxtool_toc_options['main-c6'] .' #ffffff00;}.ft-toc-scrol ::-webkit-scrollbar-thumb{background-color: '. $foxtool_toc_options['main-c6'] .';}' : NULL;
				$lig = !empty($foxtool_toc_options['main-c7']) ? '--toclight:'. $foxtool_toc_options['main-c7'] .';' : NULL;
				
				$nutbgr = !empty($foxtool_toc_options['main-b1']) ? '--tocnutbgr:'. $foxtool_toc_options['main-b1'] .';' : NULL;
				$nutbor = !empty($foxtool_toc_options['main-b2']) ? '--tocnutbor:'. $foxtool_toc_options['main-b2'] .';' : NULL;
				$nutico = !empty($foxtool_toc_options['main-b3']) ? '--tocnutico:'. $foxtool_toc_options['main-b3'] .';' : NULL;
				
				$fontsize = !empty($foxtool_toc_options['main-si1']) && $foxtool_toc_options['main-si1'] != 16 ? '--tocsize:'. $foxtool_toc_options['main-si1'] .'px;' : NULL;
				
				$mradius = !empty($foxtool_toc_options['main-r1']) && $foxtool_toc_options['main-r1'] != 10 ? '--tocradius:'. $foxtool_toc_options['main-r1'] .'px;' : NULL;
				$nradius = !empty($foxtool_toc_options['main-r2']) && $foxtool_toc_options['main-r2'] != 10 ? '--tocnutradius:'. $foxtool_toc_options['main-r2'] .'px;' : NULL;
				
				echo ':root{'. $bgr . $bor . $titback . $tittext . $lin . $sec . $lig . $nutbgr . $nutbor . $nutico . $mradius . $nradius . $fontsize .'} '. $scr;
				}
				$piton = isset($foxtool_toc_options['main-her1']) && $foxtool_toc_options['main-her1'] == 'Left' ? '.ft-toc-close{right:-55px;left:unset;}.ft-toc-main.ft-toc-main-vuot.ft-toc-main-open{left:10px;right:unset;}.ft-toc-main-vuot {left:-350px;right:unset;transition:left 0.7s ease;}@media(max-width: 400px){.ft-toc-main-open{left: unset;right: 10px !important;}}' : NULL;
				$long = !empty($foxtool_toc_options['main-her2']) && $foxtool_toc_options['main-her2'] != 30 ? '.ft-toc-close {top: '. $foxtool_toc_options['main-her2'] .'%;}' : NULL;
				$bien = !empty($foxtool_toc_options['main-her3']) && isset($foxtool_toc_options['main-her1']) && $foxtool_toc_options['main-her1'] == 'Left' ? '.ft-toc-close {right: -'. $foxtool_toc_options['main-her3'] .'px;left: unset}' : '.ft-toc-close {left: -'. $foxtool_toc_options['main-her3'] .'px;}';
				echo $piton . $long . $bien;
				echo '</style>';
		}
			
	}
}
add_action('wp_footer', 'foxtool_color_toc');
# nut bat tat toc o page, post, product
function foxtool_add_toc_metabox() {
    global $foxtool_toc_options;
    if (isset($foxtool_toc_options['posttype']) && !empty($foxtool_toc_options['posttype'])) {
        $custom_post_types = $foxtool_toc_options['posttype']; 
        add_meta_box('toc_metabox', __('TOC Settings', 'foxtool'),'foxtool_render_toc_metabox', $custom_post_types,'side', 'high');
    }
}
add_action('add_meta_boxes', 'foxtool_add_toc_metabox');
function foxtool_render_toc_metabox($post) {
    $toc_status = get_post_meta($post->ID, 'toc_status', true);
    $toc_enabled = ($toc_status === 'enabled' || $toc_status === '');
    $toc_disabled = $toc_status === 'disabled';
    ?>
    <label>
        <input type="radio" name="toc_status" value="enabled" <?php checked($toc_enabled); ?> />
        <?php _e('Enabled TOC', 'foxtool'); ?>
    </label><br/>
    <label>
        <input type="radio" name="toc_status" value="disabled" <?php checked($toc_disabled); ?> />
        <?php _e('Disabled TOC', 'foxtool'); ?>
    </label>
    <?php
    wp_nonce_field('foxtool_save_toc_metabox', 'foxtool_toc_metabox_nonce');
}
function foxtool_save_toc_metabox($post_id) {
    if (!isset($_POST['foxtool_toc_metabox_nonce']) || !wp_verify_nonce($_POST['foxtool_toc_metabox_nonce'], 'foxtool_save_toc_metabox')) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (isset($_POST['toc_status'])) {
        update_post_meta($post_id, 'toc_status', sanitize_text_field($_POST['toc_status']));
    }
}
add_action('save_post', 'foxtool_save_toc_metabox');
# add rankmath
function foxtool_add_toc_rankmathseo($toc_plugins) {
    $toc_plugins['foxtool/foxtool.php'] = 'Foxtool';
    return $toc_plugins;
}
add_filter('rank_math/researches/toc_plugins', 'foxtool_add_toc_rankmathseo');
}