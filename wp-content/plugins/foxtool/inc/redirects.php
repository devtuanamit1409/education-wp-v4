<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $foxtool_redirects_options;
# redirect 301 all
function foxtool_redirect_to_301() {
    global $foxtool_redirects_options, $foxtool_options;
	$linklogin = !empty($foxtool_options['custom-chan11']) ? '/' . $foxtool_options['custom-chan11'] : NULL;
	// 301 full site
	if (isset($foxtool_redirects_options['redi11']) && !empty($foxtool_redirects_options['redi12']) && strpos($_SERVER['REQUEST_URI'], '/wp-login.php') === false && $_SERVER['REQUEST_URI'] !== '/wp-login.php' && $_SERVER['REQUEST_URI'] !== '/wp-admin/' && $_SERVER['REQUEST_URI'] !== $linklogin &&  !current_user_can('manage_options')){
		$linkout = !empty($foxtool_redirects_options['redi12']) ? $foxtool_redirects_options['redi12'] : NULL;
		ob_clean();
		header("Cache-Control: no-cache, no-store, must-revalidate");
		header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: $linkout", true, 301);
		exit();
	}
	// 301 link line
	if (isset($foxtool_redirects_options['redi1']) && !isset($foxtool_redirects_options['redi11'])){
		if (is_array($foxtool_redirects_options) || is_object($foxtool_redirects_options)) {
			$redirects = array();
			foreach ($foxtool_redirects_options as $key => $value) {
				if (preg_match('/^rechan1(\d+)$/', $key, $matches)) {
					$n = $matches[1];
					$redirects[$foxtool_redirects_options['rechan1' . $n]] = $foxtool_redirects_options['rechan2' . $n];
				}
			}
			foreach ($redirects as $uri => $new_location) {
				$request_uri_trimmed = rtrim($_SERVER['REQUEST_URI'], '/');
				$uri_trimmed = rtrim(parse_url($uri, PHP_URL_PATH), '/'); 
				if ($request_uri_trimmed === $uri_trimmed) {
					ob_clean();
					header("Cache-Control: no-cache, no-store, must-revalidate");
					header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
					if (empty($new_location)) { 
						// Nếu không có $new_location, chuyển hướng về trang chủ
						header("HTTP/1.1 301 Moved Permanently");
						header("Location: " . home_url(), true, 301);
					} else {
						// Nếu có $new_location, thực hiện chuyển hướng đến địa chỉ mới
						header("HTTP/1.1 301 Moved Permanently");
						header("Location: $new_location", true, 301);
					}
					exit();
				}
			}
		}
	}
	// code chuyen den 503 khi bao tri
	if (isset($foxtool_redirects_options['redi3'])){
		if (strpos($_SERVER['REQUEST_URI'], '/wp-login.php') === false && $_SERVER['REQUEST_URI'] !== '/wp-login.php' && $_SERVER['REQUEST_URI'] !== '/wp-admin/' && $_SERVER['REQUEST_URI'] !== $linklogin &&  !current_user_can('manage_options')) {
			ob_clean();
			header("Cache-Control: no-cache, no-store, must-revalidate");
			header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
			header("HTTP/1.1 503 Service Temporarily Unavailable");
			header("Status: 503 Service Temporarily Unavailable");
			header("Retry-After: 3600"); 
			include(FOXTOOL_DIR . 'page/503.php');
			exit();
		}
	}
}
add_action('init', 'foxtool_redirect_to_301');
// chuyển link 404 về trang chủ
if (isset($foxtool_redirects_options['redi2'])){
function foxtool_redirect_404_to_home() {
	global $foxtool_redirects_options;
	$link = !empty($foxtool_redirects_options['redi21']) ? '/'. $foxtool_redirects_options['redi21'] : home_url();
    if (is_404()) {
        wp_redirect($link);
        exit();
    }
}
add_action('template_redirect', 'foxtool_redirect_404_to_home');
}







