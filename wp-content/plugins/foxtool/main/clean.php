<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
function foxtool_clean_options_page() {
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
			<button class="sotab sotab-select" onclick="fttab(event, 'tab1')"><i class="fa-regular fa-thumbtack"></i> <?php _e('CONTENT', 'foxtool'); ?></button>
			<button class="sotab" onclick="fttab(event, 'tab2')"><i class="fa-regular fa-comment"></i> <?php _e('COMMENT', 'foxtool'); ?></button>
			<button class="sotab" onclick="fttab(event, 'tab3')"><i class="fa-regular fa-image"></i> <?php _e('MEDIA', 'foxtool'); ?></button>
		</div>
		<div class="ft-main">
			<!-- post -->
			<div class="sotab-box ftbox" id="tab1" style="margin-bottom:-60px;">
			<h2><?php _e('CONTENT', 'foxtool'); ?></h2>
			<div class="ft-card">
			   <h3><i class="fa-regular fa-trash"></i> <?php _e('Optimize deletion of content in the database', 'foxtool'); ?></h3>
				<div class="ft-del">
				<a href="javascript:void(0)" id="delete-revisions"><i class="fa-regular fa-trash"></i> <?php _e('Delete revisions', 'foxtool'); ?></a>
				<a href="javascript:void(0)" id="delete-auto-drafts"><i class="fa-regular fa-trash"></i> <?php _e('Delete autosaves', 'foxtool'); ?></a>
				<a href="javascript:void(0)" id="delete-all-trashed-posts"><i class="fa-regular fa-trash"></i> <?php _e('Empty trash', 'foxtool'); ?></a>
				</div>
				<div class="edel" style="display:none"><div class="ft-sload"></div> <?php _e('Please wait', 'foxtool'); ?></div>
				<div id="del-result"></div>
				<p class="ft-note ft-note-red"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Delete revisions, delete autosaves, delete content (posts, pages, products...) in the trash', 'foxtool'); ?></p>
			</div>
			</div>
			<!-- comment -->
			<div class="sotab-box ftbox" id="tab2" style="display:none;margin-bottom:-60px;">
			<h2><?php _e('COMMENT', 'foxtool'); ?></h2>
			<div class="ft-card">
			   <h3><i class="fa-regular fa-trash"></i> <?php _e('Delete comments', 'foxtool'); ?></h3>
				<div class="ft-del">
				<a href="javascript:void(0)" id="delete-comen-pend"><i class="fa-regular fa-trash"></i> <?php _e('Pending', 'foxtool'); ?></a>
				<a href="javascript:void(0)" id="delete-comen-spam"><i class="fa-regular fa-trash"></i> <?php _e('Spam', 'foxtool'); ?></a>
				<a href="javascript:void(0)" id="delete-comen-trash"><i class="fa-regular fa-trash"></i> <?php _e('Trash', 'foxtool'); ?></a>
				<a href="javascript:void(0)" id="delete-comen-link"><i class="fa-regular fa-trash"></i> <?php _e('Content links', 'foxtool'); ?></a>
				</div>
				<div class="edel2" style="display:none"><div class="ft-sload"></div> <?php _e('Please wait', 'foxtool'); ?></div>
				<div id="del-result2"></div> 
				<p class="ft-note ft-note-red"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Note: deleting "Content links" will delete all comments that have a link in the body or url input field', 'foxtool'); ?></p>
			</div>
			</div>
			
			<!-- media -->
			<div class="sotab-box ftbox" id="tab3" style="display:none;margin-bottom:-60px;">
			<h2><?php _e('MEDIA', 'foxtool'); ?></h2>
			<div class="ft-card">
			   <h3><i class="fa-regular fa-image-slash"></i> <?php _e('Find and delete all 404 images in media', 'foxtool') ?></h3>
				<div class="ft-del">
				<a href="javascript:void(0)" id="delete-media"><i class="fa-regular fa-trash"></i> <?php _e('Delete all 404 images', 'foxtool'); ?></a>
				</div>	
				<div class="emed" style="display:none"><div class="ft-sload"></div> <?php _e('Please wait', 'foxtool'); ?></div>
				<div id="del-media"></div>
				<p class="ft-note ft-note-red"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Delete 404 images that do not exist saved in the database', 'foxtool'); ?></p>
			   <h3><i class="fa-regular fa-image-slash"></i> <?php _e('Find and delete all 404 thumbnail images in media', 'foxtool') ?></h3>
				<div class="ft-del">
				<a href="javascript:void(0)" id="delete-media-thum"><i class="fa-regular fa-trash"></i> <?php _e('Delete all 404 thumbnail images', 'foxtool'); ?></a>
				</div>	
				<div class="emed-thum" style="display:none"><div class="ft-sload"></div> <?php _e('Please wait', 'foxtool'); ?></div>
				<div id="del-media-thum"></div>
				<p class="ft-note ft-note-red"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Delete 404 thumbnails that do not exist and are stored in the database', 'foxtool'); ?></p>
			  <h3><i class="fa-regular fa-image-slash"></i> <?php _e('Delete cropped image', 'foxtool') ?></h3>	
				<div class="ft-card-note ft-del-crop"> 
				<?php
				global $_wp_additional_image_sizes;
				$image_sizes = get_intermediate_image_sizes();
				if (isset($_wp_additional_image_sizes) && count($_wp_additional_image_sizes) > 0) {
					$image_sizes = array_merge($image_sizes, array_keys($_wp_additional_image_sizes));
				}
				$image_sizes = array_unique($image_sizes);
				$selected_sizes = array(); 
				foreach ($image_sizes as $i => $size) {
					$width = isset($_wp_additional_image_sizes[$size]['width']) ? $_wp_additional_image_sizes[$size]['width'] : get_option($size.'_size_w');
					?>
					<p>
						<a href="javascript:void(0)" id="cropdel-<?php echo $size; ?>"><i class="fa-regular fa-trash"></i> <?php echo $size .' (W: '. $width .')'; ?></a>
					</p>
				<?php } ?>
				</div>
				<div id="delete-size-end"></div>
				<p class="ft-note ft-note-red"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Exercise caution when using this feature, as your interface may require various sizes to display properly. If youre a web designer, youll understand the need for a variety of sizes to be utilized', 'foxtool'); ?></p>
			</div>
			</div>
	
		</div>
	  </div>
	  <div class="ft-sidebar">
		<?php include( FOXTOOL_DIR . 'main/page/ft-aff.php'); ?>
	  </div>
	</div>  
	</div>
	<script>
		jQuery(document).ready(function($) {
			// xoa revisions
			$('#delete-revisions').click(function(event) {
				var ajax_nonce = '<?php echo wp_create_nonce('foxtool_post_revisions'); ?>';
				$('.edel').show();
				event.preventDefault();
				$.ajax({
					url: '<?php echo admin_url('admin-ajax.php');?>',
					type: 'POST',
					data: {
						action: 'foxtool_delete_revisions',
						security: ajax_nonce,
					},
					success: function(response) {
						$('#del-result').html('<span><?php _e('All revisions have been deleted', 'foxtool'); ?></span>');
						$('.edel').hide();
					},
					error: function(response) {
						$('#del-result').html('<span><?php _e('Error! Unable to delete', 'foxtool'); ?></span>');
					}
				});
			});
			// xoa auto-drafts
			$('#delete-auto-drafts').click(function(event) {
				var ajax_nonce = '<?php echo wp_create_nonce('foxtool_post_drafts'); ?>';
				$('.edel').show();
				event.preventDefault();
				$.ajax({
					url: '<?php echo admin_url('admin-ajax.php');?>',
					type: 'POST',
					data: {
						action: 'foxtool_delete_auto_drafts',
						security: ajax_nonce,
					},
					success: function(response) {
						$('#del-result').html('<span><?php _e('All autosaves have been deleted', 'foxtool'); ?></span>');
						$('.edel').hide();
					},
					error: function(response) {
						$('#del-result').html('<span><?php _e('Error! Unable to delete', 'foxtool'); ?></span>');
					}
				});
			});
			// xoa tat ca trong thung rac
			$('#delete-all-trashed-posts').click(function(event) {
				var ajax_nonce = '<?php echo wp_create_nonce('foxtool_post_trashed'); ?>';
				$('.edel').show();
				event.preventDefault();
				$.ajax({
					url: '<?php echo admin_url('admin-ajax.php');?>',
					type: 'POST',
					data: {
						action: 'foxtool_delete_all_trashed_posts',
						security: ajax_nonce,
					},
					success: function(response) {
						$('#del-result').html('<span><?php _e('All items in the trash have been deleted', 'foxtool'); ?></span>');
						$('.edel').hide();
					},
					error: function(response) {
						$('#del-result').html('<span><?php _e('Error! Unable to delete', 'foxtool'); ?></span>');
					}
				});
			});
			// xoa binh luan cho
			$('#delete-comen-pend').click(function(event) {
				var ajax_nonce = '<?php echo wp_create_nonce('foxtool_del_comenpend_nonce'); ?>';
				$('.edel2').show();
				event.preventDefault();
				$.ajax({
					url: '<?php echo admin_url('admin-ajax.php');?>',
					type: 'POST',
					data: {
						action: 'foxtool_del_comenpend',
						security: ajax_nonce,
					},
					success: function(response) {
						$('#del-result2').html('<span><?php _e('Delete pending comment: ', 'foxtool'); ?>' + response.data.deleted_count + '</span>');
						$('.edel2').hide();
					},
					error: function(response) {
						$('#del-result2').html('<span><?php _e('Error! Unable to delete', 'foxtool'); ?></span>');
					}
				});
			});
			// xoa binh luan spam
			$('#delete-comen-spam').click(function(event) {
				var ajax_nonce = '<?php echo wp_create_nonce('foxtool_del_comenspam_nonce'); ?>';
				$('.edel2').show();
				event.preventDefault();
				$.ajax({
					url: '<?php echo admin_url('admin-ajax.php');?>',
					type: 'POST',
					data: {
						action: 'foxtool_del_comenspam',
						security: ajax_nonce,
					},
					success: function(response) {
						$('#del-result2').html('<span><?php _e('Delete spam comments: ', 'foxtool'); ?>' + response.data.deleted_count + '</span>');
						$('.edel2').hide();
					},
					error: function(response) {
						$('#del-result2').html('<span><?php _e('Error! Unable to delete', 'foxtool'); ?></span>');
					}
				});
			});
			// xoa binh luan thung rac
			$('#delete-comen-trash').click(function(event) {
				var ajax_nonce = '<?php echo wp_create_nonce('foxtool_del_comentrash_nonce'); ?>';
				$('.edel2').show();
				event.preventDefault();
				$.ajax({
					url: '<?php echo admin_url('admin-ajax.php');?>',
					type: 'POST',
					data: {
						action: 'foxtool_del_comentrash',
						security: ajax_nonce,
					},
					success: function(response) {
						$('#del-result2').html('<span><?php _e('Delete comments in trash: ', 'foxtool'); ?>' + response.data.deleted_count + '</span>');
						$('.edel2').hide();
					},
					error: function(response) {
						$('#del-result2').html('<span><?php _e('Error! Unable to delete', 'foxtool'); ?></span>');
					}
				});
			});
			// xoa binh luan chua link
			$('#delete-comen-link').click(function(event) {
				var foxtoolcl = prompt('<?php _e('Enter from foxtool to confirm deletion:', 'foxtool') ?>');
				if (foxtoolcl === 'foxtool') {
					var ajax_nonce = '<?php echo wp_create_nonce('foxtool_del_comenlink_nonce'); ?>';
					$('.edel2').show();
					event.preventDefault();
					$.ajax({
						url: '<?php echo admin_url('admin-ajax.php');?>',
						type: 'POST',
						data: {
							action: 'foxtool_del_comenlink',
							security: ajax_nonce,
						},
						success: function(response) {
							$('#del-result2').html('<span><?php _e('Delete comments containing links: ', 'foxtool'); ?>' + response.data.deleted_count + '</span>');
							$('.edel2').hide();
						},
						error: function(response) {
							$('#del-result2').html('<span><?php _e('Error! Unable to delete', 'foxtool'); ?></span>');
						}
					});
				} else {
					alert('<?php _e('Entering incorrect content', 'foxtool') ?>');
				}
			});
			// xoa anh 404
			$('#delete-media').click(function(event) {
				var ajax_nonce = '<?php echo wp_create_nonce('foxtool_media_del'); ?>';
				$('.emed').show();
				event.preventDefault();
				$.ajax({
				url: '<?php echo admin_url('admin-ajax.php');?>',
				type: 'POST',
				data: {
					action: 'foxtool_delete_media',
					security: ajax_nonce,
				},
				success: function(response) {
					$('#del-media').html('<span><?php _e('Number of images 404 deleted: ', 'foxtool'); ?>'+ response.data.deleted_count +'</span>');
					$('.emed').hide();
				},
				error: function(response) {
					$('#del-media').html('<span><?php _e('Error! Unable to delete', 'foxtool'); ?></span>');
				}
				});
			});
			// xoa anh 404 thum
			$('#delete-media-thum').click(function(event) {
				var ajax_nonce = '<?php echo wp_create_nonce('foxtool_media_thum_del'); ?>';
				$('.emed-thum').show();
				event.preventDefault();
				$.ajax({
				url: '<?php echo admin_url('admin-ajax.php');?>',
				type: 'POST',
				data: {
					action: 'foxtool_delete_media_thum',
					security: ajax_nonce,
				},
				success: function(response) {
					$('#del-media-thum').html('<span><?php _e('Number of images 404 deleted: ', 'foxtool'); ?>'+ response.data.deleted_count +'</span>');
					$('.emed-thum').hide();
				},
				error: function(response) {
					$('#del-media-thum').html('<span><?php _e('Error! Unable to delete', 'foxtool'); ?></span>');
				}
				});
			});
			// xoa hinh anh crop
			$('a[id^="cropdel-"]').on('click', function(e) {
				e.preventDefault();
				var size = $(this).attr('id').replace('cropdel-', '');
				var foxtoolInput = prompt('<?php _e('Enter from foxtool to confirm deletion:', 'foxtool') ?>');
				if (foxtoolInput === 'foxtool') {
					var data = {
						'action': 'foxtool_delete_images_by_size',
						'size': size,
						'security': '<?php echo wp_create_nonce('foxtool_delete_crop_nonce'); ?>',
					};
					$.ajax({
						type: 'POST',
						url: '<?php echo admin_url('admin-ajax.php');?>',
						data: data,
						success: function(response) {
							$('#delete-size-end').html('<span>'+ response.data + '</span>');
						}
					});
				} else {
					alert('<?php _e('Entering incorrect content', 'foxtool') ?>');
				}
			});
		});
	</script>
	<?php
	// style foxtool
	require_once( FOXTOOL_DIR . 'main/style.php');
	echo ob_get_clean();
}
function foxtool_clean_options_link() {
	add_submenu_page ('foxtool-options', 'Clean', '<i class="fa-regular fa-broom-wide" style="width:20px;"></i> '. __('Clean', 'foxtool'), 'manage_options', 'foxtool-clean-options', 'foxtool_clean_options_page');
}
add_action('admin_menu', 'foxtool_clean_options_link');


