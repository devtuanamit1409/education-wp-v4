<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
function foxtool_extend_options_page() {
	global $foxtool_extend_options;
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
			<button class="sotab sotab-select" onclick="fttab(event, 'tab1')"><i class="fa-regular fa-share-nodes"></i> <?php _e('FREE', 'foxtool'); ?></button>
		</div>

		<div class="ft-main">
			<?php 
			if( isset($_GET['settings-updated']) ) { 
				require_once( FOXTOOL_DIR . 'main/completed.php'); 
			}
			?>
			<form method="post" action="options.php">
			<?php settings_fields('foxtool_extend_settings_group'); ?> 
			<!-- FREE -->
			<div class="sotab-box ftbox" id="tab1">
			<h2><?php _e('FREE', 'foxtool'); ?></h2>
			<div class="ft-card ft-extend-grid">
				<div class="ft-extend-box <?php if ( isset($foxtool_extend_options['code']) && 1 == $foxtool_extend_options['code'] ) echo 'ft-extend-sel'; ?>">
					<div class="ft-extend-tit">
						<span class="ft-extend-tit-span"><?php _e('Add code', 'foxtool'); ?></span>
						<div>
						<label class="nut-switch">
						<input id="more1" type="checkbox" name="foxtool_extend_settings[code]" value="1" <?php if ( isset($foxtool_extend_options['code']) && 1 == $foxtool_extend_options['code'] ) echo 'checked="checked"'; ?> />
						<span class="slider"></span></label>
						</div>
					</div>
					<div class="ft-extend-card more1">
					<img src="<?php echo esc_url(FOXTOOL_URL .'img/extend/code.png'); ?>"/>
					<span class="ft-extend-free"><?php _e('Free', 'foxtool'); ?></span>
					<?php _e('This feature allows you to add CSS, JS, and HTML code to your WordPress site through hooks like WP head, WP body, WP footer, and WP login, making it easy and convenient to supplement your code', 'foxtool'); ?>
					</div>
				</div>
			    <div class="ft-extend-box <?php if ( isset($foxtool_extend_options['clean']) && 1 == $foxtool_extend_options['clean'] ) echo 'ft-extend-sel'; ?>">
					<div class="ft-extend-tit">
						<span class="ft-extend-tit-span"><?php _e('Clean', 'foxtool'); ?></span>
						<div>
						<label class="nut-switch">
						<input id="more2" type="checkbox" name="foxtool_extend_settings[clean]" value="1" <?php if ( isset($foxtool_extend_options['clean']) && 1 == $foxtool_extend_options['clean'] ) echo 'checked="checked"'; ?> />
						<span class="slider"></span></label>
						</div>
					</div>
					<div class="ft-extend-card more2">
					<img src="<?php echo esc_url(FOXTOOL_URL .'img/extend/clean.png'); ?>"/>
					<span class="ft-extend-free"><?php _e('Free', 'foxtool'); ?></span>
					<?php _e('This feature helps optimize cleanup tasks such as clearing content, comments, and the media library. It makes your website cleaner and more optimized', 'foxtool'); ?>
					</div>
				</div>
				<div class="ft-extend-box <?php if ( isset($foxtool_extend_options['font']) && 1 == $foxtool_extend_options['font'] ) echo 'ft-extend-sel'; ?>">
					<div class="ft-extend-tit">
						<span class="ft-extend-tit-span"><?php _e('Font', 'foxtool'); ?></span>
						<div>
						<label class="nut-switch">
						<input id="more3" type="checkbox" name="foxtool_extend_settings[font]" value="1" <?php if ( isset($foxtool_extend_options['font']) && 1 == $foxtool_extend_options['font'] ) echo 'checked="checked"'; ?> />
						<span class="slider"></span></label>
						</div>
					</div>
					<div class="ft-extend-card more3">
					<img src="<?php echo esc_url(FOXTOOL_URL .'img/extend/font.png'); ?>"/>
					<span class="ft-extend-free"><?php _e('Free', 'foxtool'); ?></span>
					<?php _e('Add fonts to your website with just a few simple steps. Additionally, it allows you to quickly set fonts in the specific areas you want to change, offering great convenience', 'foxtool'); ?>
					</div>
				</div>
				<div class="ft-extend-box <?php if ( isset($foxtool_extend_options['redirect']) && 1 == $foxtool_extend_options['redirect'] ) echo 'ft-extend-sel'; ?>">
					<div class="ft-extend-tit">
						<span class="ft-extend-tit-span"><?php _e('Redirects', 'foxtool'); ?></span>
						<div>
						<label class="nut-switch">
						<input id="more4" type="checkbox" name="foxtool_extend_settings[redirect]" value="1" <?php if ( isset($foxtool_extend_options['redirect']) && 1 == $foxtool_extend_options['redirect'] ) echo 'checked="checked"'; ?> />
						<span class="slider"></span></label>
						</div>
					</div>
					<div class="ft-extend-card more4">
					<img src="<?php echo esc_url(FOXTOOL_URL .'img/extend/redirect.png'); ?>"/>
					<span class="ft-extend-free"><?php _e('Free', 'foxtool'); ?></span>
					<?php _e('This feature allows you to configure website redirects with multiple options such as 301, 404, and 503, making management much simpler', 'foxtool'); ?>
					</div>
				</div>
				<div class="ft-extend-box <?php if ( isset($foxtool_extend_options['index']) && 1 == $foxtool_extend_options['index'] ) echo 'ft-extend-sel'; ?>">
					<div class="ft-extend-tit">
						<span class="ft-extend-tit-span"><?php _e('Index now', 'foxtool'); ?></span>
						<div>
						<label class="nut-switch">
						<input id="more5" type="checkbox" name="foxtool_extend_settings[index]" value="1" <?php if ( isset($foxtool_extend_options['index']) && 1 == $foxtool_extend_options['index'] ) echo 'checked="checked"'; ?> />
						<span class="slider"></span></label>
						</div>
					</div>
					<div class="ft-extend-card more5">
					<img src="<?php echo esc_url(FOXTOOL_URL .'img/extend/index.png'); ?>"/>
					<span class="ft-extend-free"><?php _e('Free', 'foxtool'); ?></span>
					<?php _e('Leverage Google’s Indexing API to speed up the indexing of your website on Google’s search engine. You can add as many API keys as you like for unlimited indexing capacity', 'foxtool'); ?>
					</div>
				</div>
				<div class="ft-extend-box <?php if ( isset($foxtool_extend_options['toc']) && 1 == $foxtool_extend_options['toc'] ) echo 'ft-extend-sel'; ?>">
					<div class="ft-extend-tit">
						<span class="ft-extend-tit-span"><?php _e('TOC', 'foxtool'); ?></span>
						<div>
						<label class="nut-switch">
						<input id="more6" type="checkbox" name="foxtool_extend_settings[toc]" value="1" <?php if ( isset($foxtool_extend_options['toc']) && 1 == $foxtool_extend_options['toc'] ) echo 'checked="checked"'; ?> />
						<span class="slider"></span></label>
						</div>
					</div>
					<div class="ft-extend-card more6">
					<img src="<?php echo esc_url(FOXTOOL_URL .'img/extend/toc.png'); ?>"/>
					<span class="ft-extend-free"><?php _e('Free', 'foxtool'); ?></span>
					<?php _e('The table of contents is an incredibly useful feature that allows readers to easily grasp the content by summarizing the headings (h-tags) in the article', 'foxtool'); ?>
					</div>
				</div>
				<div class="ft-extend-box <?php if ( isset($foxtool_extend_options['ads']) && 1 == $foxtool_extend_options['ads'] ) echo 'ft-extend-sel'; ?>">
					<div class="ft-extend-tit">
						<span class="ft-extend-tit-span"><?php _e('Ads', 'foxtool'); ?></span>
						<div>
						<label class="nut-switch">
						<input id="more9" type="checkbox" name="foxtool_extend_settings[ads]" value="1" <?php if ( isset($foxtool_extend_options['ads']) && 1 == $foxtool_extend_options['ads'] ) echo 'checked="checked"'; ?> />
						<span class="slider"></span></label>
						</div>
					</div>
					<div class="ft-extend-card more9">
					<img src="<?php echo esc_url(FOXTOOL_URL .'img/extend/ads.png'); ?>"/>
					<span class="ft-extend-free"><?php _e('Free', 'foxtool'); ?></span>
					<?php _e('To add advertisements to your website, simply enable this feature. It provides you with various ad options to meet your distribution needs', 'foxtool'); ?>
					</div>
				</div>
				<div class="ft-extend-box <?php if ( isset($foxtool_extend_options['notify']) && 1 == $foxtool_extend_options['notify'] ) echo 'ft-extend-sel'; ?>">
					<div class="ft-extend-tit">
						<span class="ft-extend-tit-span"><?php _e('Notify', 'foxtool'); ?></span>
						<div>
						<label class="nut-switch">
						<input id="more11" type="checkbox" name="foxtool_extend_settings[notify]" value="1" <?php if ( isset($foxtool_extend_options['notify']) && 1 == $foxtool_extend_options['notify'] ) echo 'checked="checked"'; ?> />
						<span class="slider"></span></label>
						</div>
					</div>
					<div class="ft-extend-card more11">
					<img src="<?php echo esc_url(FOXTOOL_URL .'img/extend/notify.png'); ?>"/>
					<span class="ft-extend-free"><?php _e('Free', 'foxtool'); ?></span>
					<?php _e('Support quick notification setup, allowing web managers to easily deliver useful and timely content, helping users stay informed effortlessly', 'foxtool'); ?>
					</div>
				</div>
				<div class="ft-extend-box <?php if ( isset($foxtool_extend_options['shortcode']) && 1 == $foxtool_extend_options['shortcode'] ) echo 'ft-extend-sel'; ?>">
					<div class="ft-extend-tit">
						<span class="ft-extend-tit-span"><?php _e('Shortcode', 'foxtool'); ?></span>
						<div>
						<label class="nut-switch">
						<input id="more12" type="checkbox" name="foxtool_extend_settings[shortcode]" value="1" <?php if ( isset($foxtool_extend_options['shortcode']) && 1 == $foxtool_extend_options['shortcode'] ) echo 'checked="checked"'; ?> />
						<span class="slider"></span></label>
						</div>
					</div>
					<div class="ft-extend-card more12">
					<img src="<?php echo esc_url(FOXTOOL_URL .'img/extend/shortcode.png'); ?>"/>
					<span class="ft-extend-free"><?php _e('Free', 'foxtool'); ?></span>
					<?php _e('This feature compiles various shortcode templates with essential functionalities that are frequently used to enhance the user experience in WordPress', 'foxtool'); ?>
					</div>
				</div>
				<div class="ft-extend-box <?php if ( isset($foxtool_extend_options['search']) && 1 == $foxtool_extend_options['search'] ) echo 'ft-extend-sel'; ?>">
					<div class="ft-extend-tit">
						<span class="ft-extend-tit-span"><?php _e('Fox Search', 'foxtool'); ?></span>
						<div>
						<label class="nut-switch">
						<input id="more10" type="checkbox" name="foxtool_extend_settings[search]" value="1" <?php if ( isset($foxtool_extend_options['search']) && 1 == $foxtool_extend_options['search'] ) echo 'checked="checked"'; ?> />
						<span class="slider"></span></label>
						</div>
					</div>
					<div class="ft-extend-card more10">
					<img src="<?php echo esc_url(FOXTOOL_URL .'img/extend/search.png'); ?>"/>
					<span class="ft-extend-free"><?php _e('Free', 'foxtool'); ?></span>
					<?php _e('If youre tired of WordPress default search tool because its too slow, try out our lightning-fast search feature to give your users an exceptional and speedy search experience', 'foxtool'); ?>
					</div>
				</div>
				<div class="ft-extend-box <?php if ( isset($foxtool_extend_options['debug']) && 1 == $foxtool_extend_options['debug'] ) echo 'ft-extend-sel'; ?>">
					<div class="ft-extend-tit">
						<span class="ft-extend-tit-span"><?php _e('Debug', 'foxtool'); ?></span>
						<div>
						<label class="nut-switch">
						<input id="more7" type="checkbox" name="foxtool_extend_settings[debug]" value="1" <?php if ( isset($foxtool_extend_options['debug']) && 1 == $foxtool_extend_options['debug'] ) echo 'checked="checked"'; ?> />
						<span class="slider"></span></label>
						</div>
					</div>
					<div class="ft-extend-card more7">
					<img src="<?php echo esc_url(FOXTOOL_URL .'img/extend/debug.png'); ?>"/>
					<span class="ft-extend-free"><?php _e('Free', 'foxtool'); ?></span>
					<?php _e('If you need to monitor WordPress debug files without accessing the file manager, this tool is made just for that purpose', 'foxtool'); ?>
					</div>
				</div>
				<div class="ft-extend-box <?php if ( isset($foxtool_extend_options['export']) && 1 == $foxtool_extend_options['export'] ) echo 'ft-extend-sel'; ?>">
					<div class="ft-extend-tit">
						<span class="ft-extend-tit-span"><?php _e('Export', 'foxtool'); ?></span>
						<div>
						<label class="nut-switch">
						<input id="more8" type="checkbox" name="foxtool_extend_settings[export]" value="1" <?php if ( isset($foxtool_extend_options['export']) && 1 == $foxtool_extend_options['export'] ) echo 'checked="checked"'; ?> />
						<span class="slider"></span></label>
						</div>
					</div>
					<div class="ft-extend-card more8">
					<img src="<?php echo esc_url(FOXTOOL_URL .'img/extend/export.png'); ?>"/>
					<span class="ft-extend-free"><?php _e('Free', 'foxtool'); ?></span>
					<?php _e('If you want to export or import the settings of the Foxtool plugin, enable this feature. It allows you to easily transfer configurations from this site to another', 'foxtool'); ?>
					</div>
				</div>
			</div>
			</div>
			<div class="ft-submit">
				<button type="submit"><i class="fa-regular fa-floppy-disk"></i> <?php _e('SAVE CONTENT', 'foxtool'); ?></button>
			</div>
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
						location.reload(); 
					},
					error: function() {
						console.log('Error in AJAX request');
					}
				});
			});
		});
		jQuery(document).ready(function($) {
			function searchToggle(selector) {
				$(selector).each(function() {
					var id = $(this).attr('id');
					var relatedClass = '.' + id; 
					if ($(this).is(':checked')) {
						$(relatedClass).css('opacity', '1');
						$(relatedClass).css('filter', 'grayscale(0)');						
					} else {
						$(relatedClass).css('opacity', '0.6'); 
						$(relatedClass).css('filter', 'grayscale(100%)');
					}
				});
			}
			searchToggle('input[id^="more"]');
			$('input[id^="more"]').change(function() {
				searchToggle('input[id^="more"]');
			});
		});
	</script>
	<?php
	// style foxtool
	require_once( FOXTOOL_DIR . 'main/style.php');
	echo ob_get_clean();
}
function foxtool_extend_options_link() {
	add_submenu_page ('foxtool-options', 'Extend', '<i class="fa-regular fa-plus" style="width:20px;"></i> '. __('Extend', 'foxtool'), 'manage_options', 'foxtool-extend-options', 'foxtool_extend_options_page');
}
add_action('admin_menu', 'foxtool_extend_options_link');
function foxtool_extend_register_settings() {
	register_setting('foxtool_extend_settings_group', 'foxtool_extend_settings');
}
add_action('admin_init', 'foxtool_extend_register_settings');
// clear cache
function foxtool_extend_settings_cache($old_value, $value) {
    wp_cache_delete('foxtool_extend_settings', 'options');
}
add_action('update_option_foxtool_extend_settings', 'foxtool_extend_settings_cache', 10, 2);

