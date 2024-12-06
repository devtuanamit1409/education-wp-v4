<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
function foxtool_gindex_options_page() {
	global $foxtool_gindex_options;
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
			<button class="sotab sotab-select" onclick="fttab(event, 'tab1')"><i class="fa-regular fa-hand-point-up"></i> <?php _e('INDEX', 'foxtool'); ?></button>
			<button class="sotab" onclick="fttab(event, 'tab2')"><i class="fa-regular fa-gear"></i> <?php _e('SETTING', 'foxtool'); ?></button>
		</div>

		<div class="ft-main">
			<?php 
			if( isset($_GET['settings-updated']) ) { 
				require_once( FOXTOOL_DIR . 'main/completed.php'); 
			}
			?>
			<form method="post" action="options.php">
			<?php settings_fields('foxtool_gindex_settings_group'); ?> 
			<!-- INDEX -->
			<div class="sotab-box ftbox" id="tab1" style="margin-bottom:-60px;">
			<h2><?php _e('INDEX', 'foxtool'); ?></h2>
			<div class="ft-card">
			  <h3><i class="fa-regular fa-hand-point-up"></i> <?php _e('Enter manually', 'foxtool') ?></h3>
			    
				<?php 
				// bo dem requet tông
				if (is_array($foxtool_gindex_options) || is_object($foxtool_gindex_options)) {
					$count = 0; 
					foreach ($foxtool_gindex_options as $key => $value) {
						if (preg_match('/^json(\d+)$/', $key, $matches)) {
							$count++;
						}
					}
					$data_t = $count * 200;
					$data_i = !empty(get_transient('foxtool_index_count')) ? get_transient('foxtool_index_count') : 0;
					if ($data_i >= $data_t) {
						$data_full = 0;
					} else {
						$data_full = $data_t - $data_i;
					}

					echo '<div class="ft-index-count">';
					echo '<span>'. __('Total:', 'foxtool') .' '. $data_t .'</span>';
					echo '<span>'. __('Use:', 'foxtool') .' '. $data_i .'</span>';
					echo '<span>'. __('Still:', 'foxtool') .' '. $data_full .'</span>';
					echo '</div>';
				}
				?>
				<textarea style="height:500px" class="ft-code-textarea" name="foxtool_gindex_settings[url]" placeholder="<?php _e('Enter the url', 'foxtool'); ?>"></textarea>
				<div class="ft-index-nut">
					<span class="index-action" data-action="update"><i class="fa-regular fa-play"></i> <?php _e('INDEX', 'foxtool'); ?></span>
					<span class="index-action" data-action="delete"><i class="fa-regular fa-trash"></i> <?php _e('DEL', 'foxtool'); ?></span>
					<span class="index-action-check" ><i class="fa-regular fa-circle-check"></i> <?php _e('CHECK', 'foxtool'); ?></span>
				</div>
				<div class="emed" style="display:none"><div class="ft-sload"></div> <?php _e('Please wait', 'foxtool'); ?></div>
				<div id="index-bao"></div>
			</div>	
			</div>
			<!-- SETTING -->
			<div class="sotab-box ftbox" id="tab2" style="display:none">
			<h2><?php _e('SETTING', 'foxtool'); ?></h2>
			<div class="ft-card">
			  <h3><i class="fa-regular fa-gear"></i> <?php _e('Configure Google index API', 'foxtool') ?></h3>
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
						<input type="checkbox" name="foxtool_gindex_settings[posttype][]" value="<?php echo $post_type_object->name; ?>" <?php if (isset($foxtool_gindex_options['posttype']) && in_array($post_type_object->name, $foxtool_gindex_options['posttype'])) echo 'checked="checked"'; ?> />
						<span class="slider"></span>
					</label>
					<label class="ft-label-right"><?php echo $post_type_object->labels->name; ?></label>
					</p>
					<?php
				}
				?>
				<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('The "Index Now" link will be added in the custom post management section to allow quick index submission', 'foxtool'); ?></p>
				<h4><?php _e('Add Google json code', 'foxtool') ?></h4>
				<div id="sortable-list">
				<div data-id="1" class="ui-state-default ft-button-grid">
				<textarea style="height:200px" class="ft-code-textarea" name="foxtool_gindex_settings[json1]" placeholder="<?php _e('Enter json', 'foxtool'); ?>"><?php if(!empty($foxtool_gindex_options['json1'])){echo esc_textarea($foxtool_gindex_options['json1']);} ?></textarea>
				</div>
				<?php
				if (is_array($foxtool_gindex_options) || is_object($foxtool_gindex_options)) {
					foreach ($foxtool_gindex_options as $key => $value) {
						if (preg_match('/^json(\d+)$/', $key, $matches) && $matches[1] != 1) {
							$n = $matches[1];
							echo '<div data-id="' . $n . '" class="ui-state-default ft-button-grid">';
							echo '<textarea style="height:200px" class="ft-code-textarea" placeholder="'. __('Enter json', 'foxtool') .'" type="text" name="foxtool_gindex_settings[json' . $n . ']">' . sanitize_text_field($foxtool_gindex_options['json' . $n]) . '</textarea>';
							echo '<span id="ft-chatx">&#x2715</span>';
							echo '</div>';
						}
					}
				}
				?>
				</div>
				<span id="ft-chatmore"><i class="fa-regular fa-plus"></i> <?php _e('Add json', 'foxtool'); ?></span>
			</div>
			<div class="ft-submit">
				<button type="submit"><i class="fa-regular fa-floppy-disk"></i> <?php _e('SAVE CONTENT', 'foxtool'); ?></button>
			</div>
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
						console.log('Turn on successfully');
					},
					error: function() {
						console.log('Error in AJAX request');
					}
				});
			});
			// index now and del index
			$('.index-action').click(function() {
				$('.emed').show();
				var links = $('textarea[name="foxtool_gindex_settings[url]"]').val().split('\n');
				var ajax_action = $(this).data('action');
				links.forEach(function(link) {
					idexnowRequest(ajax_action, link.trim()); 
				});
			});
			function idexnowRequest(action, url) {
				var data = {
					action: 'foxtool_index_now_ajax',
					url: url,
					ajax_action: action, 
					ajax_nonce: '<?php echo wp_create_nonce('foxtool_index_now_nonce'); ?>'
				};
				$.ajax({
					url: '<?php echo admin_url('admin-ajax.php');?>',
					type: 'POST',
					data: data,
					success: function(response) {
						$('#index-bao').prepend(response);
						$('#index-bao').addClass('index-bao1');
						$('.emed').hide();
					},
					error: function(xhr, status, error) {
						console.error(xhr.responseText);
					}
				});
			}
			// index status
			$('.index-action-check').click(function() {
				$('.emed').show();
				var links = $('textarea[name="foxtool_gindex_settings[url]"]').val().split('\n');
				links.forEach(function(link) {
					indexstatusRequest(link.trim()); 
				});
			});
			function indexstatusRequest(url) {
				var data = {
					action: 'foxtool_index_status_ajax',
					url: url,
					ajax_nonce: '<?php echo wp_create_nonce('foxtool_index_status_nonce'); ?>'
				};
				$.ajax({
					url: '<?php echo admin_url('admin-ajax.php');?>',
					type: 'POST',
					data: data,
					success: function(response) {
						$('#index-bao').prepend(response);
						$('#index-bao').addClass('index-bao2');
						$('.emed').hide();
					},
					error: function(xhr, status, error) {
						console.error(xhr.responseText);
					}
				});
			}
			// them json
			var count = 0;
			$('#ft-chatmore').click(function() {
				var count = $('#sortable-list .ui-state-default:last').data('id') + 1;
				var newDiv = $('<div data-id="' + count + '" class="ui-state-default ft-button-grid">' +
					'<textarea style="height:200px" class="ft-code-textarea" placeholder="<?php _e('Enter json', 'foxtool'); ?>" name="foxtool_gindex_settings[json' + count + ']"></textarea>' +
					'<span id="ft-chatx">&#x2715</span>' +
					'</div>');
				$('#sortable-list').append(newDiv);
			});
			$('#sortable-list').on('click', '#ft-chatx', function() {
				$(this).parent('.ui-state-default').remove();
				count--;
			});
		});
	</script>
	<?php
	// style foxtool
	require_once( FOXTOOL_DIR . 'main/style.php');
	echo ob_get_clean();
}
function foxtool_gindex_options_link() {
	add_submenu_page ('foxtool-options', 'Index', '<i class="fa-regular fa-hand-point-up" style="width:20px;"></i> '. __('Index now', 'foxtool'), 'manage_options', 'foxtool-gindex-options', 'foxtool_gindex_options_page');
}
add_action('admin_menu', 'foxtool_gindex_options_link');
function foxtool_gindex_register_settings() {
	register_setting('foxtool_gindex_settings_group', 'foxtool_gindex_settings');
}
add_action('admin_init', 'foxtool_gindex_register_settings');
// clear cache
function foxtool_gindex_settings_cache($old_value, $value) {
    wp_cache_delete('foxtool_gindex_settings', 'options');
}
add_action('update_option_foxtool_gindex_settings', 'foxtool_gindex_settings_cache', 10, 2);

