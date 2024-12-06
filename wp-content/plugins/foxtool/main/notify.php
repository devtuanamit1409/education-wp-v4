<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
function foxtool_notify_options_page() {
	global $foxtool_notify_options;
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
			<button class="sotab sotab-select" onclick="fttab(event, 'tab1')"><i class="fa-regular fa-shield-halved"></i> <?php _e('BLOCKER', 'foxtool'); ?></button>
			<button class="sotab" onclick="fttab(event, 'tab2')"><i class="fa-regular fa-bell"></i> <?php _e('NOTIFY', 'foxtool'); ?></button>
			<button class="sotab" onclick="fttab(event, 'tab3')"><i class="fa-regular fa-window"></i> <?php _e('POPUP', 'foxtool'); ?></button>
			<button class="sotab" onclick="fttab(event, 'tab4')"><i class="fa-regular fa-cookie-bite"></i> <?php _e('COOKIE', 'foxtool'); ?></button>
		</div>

		<div class="ft-main">
			<?php 
			if( isset($_GET['settings-updated']) ) { 
				require_once( FOXTOOL_DIR . 'main/completed.php'); 
			}
			?>
			<form method="post" action="options.php">
			<?php settings_fields('foxtool_notify_settings_group'); ?> 
			<!-- BLOCKER -->
			<div class="sotab-box ftbox" id="tab1" >
			<h2><?php _e('BLOCKER', 'foxtool'); ?></h2>
			<div class="ft-card">
			   <h3><i class="fa-regular fa-shield-halved"></i> <?php _e('Browser ad-block notification', 'foxtool') ?></h3>
				<p>
				<label class="nut-switch">
				<input type="checkbox" name="foxtool_notify_settings[notify-block1]" value="1" <?php if ( isset($foxtool_notify_options['notify-block1']) && 1 == $foxtool_notify_options['notify-block1'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ft-label-right"><?php _e('Enable ad-block detection', 'foxtool'); ?></label>
				</p>
				<p>
				<label class="nut-switch">
				<input type="checkbox" name="foxtool_notify_settings[notify-block11]" value="1" <?php if ( isset($foxtool_notify_options['notify-block11']) && 1 == $foxtool_notify_options['notify-block11'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ft-label-right"><?php _e('Only notify, do not block access', 'foxtool'); ?></label>
				</p>
				<p style="display:flex;align-items:center;">
				<input class="ft-input-color" name="foxtool_notify_settings[notify-block-c1]" type="text" data-coloris value="<?php if(!empty($foxtool_notify_options['notify-block-c1'])){echo sanitize_text_field($foxtool_notify_options['notify-block-c1']);} ?>"/>
				<label class="ft-right-text"><?php _e('Select button color', 'foxtool'); ?></label>
				</p>
				<p style="display:flex;align-items:center;">
				<input class="ft-input-color" name="foxtool_notify_settings[notify-block-c2]" type="text" data-coloris value="<?php if(!empty($foxtool_notify_options['notify-block-c2'])){echo sanitize_text_field($foxtool_notify_options['notify-block-c2']);} ?>"/>
				<label class="ft-right-text"><?php _e('Select button border color', 'foxtool'); ?></label>
				</p>
				<p>
				<input class="ft-input-big" placeholder="<?php _e('Enter title', 'foxtool') ?>" name="foxtool_notify_settings[notify-block12]" type="text" value="<?php if(!empty($foxtool_notify_options['notify-block12'])){echo sanitize_text_field($foxtool_notify_options['notify-block12']);} ?>"/>
				</p>
				<p>
				<textarea style="height:150px;" class="ft-code-textarea" name="foxtool_notify_settings[notify-block13]" placeholder="<?php _e('Enter content here', 'foxtool'); ?>"><?php if(!empty($foxtool_notify_options['notify-block13'])){echo esc_textarea($foxtool_notify_options['notify-block13']);} ?></textarea>
				</p>
				<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Enter the title and content you want to display when ad-blocker is detected', 'foxtool'); ?></p>   
			</div>
			</div>
			<!-- NOTIFY -->
			<div class="sotab-box ftbox" id="tab2" style="display:none">
			<h2><?php _e('NOTIFY', 'foxtool'); ?></h2>
			<div class="ft-card">
			   <h3><i class="fa-regular fa-bell"></i> <?php _e('Notification at the top of the page', 'foxtool') ?></h3>
				<p>
				<label class="nut-switch">
				<input type="checkbox" name="foxtool_notify_settings[notify-notis1]" value="1" <?php if ( isset($foxtool_notify_options['notify-notis1']) && 1 == $foxtool_notify_options['notify-notis1'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ft-label-right"><?php _e('Enable notification', 'foxtool'); ?></label>
				</p>
				<p style="display:flex;align-items:center;">
				<input class="ft-input-color" name="foxtool_notify_settings[notify-notis-c1]" type="text" data-coloris value="<?php if(!empty($foxtool_notify_options['notify-notis-c1'])){echo sanitize_text_field($foxtool_notify_options['notify-notis-c1']);} ?>"/>
				<label class="ft-right-text"><?php _e('Select background color', 'foxtool'); ?></label>
				</p>
				<p>
				<textarea style="height:150px;" class="ft-code-textarea" name="foxtool_notify_settings[notify-notis11]" placeholder="<?php _e('Enter content here', 'foxtool'); ?>"><?php if(!empty($foxtool_notify_options['notify-notis11'])){echo esc_textarea($foxtool_notify_options['notify-notis11']);} ?></textarea>
				</p>
				<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Enter the content you want to display in the notification, and customize the colors to match your preferences. A notification will appear at the top of your website, making it easy for users to see', 'foxtool'); ?></p> 				
			</div>
			</div>
			<!-- POPUP -->
			<div class="sotab-box ftbox" id="tab3" style="display:none">
			<h2><?php _e('POPUP', 'foxtool'); ?></h2>
			<div class="ft-card">
			   <h3><i class="fa-regular fa-window"></i> <?php _e('Create an outstanding popup', 'foxtool') ?></h3>
				<p>
				<label class="nut-switch">
				<input type="checkbox" name="foxtool_notify_settings[notify-popup1]" value="1" <?php if ( isset($foxtool_notify_options['notify-popup1']) && 1 == $foxtool_notify_options['notify-popup1'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ft-label-right"><?php _e('Enable popup', 'foxtool'); ?></label>
				</p>
				<p>
				<input class="ft-input-small" name="foxtool_notify_settings[notify-popup-c1]" type="number" value="<?php if(!empty($foxtool_notify_options['notify-popup-c1'])){echo sanitize_text_field($foxtool_notify_options['notify-popup-c1']);} ?>"/>
				<label class="ft-label-right"><?php _e('Popup save time (.. hours)', 'foxtool'); ?></label>
				</p>
				<div id="ft-imgstyle" class="ft-imgstyle ft-imgstyle4">
					<img src="<?php echo esc_url(FOXTOOL_URL .'img/popup/1.png'); ?>" data-value="1" class="<?php if(isset($foxtool_notify_options['notify-popup-c2']) && $foxtool_notify_options['notify-popup-c2'] == '1') echo 'selected'; ?>" />
					<img src="<?php echo esc_url(FOXTOOL_URL .'img/popup/2.png'); ?>" data-value="2" class="<?php if(isset($foxtool_notify_options['notify-popup-c2']) && $foxtool_notify_options['notify-popup-c2'] == '2') echo 'selected'; ?>" />
					<img src="<?php echo esc_url(FOXTOOL_URL .'img/popup/3.png'); ?>" data-value="3" class="<?php if(isset($foxtool_notify_options['notify-popup-c2']) && $foxtool_notify_options['notify-popup-c2'] == '3') echo 'selected'; ?>" />
					<img src="<?php echo esc_url(FOXTOOL_URL .'img/popup/4.png'); ?>" data-value="4" class="<?php if(isset($foxtool_notify_options['notify-popup-c2']) && $foxtool_notify_options['notify-popup-c2'] == '4') echo 'selected'; ?>" />
				</div>
				<input type="hidden" name="foxtool_notify_settings[notify-popup-c2]" id="cutop11" value="<?php if(!empty($foxtool_notify_options['notify-popup-c2'])){echo sanitize_text_field($foxtool_notify_options['notify-popup-c2']);} else {echo sanitize_text_field('1');} ?>" />
				<script>
					document.addEventListener("DOMContentLoaded", function() {
						var imgStyles = document.querySelectorAll('#ft-imgstyle img');
						imgStyles.forEach(function(img) {
							img.addEventListener('click', function() {
								var selectedStyle = this.getAttribute('data-value');
								document.getElementById('cutop11').value = selectedStyle;
								imgStyles.forEach(function(img) {
									img.classList.remove('selected');
								});
								this.classList.add('selected');
							});
						});
					});
				</script>
				<p style="display:flex;">
				<input id="ft-add1" class="ft-input-big" name="foxtool_notify_settings[notify-popup11]" type="text" value="<?php if(!empty($foxtool_notify_options['notify-popup11'])){echo sanitize_text_field($foxtool_notify_options['notify-popup11']);} ?>" placeholder="<?php _e('Add images', 'foxtool'); ?>" />
				<button class="ft-selec" data-input-id="ft-add1"><?php _e('Select image', 'foxtool'); ?></button>
				</p>
				<p>
				<input class="ft-input-big" placeholder="<?php _e('Enter title', 'foxtool') ?>" name="foxtool_notify_settings[notify-popup12]" type="text" value="<?php if(!empty($foxtool_notify_options['notify-popup12'])){echo sanitize_text_field($foxtool_notify_options['notify-popup12']);} ?>"/>
				</p>
				<div class="ft-classic">
				<?php
				$popup_editor = !empty($foxtool_notify_options['notify-popup13']) ? wp_kses_post($foxtool_notify_options['notify-popup13']) : '';
				ob_start();
				wp_editor(
					$popup_editor,
					'userpostcontent',
					array(
						'textarea_name' => 'foxtool_notify_settings[notify-popup13]',
						'media_buttons' => false,
					)
				);
				$editor_contents = ob_get_clean();
				echo $editor_contents;
				?>
				</div>
				<p>
				<input class="ft-input-big" placeholder="<?php _e('Enter image link', 'foxtool') ?>" name="foxtool_notify_settings[notify-popup14]" type="text" value="<?php if(!empty($foxtool_notify_options['notify-popup14'])){echo sanitize_text_field($foxtool_notify_options['notify-popup14']);} ?>"/>
				</p>
				
				<p class="ft-keo">
				<input type="range" name="foxtool_notify_settings[notify-popup-m1]" min="1" max="50" value="<?php if(!empty($foxtool_notify_options['notify-popup-m1'])){echo sanitize_text_field($foxtool_notify_options['notify-popup-m1']);} else { echo sanitize_text_field('10');} ?>" class="ftslide" data-index="1">
				<span><?php _e('Border radius', 'foxtool'); ?> <span id="demo1"></span> PX</span>
				</p>
				
				<p class="ft-keo">
				<input type="range" name="foxtool_notify_settings[notify-popup-m2]" min="300" max="1000" value="<?php if(!empty($foxtool_notify_options['notify-popup-m2'])){echo sanitize_text_field($foxtool_notify_options['notify-popup-m2']);} else { echo sanitize_text_field('800');} ?>" class="ftslide" data-index="2">
				<span><?php _e('Max width', 'foxtool'); ?> <span id="demo2"></span> PX</span>
				</p>
				
				<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Enter the content you want to display and configure the customizations above so the popup can appear when users visit your website', 'foxtool'); ?></p> 				
			</div>
			</div>
			<!-- COOKIE -->
			<div class="sotab-box ftbox" id="tab4" style="display:none">
			<h2><?php _e('COOKIE', 'foxtool'); ?></h2>
			<div class="ft-card">
			  <h3><i class="fa-regular fa-cookie-bite"></i> <?php _e('Set up cookie notifications', 'foxtool') ?></h3>
				<p>
				<label class="nut-switch">
				<input type="checkbox" name="foxtool_notify_settings[notify-cookie1]" value="1" <?php if ( isset($foxtool_notify_options['notify-cookie1']) && 1 == $foxtool_notify_options['notify-cookie1'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ft-label-right"><?php _e('Enable cookie', 'foxtool'); ?></label>
				</p>
				<p style="display:flex;align-items:center;">
				<input class="ft-input-color" name="foxtool_notify_settings[notify-cookie-c1]" type="text" data-coloris value="<?php if(!empty($foxtool_notify_options['notify-cookie-c1'])){echo sanitize_text_field($foxtool_notify_options['notify-cookie-c1']);} ?>"/>
				<label class="ft-right-text"><?php _e('Select title color and button', 'foxtool'); ?></label>
				</p>
				<p>
				<?php $styles = array('Left', 'Right'); ?>
				<select name="foxtool_notify_settings[notify-cookie-c2]"> 
				<?php foreach($styles as $style) { ?> 
				<?php if(isset($foxtool_notify_options['notify-cookie-c2']) && $foxtool_notify_options['notify-cookie-c2'] == $style) { $selected = 'selected="selected"'; } else { $selected = ''; } ?>
				<option value="<?php echo $style; ?>" <?php echo $selected; ?>><?php echo $style; ?></option> 
				<?php } ?> 
				</select>
				<label class="ft-right-text"><?php _e('Location', 'foxtool'); ?></label>
				</p>
				<p>
				<input class="ft-input-big" placeholder="<?php _e('Enter the policy page link', 'foxtool') ?>" name="foxtool_notify_settings[notify-cookie-l1]" type="text" value="<?php if(!empty($foxtool_notify_options['notify-cookie-l1'])){echo sanitize_text_field($foxtool_notify_options['notify-cookie-l1']);} ?>"/>
				</p>
				<p>
				<input class="ft-input-big" placeholder="<?php _e('Enter cookie title', 'foxtool') ?>" name="foxtool_notify_settings[notify-cookie11]" type="text" value="<?php if(!empty($foxtool_notify_options['notify-cookie11'])){echo sanitize_text_field($foxtool_notify_options['notify-cookie11']);} ?>"/>
				</p>
				<p>
				<textarea style="height:150px;" class="ft-code-textarea" name="foxtool_notify_settings[notify-cookie12]" placeholder="<?php _e('Enter cookie content', 'foxtool'); ?>"><?php if(!empty($foxtool_notify_options['notify-cookie12'])){echo esc_textarea($foxtool_notify_options['notify-cookie12']);} ?></textarea>
				</p>  
				<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Display your cookie notice to inform users about cookie use and allow them to manage their preferences easily', 'foxtool'); ?></p> 
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
			// ajax select
			$('form input[type="checkbox"]').change(function() {
				var currentForm = $(this).closest('form');
				$.ajax({
					type: 'POST',
					url: currentForm.attr('action'), 
					data: currentForm.serialize(), 
					success: function(response) {
						console.log('Turn on successfully');
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
function foxtool_notify_options_link() {
	add_submenu_page ('foxtool-options', 'Notify', '<i class="fa-regular fa-bell" style="width:20px;"></i> '. __('Notify', 'foxtool'), 'manage_options', 'foxtool-notify-options', 'foxtool_notify_options_page');
}
add_action('admin_menu', 'foxtool_notify_options_link');
function foxtool_notify_register_settings() {
	register_setting('foxtool_notify_settings_group', 'foxtool_notify_settings');
}
add_action('admin_init', 'foxtool_notify_register_settings');
// clear cache
function foxtool_notify_settings_cache($old_value, $value) {
    wp_cache_delete('foxtool_notify_settings', 'options');
}
add_action('update_option_foxtool_notify_settings', 'foxtool_notify_settings_cache', 10, 2);

