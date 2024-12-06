<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
function foxtool_redirects_options_page() {
	global $foxtool_redirects_options;
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
			<button class="sotab sotab-select" onclick="fttab(event, 'tab1')"><i class="fa-regular fa-compass"></i> <?php _e('301', 'foxtool'); ?></button>
			<button class="sotab" onclick="fttab(event, 'tab2')"><i class="fa-regular fa-do-not-enter"></i> <?php _e('404', 'foxtool'); ?></button>
			<button class="sotab" onclick="fttab(event, 'tab3')"><i class="fa-regular fa-bug"></i> <?php _e('503', 'foxtool'); ?></button>
		</div>

		<div class="ft-main">
			<?php 
			if( isset($_GET['settings-updated']) ) { 
				require_once( FOXTOOL_DIR . 'main/completed.php'); 
			}
			?>
			<form method="post" action="options.php">
			<?php settings_fields('foxtool_redirects_settings_group'); ?> 
			<!-- 301 -->
			<div class="sotab-box ftbox" id="tab1">
			<h2><?php _e('301', 'foxtool'); ?></h2>
			<div class="ft-card">
			  <h3><i class="fa-regular fa-compass"></i> <?php _e('Redirect 301 whole page', 'foxtool') ?></h3>
				<p>
				<label class="nut-switch">
				<input type="checkbox" name="foxtool_redirects_settings[redi11]" value="1" <?php if ( isset($foxtool_redirects_options['redi11']) && 1 == $foxtool_redirects_options['redi11'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ft-label-right"><?php _e('Enable site-wide 301 redirects', 'foxtool'); ?></label>
				</p>
				<input class="ft-input-big" placeholder="<?php _e('Enter the link', 'foxtool'); ?>" type="text" name="foxtool_redirects_settings[redi12]" value="<?php if(!empty($foxtool_redirects_options['redi12'])){echo sanitize_text_field($foxtool_redirects_options['redi12']);} ?>" />
				<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('This function will redirect all of your website pages to the destination page of your choice', 'foxtool'); ?></p>
			  <h3><i class="fa-regular fa-compass"></i> <?php _e('Redirect 301 to a custom page', 'foxtool') ?></h3>
			    <p>
				<label class="nut-switch">
				<input type="checkbox" name="foxtool_redirects_settings[redi1]" value="1" <?php if ( isset($foxtool_redirects_options['redi1']) && 1 == $foxtool_redirects_options['redi1'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ft-label-right"><?php _e('Enable 301 redirection', 'foxtool'); ?></label>
				</p>
				<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('This function allows you to redirect 301 links to the target page', 'foxtool'); ?></p>
				
				<div id="sortable-list">
				<div data-id="1" class="ui-state-default ft-button-grid">
				<input class="ft-input-big" placeholder="<?php _e('Enter the link', 'foxtool'); ?>" type="text" name="foxtool_redirects_settings[rechan11]" value="<?php if(!empty($foxtool_redirects_options['rechan11'])){echo sanitize_text_field($foxtool_redirects_options['rechan11']);} ?>" />
				<input class="ft-input-big" placeholder="<?php _e('Enter the link', 'foxtool'); ?>" type="text" name="foxtool_redirects_settings[rechan21]" value="<?php if(!empty($foxtool_redirects_options['rechan21'])){echo sanitize_text_field($foxtool_redirects_options['rechan21']);} ?>" />
				</div>
				<?php
				if (is_array($foxtool_redirects_options) || is_object($foxtool_redirects_options)) {
					foreach ($foxtool_redirects_options as $key => $value) {
						if (preg_match('/^rechan1(\d+)$/', $key, $matches) && $matches[1] != 1) {
							$n = $matches[1];
							echo '<div data-id="' . $n . '" class="ui-state-default ft-button-grid">';
							echo '<input class="ft-input-big" placeholder="'. __('Enter the link', 'foxtool') .'" type="text" name="foxtool_redirects_settings[rechan1' . $n . ']" value="' . sanitize_text_field($foxtool_redirects_options['rechan1' . $n]) . '" />';
							echo '<input class="ft-input-big" placeholder="'. __('Enter the link', 'foxtool') .'" type="text" name="foxtool_redirects_settings[rechan2' . $n . ']" value="' . sanitize_text_field($foxtool_redirects_options['rechan2' . $n]) . '" />';
							echo '<span id="ft-chatx">&#x2715</span>';
							echo '</div>';
						}
					}
				}
				?>
				</div>
				<span id="ft-chatmore"><i class="fa-regular fa-plus"></i> <?php _e('Add link', 'foxtool'); ?></span>
			</div>	
			</div>
			<!-- 404 -->
			<div class="sotab-box ftbox" id="tab2" style="display:none">
			<h2><?php _e('404', 'foxtool'); ?></h2>
			<div class="ft-card">
			  <h3><i class="fa-regular fa-do-not-enter"></i> <?php _e('404 redirects', 'foxtool') ?></h3>
				<p>
				<label class="nut-switch">
				<input type="checkbox" name="foxtool_redirects_settings[redi2]" value="1" <?php if ( isset($foxtool_redirects_options['redi2']) && 1 == $foxtool_redirects_options['redi2'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ft-label-right"><?php _e('Enable 404 redirection', 'foxtool'); ?></label>
				</p>
				<select id="foxtool-toc-page-select">
					<option value=""><?php _e('Select redirect page', 'foxtool'); ?></option>
					<?php
					$pages = get_pages();
					foreach ($pages as $page) {
						echo '<option value="' . esc_attr($page->post_name) . '">' . esc_html($page->post_title) . '</option>';
					}
					?>
				</select>
				<div id="foxtool-toc-tags">
					<?php 
					if (!empty($foxtool_redirects_options['redi21'])) {
						$page_slug = $foxtool_redirects_options['redi21'];
						if (!empty($page_slug)) {
							echo '<span class="foxtool-toc-tag">' . esc_html($page_slug) . ' <span class="remove-tag" data-slug="' . esc_attr($page_slug) . '">&times;</span></span>';
						}
					} 
					?>
				</div>
				<input id="foxtool-hi-input" class="ft-input-big" type="text" style="display:none;" name="foxtool_redirects_settings[redi21]" value="<?php if(!empty($foxtool_redirects_options['redi21'])){echo sanitize_text_field($foxtool_redirects_options['redi21']);} ?>" />
				<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Redirect the 404 page to the homepage or a custom page of your choice, leave the field blank if you want to redirect to the homepage', 'foxtool'); ?></p>
			</div>
			</div>
			<!-- 503 -->
			<div class="sotab-box ftbox" id="tab3" style="display:none">
			<h2><?php _e('503', 'foxtool'); ?></h2>
			<div class="ft-card">
			  <h3><i class="fa-regular fa-bug"></i> <?php _e('Maintenance mode for developers (503)', 'foxtool') ?></h3>
				<label class="nut-switch">
				<input type="checkbox" name="foxtool_redirects_settings[redi3]" value="1" <?php if ( isset($foxtool_redirects_options['redi3']) && 1 == $foxtool_redirects_options['redi3'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ft-label-right"><?php _e('Enable 503 maintenance mode', 'foxtool'); ?></label>
				<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('All links on your website will redirect to the maintenance page, and only logged-in admin accounts can view the content', 'foxtool'); ?></p>
				<p>
				<input class="ft-input-big" placeholder="<?php _e('Enter title', 'foxtool') ?>" name="foxtool_redirects_settings[redi31]" type="text" value="<?php if(!empty($foxtool_redirects_options['redi31'])){echo sanitize_text_field($foxtool_redirects_options['redi31']);} ?>"/>
				</p>
				<p>
				<textarea style="height:150px;" class="ft-code-textarea" name="foxtool_redirects_settings[redi32]" placeholder="<?php _e('Enter content here', 'foxtool'); ?>"><?php if(!empty($foxtool_redirects_options['redi32'])){echo esc_textarea($foxtool_redirects_options['redi32']);} ?></textarea>
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
			// them link
			var count = 0;
			$('#ft-chatmore').click(function() {
				var count = $('#sortable-list .ui-state-default:last').data('id') + 1;
				var newDiv = $('<div data-id="' + count + '" class="ui-state-default ft-button-grid">' +
					'<input class="ft-input-big" placeholder="<?php _e('Enter the link', 'foxtool'); ?>" type="text" name="foxtool_redirects_settings[rechan1' + count + ']" />' +
					'<input class="ft-input-big" placeholder="<?php _e('Enter the link', 'foxtool'); ?>" type="text" name="foxtool_redirects_settings[rechan2' + count + ']" />' +
					'<span id="ft-chatx">&#x2715</span>' +
					'</div>');
				$('#sortable-list').append(newDiv);
			});
			$('#sortable-list').on('click', '#ft-chatx', function() {
				$(this).parent('.ui-state-default').remove();
				count--;
			});
			// chon trang can chuyen
			function updateNoPageMessage() {
				if ($('#foxtool-toc-tags .foxtool-toc-tag').length === 0) {
					$('#foxtool-toc-tags').append('<span class="ftno-page"><?php _e("No pages selected", "foxtool"); ?></span>');
				} else {
					$('#foxtool-toc-tags .ftno-page').remove();
				}
			}
			$('#foxtool-toc-page-select').change(function() {
				var selectedPage = $(this).val();
				if (selectedPage) {
					var formattedPage = selectedPage; // Prepend slash
					$('#foxtool-toc-tags').html('<span class="foxtool-toc-tag">' + formattedPage + ' <span class="remove-tag" data-slug="' + formattedPage + '">&times;</span></span>');
					$('#foxtool-hi-input').val(formattedPage); // Set the input value with slash
					updateNoPageMessage();
				}
				$(this).val('');
			});
			$(document).on('click', '.remove-tag', function() {
				$(this).parent('.foxtool-toc-tag').remove();
				$('#foxtool-hi-input').val('');
				updateNoPageMessage();
			});
			updateNoPageMessage();
		});
	</script>
	<?php
	// style foxtool
	require_once( FOXTOOL_DIR . 'main/style.php');
	echo ob_get_clean();
}
function foxtool_redirects_options_link() {
	add_submenu_page ('foxtool-options', 'Redirects', '<i class="fa-regular fa-compass" style="width:20px;"></i> '. __('Redirects', 'foxtool'), 'manage_options', 'foxtool-redirects-options', 'foxtool_redirects_options_page');
}
add_action('admin_menu', 'foxtool_redirects_options_link');
function foxtool_redirects_register_settings() {
	register_setting('foxtool_redirects_settings_group', 'foxtool_redirects_settings');
}
add_action('admin_init', 'foxtool_redirects_register_settings');
// clear cache
function foxtool_redirects_settings_cache($old_value, $value) {
    wp_cache_delete('foxtool_redirects_settings', 'options');
}
add_action('update_option_foxtool_redirects_settings', 'foxtool_redirects_settings_cache', 10, 2);

