<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
function foxtool_code_options_page() {
	global $foxtool_code_options;
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
			<button class="sotab sotab-select" onclick="fttab(event, 'tab1')"><i class="fa-brands fa-css3"></i> <?php _e('CSS', 'foxtool'); ?></button>
			<button class="sotab" onclick="fttab(event, 'tab2')"><i class="fa-regular fa-code"></i> <?php _e('WP HEAD', 'foxtool'); ?></button>
			<button class="sotab" onclick="fttab(event, 'tab3')"><i class="fa-regular fa-code"></i> <?php _e('WP BODY', 'foxtool'); ?></button>
			<button class="sotab" onclick="fttab(event, 'tab4')"><i class="fa-regular fa-code"></i> <?php _e('WP FOOTER', 'foxtool'); ?></button>
			<button class="sotab" onclick="fttab(event, 'tab5')"><i class="fa-regular fa-arrow-right-to-bracket"></i> <?php _e('WP LOGIN', 'foxtool'); ?></button>
		</div>

		<div class="ft-main">
			<?php 
			if( isset($_GET['settings-updated']) ) { 
				require_once( FOXTOOL_DIR . 'main/completed.php'); 
			}
			$ftnote = '<div class="ftnotecode"><span><i class="fa-regular fa-arrow-left"></i> Ctrl-Z, Cmd-Z: Undo</span><span><i class="fa-regular fa-arrow-right"></i> Ctrl-Y, Cmd-Y: Redo</span> <span><i class="fa-regular fa-magnifying-glass"></i> Ctrl-F, Cmd-F: Find</span><span><i class="fa-regular fa-filter"></i> Ctrl-H, Cmd-H: Replace</span></div>';
			?>
			<form method="post" action="options.php">
			<?php settings_fields('foxtool_code_settings_group'); ?> 
			<!-- CSS -->
			<div class="sotab-box ftbox" id="tab1">
			<h2><?php _e('CSS', 'foxtool'); ?></h2>
			<div class="ft-card">
			  <h3><i class="fa-regular fa-code"></i> <?php _e('Add CSS to your website', 'foxtool') ?></h3>
				<?php echo $ftnote; ?>
				<p>
				<textarea class="ft-code-textarea ft-dev" name="foxtool_code_settings[code1]" placeholder="<?php _e('Enter CSS here', 'foxtool'); ?>"><?php if(!empty($foxtool_code_options['code1'])){echo esc_textarea($foxtool_code_options['code1']);} ?></textarea>
				</p>
			  <h3><i class="fa-regular fa-code"></i> <?php _e('Add CSS for tablet size', 'foxtool') ?></h3>
				<?php echo $ftnote; ?>
				<p>
				<textarea class="ft-code-textarea ft-dev" name="foxtool_code_settings[code11]" placeholder="<?php _e('Enter CSS here', 'foxtool'); ?>"><?php if(!empty($foxtool_code_options['code11'])){echo esc_textarea($foxtool_code_options['code11']);} ?></textarea>
				</p>
			  <h3><i class="fa-regular fa-code"></i> <?php _e('Add CSS for mobile size', 'foxtool') ?></h3>
				<?php echo $ftnote; ?>
				<p>
				<textarea class="ft-code-textarea ft-dev" name="foxtool_code_settings[code12]" placeholder="<?php _e('Enter CSS here', 'foxtool'); ?>"><?php if(!empty($foxtool_code_options['code12'])){echo esc_textarea($foxtool_code_options['code12']);} ?></textarea>
				</p>
			</div>	
			</div>
			<!-- Javascript 1 -->
			<div class="sotab-box ftbox" id="tab2" style="display:none">
			<h2><?php _e('WP HEAD', 'foxtool'); ?></h2>
			<div class="ft-card">
			  <h3><i class="fa-regular fa-code"></i> <?php _e('Add code to WP head', 'foxtool') ?></h3>
				<?php echo $ftnote; ?>
				<p>
				<textarea class="ft-code-textarea ft-dev" name="foxtool_code_settings[code2]" placeholder="<?php _e('Enter code here', 'foxtool'); ?>"><?php if(!empty($foxtool_code_options['code2'])){echo esc_textarea($foxtool_code_options['code2']);} ?></textarea>
				</p>
			</div>
			</div>
			<!-- Javascript 2 -->
			<div class="sotab-box ftbox" id="tab3" style="display:none">
			<h2><?php _e('WP BODY', 'foxtool'); ?></h2>
			<div class="ft-card">
			  <h3><i class="fa-regular fa-code"></i> <?php _e('Add code to WP body', 'foxtool') ?></h3>
				<?php echo $ftnote; ?>
				<p>
				<textarea class="ft-code-textarea ft-dev" name="foxtool_code_settings[code3]" placeholder="<?php _e('Enter code here', 'foxtool'); ?>"><?php if(!empty($foxtool_code_options['code3'])){echo esc_textarea($foxtool_code_options['code3']);} ?></textarea>
				</p>
			</div>
			</div>
			<!-- Javascript 3 -->
			<div class="sotab-box ftbox" id="tab4" style="display:none">
			<h2><?php _e('WP FOOTER', 'foxtool'); ?></h2>
			<div class="ft-card">
			  <h3><i class="fa-regular fa-code"></i> <?php _e('Add code to WP footer', 'foxtool') ?></h3>
				<?php echo $ftnote; ?>
				<p>
				<textarea class="ft-code-textarea ft-dev" name="foxtool_code_settings[code4]" placeholder="<?php _e('Enter code here', 'foxtool'); ?>"><?php if(!empty($foxtool_code_options['code4'])){echo esc_textarea($foxtool_code_options['code4']);} ?></textarea>
				</p>
			</div>	
			</div>
			<!-- login 1 -->
			<div class="sotab-box ftbox" id="tab5" style="display:none">
			<h2><?php _e('WP LOGIN', 'foxtool'); ?></h2>
			<div class="ft-card">
			  <h3><i class="fa-regular fa-code"></i> <?php _e('Add code to WP login', 'foxtool') ?></h3>
				<?php echo $ftnote; ?>
				<p>
				<textarea class="ft-code-textarea ft-dev" name="foxtool_code_settings[code5]" placeholder="<?php _e('Enter code here', 'foxtool'); ?>"><?php if(!empty($foxtool_code_options['code5'])){echo esc_textarea($foxtool_code_options['code5']);} ?></textarea>
				</p>
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
		$('#ft-save-fast').click(function(e) {
			e.preventDefault(); 
			var currentForm = $(this).closest('form');
			$.ajax({
				type: 'POST',
				url: currentForm.attr('action'),  
				data: currentForm.serialize(),   
				success: function(response) {
					$('#ft-save-fast').html('<i class="fa-regular fa-check"></i>');
					setTimeout(function() {
						$('#ft-save-fast').html('<i class="fa-regular fa-floppy-disk"></i>');
					}, 1000); 
				},
				error: function() {
					console.log('Error in AJAX request'); 
				}
			});
		});
	});
	</script>
	<?php
	// style foxtool
	require_once( FOXTOOL_DIR . 'main/style.php');
	echo ob_get_clean();
}
function foxtool_code_options_link() {
	add_submenu_page ('foxtool-options', 'Code', '<i class="fa-regular fa-code-simple" style="width:20px;"></i> '. __('Add code', 'foxtool'), 'manage_options', 'foxtool-code-options', 'foxtool_code_options_page');
}
add_action('admin_menu', 'foxtool_code_options_link');
function foxtool_code_register_settings() {
	register_setting('foxtool_code_settings_group', 'foxtool_code_settings');
}
add_action('admin_init', 'foxtool_code_register_settings');
// clear cache
function foxtool_code_settings_cache($old_value, $value) {
    wp_cache_delete('foxtool_code_settings', 'options');
}
add_action('update_option_foxtool_code_settings', 'foxtool_code_settings_cache', 10, 2);

