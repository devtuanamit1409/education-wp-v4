<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
function foxtool_ads_options_page() {
	global $foxtool_ads_options;
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
			<button class="sotab sotab-select" onclick="fttab(event, 'tab1')"><i class="fa-regular fa-computer-mouse"></i> <?php _e('CLICK', 'foxtool'); ?></button>
			<button class="sotab" onclick="fttab(event, 'tab2')"><i class="fa-regular fa-rectangle-ad"></i> <?php _e('ADSENSE', 'foxtool'); ?></button>
			<button class="sotab" onclick="fttab(event, 'tab3')"><i class="fa-regular fa-file-check"></i> <?php _e('ADS.TXT', 'foxtool'); ?></button>
		</div>

		<div class="ft-main">
			<?php 
			if( isset($_GET['settings-updated']) ) { 
				require_once( FOXTOOL_DIR . 'main/completed.php'); 
			}
			?>
			<form method="post" action="options.php">
			<?php settings_fields('foxtool_ads_settings_group'); ?> 
			<!-- CLICK -->
			<div class="sotab-box ftbox" id="tab1" >
			<h2><?php _e('CLICK', 'foxtool'); ?></h2>
			<div class="ft-card">
			   <h3><i class="fa-regular fa-computer-mouse"></i> <?php _e('Ads appear when clicking on the website', 'foxtool') ?></h3>
				<p>
				<label class="nut-switch">
				<input type="checkbox" name="foxtool_ads_settings[ads-click1]" value="1" <?php if ( isset($foxtool_ads_options['ads-click1']) && 1 == $foxtool_ads_options['ads-click1'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ft-label-right"><?php _e('Enable annoying ads', 'foxtool'); ?></label>
				</p>
				<p>
				<label class="nut-switch">
				<input type="checkbox" name="foxtool_ads_settings[ads-click-c1]" value="1" <?php if ( isset($foxtool_ads_options['ads-click-c1']) && 1 == $foxtool_ads_options['ads-click-c1'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ft-label-right"><?php _e('Enable minimize the window', 'foxtool'); ?></label>
				</p>
				<p>
				<input class="ft-input-small" name="foxtool_ads_settings[ads-click-c2]" type="number" placeholder="24" value="<?php if(!empty($foxtool_ads_options['ads-click-c2'])){echo sanitize_text_field($foxtool_ads_options['ads-click-c2']);} ?>"/>
				<label class="ft-label-right"><?php _e('Ads display after (.. hours)', 'foxtool'); ?></label>
				</p>
				<p>
				<?php $styles = array('Body', 'Link'); ?>
				<select name="foxtool_ads_settings[ads-click-c3]"> 
				<?php foreach($styles as $style) { ?> 
				<?php if(isset($foxtool_ads_options['ads-click-c3']) && $foxtool_ads_options['ads-click-c3'] == $style) { $selected = 'selected="selected"'; } else { $selected = ''; } ?>
				<option value="<?php echo $style; ?>" <?php echo $selected; ?>><?php echo $style; ?></option> 
				<?php } ?> 
				</select>
				<label class="ft-right-text"><?php _e('Action taken', 'foxtool'); ?></label>
				</p>
				<p>
				<textarea style="height:200px;" class="ft-code-textarea" name="foxtool_ads_settings[ads-click11]" placeholder="<?php _e('Enter ad links per line', 'foxtool'); ?>"><?php if(!empty($foxtool_ads_options['ads-click11'])){echo esc_textarea($foxtool_ads_options['ads-click11']);} ?></textarea>
				</p>
				<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Enable and enter ad links you want, each on a separate line, for automatic rotation each time a user visits', 'foxtool'); ?></p>
			</div>
			</div>
			<!-- ADSENSE -->
			<div class="sotab-box ftbox" id="tab2" style="display:none">
			<h2><?php _e('ADSENSE', 'foxtool'); ?></h2>
			<div class="ft-card">
			   <h3><i class="fa-regular fa-rectangle-ad"></i> <?php _e('Set up Adsense ads', 'foxtool') ?></h3>
				<p>
				<label class="nut-switch">
				<input type="checkbox" name="foxtool_ads_settings[ads-sense1]" value="1" <?php if ( isset($foxtool_ads_options['ads-sense1']) && 1 == $foxtool_ads_options['ads-sense1'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ft-label-right"><?php _e('Enable Adsense', 'foxtool'); ?></label>
				</p>
				<p>
				<textarea style="height:90px;" class="ft-code-textarea" name="foxtool_ads_settings[ads-sense11]" placeholder="<?php _e('Enter Adsense code here', 'foxtool'); ?>"><?php if(!empty($foxtool_ads_options['ads-sense11'])){echo esc_textarea($foxtool_ads_options['ads-sense11']);} ?></textarea>
				</p>
				<h4><?php _e('Option to display ads in custom posts', 'foxtool') ?></h4>
				<?php 
				$args = array(
				'public'   => true,
				);
				$post_types = get_post_types($args, 'objects'); 
				foreach ($post_types as $post_type_object) {
					if ($post_type_object->name == 'attachment') {
						continue;
					}
					?>
					<label class="nut-switch">
						<input type="checkbox" name="foxtool_ads_settings[posttype][]" value="<?php echo $post_type_object->name; ?>" <?php if (isset($foxtool_ads_options['posttype']) && in_array($post_type_object->name, $foxtool_ads_options['posttype'])) echo 'checked="checked"'; ?> />
						<span class="slider"></span>
					</label>
					<label class="ft-label-right"><?php echo $post_type_object->labels->name; ?></label>
					</p>
					<?php
				}
				?>
				<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Select the custom post for which you want to show ads', 'foxtool'); ?></p>
				<h5><?php _e('Top and bottom positions', 'foxtool') ?></h5>
				<p>
				<textarea style="height:90px;" class="ft-code-textarea" name="foxtool_ads_settings[ads-sense-p1]" placeholder="<?php _e('Add ads to the top of the article', 'foxtool'); ?>"><?php if(!empty($foxtool_ads_options['ads-sense-p1'])){echo esc_textarea($foxtool_ads_options['ads-sense-p1']);} ?></textarea>
				</p>
				<p>
				<textarea style="height:90px;" class="ft-code-textarea" name="foxtool_ads_settings[ads-sense-p2]" placeholder="<?php _e('Add ads at the bottom of the article', 'foxtool'); ?>"><?php if(!empty($foxtool_ads_options['ads-sense-p2'])){echo esc_textarea($foxtool_ads_options['ads-sense-p2']);} ?></textarea>
				</p>
				<h5><?php _e('Custom position in posts', 'foxtool') ?></h5>
				<p>
				<input class="ft-input-small" placeholder="<?php _e('Tag', 'foxtool') ?>" name="foxtool_ads_settings[ads-sense-c1]" type="text" value="<?php if(!empty($foxtool_ads_options['ads-sense-c1'])){echo sanitize_text_field($foxtool_ads_options['ads-sense-c1']);} else {echo sanitize_text_field('p');} ?>"/>
				<input class="ft-input-small" placeholder="<?php _e('Quantity', 'foxtool') ?>" name="foxtool_ads_settings[ads-sense-c2]" type="number" value="<?php if(!empty($foxtool_ads_options['ads-sense-c2'])){echo sanitize_text_field($foxtool_ads_options['ads-sense-c2']);} else {echo sanitize_text_field('10');} ?>"/>
				</p>
				<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Enter the tag and tag number for the ad to appear', 'foxtool'); ?></p>
				<p>
				<textarea style="height:90px;" class="ft-code-textarea" name="foxtool_ads_settings[ads-sense-c3]" placeholder="<?php _e('Add ads to custom post placement', 'foxtool'); ?>"><?php if(!empty($foxtool_ads_options['ads-sense-c3'])){echo esc_textarea($foxtool_ads_options['ads-sense-c3']);} ?></textarea>
				</p>
			</div>
			</div>
			<!-- ADSTXT -->
			<div class="sotab-box ftbox" id="tab3" style="display:none">
			<h2><?php _e('ADS.TXT', 'foxtool'); ?></h2>
			<div class="ft-card">
			   <h3><i class="fa-regular fa-file-check"></i> <?php _e('Set up the ads.txt file', 'foxtool') ?></h3>
				<p>
				<label class="nut-switch">
				<input type="checkbox" name="foxtool_ads_settings[ads-adstxt1]" value="1" <?php if ( isset($foxtool_ads_options['ads-adstxt1']) && 1 == $foxtool_ads_options['ads-adstxt1'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ft-label-right"><?php _e('Enable ads.txt', 'foxtool'); ?></label>
				</p>
				<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('You can preview your file here:', 'foxtool'); echo ' <a target="_blank" href="' . esc_url(home_url( '/ads.txt' )) . '">ads.txt</a>'; ?><br>
				<?php _e('For an Nginx server, if the ads.txt file already exists in the root directory of the website, it will prioritize the static file, so this function will not work. If you want to use it, you can either configure Nginx or delete the static file before proceeding', 'foxtool'); ?>
				</p>
				<p>
				<textarea class="ft-code-textarea ft-dev" name="foxtool_ads_settings[ads-adstxt2]" placeholder="<?php _e('Enter code here', 'foxtool'); ?>"><?php if(!empty($foxtool_ads_options['ads-adstxt2'])){echo esc_textarea($foxtool_ads_options['ads-adstxt2']);} ?></textarea>
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
function foxtool_ads_options_link() {
	add_submenu_page ('foxtool-options', 'Ads', '<i class="fa-regular fa-rectangle-ad" style="width:20px;"></i> '. __('Ads', 'foxtool'), 'manage_options', 'foxtool-ads-options', 'foxtool_ads_options_page');
}
add_action('admin_menu', 'foxtool_ads_options_link');
function foxtool_ads_register_settings() {
	register_setting('foxtool_ads_settings_group', 'foxtool_ads_settings');
}
add_action('admin_init', 'foxtool_ads_register_settings');
// clear cache
function foxtool_ads_settings_cache($old_value, $value) {
    wp_cache_delete('foxtool_ads_settings', 'options');
}
add_action('update_option_foxtool_ads_settings', 'foxtool_ads_settings_cache', 10, 2);

