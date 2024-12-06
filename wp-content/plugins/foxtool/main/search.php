<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
function foxtool_search_options_page() {
	global $foxtool_search_options;
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
			<button class="sotab sotab-select" onclick="fttab(event, 'tab1')"><i class="fa-regular fa-magnifying-glass"></i> <?php _e('FOX SEARCH', 'foxtool'); ?></button>
		</div>

		<div class="ft-main">
			<?php 
			if( isset($_GET['settings-updated']) ) { 
				require_once( FOXTOOL_DIR . 'main/completed.php'); 
			}
			?>
			<form method="post" action="options.php">
			<?php settings_fields('foxtool_search_settings_group'); ?> 
			<!-- SEARCH -->
			<div class="sotab-box ftbox" id="tab1" >
			<h2><?php _e('FOX SEARCH', 'foxtool'); ?></h2>
			<div class="ft-card">
			  <h3><i class="fa-regular fa-magnifying-glass"></i> <?php _e('Quick search', 'foxtool') ?></h3>
				<p>
				<label class="nut-switch">
				<input type="checkbox" name="foxtool_search_settings[main-search1]" value="1" <?php if ( isset($foxtool_search_options['main-search1']) && 1 == $foxtool_search_options['main-search1'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ft-label-right"><?php _e('Enable quick search', 'foxtool'); ?></label>
				</p>
				<div class="tb-doi" id="tb-doi-sogiay" style="display:none"><div class="ft-sload"></div> <?php _e('Automatic initialization after <span id="sogiay" style="padding: 5px;">3</span>s', 'foxtool'); ?></div>
				<p>
				<input class="ft-input-small" name="foxtool_search_settings[main-search-c1]" type="number" placeholder="10" value="<?php if(!empty($foxtool_search_options['main-search-c1'])){echo sanitize_text_field($foxtool_search_options['main-search-c1']);} ?>"/>
				<label class="ft-label-right"><?php _e('Number of items displayed', 'foxtool'); ?></label>
				</p>
				
				<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Enter the number of posts or products to be displayed when searching', 'foxtool'); ?></p>
				
				<p style="display:flex;align-items:center;">
				<input class="ft-input-color" name="foxtool_search_settings[main-search-c2]" type="text" data-coloris value="<?php if(!empty($foxtool_search_options['main-search-c2'])){echo sanitize_text_field($foxtool_search_options['main-search-c2']);} ?>"/>
				<label class="ft-right-text"><?php _e('Main color', 'foxtool'); ?></label>
				</p>
				
				<p>
				<?php $styles = array('Light', 'Dark'); ?>
				<select name="foxtool_search_settings[main-search-s1]"> 
				<?php foreach($styles as $style) { ?> 
				<?php if(isset($foxtool_search_options['main-search-s1']) && $foxtool_search_options['main-search-s1'] == $style) { $selected = 'selected="selected"'; } else { $selected = ''; } ?>
				<option value="<?php echo $style; ?>" <?php echo $selected; ?>><?php echo $style; ?></option> 
				<?php } ?> 
				</select>
				<label class="ft-right-text"><?php _e('Light / Dark', 'foxtool'); ?></label>
				</p>
				
				<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Change the color of the search box to your preferences', 'foxtool'); ?></p>
				
				<h4><?php _e('Use shortcodes', 'foxtool'); ?></h4>
				<p>
				<label class="nut-switch">
				<input type="checkbox" name="foxtool_search_settings[main-search-code1]" value="1" <?php if ( isset($foxtool_search_options['main-search-code1']) && 1 == $foxtool_search_options['main-search-code1'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ft-label-right"><?php _e('Enable shortcodes', 'foxtool'); ?></label>
				
				<p><input class='ft-input-big ft-view-in' type='text' value='[foxsearch]'/></p>
				
				</p>
				<p style="display:flex;align-items:center;">
				<input class="ft-input-color" name="foxtool_search_settings[main-search-code2]" type="text" data-coloris value="<?php if(!empty($foxtool_search_options['main-search-code2'])){echo sanitize_text_field($foxtool_search_options['main-search-code2']);} ?>"/>
				<label class="ft-right-text"><?php _e('Icon color', 'foxtool'); ?></label>
				</p>
				
				<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('You can use the shortcode to add the search icon to the location you want', 'foxtool'); ?></p>
				
				<h4><?php _e('Create custom post type data', 'foxtool'); ?></h4>
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
						<input type="checkbox" name="foxtool_search_settings[main-search-posttype][]" value="<?php echo $post_type_object->name; ?>" <?php if (isset($foxtool_search_options['main-search-posttype']) && in_array($post_type_object->name, $foxtool_search_options['main-search-posttype'])) echo 'checked="checked"'; ?> />
						<span class="slider"></span>
					</label>
					<label class="ft-label-right"><?php echo $post_type_object->labels->name; ?></label>
					</p>
					<?php
				}
				?>
				<div class="save-json">
				<a href="javascript:void(0)" id="save-json"><i class="fa-regular fa-database"></i> <?php _e('Generate data', 'foxtool'); ?></a>
				<a href="javascript:void(0)" id="delete-json-folder"><i class="fa-regular fa-trash"></i> <?php _e('Delete data', 'foxtool'); ?></a>
				</div> 
				<div id="tb-json"></div>
				<div class="tb-doi" id="tb-doi" style="display:none"><div class="ft-sload"></div> <span id="starprocess"></span></div>
				<script>
				jQuery(document).ready(function($) {
					function searchToggle() {
						if ($('input[name="foxtool_search_settings[main-search1]"]').is(':checked')) {
							$('.save-json').css('opacity', '1'); 
							$('.save-json').css('pointer-events', 'auto'); 
						} else {
							$('.save-json').css('opacity', '0.3'); 
							$('.save-json').css('pointer-events', 'none');
						}
					}
					searchToggle();
					$('input[name="foxtool_search_settings[main-search1]"]').change(function() {
						searchToggle();
					});
				});
				jQuery(document).ready(function($) {
						$('input[name="foxtool_search_settings[main-search1]"]').change(function() {
							if ($(this).is(':checked')) {
								$('#tb-doi-sogiay').show();
								var $targetCheckbox = $('input[name="foxtool_search_settings[main-search-posttype][]"]').prop('checked', false);
								if ($targetCheckbox.length > 0) {
									$targetCheckbox.prop('checked', true);
									var countdown = 3;
										var countdownInterval = setInterval(function() {
											$('#sogiay').text(countdown);
											countdown--;
											if (countdown < 0) {
												clearInterval(countdownInterval);
												// $('#save-json').trigger('click');
												$('#tb-doi-sogiay').hide();
												// $('html, body').animate({
												//    scrollTop: $('#save-json').offset().top
												// }, 1000);
											}
										}, 1000);
								}
							}else{
								$('input[name="foxtool_search_settings[main-search-posttype][]"]').prop('checked', false);
							}
						});
				});
				jQuery(document).ready(function($){
					jQuery(document).ready(function($){
					$('#save-json').on('click', function() {
						$('#tb-doi').show();
						var ajax_nonce = '<?php echo wp_create_nonce('foxtool_search_get'); ?>';
						var page = 1;
						var sopost = 0;
						function callAjax() {
							$.ajax({
								type: 'POST',
								url: '<?php echo admin_url('admin-ajax.php'); ?>',
								data: {
									action: 'foxtool_json_get',
									security: ajax_nonce,
									page: page
								},
								success: function(response) {
									var jsonResponse = JSON.parse(response);
									if (jsonResponse.page === -1) {
										$('#loadbarprocess').html('<span><?php _e("Number of data completed: '+sopost+'", "foxtool"); ?></span>');
										$('#tb-doi').hide();
									} else {
										sopost = jsonResponse.count;
										var html = '<span><?php _e("Please wait: '+sopost+'", "foxtool"); ?></span>';
										$('#starprocess').html(html);
										page = jsonResponse.page;
										callAjax();
									}
								}
							});
						}
						callAjax();
					});
					$('#delete-json-folder').on('click', function() {
						var ajax_nonce = '<?php echo wp_create_nonce('foxtool_search_del'); ?>'; 
						function callAjax() {
							$.ajax({
								type: 'POST',
								url: '<?php echo admin_url('admin-ajax.php'); ?>',
								data: {
									action: 'foxtool_json_del', 
									security: ajax_nonce
								},
								success: function(response) {
									$('#loadbarprocess').html('<span><?php _e("Deletion successful", "foxtool"); ?></span>'); 
								}
							});
						}
						callAjax();
					});	
				});	
				});
				</script>
				<div id="loadbarprocess"></div>
				<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Configure the options and create search data. If you want to refresh, you can delete the search data and recreate it. After enabling quick search and completing data creation, a quick search popup will appear when you enter the search box on the website', 'foxtool'); ?></p>
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
function foxtool_search_options_link() {
	add_submenu_page ('foxtool-options', 'Fox Search', '<i class="fa-regular fa-magnifying-glass" style="width:20px;"></i> '. __('Fox Search', 'foxtool'), 'manage_options', 'foxtool-search-options', 'foxtool_search_options_page');
}
add_action('admin_menu', 'foxtool_search_options_link');
function foxtool_search_register_settings() {
	register_setting('foxtool_search_settings_group', 'foxtool_search_settings');
}
add_action('admin_init', 'foxtool_search_register_settings');
// clear cache
function foxtool_search_settings_cache($old_value, $value) {
    wp_cache_delete('foxtool_search_settings', 'options');
}
add_action('update_option_foxtool_search_settings', 'foxtool_search_settings_cache', 10, 2);

