<?php 
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $foxtool_options; ?>
<h2><?php _e('TOOL', 'foxtool'); ?></h2>
<div class="ft-on">
<label class="nut-fton">
<input class="toggle-checkbox" id="check3" data-target="play3" type="checkbox" name="foxtool_settings[tool]" value="1" <?php if ( isset($foxtool_options['tool']) && 1 == $foxtool_options['tool'] ) echo 'checked="checked"'; ?> />
<span class="ftder"></span></label>
<label class="ft-on-right"><?php _e('ON/OFF', 'foxtool'); ?></label>
</div>
<div id="play3" class="ft-card toggle-div">
  <h3><i class="fa-regular fa-pen-to-square"></i> <?php _e('Text editor tool', 'foxtool') ?></h3>
	<!-- tool class 1 -->
	<label class="nut-switch">
	<input type="checkbox" name="foxtool_settings[tool-edit1]" value="1" <?php if ( isset($foxtool_options['tool-edit1']) && 1 == $foxtool_options['tool-edit1'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ft-label-right"><?php _e('Enable Classic Editor', 'foxtool'); ?></label>
	<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('If you find the new editor too difficult to use, then revert it to the Classic Editor version', 'foxtool'); ?></p>
	<!-- tool class 11 -->
	<label class="nut-switch">
	<input type="checkbox" name="foxtool_settings[tool-edit11]" value="1" <?php if ( isset($foxtool_options['tool-edit11']) && 1 == $foxtool_options['tool-edit11'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ft-label-right"><?php _e('Enhance features for Classic Editor', 'foxtool'); ?></label>
	<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Enable this feature if you want to add additional functionalities to the Classic Editor to enhance professional editing', 'foxtool'); ?></p>
	<!-- tool class 11 -->
	<label class="nut-switch">
	<input type="checkbox" name="foxtool_settings[tool-edit12]" value="1" <?php if ( isset($foxtool_options['tool-edit12']) && 1 == $foxtool_options['tool-edit12'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ft-label-right"><?php _e('Add Classic Editor button', 'foxtool'); ?></label>
	<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Enable this feature if you want to add the Classic Editor button in the post and page management interface. With this feature, you dont need to set the Classic Editor as default but can use it in parallel', 'foxtool'); ?></p>
  <h3><i class="fa-regular fa-box"></i> <?php _e('Optimize Widgets', 'foxtool') ?></h3>
	<!-- tool class 1 -->
	<label class="nut-switch">
	<input type="checkbox" name="foxtool_settings[tool-widget1]" value="1" <?php if ( isset($foxtool_options['tool-widget1']) && 1 == $foxtool_options['tool-widget1'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ft-label-right"><?php _e('Enable Classic Widget', 'foxtool'); ?></label>
	<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('If you find the new Widget Manager too difficult to use, then revert it to the Classic Widget version', 'foxtool'); ?></p>
	
  <h3><i class="fa-regular fa-gear"></i> <?php _e('Disable automatic updates', 'foxtool') ?></h3>
	<!-- tool off upload 1 -->
	<p>
	<label class="nut-switch">
	<input type="checkbox" name="foxtool_settings[tool-upload1]" value="1" <?php if ( isset($foxtool_options['tool-upload1']) && 1 == $foxtool_options['tool-upload1'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ft-label-right"><?php _e('Disable core updates', 'foxtool'); ?></label>
	</p>
	<p>
	<!-- tool off upload 2 -->
	<label class="nut-switch">
	<input type="checkbox" name="foxtool_settings[tool-upload2]" value="1" <?php if ( isset($foxtool_options['tool-upload2']) && 1 == $foxtool_options['tool-upload2'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ft-label-right"><?php _e('Disable language pack updates', 'foxtool'); ?></label>
	</p>
	<p>
	<!-- tool off upload 3 -->
	<label class="nut-switch">
	<input type="checkbox" name="foxtool_settings[tool-upload3]" value="1" <?php if ( isset($foxtool_options['tool-upload3']) && 1 == $foxtool_options['tool-upload3'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ft-label-right"><?php _e('Disable theme updates', 'foxtool'); ?></label>
	</p>
	<p>
	<!-- tool off upload 4 -->
	<label class="nut-switch">
	<input type="checkbox" name="foxtool_settings[tool-upload4]" value="1" <?php if ( isset($foxtool_options['tool-upload4']) && 1 == $foxtool_options['tool-upload4'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ft-label-right"><?php _e('Disable plugin updates', 'foxtool'); ?></label>
	</p>
	<!-- tool off upload 5 -->
	<label class="nut-switch">
	<input type="checkbox" name="foxtool_settings[tool-upload5]" value="1" <?php if ( isset($foxtool_options['tool-upload5']) && 1 == $foxtool_options['tool-upload5'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ft-label-right"><?php _e('Disable update & maintenance notifications', 'foxtool'); ?></label>
	</p>
	<!-- tool off upload 6 -->
	<label class="nut-switch">
	<input type="checkbox" name="foxtool_settings[tool-upload6]" value="1" <?php if ( isset($foxtool_options['tool-upload6']) && 1 == $foxtool_options['tool-upload6'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ft-label-right"><?php _e('Disable automatic update checking (core, language packs, themes, plugins)', 'foxtool'); ?></label>
	</p>
	
	<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('You can disable the automatic update feature of WordPress', 'foxtool'); ?></p>	
	
  <h3><i class="fa-regular fa-gear"></i> <?php _e('Management tool', 'foxtool') ?></h3>
	<!-- tool manager 2 -->
	<label class="nut-switch">
	<input type="checkbox" name="foxtool_settings[tool-mana2]" value="1" <?php if ( isset($foxtool_options['tool-mana2']) && 1 == $foxtool_options['tool-mana2'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ft-label-right"><?php _e('Disallow text copying and access to DevTools', 'foxtool'); ?></label>
	<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('This function prevents users from copying text, accessing right-click options, and accessing DevTools', 'foxtool'); ?></p>
	
	<label class="nut-switch">
	<input type="checkbox" name="foxtool_settings[tool-mana21]" value="1" <?php if ( isset($foxtool_options['tool-mana21']) && 1 == $foxtool_options['tool-mana21'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ft-label-right"><?php _e('Copy pre-set content', 'foxtool'); ?></label>
	<p>
	<label class="nut-switch">
	<input type="checkbox" name="foxtool_settings[tool-mana23]" value="1" <?php if ( isset($foxtool_options['tool-mana23']) && 1 == $foxtool_options['tool-mana23'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ft-label-right"><?php _e('Attach copyright content', 'foxtool'); ?></label>
	</p>
	<p>
	<input class="ft-input-big" placeholder="<?php _e('Enter content here', 'foxtool'); ?>" name="foxtool_settings[tool-mana22]" type="text" value="<?php if(!empty($foxtool_options['tool-mana22'])){echo sanitize_text_field($foxtool_options['tool-mana22']);} ?>"/>
	</p>
	<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('If users copy content on the page, instead of receiving the content, they will receive the content you have set', 'foxtool'); ?></p>
	
	<!-- tool manager 2 -->
	<label class="nut-switch">
	<input type="checkbox" name="foxtool_settings[tool-mana3]" value="1" <?php if ( isset($foxtool_options['tool-mana3']) && 1 == $foxtool_options['tool-mana3'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ft-label-right"><?php _e('Enable Classic Editor in category description', 'foxtool'); ?></label>
	<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('This feature allows you to add the Classic Editor to the category description box when editing posts or products', 'foxtool'); ?></p>
  
  <h3><i class="fa-regular fa-eye-slash"></i> <?php _e('Hide the tools you want', 'foxtool') ?></h3>
	<?php global $menu;
	if (is_array($menu)) {
		foreach ($menu as $index => $item) { 
			if(!empty($item[0])){ ?>
			<p>
			<label class="nut-switch">
			<input type="checkbox" name="foxtool_settings[tool-hiden<?php echo $index; ?>]" value="1" <?php if ( isset($foxtool_options['tool-hiden'. $index]) && 1 == $foxtool_options['tool-hiden'. $index] ) echo 'checked="checked"'; ?> />
			<span class="slider"></span></label>
			<label class="ft-label-right"><?php echo preg_replace('/\d/', '', $item[0]); ?></label>
			</p>
			<?php } 
		} 
	} ?>
	<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('If you find the tools above unnecessary, you can hide them to make the WP admin interface cleaner. This function only hides them without blocking access to their links', 'foxtool'); ?></p>
</div>	