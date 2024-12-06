<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $foxtool_ads_options;
# code ads click
if (isset($foxtool_ads_options['ads-click1']) && !empty($foxtool_ads_options['ads-click11'])){
function foxtool_adsclick_footer(){
    global $foxtool_ads_options;
    if (!current_user_can('administrator')){
        $mini = isset($foxtool_ads_options['ads-click-c1']) ? 'left=2000,top=2000,width=200,height=100,location=no,toolbar=no,menubar=no,scrollbars=no,resizable=no' : NULL;
        $hau = !empty($foxtool_ads_options['ads-click-c2']) ? $foxtool_ads_options['ads-click-c2'] : 24;
        $lislink = $foxtool_ads_options['ads-click11'];
        $lislink = explode("\n", str_replace("\r", "",  $lislink));
        $links = implode(",", $lislink);
        $clickTarget = isset($foxtool_ads_options['ads-click-c3']) && $foxtool_ads_options['ads-click-c3'] == 'Link' ? 'link' : 'body';
        echo '<div data-adsclick data-links="' . esc_attr($links) . '" data-mini="' . esc_attr($mini) . '" data-hours="' . esc_attr($hau) . '" data-click-target="' . esc_attr($clickTarget) . '"></div>';
    }
}
add_action('wp_footer', 'foxtool_adsclick_footer');
function foxtool_adsclick_enqueue() {
    if (!current_user_can('administrator')){
	wp_enqueue_script( 'foxtool-ads', FOXTOOL_URL . 'link/ads/foxads.js', array(), FOXTOOL_VERSION, true);
	}
}
add_action('wp_enqueue_scripts', 'foxtool_adsclick_enqueue');
}
# adsense
if (isset($foxtool_ads_options['ads-sense1'])) {
function foxtool_adsense_code(){
    global $foxtool_ads_options;
    if (!empty($foxtool_ads_options['ads-sense11'])) {
		echo $foxtool_ads_options['ads-sense11'];
	}
}
add_action('wp_head', 'foxtool_adsense_code');
// vi tri tuy chinh
function foxtool_adsense_after( $insertion, $paragraph_interval, $content, $tag = 'p' ) {
    $pattern = "/(<$tag\b[^>]*>)(.*?)(<\/$tag>)/is";
    $matches = array();
    preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE);
    $new_content = '';
    $last_pos = 0;
    foreach ($matches[0] as $index => $match) {
        $pos = $match[1];
        $length = strlen($match[0]);
        // Chèn nội dung sau mỗi nhóm $paragraph_interval thẻ
        if ( ($index + 1) % $paragraph_interval == 0 ) {
            $new_content .= substr($content, $last_pos, $pos - $last_pos) . $match[0] . $insertion;
        } else {
            $new_content .= substr($content, $last_pos, $pos - $last_pos) . $match[0];
        }
        
        $last_pos = $pos + $length;
    }
    $new_content .= substr($content, $last_pos);
    return $new_content;
}
// add vao content
function foxtool_adsense_content_custom($content) {
    global $foxtool_ads_options;
	if (isset($foxtool_ads_options['posttype']) && !empty($foxtool_ads_options['posttype'])) {
		$current_post_type = get_post_type();
        if (in_array($current_post_type, $foxtool_ads_options['posttype'])) {
			if (!empty($foxtool_ads_options['ads-sense-p1'])) {
				$content = $foxtool_ads_options['ads-sense-p1'] . $content;
			}
			if (!empty($foxtool_ads_options['ads-sense-c3'])) {
				$tag = !empty($foxtool_ads_options['ads-sense-c1']) ? sanitize_text_field($foxtool_ads_options['ads-sense-c1']) : 'p';
				$paragraph_interval = !empty($foxtool_ads_options['ads-sense-c2']) ? (int)sanitize_text_field($foxtool_ads_options['ads-sense-c2']) : 10;
				$insertion = $foxtool_ads_options['ads-sense-c3']; 
				$content = foxtool_adsense_after($insertion, $paragraph_interval, $content, $tag);
			}
			if (!empty($foxtool_ads_options['ads-sense-p2'])) {
				$content .= $foxtool_ads_options['ads-sense-p2'];
			}
		}
	}
    return $content;
}
add_filter('the_content', 'foxtool_adsense_content_custom');
}
# ads.txt
class foxtool_ads_txt {
    public function setup(): void {
        add_action( 'init', [ $this, 'add_rewrite_rules' ], 10 );
        add_action( 'init', [ $this, 'flush_rewrite_rules' ], 20 );
        add_filter( 'query_vars', [ $this, 'add_query_vars' ] );
        add_action( 'template_redirect', [ $this, 'output' ], 0 );
    }
    public function add_rewrite_rules(): void {
        // Đăng ký các rule mới
        add_rewrite_rule( '^ads\.txt$', 'index.php?ads_txt=1', 'top' );
    }
    public function flush_rewrite_rules(): void {
        global $foxtool_ads_options;
        if ( isset($foxtool_ads_options['ads-adstxt1']) ) {
            flush_rewrite_rules(); 
        }
    }
    public function add_query_vars( array $vars ): array {
        $vars[] = 'ads_txt';
        return $vars;
    }
    public function output(): void {
		global $foxtool_ads_options;
        $is_ads_txt = get_query_var( 'ads_txt' );
		$content = !empty($foxtool_ads_options['ads-adstxt2']) ? $foxtool_ads_options['ads-adstxt2'] : '';
        if (! $is_ads_txt) {
            return;
        }
        status_header( 200 );
        header( 'Content-Type: text/plain; charset=utf-8', true );
        echo $content; 
        exit;
    }
}
if (isset($foxtool_ads_options['ads-adstxt1']) ) {
	$foxtool_ads_txt = new foxtool_ads_txt();
	$foxtool_ads_txt->setup();
}

