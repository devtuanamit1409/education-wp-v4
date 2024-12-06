<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
function foxtool_debug_options_page() {
	global $foxtool_debug_options;
	ob_start(); 
	?>
	<div class="wrap ft-wrap">
	<div class="ft-wrap-top">
		<?php include( FOXTOOL_DIR . 'main/page/ft-aff-top.php'); ?>
	</div>
	<div class="ft-wrap2">
	  <div class="ft-box">
		<div class="ft-menu">
			<div class="ft-logo ft-logoquay">
			<a class="ft-logoquaya" href="https://foxplugin.com" target="_blank">
			<span><?php foxtool_logo(); ?></span>
			</a>
			</div>
			<button class="sotab sotab-select" onclick="fttab(event, 'tab1')"><i class="fa-regular fa-square-terminal"></i> <?php _e('DEBUG', 'foxtool'); ?></button>
		</div>

		<div class="ft-main">
			<?php 
			if( isset($_GET['settings-updated']) ) { 
				require_once( FOXTOOL_DIR . 'main/completed.php');   
			}
			?>
			<form method="post" action="options.php">
			<?php settings_fields('foxtool_debug_settings_group'); ?> 
			<!-- DEBUG -->
			<div class="sotab-box ftbox" id="tab1">
			<h2><?php _e('DEBUG', 'foxtool'); ?></h2>
			<div class="ft-card">
			  <h3><i class="fa-regular fa-ban-bug"></i> <?php _e('Debug settings', 'foxtool') ?></h3>
				<p>
				<label class="nut-switch">
				<input type="checkbox" name="foxtool_debug_settings[debug1]" value="1" <?php if ( isset($foxtool_debug_options['debug1']) && 1 == $foxtool_debug_options['debug1'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ft-label-right"><?php _e('Enable WP_DEBUG', 'foxtool'); ?></label>
				</p>
				<p>
				<label class="nut-switch">
				<input type="checkbox" name="foxtool_debug_settings[debug2]" value="1" <?php if ( isset($foxtool_debug_options['debug2']) && 1 == $foxtool_debug_options['debug2'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ft-label-right"><?php _e('Enable WP_DEBUG_LOG', 'foxtool'); ?></label>
				</p>
				<p>
				<label class="nut-switch">
				<input type="checkbox" name="foxtool_debug_settings[debug3]" value="1" <?php if ( isset($foxtool_debug_options['debug3']) && 1 == $foxtool_debug_options['debug3'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ft-label-right"><?php _e('Enable WP_DEBUG_DISPLAY', 'foxtool'); ?></label>
				</p>
				<?php
				if (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
				$debug_log_path = WP_CONTENT_DIR . '/debug.log';
				if (file_exists($debug_log_path)) { ?>
						<p>
						<div class="ft-pre-tit"><span></span><span></span><span></span></div>
						<div class="ft-pre-me">
							<a class="delete-debug" href="javascript:void(0)" id="delete-debug"><i class="fa-regular fa-trash"></i> <?php _e('Clear all', 'foxtool'); ?></a>
							<a class="delete-debug" href="javascript:void(0)" id="load-debug"><i class="fa-regular fa-arrows-rotate"></i> <?php _e('Refresh', 'foxtool'); ?></a>
						</div>
						<div id="delete-debug-not"></div>
						<textarea id="debug-log-content" class="ft-pre"></textarea>
						</p>
						<?php
					} else {
						echo '<div class="ebug">'. __('The debug file does not exist', 'foxtool') .'</div>';
					}
				} else {
					echo '<div class="ebug">'. __('Debug is not enabled', 'foxtool') .'</div>';
				}
				?>  
			</div>	
			</div>
			
			<div class="ft-submit">
				<button type="submit"><i class="fa-regular fa-floppy-disk"></i> <?php _e('SAVE CONTENT', 'foxtool'); ?></button>
			</div>
				<button id="ft-save-fast" type="submit"><i class="fa-regular fa-floppy-disk"></i></button>
			</form>
			
		</div>
		
	  </div>
      <div class="ft-sidebar">
		<?php include( FOXTOOL_DIR . 'main/page/ft-aff.php'); ?>
	  </div>
	</div>	
	</div>
	<script>
	jQuery(document).ready(function($) {
		$('form input[type="checkbox"]').change(function() {
			var currentForm = $(this).closest('form');
			$.ajax({
				type: 'POST',
				url: currentForm.attr('action'), 
				data: currentForm.serialize(), 
				success: function(response) {
					location.reload(); 
				},
				error: function() {
					console.log('Error in AJAX request');
				}
			});
		});
		$('#delete-debug').on('click', function(e) {
			var ajax_nonce = '<?php echo wp_create_nonce('foxtool_nonce_deldebug'); ?>';
			e.preventDefault();
			var data = {
				'action': 'foxtool_clear_debug_log',
				'security': ajax_nonce,
			};
			$.post(ajaxurl, data, function(response) {
				if (response.success) {
					$('#delete-debug-not').html('<span><?php _e("Cleaned the screen", "foxtool"); ?></span>');
					loadDebugLog();
				} else {
					$('#delete-debug-not').html('<span><?php _e("Error cannot be cleaned", "foxtool"); ?></span>');
				}
			});
		});
		$('#load-debug').on('click', function(e) {
			e.preventDefault();
			$('#delete-debug-not').html('<span><?php _e("Screen refreshed", "foxtool"); ?></span>');
			loadDebugLog(); 
		});
		function loadDebugLog(){
			var ajax_nonce = '<?php echo wp_create_nonce('foxtool_nonce_getdebug'); ?>';
			var data = {
				action: 'foxtool_get_debug_log',
				security: ajax_nonce
			};
			$.post(ajaxurl, data, function(response) {
				if (response.success) {
					$('#debug-log-content').val(response.data);
				} else {
					alert(response.data);
				}
			});
		}
		loadDebugLog();
	});
	</script>
	<?php
	// style foxtool
	require_once( FOXTOOL_DIR . 'main/style.php');
	echo ob_get_clean();
}
function foxtool_debug_options_link() {
	add_submenu_page ('foxtool-options', 'Debug', '<i class="fa-regular fa-gear" style="width:20px;"></i> '. __('Debug', 'foxtool'), 'manage_options', 'foxtool-debug-options', 'foxtool_debug_options_page');
}
add_action('admin_menu', 'foxtool_debug_options_link');
function foxtool_debug_register_settings() {
	register_setting('foxtool_debug_settings_group', 'foxtool_debug_settings');
}
add_action('admin_init', 'foxtool_debug_register_settings');
// clear cache
function foxtool_debug_settings_cache($old_value, $value) {
    wp_cache_delete('foxtool_debug_settings', 'options');
}
add_action('update_option_foxtool_debug_settings', 'foxtool_debug_settings_cache', 10, 2);









