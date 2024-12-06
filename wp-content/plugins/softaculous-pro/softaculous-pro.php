<?php

/*
Plugin Name: Softaculous Pro
Plugin URI: https://www.softaculous.com/pro/
Description: Softaculous Pro helps users get familiar with WordPress and build a functional website with a seamless onboarding process. Softaculous also offers an assistant which provides tour of essential aspects of WordPress for the user to understand the functionality. AI functionality (Coming Soon).
Version: 2.1.5
Author: Softaculous
Author URI: https://www.softaculous.com
License: LGPL v2.1
License URI: https://www.gnu.org/licenses/old-licenses/lgpl-2.1.en.html
Text Domain: softaculous-pro
*/

/*
 * This file belongs to the softaculous plugin.
 *
 * (c) Softaculous <sales@softaculous.com>
 *
 * You can view the LICENSE file that was distributed with this source code
 * for copywright and license information.
 */

if(!defined('ABSPATH')){
	die('HACKING ATTEMPT!');
}

if(defined('SOFTACULOUS_PRO_VERSION')) {
	return;
}

define('SOFTACULOUS_PRO_FILE', __FILE__);
define('SOFTACULOUS_PRO_VERSION', '2.1.5');
define('SOFTACULOUS_PRO_BASE', 'softaculous-pro/softaculous-pro.php');
define('SOFTACULOUS_PRO_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SOFTACULOUS_PRO_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('SOFTACULOUS_PRO_WWW_URL', 'https://www.softaculous.com/');
define('SOFTACULOUS_PRO_AI_API', 'https://api.softaculous.com/ai');
define('SOFTACULOUS_PRO_URL', 'https://www.softaculous.com/');
define('SOFTACULOUS_PRO_PFX_API', 'https://a.softaculous.com/popularfx/');
define('SOFTACULOUS_PRO_PAGELAYER_API', 'https://api.pagelayer.com/');
define('SOFTACULOUS_PRO_BUY', 'https://www.softaculous.com/clients?ca=softaculous_pro_buy');

include_once SOFTACULOUS_PRO_PLUGIN_PATH . 'main/functions.php';

// Activation Hook
register_activation_hook(__FILE__, 'softaculous_pro_activation_hook');

// De-activation Hook
register_deactivation_hook(__FILE__, 'softaculous_pro_deactivation_hook');

// Uninstall hook
register_uninstall_hook(__FILE__, 'softaculous_pro_uninstall_hook');

add_action('plugins_loaded', 'softaculous_pro_load_plugin');