<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
function foxtool_font_options_page() {
	if( isset($_GET['delete_font_key']) ){
       $delete = foxtool_delete_font();
    }
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
			<button class="sotab sotab-select" onclick="fttab(event, 'tab1')"><i class="fa-regular fa-book-font"></i> <?php _e('ADD FONT', 'foxtool'); ?></button>
			<button class="sotab" onclick="fttab(event, 'tab2')"><i class="fa-regular fa-font-case"></i> <?php _e('USE FONT', 'foxtool'); ?></button>
		</div>

		<div class="ft-main">
			<?php 
			if( isset($_GET['valid']) && $_GET['valid'] == 'true' ){
                    echo '<div class="ft-updated">'. __('Upload font success', 'foxtool'). '</div>';
            } else if( isset($_GET['valid']) && $_GET['valid'] == 'false' ){
                    echo '<div class="ft-updated">'. __('Upload font error', 'foxtool'). '</div>';
            }
                if( isset($_GET['delete_font_key']) ){
                    echo '<div class="ft-updated">'.$delete['status'].'</div>';
            }
			?>
			<!-- FONT -->
			<div class="sotab-box ftbox" id="tab1" style="margin-bottom:-80px;">
			<form id="ft-form-font" method="post" enctype="multipart/form-data">
			<h2><?php _e('ADD FONT', 'foxtool'); ?></h2>
			<div class="ft-card">
			  <h3><i class="fa-regular fa-book-font"></i> <?php _e('Add fonts', 'foxtool') ?></h3>
			    <p>
				<input class="ft-input-big" type="text" placeholder="<?php _e('Name the font', 'foxtool'); ?>" name="foxtool_font_name" required />
				</p>
				<label class="ft-label-file">
				<input class="ft-input-file" type="file" name="foxtool_upload_file" required />
				<b id="file-co"><?php _e('BROWSE FILE', 'foxtool'); ?></b>
				<p><em><?php _e('Accepted Font Format : woff2, ttf, otf, off | Font Size: Upto 25 MB', 'foxtool'); ?></em></p>
				</label>
				<div class="ft-label-sub">
					<button type="submit"><i class="fa-regular fa-folder-arrow-up"></i> <?php _e('UPLOAD FONT', 'foxtool'); ?></button>
				</div>
			    <ul class="font-list">
                    <?php 
                    $fontsData = foxtool_get_uploaded_font_data();
                    if (!empty($fontsData)){
                         foreach ($fontsData as $key => $fontData){ ?>
                            <li>
                                <article class="ft-box-font">
                                    <div class="ft-box-top">
                                        <div class="font-name font-demo <?php echo $fontData['font_name'];?>"><?php echo $fontData['font_name'];?></div>
                                        <div class="font-class"><?php _e('font-family:', 'foxtool'); ?> '<?php echo $fontData['font_name'];?>', sans-serif;</div>
                                    </div>
                                    <div class="ft-box-dow">
                                        <div class="font-dele">
										<a onclick="if (!confirm('<?php _e('Do you want to delete?', 'foxtool'); ?>')){return false;}" href="admin.php?page=foxtool-font-options&delete_font_key=<?php echo $key; ?>"><i class="fa-regular fa-trash"></i></a>
										</div>
                                    </div>
                                </article>
                            </li>
                    <?php }
                    }
                    ?>
                </ul>
			</div>
			</form>
			</div>
			<!-- set font -->
			<div class="sotab-box ftbox" id="tab2" style="display:none">
			<?php 
			global $foxtool_fontset_options;
			if( isset($_GET['settings-updated']) ) { 
				require_once( FOXTOOL_DIR . 'main/completed.php');
			}
			?>
			<form method="post" action="options.php">
			<?php settings_fields('foxtool_fontset_settings_group'); ?>
			<h2><?php _e('USE FONT', 'foxtool'); ?></h2>
			<div class="ft-card">
			  <h3><i class="fa-regular fa-font-case"></i> <?php _e('Use fonts on the web', 'foxtool') ?></h3>
				<?php
				if (!empty($fontsData)){
					$p_contents = array(
						'<h4>'. __('Choose a font for the', 'foxtool'). ' <i style="color:#ff4444">div</i> tag</h4>',
						'<h4>'. __('Choose a font for the', 'foxtool'). ' <i style="color:#ff4444">p</i> tag</h4>',
						'<h4>'. __('Choose a font for the', 'foxtool'). ' <i style="color:#ff4444">a</i> tag</h4>',
						'<h4>'. __('Choose a font for the', 'foxtool'). ' <i style="color:#ff4444">span</i> tag</h4>',
						'<h4>'. __('Choose a font for the', 'foxtool'). ' <i style="color:#ff4444">button</i> tag</h4>',
						'<h4>'. __('Choose a font for the', 'foxtool'). ' <i style="color:#ff4444">input</i> tag</h4>',
						'<h4>'. __('Choose a font for the', 'foxtool'). ' <i style="color:#ff4444">textarea</i> tag</h4>',
						'<h4>'. __('Choose a font for the', 'foxtool'). ' <i style="color:#ff4444">select</i> tag</h4>',
						'<h4>'. __('Choose a font for the', 'foxtool'). ' <i style="color:#ff4444">h1</i> tag</h4>',
						'<h4>'. __('Choose a font for the', 'foxtool'). ' <i style="color:#ff4444">h2</i> tag</h4>',
						'<h4>'. __('Choose a font for the', 'foxtool'). ' <i style="color:#ff4444">h3</i> tag</h4>',
						'<h4>'. __('Choose a font for the', 'foxtool'). ' <i style="color:#ff4444">h4</i> tag</h4>',
						'<h4>'. __('Choose a font for the', 'foxtool'). ' <i style="color:#ff4444">h5</i> tag</h4>',
						'<h4>'. __('Choose a font for the', 'foxtool'). ' <i style="color:#ff4444">h6</i> tag</h4>',
					);
					echo '<div class="ft-font-box">';
					for ($i = 1; $i <= 14; $i++) { 
					echo '<div class="ft-font-sel">' . $p_contents[$i - 1] . ''; 
						$selected = ($foxtool_fontset_options['font' . $i] ?? '') === 'Default' ? 'selected="selected"' : ''; ?>
						<p>
						<select name="foxtool_fontset_settings[font<?php echo $i; ?>]" class="font-select select2" style="width:100%;">
							<option value="Default" <?php echo $selected; ?>>Default</option> 
							<?php 
							foreach ($fontsData as $fontData) {
								$selected = ($foxtool_fontset_options['font' . $i] ?? '') === $fontData['font_name'] ? 'selected="selected"' : ''; ?>
								<option value="<?php echo $fontData['font_name']; ?>" <?php echo $selected; ?>><?php echo $fontData['font_name']; ?></option> 
							<?php } ?>
						</select>
						</p>
						<div id="fontview<?php echo $i; ?>" class="font-view">This is a font demo</div>
						</div>
					<?php 
					} 
					echo '</div>';
				} 
				?>
			<div class="ft-submit">
			<button type="submit"><i class="fa-regular fa-floppy-disk"></i> <?php _e('SAVE CONTENT', 'foxtool'); ?></button>
			</div>
			</form>
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
	jQuery('#ft-form-font').on('submit', function(e){
		e.preventDefault();
		var formData = new FormData(this);
		var ajax_nonce = '<?php echo wp_create_nonce('foxtool_font_nonce'); ?>';
		formData.append('action', 'foxtool_upload_fonts');
		formData.append('security', ajax_nonce);
		jQuery.ajax({
			url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
			type: 'POST',
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			beforeSend: function(){},
			success: function(response) {
				if (response == '1' || response == 1) {
					window.location.href = "admin.php?page=foxtool-font-options&valid=true";
				} else {
					window.location.href = "admin.php?page=foxtool-font-options&valid=false";
				}
			},
		})
	});
	jQuery(document).ready(function($){
		$('.ft-input-file').change(function() {
			var fileName = $(this).val().split('\\').pop();
			$('#file-co').text(fileName);
		});
		$('.font-select').change(function(){
			var selectedFont = $(this).val(); 
			var divId = $(this).attr('name').replace('foxtool_fontset_settings[font', 'fontview').replace(']', '');
			if(selectedFont === 'Default') {
				$('#' + divId).hide();
			} else {
				$('#' + divId).css('font-family', selectedFont);
				$('#' + divId).show(); 
			} 
		});
		$('.font-select').each(function() {
			var selectedFont = $(this).val(); 
			var divId = $(this).attr('name').replace('foxtool_fontset_settings[font', 'fontview').replace(']', '');
			if(selectedFont === 'Default') {
				$('#' + divId).hide();
			} else {
				$('#' + divId).css('font-family', selectedFont);
				$('#' + divId).show(); 
			}
		});
	});
	// select font
	jQuery(document).ready(function($) {
		$('.select2').select2({
			width: 'resolve'  
		});
	});
	</script>
	<?php
	// style foxtool
	require_once( FOXTOOL_DIR . 'main/style.php');
	echo ob_get_clean();
}
function foxtool_font_options_link() {
	add_submenu_page ('foxtool-options', 'Font', '<i class="fa-regular fa-book-font" style="width:20px;"></i> '. __('Font', 'foxtool'), 'manage_options', 'foxtool-font-options', 'foxtool_font_options_page');
}
add_action('admin_menu', 'foxtool_font_options_link');
function foxtool_font_register_settings() {
	register_setting('foxtool_fontset_settings_group', 'foxtool_fontset_settings');
}
add_action('admin_init', 'foxtool_font_register_settings');
// clear cache
function foxtool_fontset_settings_cache($old_value, $value) {
    wp_cache_delete('foxtool_fontset_settings', 'options');
}
add_action('update_option_foxtool_fontset_settings', 'foxtool_fontset_settings_cache', 10, 2);









