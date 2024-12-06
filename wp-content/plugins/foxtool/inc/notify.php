<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } 
global $foxtool_notify_options;
# phát hiện chặn quảng cáo
if(isset($foxtool_notify_options['notify-block1'])){
function foxtool_blocker_assets(){
	if(!is_user_logged_in()){
	wp_enqueue_style( 'foxblocker', FOXTOOL_URL . 'link/notify/foxblocker.css', array(), FOXTOOL_VERSION);
	wp_enqueue_script( 'foxblocker', FOXTOOL_URL . 'link/notify/foxblocker.js', array('jquery'), FOXTOOL_VERSION);
	}
}
add_action('wp_enqueue_scripts', 'foxtool_blocker_assets');
// ads fake
function foxtool_blocker_adsfake(){
	if(!is_user_logged_in()){
	echo '<div style="display:none" id="googleads"><div class="ads ad adsbox doubleclick ad-placement carbon-ads" style="background-color:red;height:300px;width:100%;"><!-- Quang cao -->Google Ads, Facebook Ads Google Adsense<!-- Ads --></div></div>';
	}
}
add_action('wp_head', 'foxtool_blocker_adsfake');
// add footer
function foxtool_blocker_footer(){ 
	global $foxtool_notify_options;
	if(!is_user_logged_in()){
		$title = !empty($foxtool_notify_options['notify-block12']) ? $foxtool_notify_options['notify-block12'] : __('Ad-blocker detection', 'foxtool');
		$content = !empty($foxtool_notify_options['notify-block13']) ? $foxtool_notify_options['notify-block13'] : __('Please disable ad-blocker on your browser to access and view the content', 'foxtool');
		$botbg = !empty($foxtool_notify_options['notify-block-c1']) ? 'background:'. $foxtool_notify_options['notify-block-c1'] .';' : NULL;
		$botbor = !empty($foxtool_notify_options['notify-block-c2']) ? 'border-bottom: 6px solid '. $foxtool_notify_options['notify-block-c2'] .';' : NULL;
		$setlock = isset($foxtool_notify_options['notify-block11']);
		?>
		<div id="ft-blocker" class="ft-blocker" style="display:none">
			<div class="ft-blocker-box">
				<div class="ft-blocker-card" id="ft-blockid" data-enabled="<?php echo json_encode($setlock); ?>">
					<p class="ft-blocker-tit"><?php echo $title; ?></p>
					<div class="ft-blocker-cont"><?php echo $content; ?></div>
					<?php if(isset($foxtool_notify_options['notify-block11'])){ ?>
					<div><span style="<?php echo $botbg . $botbor; ?>" id="ft-blocker-clo"><?php _e('Agree', 'foxtool') ?></span></div>
					<?php } ?>
				</div>
			</div>
		</div>
		<?php
	}
}
add_action('wp_footer', 'foxtool_blocker_footer');
}
# thong bao
if(isset($foxtool_notify_options['notify-notis1'])){
function foxtool_notic_footer(){ 
	global $foxtool_notify_options;
	$content = !empty($foxtool_notify_options['notify-notis11']) ? $foxtool_notify_options['notify-notis11'] : __('You havent added content yet', 'foxtool');
	$colorbg = !empty($foxtool_notify_options['notify-notis-c1']) ? 'style="background-color:'. $foxtool_notify_options['notify-notis-c1']. ';"' : NULL;
	?>
	<div class="noti-info noti-message" id="noti-message" <?php echo $colorbg; ?>>
	<div class="fix-message noti-message-box">
		<div class="noti-message-1"><?php echo $content; ?></div>
		<div class="noti-message-2"><button onclick="ftnone(event, 'noti-message')">&#215;</button></div>
	</div>
	</div>
	<style> .noti-message {background-size: 40px 40px;background-image: linear-gradient(135deg, rgba(255, 255, 255, .05) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .05) 50%, rgba(255, 255, 255, .05) 75%, transparent 75%, transparent);width: 100%;border: none;color: #fff;padding: 20px 0px;position: fixed;top:0;text-shadow: 0 1px 0 rgba(0,0,0,.5);-webkit-animation: animate-ms 2s linear infinite;-moz-animation: animate-ms 2s linear infinite;z-index:9999;box-sizing: border-box;}.fix-message{max-width:1200px;margin:auto;padding: 0px 20px;}.noti-message a{color: #fff444;display: inline-block;}.noti-message-box{display:flex;}.noti-message-1{width:100%;animation: ani 1.5s;font-size: 16px;}@keyframes ani{from{filter: blur(5px);opacity: 0;}to{letter-spacing: 0;filter: blur(0);opacity: 1px;}}@keyframes thongbao-top {0% {transform: translate3d(0, 0, 0) scale(1);}33.3333% {transform: translate3d(0, 0, 0) scale(0.5);}66.6666% {transform: translate3d(0, 0, 0) scale(1);}100% {transform: translate3d(0, 0, 0) scale(1);}0% {box-shadow: 0 0 0 0px #fff,0 0 0 0px #fff;}50% {transform: scale(0.98);}100% {box-shadow: 0 0 0 15px rgba(0,210,255,0),0 0 0 30px rgba(0,210,255,0);}}.noti-message-2{width:30px;margin-left:10px;display: flex;align-items: center;}.noti-message-2 button {min-height:0px;min-width:0px;padding: 0px;width: 30px;height: 30px;display: flex;align-items: center;justify-content: center;font-size: 16px;background: #ffffff29;color: #fff;border-radius: 100%;animation: thongbao-top 1000ms infinite;margin:0px;border:none}.noti-info {background-color: #4ea5cd;}@-webkit-keyframes animate-ms {from {background-position: 0 0;}to {background-position: -80px 0;}}@-moz-keyframes animate-ms {from {background-position: 0 0;}to {background-position: -80px 0;}}</style>
	<?php
}
add_action('wp_footer', 'foxtool_notic_footer');	
}
# cookie
if(isset($foxtool_notify_options['notify-cookie1'])){
function foxtool_cookie_assets(){
	wp_enqueue_style( 'foxcookie', FOXTOOL_URL . 'link/notify/foxcookie.css', array(), FOXTOOL_VERSION);
	wp_enqueue_script( 'foxcookie', FOXTOOL_URL . 'link/notify/foxcookie.js', array(), FOXTOOL_VERSION, true);
}
add_action('wp_enqueue_scripts', 'foxtool_cookie_assets');
function foxtool_cookie_footer(){ 
	global $foxtool_notify_options;
	$title = !empty($foxtool_notify_options['notify-cookie11']) ? $foxtool_notify_options['notify-cookie11'] : __('Cookies', 'foxtool');
	$content = !empty($foxtool_notify_options['notify-cookie12']) ? $foxtool_notify_options['notify-cookie12'] : __('This website uses cookies to ensure you get the best experience on our site. By continuing to browse, you agree to our use of cookies. For more information, please read our Cookie Policy', 'foxtool');
	$colortit = !empty($foxtool_notify_options['notify-cookie-c1']) ? '--cookiecolor:'. $foxtool_notify_options['notify-cookie-c1'] .';' : NULL;
	$right = isset($foxtool_notify_options['notify-cookie-c2']) && $foxtool_notify_options['notify-cookie-c2'] == 'Right' ? '.ft-cookie{right:10px;left:unset;}' : NULL;
	$link = !empty($foxtool_notify_options['notify-cookie-l1']) ? $foxtool_notify_options['notify-cookie-l1'] : 'javascript:void(0)';
	?>
	<div id="ft-cookie" class="ft-cookie">
		<div class="ft-cookie-tit">
			<span class="ft-cookie-text"><svg xmlns="http://www.w3.org/2000/svg" width="30px" height="30px" viewBox="0 0 24 24"><path fill="currentColor" d="M21.598 11.064a1.006 1.006 0 0 0-.854-.172A2.938 2.938 0 0 1 20 11c-1.654 0-3-1.346-3.003-2.937c.005-.034.016-.136.017-.17a.998.998 0 0 0-1.254-1.006A2.963 2.963 0 0 1 15 7c-1.654 0-3-1.346-3-3c0-.217.031-.444.099-.716a1 1 0 0 0-1.067-1.236A9.956 9.956 0 0 0 2 12c0 5.514 4.486 10 10 10s10-4.486 10-10c0-.049-.003-.097-.007-.16a1.004 1.004 0 0 0-.395-.776zM12 20c-4.411 0-8-3.589-8-8a7.962 7.962 0 0 1 6.006-7.75A5.006 5.006 0 0 0 15 9l.101-.001a5.007 5.007 0 0 0 4.837 4C19.444 16.941 16.073 20 12 20z"/><circle cx="12.5" cy="11.5" r="1.5" fill="currentColor"/><circle cx="8.5" cy="8.5" r="1.5" fill="currentColor"/><circle cx="7.5" cy="12.5" r="1.5" fill="currentColor"/><circle cx="15.5" cy="15.5" r="1.5" fill="currentColor"/><circle cx="10.5" cy="16.5" r="1.5" fill="currentColor"/></svg> <?php echo $title; ?></span>
			<span class="ft-cookie-close">&#215;</span>
		</div>
		<span class="ft-cookie-content"><?php echo $content; ?></span>
		<div class="ft-cookie-but">
			<a title="<?php _e('Policy', 'foxtool'); ?>" href="<?php echo $link; ?>" target="_blank"><?php _e('Policy', 'foxtool'); ?></a>
			<a title="<?php _e('Agree', 'foxtool'); ?>" class="ft-cookie-oke" href="javascript:void(0)"><?php _e('Agree', 'foxtool'); ?></a>
		</div>
	</div>
	<?php
	echo '<style>:root{'. $colortit .'}'. $right .'</style>';
}
add_action('wp_footer', 'foxtool_cookie_footer');	
}  
#popup
if(isset($foxtool_notify_options['notify-popup1'])){
function foxtool_popup_assets(){
	wp_enqueue_script( 'foxpopup', FOXTOOL_URL . 'link/notify/foxpopup.js', array('jquery'), FOXTOOL_VERSION, true);
	wp_enqueue_style( 'foxpopup', FOXTOOL_URL . 'link/notify/foxpopup.css', array(), FOXTOOL_VERSION);
}
add_action('wp_enqueue_scripts', 'foxtool_popup_assets');
function foxtool_popup_footer(){ 
	global $foxtool_notify_options;
	$time = !empty($foxtool_notify_options['notify-popup-c1']) ? $foxtool_notify_options['notify-popup-c1'] : NULL;
	$title1 = !empty($foxtool_notify_options['notify-popup12']) ? '<div class="ft-popupnew-tit">'. $foxtool_notify_options['notify-popup12'] .'</div>' : NULL;
	$title2 = !empty($foxtool_notify_options['notify-popup12']) ? ''. $foxtool_notify_options['notify-popup12'] : __('Hello', 'foxtool');
	$images = !empty($foxtool_notify_options['notify-popup11']) ? '<img alt="'. $title2 .'" src="'. $foxtool_notify_options['notify-popup11'] .'" />' : NULL;
	$content = !empty($foxtool_notify_options['notify-popup13']) ? do_shortcode($foxtool_notify_options['notify-popup13']) : NULL;
	$link1 = !empty($foxtool_notify_options['notify-popup14']) ? '<a title="'. $title2 .'" href="'. $foxtool_notify_options['notify-popup14'] .'">'. $images .'</a>' : $images;
	
	$borderr = !empty($foxtool_notify_options['notify-popup-m1']) ? $foxtool_notify_options['notify-popup-m1'] .'px' : '0px';
	$maxwidth = !empty($foxtool_notify_options['notify-popup-m2']) ? '.ft-modal{max-width:'. $foxtool_notify_options['notify-popup-m2'] .'px;}' : NULL;
	// skin 2
	if (!empty($foxtool_notify_options['notify-popup-c2']) && $foxtool_notify_options['notify-popup-c2'] == '2'){ ?>
	<div id="ftpopupex" class="ft-modal ft-modal-skin2">
		<div class="ft-popupnew-content">
			<?php echo $title1; ?>
			<div class="ft-popupnew-img"><?php echo $link1; ?></div>
			<div class="ft-popupnew-tex"><?php echo $content; ?></div>
		</div>
	</div>
	<style><?php echo $maxwidth; ?>.ft-modal{border-radius:<?php echo $borderr; ?>;}.ft-modal-skin2 .ft-popupnew-img img{border-radius:calc(<?php echo $borderr ?> - 2px);}</style>
	
	<?php 
	// skin 3
	} else if (!empty($foxtool_notify_options['notify-popup-c2']) && $foxtool_notify_options['notify-popup-c2'] == '3'){ ?>
	<div id="ftpopupex" class="ft-modal ft-modal-skin3">
		<div class="ft-popupnew">
			<div class="ft-popupnew-img"><?php echo $link1; ?></div>
			<div class="ft-popupnew-content">
				<?php echo $title1; ?>
				<div class="ft-popupnew-tex"><?php echo $content; ?></div>
			</div>
		</div>
	</div>
	<style><?php echo $maxwidth; ?>.ft-modal{border-radius:<?php echo $borderr; ?>;}.ft-modal-skin3 .ft-popupnew-img img{border-radius: <?php echo $borderr .' '. $borderr .' 0px 0px'; ?>;}</style>
	<?php
	// skin 4
	} else if (!empty($foxtool_notify_options['notify-popup-c2']) && $foxtool_notify_options['notify-popup-c2'] == '4'){ ?>
	<div id="ftpopupex" class="ft-modal ft-modal-skin4">
		<div class="ft-popupnew-img"><?php echo $link1; ?></div>
	</div>
	<style><?php echo $maxwidth; ?>.ft-modal-skin4 .ft-popupnew-img img{border-radius: <?php echo $borderr; ?>;}</style>
	
	<?php
	// skin 1
	} else { ?>
	<div id="ftpopupex" class="ft-modal ft-modal-skin1">
		<div class="ft-popupnew">
			<div class="ft-popupnew-img"><?php echo $link1; ?></div>
			<div class="ft-popupnew-content">
				<?php echo $title1; ?>
				<div class="ft-popupnew-tex"><?php echo $content; ?></div>
			</div>
		</div>
	</div>
	<style><?php echo $maxwidth; ?>.ft-modal{border-radius:<?php echo $borderr; ?>;}.ft-modal-skin1 .ft-popupnew-img img{border-radius:<?php echo $borderr .' 0px 0px '. $borderr; ?>;} @media(max-width:700px){.ft-modal-skin1 .ft-popupnew-img img{border-radius: <?php echo $borderr .' '. $borderr .' 0px 0px'; ?>;}}</style>
	
	<?php } ?>
	<span id="popup-timer" data-time="<?php echo $time; ?>"></span>
	<?php
}
add_action('wp_footer', 'foxtool_popup_footer');
}


