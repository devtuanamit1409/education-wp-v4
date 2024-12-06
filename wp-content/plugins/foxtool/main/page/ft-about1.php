<?php 
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $foxtool_options; ?>
<h2><?php _e('ABOUT', 'foxtool'); ?></h2>
  <h3><i class="fa-regular fa-star"></i> <?php _e('Information about your website', 'foxtool') ?></h3>
	<div class="ft-card-note"> 
	<p><?php $theme = wp_get_theme(); echo 'Theme: ' . esc_html($theme->Name) .' <b>'. esc_html($theme->Version) .'</b>'; ?></p>
	<p><?php $foxtool = FOXTOOL_VERSION; echo __('Foxtool:', 'foxtool'). ' <b>'. esc_html($foxtool) .'</b>'; ?></p>
	<p>WordPress: <b><?php echo esc_html(get_bloginfo('version')); ?></b></p>
	<p>Lang: <b><?php echo esc_html($lang=get_bloginfo("language")); ?></b></p>
	<p>PHP: <b><?php echo esc_html(phpversion()); ?></b></p>
	<p><?php foxtool_display_db_info(); ?></p>
	<p><?php echo esc_html($_SERVER['SERVER_SOFTWARE']); ?></p>
	<p>
	<?php
	$active_plugins = get_option('active_plugins');
	echo 'Plugin: ';
	foreach ($active_plugins as $plugin_path) {
		$plugin_info = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin_path);
		echo '<b>'. esc_html($plugin_info['Name']) . '</b>, ';
	}
	?>
	</p>
	<p>
	<?php
	$admin_users = get_users(array(
		'role' => 'administrator',
	));
	if (!empty($admin_users)) {
		echo 'User Admin: ';
		foreach ($admin_users as $admin_user) {
			echo '<b>' . esc_html($admin_user->user_nicename) . '</b>, ';
		}
	} 
	?>
	</p>
	<p>
	<?php
	$user_count = count_users();
	echo 'User: ' . esc_html($user_count['total_users']);
	?>
	</p>
	<p>
	<?php
	// check gd
	if (extension_loaded('gd') || extension_loaded('gd2')) {
		echo __('The GD library has been installed', 'foxtool');
	} else {
		echo __('The GD library has not been installed', 'foxtool');
	}
	?>
	</p>
	</div>
  <h3><i class="fa-regular fa-star"></i> <?php _e('Plugin development', 'foxtool') ?></h3>
	<div class="ft-card-note"> 
	<p><?php _e('Developed by:', 'foxtool') ?> <b><a target="_blank" href="https://foxplugin.com">Fox Plugin</a></b></p>
	<p><?php _e('Author:', 'foxtool') ?> <b><a target="_blank" href="https://www.facebook.com/adfoxtheme">IHOAN (NGUYEN NGOC HOAN)</a></b></p>
	<p><?php _e('Contributions:', 'foxtool') ?> <b>Vu Ngoc Tuan, AR T RU, Linh Tran, Di Hu Hoa Tung, Minh Nhut</b></p>
	</div>