<?php 
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $foxtool_options; ?>
<h2><?php _e('SETTING', 'foxtool'); ?></h2>
  <h3><i class="fa-regular fa-bomb"></i> <?php _e('Advanced settings', 'foxtool') ?></h3>
	<!-- foxtool 1 -->
	<label class="nut-switch">
	<input type="checkbox" name="foxtool_settings[foxtool1]" value="1" <?php if ( isset($foxtool_options['foxtool1']) && 1 == $foxtool_options['foxtool1'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ft-label-right"><?php _e('Hide Admin account from profile page', 'foxtool'); ?></label>
	<p>
	<?php
	$foxadmins = foxtool_get_admin_users();
    echo '<select name="foxtool_settings[foxtool11]">';
	echo '<option value="">'. __('Do not select', 'foxtool') .'</option>';
    foreach ($foxadmins as $admin) {
		$selected = isset($foxtool_options['foxtool11']) && $foxtool_options['foxtool11'] == $admin->ID ? 'selected="selected"' : '';
        echo '<option value="' . esc_attr($admin->ID) . '" '. $selected .'>' . esc_html($admin->display_name) . '</option>';
    }
    echo '</select>';
	?>
	<label class="ft-right-text"><?php _e('Select admin', 'foxtool'); ?></label>
	</p>
	<p class="ft-note ft-note-red"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('If you want to hide a specific Administrator account, select the user', 'foxtool'); ?></p>
	<!-- foxtool 1 -->
	<label class="nut-switch">
	<input type="checkbox" name="foxtool_settings[foxtool2]" value="1" <?php if ( isset($foxtool_options['foxtool2']) && 1 == $foxtool_options['foxtool2'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ft-label-right"><?php _e('Limit Foxtool display', 'foxtool'); ?></label>
	<p>
	<?php
    echo '<select name="foxtool_settings[foxtool21]">';
	echo '<option value="">'. __('Do not select', 'foxtool') .'</option>';
    foreach ($foxadmins as $admin) {
		$selected = isset($foxtool_options['foxtool21']) && $foxtool_options['foxtool21'] == $admin->ID ? 'selected="selected"' : '';
        echo '<option value="' . esc_attr($admin->ID) . '" '. $selected .'>' . esc_html($admin->display_name) . '</option>';
    }
    echo '</select>';
	?>
	<label class="ft-right-text"><?php _e('Select admin', 'foxtool'); ?></label>
	</p>
	<p class="ft-note ft-note-red"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('This feature allows you to only display Foxtool to a specific Admin account', 'foxtool'); ?><br>
	<b><?php _e('Delete settings:', 'foxtool'); ?> <?php echo admin_url('?del=adminfoxtool');?></b>
	</p>
  <h3><i class="fa-regular fa-eye-slash"></i> <?php _e('Hide Foxtool', 'foxtool') ?></h3>
	<!-- tool hiden 1 -->
	<p>
	<label class="nut-switch">
	<input type="checkbox" name="foxtool_settings[foxtool3]" value="1" <?php if ( isset($foxtool_options['foxtool3']) && 1 == $foxtool_options['foxtool3'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ft-label-right"><?php _e('Hide Foxtool', 'foxtool'); ?></label>
	</p>
	<p class="ft-note ft-note-red"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('You can hide Foxtool from the WP menu, but you can still access it through the link. This will hide Foxtool for all accounts', 'foxtool'); ?><br>
	<b><?php echo admin_url('/admin.php?page=foxtool-options');?></b>
	</p>
	<!-- tool hiden 2 -->
	<p>
	<label class="nut-switch">
	<input type="checkbox" name="foxtool_settings[foxtool4]" value="1" <?php if ( isset($foxtool_options['foxtool4']) && 1 == $foxtool_options['foxtool4'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ft-label-right"><?php _e('Hide Foxtool in the plugin manager', 'foxtool'); ?></label>
	</p>
	<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('This feature will hide Foxtool from the plugin management page', 'foxtool'); ?>
	</p>
  <h3><i class="fa-regular fa-language"></i> <?php _e('Display language settings', 'foxtool') ?></h3>	
	<p>
	<?php $styles = array('Automatic', 'English', 'Việt Nam', 'Indonesia'); ?>
	<select name="foxtool_settings[lang]"> 
	<?php foreach($styles as $style) { ?> 
	<?php if(isset($foxtool_options['lang']) && $foxtool_options['lang'] == $style) { $selected = 'selected="selected"'; } else { $selected = ''; } ?>
	<option value="<?php echo $style; ?>" <?php echo $selected; ?>><?php echo $style; ?></option> 
	<?php } ?> 
	</select>
	<label class="ft-right-text"><?php _e('Language', 'foxtool'); ?></label>
	</p>
	<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('If you dont want to automatically switch the language based on WordPress context, please select the language you prefer', 'foxtool'); ?>
	</p>
  <h3><i class="fa-regular fa-palette"></i> <?php _e('Customize display', 'foxtool') ?></h3>
	<div id="ft-imgstyle" class="ft-imgstyle">
		<img src="<?php echo esc_url(FOXTOOL_URL .'img/style/1.jpg'); ?>" data-value="Default" class="<?php if(isset($foxtool_options['foxtool5']) && $foxtool_options['foxtool5'] == 'Default') echo 'selected'; ?>" />
		<img src="<?php echo esc_url(FOXTOOL_URL .'img/style/2.jpg'); ?>" data-value="WordPress" class="<?php if(isset($foxtool_options['foxtool5']) && $foxtool_options['foxtool5'] == 'WordPress') echo 'selected'; ?>" />
		<img src="<?php echo esc_url(FOXTOOL_URL .'img/style/3.jpg'); ?>" data-value="Bright" class="<?php if(isset($foxtool_options['foxtool5']) && $foxtool_options['foxtool5'] == 'Bright') echo 'selected'; ?>" />
		<img src="<?php echo esc_url(FOXTOOL_URL .'img/style/4.jpg'); ?>" data-value="Girly" class="<?php if(isset($foxtool_options['foxtool5']) && $foxtool_options['foxtool5'] == 'Girly') echo 'selected'; ?>" />
		<img src="<?php echo esc_url(FOXTOOL_URL .'img/style/5.jpg'); ?>" data-value="Black" class="<?php if(isset($foxtool_options['foxtool5']) && $foxtool_options['foxtool5'] == 'Black') echo 'selected'; ?>" />
		<img src="<?php echo esc_url(FOXTOOL_URL .'img/style/6.jpg'); ?>" data-value="Coffe" class="<?php if(isset($foxtool_options['foxtool5']) && $foxtool_options['foxtool5'] == 'Coffe') echo 'selected'; ?>" />
		<img src="<?php echo esc_url(FOXTOOL_URL .'img/style/7.jpg'); ?>" data-value="Rocket" class="<?php if(isset($foxtool_options['foxtool5']) && $foxtool_options['foxtool5'] == 'Rocket') echo 'selected'; ?>" />
		<img src="<?php echo esc_url(FOXTOOL_URL .'img/style/8.jpg'); ?>" data-value="Blue" class="<?php if(isset($foxtool_options['foxtool5']) && $foxtool_options['foxtool5'] == 'Blue') echo 'selected'; ?>" />
	</div>
	<input type="hidden" name="foxtool_settings[foxtool5]" id="foxtool5" value="<?php if(!empty($foxtool_options['foxtool5'])){echo sanitize_text_field($foxtool_options['foxtool5']);} else {echo sanitize_text_field('Default');} ?>" />
	<script>
		document.addEventListener("DOMContentLoaded", function() {
			var imgStyles = document.querySelectorAll('#ft-imgstyle img');
			imgStyles.forEach(function(img) {
				img.addEventListener('click', function() {
					var selectedStyle = this.getAttribute('data-value');
					document.getElementById('foxtool5').value = selectedStyle;
					imgStyles.forEach(function(img) {
						img.classList.remove('selected');
					});
					this.classList.add('selected');
				});
			});
		});
		jQuery(document).ready(function($) {
			$('#ft-imgstyle img').click(function() { 
				var selectedStyle = $(this).attr('data-value');
				$('#foxtool5').val(selectedStyle);
				$('.ft-imgstyle img').removeClass('selected');
				$(this).addClass('selected');
				var currentForm = $(this).closest('form');
				$.ajax({
					type: 'POST',
					url: currentForm.attr('action'),
					data: currentForm.serialize(),
					success: function(response) {
						location.reload(); 
					},
				});
			});
		});
	</script>
	<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Choose display interface according to your preference', 'foxtool'); ?>
	</p>
	<p>
	<input class="ft-input-big" placeholder="<?php _e('Enter name', 'foxtool'); ?>" name="foxtool_settings[foxtool6]" type="text" value="<?php if(!empty($foxtool_options['foxtool6'])){echo sanitize_text_field($foxtool_options['foxtool6']);} ?>"/>
	</p>
	<p>
	<?php $styles = array('icon 1', 'icon 2', 'icon 3', 'icon 4', 'icon 5', 'icon 6'); ?>
	<select name="foxtool_settings[foxtool61]"> 
	<?php foreach($styles as $style) { ?> 
	<?php if(isset($foxtool_options['foxtool61']) && $foxtool_options['foxtool61'] == $style) { $selected = 'selected="selected"'; } else { $selected = ''; } ?>
	<option value="<?php echo $style; ?>" <?php echo $selected; ?>><?php echo $style; ?></option> 
	<?php } ?> 
	</select>
	<label class="ft-right-text"><?php _e('Icon', 'foxtool'); ?></label>
	</p>
	<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Change display name and icon to your preference', 'foxtool'); ?>
	</p>
	