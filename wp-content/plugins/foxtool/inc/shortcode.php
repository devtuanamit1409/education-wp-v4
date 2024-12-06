<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } 
global $foxtool_shortcode_options;
# shortcode an noi dung theo nhom
if (isset($foxtool_shortcode_options['shortcode-s1'])){
function foxtool_content_pro($atts, $content = null) {
    global $foxtool_shortcode_options;
    $roleuser = !empty($foxtool_shortcode_options['shortcode-s11']) ? $foxtool_shortcode_options['shortcode-s11'] : 'subscriber';
    $locked_content = !empty($foxtool_shortcode_options['shortcode-s12']) ? $foxtool_shortcode_options['shortcode-s12'] : __('This content is locked! You need to log in to view', 'foxtool');
    if (current_user_can($roleuser) || current_user_can('administrator')) {
        return '<div>'. do_shortcode($content) .'</div>';
    } else {
        return '<div class="ft-vip">' . $locked_content . '</div><style>.ft-vip{box-sizing: border-box;background: #ffb9905c;border: 1px solid #ff7829bf;padding: 30px;border-radius: 5px;font-weight: bold;color: #e35602}</style>';
    }
}
add_shortcode('vip', 'foxtool_content_pro');
}
# shortcode chữ ký
if (isset($foxtool_shortcode_options['shortcode-s2'])){
function foxtool_sign_shortcode(){
	global $foxtool_shortcode_options;
	$shortcode_s21 = !empty($foxtool_shortcode_options['shortcode-s21']) ? $foxtool_shortcode_options['shortcode-s21'] : '';
    return '<div>'. do_shortcode(wpautop($shortcode_s21)) .'</div>'; 
}
add_shortcode('sign', 'foxtool_sign_shortcode');
}
# shortcode titday
if (isset($foxtool_shortcode_options['shortcode-s3'])){
// titday
function foxtool_dateday_shortcode(){
	global $foxtool_shortcode_options;
	if(isset($foxtool_shortcode_options['shortcode-s31']) && $foxtool_shortcode_options['shortcode-s31'] == 'EN'){
	$date = date_i18n('Y/m/d');	
	} else {
    $date = date_i18n('d/m/Y');
	}
    return $date;
}
add_shortcode('titday', 'foxtool_dateday_shortcode');
// titmoth
function foxtool_datemonth_shortcode(){
	global $foxtool_shortcode_options;
	if(isset($foxtool_shortcode_options['shortcode-s31']) && $foxtool_shortcode_options['shortcode-s31'] == 'EN'){
	$date = date_i18n('Y/m');	
	} else {
    $date = date_i18n('m/Y');
	}
    return $date;
}
add_shortcode('titmonth', 'foxtool_datemonth_shortcode');
// tityear
function foxtool_dateyear_shortcode(){
    $date = date_i18n('Y');
    return $date;
}
add_shortcode('tityear', 'foxtool_dateyear_shortcode');
}
# shortcode gget
if (isset($foxtool_shortcode_options['shortcode-s4'])){
function foxtool_gget_shortcode($atts, $content = null) {
	global $foxtool_shortcode_options;
	$time = !empty($foxtool_shortcode_options['shortcode-s41']) ? $foxtool_shortcode_options['shortcode-s41'] : '10';
	$win = isset($foxtool_shortcode_options['shortcode-s4a']) ? 'true' : 'false';
    $atts = shortcode_atts(
        array(
            'url' => '', 
			'aff' => 'javascript:void(0);', 
            'timer' => $time,     
            'window' => $win,  
        ),
        $atts,
        'gget'
    );
    if (empty($content)) {
        $content = __('Download', 'foxtool');
    }
	$target_attr = !empty($atts['aff']) && $atts['aff'] !== 'javascript:void(0);' ? ' target="_blank"' : '';
    ob_start();
    ?>
    <div class="foxggetpro" data-secon="<?php _e('Please wait', 'foxtool'); ?>" data-next="<?php _e('Continue', 'foxtool'); ?>">
        <a class="foxgget foxgetskin"
		   href="<?php echo esc_attr($atts['aff']); ?>"
		   <?php echo $target_attr; ?>
           data-timer="<?php echo esc_attr($atts['timer']); ?>" 
           data-link="<?php echo base64_encode($atts['url']); ?>"  
           data-window="<?php echo esc_attr($atts['window']); ?>">
           <span class="ggettext"><svg xmlns="http://www.w3.org/2000/svg" width="25px" height="25px" viewBox="0 0 512 512"><path fill="currentColor" d="M376 160H272v153.37l52.69-52.68a16 16 0 0 1 22.62 22.62l-80 80a16 16 0 0 1-22.62 0l-80-80a16 16 0 0 1 22.62-22.62L240 313.37V160H136a56.06 56.06 0 0 0-56 56v208a56.06 56.06 0 0 0 56 56h240a56.06 56.06 0 0 0 56-56V216a56.06 56.06 0 0 0-56-56ZM272 48a16 16 0 0 0-32 0v112h32Z"/></svg> <?php echo esc_html($content); ?></span>
        </a>
    </div>
    <?php
    $output = ob_get_clean();
    return $output;
}
add_shortcode('gget', 'foxtool_gget_shortcode');
function foxtool_gget_enqueue() {
	wp_enqueue_style( 'foxgget', FOXTOOL_URL . 'link/shortcode/foxgget.css', array(), FOXTOOL_VERSION);
	wp_enqueue_script( 'foxgget', FOXTOOL_URL . 'link/shortcode/foxgget.js', array(), FOXTOOL_VERSION, true);
}
add_action('wp_enqueue_scripts', 'foxtool_gget_enqueue');
function foxtool_gget_shortcode_css(){
	global $foxtool_shortcode_options;
	$colorbg = !empty($foxtool_shortcode_options['shortcode-s42']) ? '--ggetcolor:'. $foxtool_shortcode_options['shortcode-s42'] .';' : NULL;
	$colorbo = !empty($foxtool_shortcode_options['shortcode-s43']) && !empty($foxtool_shortcode_options['shortcode-s44']) ? 'a.foxgget.foxgetskin {border-bottom:'. $foxtool_shortcode_options['shortcode-s44'] .'px solid '. $foxtool_shortcode_options['shortcode-s43'] .' !important;}' : NULL;
	$borderru = !empty($foxtool_shortcode_options['shortcode-s45']) ? '--ggetborra:'. $foxtool_shortcode_options['shortcode-s45'] .'px;' : NULL;
	$center = isset($foxtool_shortcode_options['shortcode-s4b']) ? '.foxggetpro {text-align:center !important;}' : NULL;
	echo '<style>:root{'. $colorbg . $borderru .'}'. $colorbo . $center .'</style>';
}
add_action('wp_head', 'foxtool_gget_shortcode_css');
}

