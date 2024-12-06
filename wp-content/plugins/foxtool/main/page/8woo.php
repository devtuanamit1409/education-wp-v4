<?php 
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $foxtool_options; ?>
<h2><?php _e('WOOCOMMERCE', 'foxtool'); ?></h2>
<div class="ft-on">
<label class="nut-fton">
<input class="toggle-checkbox" id="check8" data-target="play8" type="checkbox" name="foxtool_settings[woo]" value="1" <?php if ( isset($foxtool_options['woo']) && 1 == $foxtool_options['woo'] ) echo 'checked="checked"'; ?> />
<span class="ftder"></span></label>
<label class="ft-on-right"><?php _e('ON/OFF', 'foxtool'); ?></label>
</div>
<div id="play8" class="ft-card toggle-div">
  <h3><i class="fa-regular fa-text-size"></i> <?php _e('Modify content', 'foxtool') ?></h3>
	<p>
	<input class="ft-input-big" placeholder="<?php _e('Change the Buy Now text on the single product page', 'foxtool'); ?>" name="foxtool_settings[woo-text1]" type="text" value="<?php if(!empty($foxtool_options['woo-text1'])){echo sanitize_text_field($foxtool_options['woo-text1']);} ?>"/>
	</p>
	<p>
	<input class="ft-input-big" placeholder="<?php _e('The text to replace Buy Now on the product page', 'foxtool'); ?>" name="foxtool_settings[woo-text2]" type="text" value="<?php if(!empty($foxtool_options['woo-text2'])){echo sanitize_text_field($foxtool_options['woo-text2']);} ?>"/>
	</p>
	<p>
	<input class="ft-input-big" placeholder="<?php _e('Display text if the price is 0', 'foxtool'); ?>" name="foxtool_settings[woo-text3]" type="text" value="<?php if(!empty($foxtool_options['woo-text3'])){echo sanitize_text_field($foxtool_options['woo-text3']);} ?>"/>
	</p>
	<p>
	<input class="ft-input-big" placeholder="<?php _e('Display text if out of stock', 'foxtool'); ?>" name="foxtool_settings[woo-text4]" type="text" value="<?php if(!empty($foxtool_options['woo-text4'])){echo sanitize_text_field($foxtool_options['woo-text4']);} ?>"/>
	</p>
	<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Modify some information on Woocommerce that you want', 'foxtool'); ?></p>
  <h3><i class="fa-regular fa-text-size"></i> <?php _e('Customize Currency Unit', 'foxtool') ?></h3>
	<p>
	<input class="ft-input-big" placeholder="<?php _e('Enter units', 'foxtool'); ?>" name="foxtool_settings[woo-cy1]" type="text" value="<?php if(!empty($foxtool_options['woo-cy1'])){echo sanitize_text_field($foxtool_options['woo-cy1']);} ?>"/>
	</p>
	<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('This feature allows you to customize the currency symbol as desired', 'foxtool'); ?></p>
	
  <h3><i class="fa-brands fa-telegram"></i> <?php _e('Configure order notifications to be sent to Telegram', 'foxtool') ?></h3>
	<p>
	<label class="nut-switch">
	<input type="checkbox" name="foxtool_settings[woo-tele1]" value="1" <?php if (isset($foxtool_options['woo-tele1']) && 1 == $foxtool_options['woo-tele1']) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ft-label-right"><?php _e('Enable notifications', 'foxtool'); ?></label>
	</p>
	<p><input class="ft-input-big"  placeholder="<?php _e('API Token', 'foxtool'); ?>" name="foxtool_settings[woo-tele11]" type="text" value="<?php if(!empty($foxtool_options['woo-tele11'])){echo sanitize_text_field($foxtool_options['woo-tele11']);} ?>"/></p>
	<p><input class="ft-input-big"  placeholder="<?php _e('Chat ID', 'foxtool'); ?>" name="foxtool_settings[woo-tele12]" type="text" value="<?php if(!empty($foxtool_options['woo-tele12'])){echo sanitize_text_field($foxtool_options['woo-tele12']);} ?>"/></p>
	<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('With this feature, you can notify your orders to your Telegram group, helping you manage orders conveniently', 'foxtool'); ?></p>
</div>	