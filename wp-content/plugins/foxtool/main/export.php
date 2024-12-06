<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
	function foxtool_export_options_page() {
	global $foxtool_extend_options;
	$combined_settings = array(
		'tool' => get_option('foxtool_settings')
	);
	$option_keys = array(
		'code' => 'foxtool_code_settings',
		'clean' => 'foxtool_extend_settings',
		'font' => 'foxtool_fontset_settings',
		'redirect' => 'foxtool_redirects_settings',
		'index' => 'foxtool_gindex_settings',
		'toc' => 'foxtool_toc_settings',
		'ads' => 'foxtool_ads_settings',
		'notify' => 'foxtool_notify_settings',
		'shortcode' => 'foxtool_shortcode_settings',
		'search' => 'foxtool_search_settings',
		'debug' => 'foxtool_debug_settings'
	);
	foreach ($option_keys as $key => $option_name) {
		if (isset($foxtool_extend_options[$key])) {
			$combined_settings[$key] = get_option($option_name);
		}
	}
	$textarea = esc_textarea(base64_encode(json_encode($combined_settings)));
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
			<button class="sotab sotab-select" onclick="fttab(event, 'tab1')"><i class="fa-regular fa-gear"></i> <?php _e('FOXTOOL', 'foxtool'); ?></button>
		</div>
		<div class="ft-main">
			<?php
			if (isset($_POST['foxtool_import_tool']) && !empty($_POST['foxtool_export_tool'])) {
				$imported_config = json_decode(base64_decode(stripslashes($_POST['foxtool_export_tool'])), true);
				if ($imported_config && is_array($imported_config)) {
					$option_keys = array(
						'tool' => 'foxtool_settings',
						'code' => 'foxtool_code_settings',
						'clean' => 'foxtool_extend_settings',
						'font' => 'foxtool_fontset_settings',
						'redirect' => 'foxtool_redirects_settings',
						'index' => 'foxtool_gindex_settings',
						'toc' => 'foxtool_toc_settings',
						'ads' => 'foxtool_ads_settings',
						'notify' => 'foxtool_notify_settings',
						'shortcode' => 'foxtool_shortcode_settings',
						'search' => 'foxtool_search_settings',
						'debug' => 'foxtool_debug_settings'
					);
					foreach ($option_keys as $key => $option_name) {
						if (isset($imported_config[$key]) && is_array($imported_config[$key])) {
							if (get_option($option_name) === false) {
								add_option($option_name, $imported_config[$key]);
							} else {
								update_option($option_name, $imported_config[$key]);
							}
						}
					}
					echo '<div class="ft-updated">' . __('New configuration has been successfully added', 'foxtool') . '</div>';
				} else {
					echo '<div class="ft-updated">' . __('Invalid data', 'foxtool') . '</div>';
				}
			}
			?>
			<!-- Xuất nhập tool -->
			<div class="sotab-box ftbox" id="tab1">
			<form method="post" action="<?php echo menu_page_url('foxtool-export-options', false); ?>">
			<h2><?php _e('FOXTOOL', 'foxtool'); ?></h2>
			<div class="ft-card">
			  <h3><i class="fa-regular fa-download"></i> <?php _e('Export', 'foxtool') ?></h3>
				<p>
				<textarea style="height:250px" class="ft-code-textarea" id="foxtool-json"><?php echo $textarea; ?></textarea>
				</p>
				<button type="button" id="foxtool-dow-json"><?php _e('Download', 'foxtool'); ?></button>
			  <h3><i class="fa-regular fa-upload"></i> <?php _e('Import', 'foxtool') ?></h3>
				<p>
				<textarea style="height:250px" class="ft-code-textarea" id="foxtool-import-json" name="foxtool_export_tool" placeholder="<?php _e('Enter data here', 'foxtool'); ?>"></textarea>
				</p>
				<input type="file" id="foxtool-upload-json" accept=".json" style="display:none;" />
				<button type="button" id="foxtool-upload-button" ><?php _e('Upload', 'foxtool'); ?></button>
			</div>
			<div class="ft-submit">
				<button type="submit" name="foxtool_import_tool"><i class="fa-regular fa-file-import"></i> <?php _e('IMPORT FOXTOOL DATA', 'foxtool'); ?></button>
			</div>
			</form>
			</div>
		</div>
	  </div>
	  <div class="ft-sidebar">
		<?php include( FOXTOOL_DIR . 'main/page/ft-aff.php'); ?>
	  </div>
	</div>  
	</div>
	<script type="text/javascript">
	document.addEventListener('DOMContentLoaded', function() {
		// xuat
		const downloadButton = document.getElementById('foxtool-dow-json');
		const etextarea = document.getElementById('foxtool-json');
		downloadButton.addEventListener('click', function() {
			const data = etextarea.value;
			if (data.trim() !== '') {
				const currentDate = new Date();
				const year = currentDate.getFullYear();
				const month = String(currentDate.getMonth() + 1).padStart(2, '0'); 
				const day = String(currentDate.getDate()).padStart(2, '0'); 
				const formattedDate = `${year}-${month}-${day}`;
				const blob = new Blob([data], { type: 'application/json' });
				const url = URL.createObjectURL(blob);
				const a = document.createElement('a');
				a.href = url;
				a.download = `foxtool-${formattedDate}.json`;
				a.style.display = 'none';
				document.body.appendChild(a);
				a.click();
				document.body.removeChild(a);
				URL.revokeObjectURL(url);
			} else {
				alert('<?php _e('The textarea is empty', 'foxtool'); ?>');
			}
		});
		// nhap
		const uploadButton = document.getElementById('foxtool-upload-button');
		const uploadInput = document.getElementById('foxtool-upload-json');    
		const itextarea = document.getElementById('foxtool-import-json');
		uploadButton.addEventListener('click', function() {
			uploadInput.click();
		});
		uploadInput.addEventListener('change', function(event) {
			const file = event.target.files[0]; 
			if (file && file.type === 'application/json') {
				const reader = new FileReader();
				reader.onload = function(e) {
					try {
						const jsonContent = e.target.result;
						itextarea.value = jsonContent;
					} catch (err) {
						alert('<?php _e('The uploaded file is not a valid JSON format', 'foxtool'); ?>');
					}
				};
				reader.readAsText(file);
			} else {
				alert('<?php _e('Please upload a valid JSON file', 'foxtool'); ?>');
			}
			uploadInput.value = '';
		});
	});
	</script>
	<?php
	// style foxtool
	require_once( FOXTOOL_DIR . 'main/style.php');
	echo ob_get_clean();
}
function foxtool_export_options_link() {
	add_submenu_page ('foxtool-options', 'Export', '<i class="fa-regular fa-file-export" style="width:20px;"></i> '. __('Export', 'foxtool'), 'manage_options', 'foxtool-export-options', 'foxtool_export_options_page');
}
add_action('admin_menu', 'foxtool_export_options_link');


