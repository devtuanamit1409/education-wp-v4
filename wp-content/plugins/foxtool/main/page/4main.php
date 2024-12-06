<?php 
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $foxtool_options; ?>
<h2><?php _e('DISPLAY', 'foxtool'); ?></h2>
<div class="ft-on">
<label class="nut-fton">
<input class="toggle-checkbox" id="check4" data-target="play4" type="checkbox" name="foxtool_settings[main]" value="1" <?php if ( isset($foxtool_options['main']) && 1 == $foxtool_options['main'] ) echo 'checked="checked"'; ?> />
<span class="ftder"></span></label>
<label class="ft-on-right"><?php _e('ON/OFF', 'foxtool'); ?></label>
</div>
<div id="play4" class="ft-card toggle-div">
  <h3><i class="fa-regular fa-icons"></i> <?php _e('Add font Awesome to the website', 'foxtool') ?></h3>
	<!-- main add font icon 1 -->
	<label class="nut-switch">
	<input type="checkbox" name="foxtool_settings[main-add1]" value="1" <?php if ( isset($foxtool_options['main-add1']) && 1 == $foxtool_options['main-add1'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ft-label-right"><?php _e('Enable font Awesome', 'foxtool'); ?></label>
	<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('If you want to use font Awesome, you can enable it (it an icon font). You can search for icons at:', 'foxtool'); ?><br>
	<b><a target="_blank" href="https://fontawesome.com/search"><?php _e('Access Font Awesome to find icons', 'foxtool'); ?></a></b><br>
	<?php _e('Free to use "fa-regular" and "fa-brands" styles', 'foxtool'); ?>
	</p>
  <h3><i class="fa-regular fa-snowflake"></i> <?php _e('Decorative effects for the website', 'foxtool') ?></h3>
	<!-- add font Google 1 -->
	<?php $styles = array('None', 'Snow1', 'Snow2', 'Lunar1', 'Lunar2', 'Vietnam', 'Indonesia'); ?>
	<select name="foxtool_settings[main-hover1]"> 
	<?php foreach($styles as $style) { ?> 
	<?php if(isset($foxtool_options['main-hover1']) && $foxtool_options['main-hover1'] == $style) { $selected = 'selected="selected"'; } else { $selected = ''; } ?>
	<option value="<?php echo $style; ?>" <?php echo $selected; ?>><?php echo $style; ?></option> 
	<?php } ?> 
	</select>
	<label class="ft-right-text"><?php _e('Effect', 'foxtool'); ?></label>
	<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Choose decorations for the website, such as Christmas or Lunar New Year (If any effects cause issues on your website, it may be due to javascript conflicts. You can switch to other effects to use)', 'foxtool'); ?></p>
  <h3><i class="fa-regular fa-circle-half-stroke"></i> <?php _e('Dark mode', 'foxtool') ?></h3>
	<!-- darkmode -->
	<p>
	<label class="nut-switch">
	<input type="checkbox" name="foxtool_settings[main-mode1]" value="1" <?php if ( isset($foxtool_options['main-mode1']) && 1 == $foxtool_options['main-mode1'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ft-label-right"><?php _e('Enable dark mode', 'foxtool'); ?></label>
	</p>
	<p style="display:flex;align-items:center;">
	<input class="ft-input-color" name="foxtool_settings[main-mode-c1]" type="text" data-coloris value="<?php if(!empty($foxtool_options['main-mode-c1'])){echo sanitize_text_field($foxtool_options['main-mode-c1']);} ?>"/>
	<label class="ft-right-text"><?php _e('Icon color', 'foxtool'); ?></label>
	</p>
	<p>
	<?php $styles = array('Default', 'Toggle', 'System', 'Time'); ?>
	<select name="foxtool_settings[main-mode10]"> 
	<?php foreach($styles as $style) { ?> 
	<?php if(isset($foxtool_options['main-mode10']) && $foxtool_options['main-mode10'] == $style) { $selected = 'selected="selected"'; } else { $selected = ''; } ?>
	<option value="<?php echo $style; ?>" <?php echo $selected; ?>><?php echo $style; ?></option> 
	<?php } ?> 
	</select>
	<label class="ft-right-text"><?php _e('Display type', 'foxtool'); ?></label>
	</p>
	<p>
	<?php $styles = array('Left', 'Right'); ?>
	<select name="foxtool_settings[main-mode11]"> 
	<?php foreach($styles as $style) { ?> 
	<?php if(isset($foxtool_options['main-mode11']) && $foxtool_options['main-mode11'] == $style) { $selected = 'selected="selected"'; } else { $selected = ''; } ?>
	<option value="<?php echo $style; ?>" <?php echo $selected; ?>><?php echo $style; ?></option> 
	<?php } ?> 
	</select>
	<label class="ft-right-text"><?php _e('Location', 'foxtool'); ?></label>
	</p>
	<p class="ft-keo">
	<input type="range" name="foxtool_settings[main-mode12]" min="10" max="300" value="<?php if(!empty($foxtool_options['main-mode12'])){echo sanitize_text_field($foxtool_options['main-mode12']);} else { echo sanitize_text_field('30');} ?>" class="ftslide" data-index="5">
	<span><?php _e('Spacing below', 'foxtool'); ?> <span id="demo5"></span> PX</span>
	</p>
	<p class="ft-keo">
	<input type="range" name="foxtool_settings[main-mode13]" min="10" max="100" value="<?php if(!empty($foxtool_options['main-mode13'])){echo sanitize_text_field($foxtool_options['main-mode13']);} else { echo sanitize_text_field('30');} ?>" class="ftslide" data-index="6">
	<span><?php _e('Border distance', 'foxtool'); ?> <span id="demo6"></span> PX</span>
	</p>
	<p>
	<label class="nut-switch">
	<input type="checkbox" name="foxtool_settings[main-mode-s1]" value="1" <?php if ( isset($foxtool_options['main-mode-s1']) && 1 == $foxtool_options['main-mode-s1'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ft-label-right"><?php _e('Use shortcodes', 'foxtool'); ?></label>
	</p>
	<input class="ft-input-big ft-view-in" type="text" value="[foxdark]"/>
	<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('This function allows you to initiate a dark mode library, enabling the website to switch between light and dark modes', 'foxtool'); ?></p>
   <h3><i class="fa-regular fa-computer-mouse-scrollwheel"></i> <?php _e('Customize scroll bar', 'foxtool') ?></h3>
	<!-- main add font icon 1 -->
	<label class="nut-switch">
	<input type="checkbox" name="foxtool_settings[main-scroll1]" value="1" <?php if ( isset($foxtool_options['main-scroll1']) && 1 == $foxtool_options['main-scroll1'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ft-label-right"><?php _e('Enable', 'foxtool'); ?></label>
	<p style="display:flex;align-items:center;">
	<input class="ft-input-color" name="foxtool_settings[main-scroll11]" type="text" data-coloris value="<?php if(!empty($foxtool_options['main-scroll11'])){echo sanitize_text_field($foxtool_options['main-scroll11']);} ?>"/>
	<label class="ft-right-text"><?php _e('Background color', 'foxtool'); ?></label>
	</p>
	<p style="display:flex;align-items:center;">
	<input class="ft-input-color" name="foxtool_settings[main-scroll12]" type="text" data-coloris value="<?php if(!empty($foxtool_options['main-scroll12'])){echo sanitize_text_field($foxtool_options['main-scroll12']);} ?>"/>
	<label class="ft-right-text"><?php _e('Bar color', 'foxtool'); ?></label>
	</p>
	<p class="ft-keo">
	<input type="range" name="foxtool_settings[main-scroll13]" min="1" max="10" value="<?php if(!empty($foxtool_options['main-scroll13'])){echo sanitize_text_field($foxtool_options['main-scroll13']);} else { echo sanitize_text_field('1');} ?>" class="ftslide" data-index="13">
	<span><?php _e('Border radius', 'foxtool'); ?> <span id="demo13"></span> PX</span>
	</p>
	<p class="ft-keo">
	<input type="range" name="foxtool_settings[main-scroll14]" min="7" max="20" value="<?php if(!empty($foxtool_options['main-scroll14'])){echo sanitize_text_field($foxtool_options['main-scroll14']);} else { echo sanitize_text_field('10');} ?>" class="ftslide" data-index="14">
	<span><?php _e('Bar size', 'foxtool'); ?> <span id="demo14"></span> PX</span>
	</p>
	<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('This function allows you to customize the color of the scrollbar to your liking', 'foxtool'); ?></p>
</div>	