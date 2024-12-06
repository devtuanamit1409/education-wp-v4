<?php 
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $foxtool_options; ?>
<h2><?php _e('CHAT', 'foxtool'); ?></h2>
<div class="ft-on">
<label class="nut-fton">
<input class="toggle-checkbox" id="check12" data-target="play12" type="checkbox" name="foxtool_settings[chat]" value="1" <?php if ( isset($foxtool_options['chat']) && 1 == $foxtool_options['chat'] ) echo 'checked="checked"'; ?> />
<span class="ftder"></span></label>
<label class="ft-on-right"><?php _e('ON/OFF', 'foxtool'); ?></label>
</div>
<div id="play12" class="ft-card toggle-div">
  <h3><i class="fa-regular fa-message-lines"></i> <?php _e('Create a chat feature for users', 'foxtool') ?></h3>
	<p>
	<label class="nut-switch">
	<input type="checkbox" name="foxtool_settings[chat-nut1]" value="1" <?php if ( isset($foxtool_options['chat-nut1']) && 1 == $foxtool_options['chat-nut1'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ft-label-right"><?php _e('Enable chat button', 'foxtool'); ?></label>
	</p>
	<p>
	<label class="nut-switch">
	<input type="checkbox" name="foxtool_settings[chat-nut-js]" value="1" <?php if ( isset($foxtool_options['chat-nut-js']) && 1 == $foxtool_options['chat-nut-js'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ft-label-right"><?php _e('Hide chat button when scrolling down', 'foxtool'); ?></label>
	</p>
	<h4><?php _e('Display options', 'foxtool'); ?></h4>
	<div id="ft-imgstyle3" class="ft-imgstyle ft-imgstyle6">
		<img src="<?php echo esc_url(FOXTOOL_URL .'img/chat/1.png'); ?>" data-value="Default" class="<?php if(isset($foxtool_options['chat-nut-skin']) && $foxtool_options['chat-nut-skin'] == 'Default') echo 'selected'; ?>" />
		<img src="<?php echo esc_url(FOXTOOL_URL .'img/chat/2.png'); ?>" data-value="Total" class="<?php if(isset($foxtool_options['chat-nut-skin']) && $foxtool_options['chat-nut-skin'] == 'Total') echo 'selected'; ?>" />
		<img src="<?php echo esc_url(FOXTOOL_URL .'img/chat/3.png'); ?>" data-value="Effective" class="<?php if(isset($foxtool_options['chat-nut-skin']) && $foxtool_options['chat-nut-skin'] == 'Effective') echo 'selected'; ?>" />
		<img src="<?php echo esc_url(FOXTOOL_URL .'img/chat/4.png'); ?>" data-value="Leaves" class="<?php if(isset($foxtool_options['chat-nut-skin']) && $foxtool_options['chat-nut-skin'] == 'Leaves') echo 'selected'; ?>" />
		<img src="<?php echo esc_url(FOXTOOL_URL .'img/chat/5.png'); ?>" data-value="Floating" class="<?php if(isset($foxtool_options['chat-nut-skin']) && $foxtool_options['chat-nut-skin'] == 'Floating') echo 'selected'; ?>" />
		<img src="<?php echo esc_url(FOXTOOL_URL .'img/chat/6.png'); ?>" data-value="Tap" class="<?php if(isset($foxtool_options['chat-nut-skin']) && $foxtool_options['chat-nut-skin'] == 'Tap') echo 'selected'; ?>" />
	</div>
	<input type="hidden" name="foxtool_settings[chat-nut-skin]" id="chat-nut-skin" value="<?php if(!empty($foxtool_options['chat-nut-skin'])){echo sanitize_text_field($foxtool_options['chat-nut-skin']);} else {echo sanitize_text_field('Default');} ?>" />
	<script>
		document.addEventListener("DOMContentLoaded", function() {
			var imgStyles = document.querySelectorAll('#ft-imgstyle3 img');
			imgStyles.forEach(function(img) {
				img.addEventListener('click', function() {
					var selectedStyle = this.getAttribute('data-value');
					document.getElementById('chat-nut-skin').value = selectedStyle;
					imgStyles.forEach(function(img) {
						img.classList.remove('selected');
					});
					this.classList.add('selected');
				});
			});
		});
	</script>
	<h4><?php _e('Configure buttons', 'foxtool'); ?></h4>
	<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Visit this page to get the SVG icon:', 'foxtool'); ?> <b><a target="_blank" href="https://lineicons.com/free-icons">lineicons.com</a> [Copy SVG]</b><br>
	<b>Custom, Maps:</b> <?php _e('Enter link', 'foxtool'); ?><br>
	<b>Phone, SMS, Messenger, Telegram, Zalo, Whatsapp, Viber, Skype, Tiktok, Mail:</b> <?php _e('Enter ID', 'foxtool'); ?>
	</p>
	<div id="sortable-list">
	<div data-id="1" class="ui-state-default ft-button-grid">
	<?php $styles = array('Custom', 'Phone', 'SMS', 'Messenger', 'Telegram', 'Zalo', 'Whatsapp', 'Viber', 'Skype', 'Tiktok', 'Mail', 'Maps'); ?>
	<select name="foxtool_settings[chat-nut11]"> 
	<?php foreach($styles as $style) { ?> 
	<?php if(isset($foxtool_options['chat-nut11']) && $foxtool_options['chat-nut11'] == $style) { $selected = 'selected="selected"'; } else { $selected = ''; } ?>
	<option value="<?php echo $style; ?>" <?php echo $selected; ?>><?php echo $style; ?></option> 
	<?php } ?> 
	</select>
	<div class="ft-button-grid-in">
	<input class="ft-input-big" placeholder="<?php _e('Enter button name', 'foxtool'); ?>" type="text" name="foxtool_settings[chat-nut21]" value="<?php if(!empty($foxtool_options['chat-nut21'])){echo sanitize_text_field($foxtool_options['chat-nut21']);} ?>" />
	<input class="ft-input-big" placeholder="<?php _e('Enter link', 'foxtool'); ?>" type="text" name="foxtool_settings[chat-nut31]" value="<?php if(!empty($foxtool_options['chat-nut31'])){echo sanitize_text_field($foxtool_options['chat-nut31']);} ?>" />
	<textarea style="height:40px;" class="ft-code-textarea" name="foxtool_settings[chat-nut41]" placeholder="<?php _e('Enter the SVG icon', 'foxtool'); ?>"><?php if(!empty($foxtool_options['chat-nut41'])){echo esc_textarea($foxtool_options['chat-nut41']);} ?></textarea>
	</div>
	<div class="fr-move"><i class="fa-regular fa-grip-dots-vertical"></i></div>
	</div>
	<?php
	if (is_array($foxtool_options) || is_object($foxtool_options)) {
		foreach ($foxtool_options as $key => $value) {
			if (preg_match('/^chat-nut1(\d+)$/', $key, $matches) && $matches[1] != 1) {
				$n = $matches[1];
				echo '<div data-id="' . $n . '" class="ui-state-default ft-button-grid">';
				echo '<select name="foxtool_settings[chat-nut1' . $n . ']">';
				foreach ($styles as $style) {
					$selected = ($style == $value) ? 'selected="selected"' : '';
					echo '<option value="' . $style . '" ' . $selected . '>' . $style . '</option>';
				}
				echo '</select>';
				echo '<div class="ft-button-grid-in">';
				echo '<input class="ft-input-big" placeholder="'. __('Enter button name', 'foxtool') .'" type="text" name="foxtool_settings[chat-nut2' . $n . ']" value="' . sanitize_text_field($foxtool_options['chat-nut2' . $n]) . '" />';
				echo '<input class="ft-input-big" placeholder="'. __('Enter link', 'foxtool') .'" type="text" name="foxtool_settings[chat-nut3' . $n . ']" value="' . sanitize_text_field($foxtool_options['chat-nut3' . $n]) . '" />';
				echo '<textarea style="height:40px;" class="ft-code-textarea" name="foxtool_settings[chat-nut4' . $n . ']" placeholder="'. __('Enter the SVG icon', 'foxtool') .'">' . esc_textarea($foxtool_options['chat-nut4' . $n]) . '</textarea>';
				echo '</div>';
				echo '<div class="fr-move"><i class="fa-regular fa-grip-dots-vertical"></i></div><span id="ft-chatx">&#x2715</span>';
				echo '</div>';
			}
		}
	}
	?>
	</div>
	<span id="ft-chatmore"><i class="fa-regular fa-plus"></i> <?php _e('Add field', 'foxtool'); ?></span>
	<script>
	jQuery(document).ready(function($){
		var count = 0;
		$('#ft-chatmore').click(function() {
			var count = $('#sortable-list .ui-state-default:last').data('id') + 1;
			var newDiv = $('<div data-id="' + count + '" class="ui-state-default ft-button-grid">' +
				'<select name="foxtool_settings[chat-nut1' + count + ']">' +
				'<option value="Custom">Custom</option>' +
				'<option value="Phone">Phone</option>' +
				'<option value="SMS">SMS</option>' +
				'<option value="Messenger">Messenger</option>' +
				'<option value="Telegram">Telegram</option>' +
				'<option value="Zalo">Zalo</option>' +
				'<option value="Whatsapp">Whatsapp</option>' +
				'<option value="Viber">Viber</option>' +
				'<option value="Skype">Skype</option>' +
				'<option value="Skype">Tiktok</option>' +
				'<option value="Mail">Mail</option>' +
				'<option value="Maps">Maps</option>' +
				'</select>' +
				'<div class="ft-button-grid-in">' +
				'<input class="ft-input-big" placeholder="<?php _e('Enter button name', 'foxtool'); ?>" type="text" name="foxtool_settings[chat-nut2' + count + ']" />' +
				'<input class="ft-input-big" placeholder="<?php _e('Enter link', 'foxtool'); ?>" type="text" name="foxtool_settings[chat-nut3' + count + ']" />' +
				'<textarea style="height:40px;" class="ft-code-textarea" name="foxtool_settings[chat-nut4' + count + ']" placeholder="<?php _e('Enter the SVG icon', 'foxtool'); ?>"></textarea>' +
				'</div>' +
				'<div class="fr-move"><i class="fa-regular fa-grip-dots-vertical"></i></div><span id="ft-chatx">&#x2715</span>' +
				'</div>');
			$('#sortable-list').append(newDiv);
		});
		$('#sortable-list').on('click', '#ft-chatx', function() {
			$(this).parent('.ui-state-default').remove();
			count--;
		});
	});
	// keo qua lai
	jQuery(function($) {
		$("#sortable-list").sortable({
			connectWith: "#sortable-list",
			update: function(event, ui) {
				updateNames();
			}
		}).disableSelection();

		function updateNames() {
			$("#sortable-list .ui-state-default").each(function(index) {
				var newIndex = index + 1;
				var selectName = 'foxtool_settings[chat-nut1' + newIndex + ']';
				var inputName2Name = 'foxtool_settings[chat-nut2' + newIndex + ']';
				var inputName3Name = 'foxtool_settings[chat-nut3' + newIndex + ']';
				var textareaName = 'foxtool_settings[chat-nut4' + newIndex + ']';
				$(this).find("select").attr("name", selectName);
				$(this).find('input[name^="foxtool_settings[chat-nut2"]').attr("name", inputName2Name);
				$(this).find('input[name^="foxtool_settings[chat-nut3"]').attr("name", inputName3Name);
				$(this).find('textarea[name^="foxtool_settings[chat-nut4"]').attr("name", textareaName);
			});
		}
	});
	</script>
	<h4><?php _e('Default style', 'foxtool'); ?></h4>
	<p style="display:flex;align-items:center;">
	<input class="ft-input-color" name="foxtool_settings[chat-nut-color]" type="text" data-coloris value="<?php if(!empty($foxtool_options['chat-nut-color'])){echo $foxtool_options['chat-nut-color'];} ?>"/>
	<label class="ft-right-text"><?php _e('Select button color', 'foxtool'); ?></label>
	</p>
	<p>
	<?php $styles = array('Icon1', 'Icon2', 'Icon3', 'Icon4', 'Icon5'); ?>
	<select name="foxtool_settings[chat-nut-ico]"> 
	<?php foreach($styles as $style) { ?> 
	<?php if(isset($foxtool_options['chat-nut-ico']) && $foxtool_options['chat-nut-ico'] == $style) { $selected = 'selected="selected"'; } else { $selected = ''; } ?>
	<option value="<?php echo $style; ?>" <?php echo $selected; ?>><?php echo $style; ?></option> 
	<?php } ?> 
	</select>
	<label class="ft-right-text"><?php _e('Select icon', 'foxtool'); ?></label>
	</p>
	
	<h4><?php _e('Customize chat button', 'foxtool'); ?></h4>
	<p>
	<label class="nut-switch">
	<input type="checkbox" name="foxtool_settings[chat-nut-new]" value="1" <?php if ( isset($foxtool_options['chat-nut-new']) && 1 == $foxtool_options['chat-nut-new'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ft-label-right"><?php _e('Open in a new tab', 'foxtool'); ?></label>
	</p>
	<p style="display:flex;align-items:center;">
	<input class="ft-input-color" name="foxtool_settings[chat-ico-color]" type="text" data-coloris value="<?php if(!empty($foxtool_options['chat-ico-color'])){echo $foxtool_options['chat-ico-color'];} ?>"/>
	<label class="ft-right-text"><?php _e('Select icon color', 'foxtool'); ?></label>
	</p>
	<p>
	<?php $styles = array('Left', 'Right'); ?>
	<select name="foxtool_settings[chat-nut-mar]"> 
	<?php foreach($styles as $style) { ?> 
	<?php if(isset($foxtool_options['chat-nut-mar']) && $foxtool_options['chat-nut-mar'] == $style) { $selected = 'selected="selected"'; } else { $selected = ''; } ?>
	<option value="<?php echo $style; ?>" <?php echo $selected; ?>><?php echo $style; ?></option> 
	<?php } ?> 
	</select>
	<label class="ft-right-text"><?php _e('Button position', 'foxtool'); ?></label>
	</p>
	
	<p class="ft-keo">
	<input type="range" name="foxtool_settings[chat-nut-bot]" min="10" max="300" value="<?php if(!empty($foxtool_options['chat-nut-bot'])){echo sanitize_text_field($foxtool_options['chat-nut-bot']);} else { echo sanitize_text_field('10');} ?>" class="ftslide" data-index="1">
	<span><?php _e('Spacing below', 'foxtool'); ?> <span id="demo1"></span> PX</span>
	</p>
	<p class="ft-keo">
	<input type="range" name="foxtool_settings[chat-nut-lr]" min="10" max="100" value="<?php if(!empty($foxtool_options['chat-nut-lr'])){echo sanitize_text_field($foxtool_options['chat-nut-lr']);} else { echo sanitize_text_field('10');} ?>" class="ftslide" data-index="2">
	<span><?php _e('Border distance', 'foxtool'); ?> <span id="demo2"></span> PX</span>
	</p>
	<p class="ft-keo">
	<input type="range" name="foxtool_settings[chat-nut-op]" min="0" max="1" step="0.1" value="<?php if(!empty($foxtool_options['chat-nut-op'])){echo sanitize_text_field($foxtool_options['chat-nut-op']);} else { echo sanitize_text_field('1');} ?>" class="ftslide" data-index="3">
	<span><?php _e('Transparency level', 'foxtool'); ?> <span id="demo3"></span></span>
	</p>
	<p class="ft-keo">
	<input type="range" name="foxtool_settings[chat-nut-rus]" min="1" max="50" value="<?php if(!empty($foxtool_options['chat-nut-rus'])){echo sanitize_text_field($foxtool_options['chat-nut-rus']);} else { echo sanitize_text_field('50');} ?>" class="ftslide" data-index="4">
	<span><?php _e('Border radius', 'foxtool'); ?> <span id="demo4"></span> PX</span>
	</p>
	
	
  <h3><i class="fa-regular fa-bars"></i> <?php _e('Contact bar on mobile', 'foxtool') ?></h3>
	<p>
	<label class="nut-switch">
	<input type="checkbox" name="foxtool_settings[chat-nav1]" value="1" <?php if ( isset($foxtool_options['chat-nav1']) && 1 == $foxtool_options['chat-nav1'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ft-label-right"><?php _e('Enable contact bar', 'foxtool'); ?></label>
	</p>
	<p>
	<label class="nut-switch">
	<input type="checkbox" name="foxtool_settings[chat-nav-js]" value="1" <?php if ( isset($foxtool_options['chat-nav-js']) && 1 == $foxtool_options['chat-nav-js'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ft-label-right"><?php _e('Hide bar when pulled down', 'foxtool'); ?></label>
	</p>
	<h4><?php _e('Display options', 'foxtool'); ?></h4>
	<div id="ft-imgstyle4" class="ft-imgstyle ft-imgstyle5">
		<img src="<?php echo esc_url(FOXTOOL_URL .'img/chat/n1.png'); ?>" data-value="Default" class="<?php if(isset($foxtool_options['chat-nav-skin']) && $foxtool_options['chat-nav-skin'] == 'Default') echo 'selected'; ?>" />
		<img src="<?php echo esc_url(FOXTOOL_URL .'img/chat/n2.png'); ?>" data-value="Simple" class="<?php if(isset($foxtool_options['chat-nav-skin']) && $foxtool_options['chat-nav-skin'] == 'Simple') echo 'selected'; ?>" />
		<img src="<?php echo esc_url(FOXTOOL_URL .'img/chat/n3.png'); ?>" data-value="Docky" class="<?php if(isset($foxtool_options['chat-nav-skin']) && $foxtool_options['chat-nav-skin'] == 'Docky') echo 'selected'; ?>" />
		<img src="<?php echo esc_url(FOXTOOL_URL .'img/chat/n4.png'); ?>" data-value="Momo" class="<?php if(isset($foxtool_options['chat-nav-skin']) && $foxtool_options['chat-nav-skin'] == 'Momo') echo 'selected'; ?>" />
		<img src="<?php echo esc_url(FOXTOOL_URL .'img/chat/n5.png'); ?>" data-value="Lom" class="<?php if(isset($foxtool_options['chat-nav-skin']) && $foxtool_options['chat-nav-skin'] == 'Lom') echo 'selected'; ?>" />
	</div>
	<input type="hidden" name="foxtool_settings[chat-nav-skin]" id="chat-nav-skin" value="<?php if(!empty($foxtool_options['chat-nav-skin'])){echo sanitize_text_field($foxtool_options['chat-nav-skin']);} else {echo sanitize_text_field('Default');} ?>" />
	<script>
		document.addEventListener("DOMContentLoaded", function() {
			var imgStyles = document.querySelectorAll('#ft-imgstyle4 img');
			imgStyles.forEach(function(img) {
				img.addEventListener('click', function() {
					var selectedStyle = this.getAttribute('data-value');
					document.getElementById('chat-nav-skin').value = selectedStyle;
					imgStyles.forEach(function(img) {
						img.classList.remove('selected');
					});
					this.classList.add('selected');
				});
			});
		});
	</script>
	<h4><?php _e('Main button', 'foxtool'); ?></h4>
	<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Enable the contact bar and configure the content below for use', 'foxtool'); ?><br>
	<?php _e('Visit this page to get the SVG icon:', 'foxtool'); ?> <b><a target="_blank" href="https://lineicons.com/free-icons">lineicons.com</a> [Copy SVG]</b><br>
	<b><?php _e('To create a menu on the navigation bar:', 'foxtool'); ?></b><br>
	<?php _e('Step 1: Go to Appearance > Menus > Create a new menu > check Navigation bar (Foxtool)', 'foxtool'); ?><br>
	<?php _e('Step 2: Below, if you want the menu to open on a specific button, add <b style="color:red">#foxnavi</b> to the field (#id or .class). For (Enter link), enter <b style="color:red">#</b>. Note: Only add it to one of the 5 buttons below', 'foxtool'); ?>
	</p>
	<div class="ft-button-grid2">
		<div class="ft-button-grid-in2">
			<textarea style="height:60px;" class="ft-code-textarea" name="foxtool_settings[chat-nav01]" placeholder="<?php _e('Enter the SVG icon', 'foxtool'); ?>"><?php if(!empty($foxtool_options['chat-nav01'])){echo esc_textarea($foxtool_options['chat-nav01']);} ?></textarea>
			<input class="ft-input-big" placeholder="<?php _e('Enter name', 'foxtool'); ?>" type="text" name="foxtool_settings[chat-nav02]" value="<?php if(!empty($foxtool_options['chat-nav02'])){echo sanitize_text_field($foxtool_options['chat-nav02']);} ?>" />
			<input class="ft-input-big" placeholder="<?php _e('Enter link', 'foxtool'); ?>" type="text" name="foxtool_settings[chat-nav03]" value="<?php if(!empty($foxtool_options['chat-nav03'])){echo sanitize_text_field($foxtool_options['chat-nav03']);} ?>" />
			<input class="ft-input-big" placeholder="<?php _e('#id or .class', 'foxtool'); ?>" type="text" name="foxtool_settings[chat-nav04]" value="<?php if(!empty($foxtool_options['chat-nav04'])){echo sanitize_text_field($foxtool_options['chat-nav04']);} ?>" />
		</div>
	</div>
	<p class="ft-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('"Enter link" left blank if you want to call the chat list', 'foxtool'); ?></p>
	<h4><?php _e('4 customizable buttons', 'foxtool'); ?></h4>
	<div id="sortable-list2">
	<?php
	for ($i = 1; $i <= 4; $i++) { ?>
		<div class="ui-state-default2 ft-button-grid2">
		 <div class="ft-button-grid-in2">
			<textarea style="height:60px;" class="ft-code-textarea" name="foxtool_settings[chat-nav1<?php echo $i ?>]" placeholder="<?php _e('Enter the SVG icon', 'foxtool'); ?>"><?php if(!empty($foxtool_options['chat-nav1'. $i])){echo esc_textarea($foxtool_options['chat-nav1'. $i]);} ?></textarea>
			<input class="ft-input-big" placeholder="<?php _e('Enter name', 'foxtool'); ?>" type="text" name="foxtool_settings[chat-nav2<?php echo $i ?>]" value="<?php if(!empty($foxtool_options['chat-nav2'. $i])){echo sanitize_text_field($foxtool_options['chat-nav2'. $i]);} ?>" />
			<input class="ft-input-big" placeholder="<?php _e('Enter link', 'foxtool'); ?>" type="text" name="foxtool_settings[chat-nav3<?php echo $i ?>]" value="<?php if(!empty($foxtool_options['chat-nav3'. $i])){echo sanitize_text_field($foxtool_options['chat-nav3'. $i]);} ?>" />
			<input class="ft-input-big" placeholder="<?php _e('#id or .class', 'foxtool'); ?>" type="text" name="foxtool_settings[chat-nav4<?php echo $i ?>]" value="<?php if(!empty($foxtool_options['chat-nav4'. $i])){echo sanitize_text_field($foxtool_options['chat-nav4'. $i]);} ?>" />
		 </div>
		 <div class="fr-move"><i class="fa-regular fa-grip-dots-vertical"></i></div>
		</div>
	<?php } ?>
	</div>
	<script>
	jQuery(function($) {
		$("#sortable-list2").sortable({
			connectWith: "#sortable-list2",
			update: function(event, ui){
				updateNames();
			}
		}).disableSelection();
		function updateNames() {
			$("#sortable-list2 .ui-state-default2").each(function(index) {
				var newIndex = index + 1;
				var textareaName = 'foxtool_settings[chat-nav1' + newIndex + ']';
				var inputName2Name = 'foxtool_settings[chat-nav2' + newIndex + ']';
				var inputName3Name = 'foxtool_settings[chat-nav3' + newIndex + ']';
				$(this).find('textarea[name^="foxtool_settings[chat-nav1"]').attr("name", textareaName);
				$(this).find('input[name^="foxtool_settings[chat-nav2"]').attr("name", inputName2Name);
				$(this).find('input[name^="foxtool_settings[chat-nav3"]').attr("name", inputName3Name);
			});
		}
	});
	</script>
	<h4><?php _e('Customize', 'foxtool'); ?></h4>
	<select id="foxtool-toc-page-select">
		<option value=""><?php _e('Select the page to hide', 'foxtool'); ?></option>
		<?php
		$pages = get_pages();
		foreach ($pages as $page) {
			echo '<option value="' . esc_attr($page->post_name) . '">' . esc_html($page->post_title) . '</option>';
		}
		?>
	</select>
	<div id="foxtool-toc-tags">
		<?php 
		if (!empty($foxtool_options['chat-nav-hi'])) {
			$selected_pages = explode("\n", $foxtool_options['chat-nav-hi']);
			foreach ($selected_pages as $page_slug) {
				if (!empty($page_slug)) {
					echo '<span class="foxtool-toc-tag">' . esc_html($page_slug) . ' <span class="remove-tag" data-slug="' . esc_attr($page_slug) . '">&times;</span></span>';
				}
			}
		} 
		?>
	</div>
	<textarea id="foxtool-hi-textarea" name="foxtool_settings[chat-nav-hi]" style="display:none;"><?php if(!empty($foxtool_options['chat-nav-hi'])){echo esc_textarea($foxtool_options['chat-nav-hi']);} ?></textarea>
	<script>
	jQuery(document).ready(function($) {
		function updateNoPageMessage() {
			if ($('#foxtool-toc-tags .foxtool-toc-tag').length === 0) {
				$('#foxtool-toc-tags').append('<span class="ftno-page"><?php _e('No pages selected', 'foxtool'); ?></span>');
			} else {
				$('#foxtool-toc-tags .ftno-page').remove();
			}
		}
		$('#foxtool-toc-page-select').change(function() {
			var selectedPage = $(this).val();
			if (selectedPage && !isPageAlreadyAdded(selectedPage)) {
				var newTag = $('<span class="foxtool-toc-tag">' + selectedPage + ' <span class="remove-tag" data-slug="' + selectedPage + '">&times;</span></span>');
				$('#foxtool-toc-tags').append(newTag);
				updateTextarea();
				updateNoPageMessage();
			}
			$(this).val('');
		});
		$(document).on('click', '.remove-tag', function() {
			$(this).parent('.foxtool-toc-tag').remove();
			updateTextarea();
			updateNoPageMessage();
		});
		function isPageAlreadyAdded(pageSlug) {
			var exists = false;
			$('#foxtool-toc-tags .foxtool-toc-tag').each(function() {
				if ($(this).find('.remove-tag').data('slug') === pageSlug) {
					exists = true;
					return false; 
				}
			});
			return exists;
		}
		function updateTextarea() {
			var selectedPages = [];
			$('#foxtool-toc-tags .foxtool-toc-tag').each(function() {
				selectedPages.push($(this).find('.remove-tag').data('slug'));
			});
			$('#foxtool-hi-textarea').val(selectedPages.join("\n"));
		}
		updateNoPageMessage();
	});
	</script>
	<p style="display:flex;align-items:center;">
	<input class="ft-input-color" name="foxtool_settings[chat-nav-c1]" type="text" data-coloris value="<?php if(!empty($foxtool_options['chat-nav-c1'])){echo $foxtool_options['chat-nav-c1'];} ?>"/>
	<label class="ft-right-text"><?php _e('Outstanding color', 'foxtool'); ?></label>
	</p>
	<p style="display:flex;align-items:center;">
	<input class="ft-input-color" name="foxtool_settings[chat-nav-c3]" type="text" data-coloris value="<?php if(!empty($foxtool_options['chat-nav-c3'])){echo $foxtool_options['chat-nav-c3'];} ?>"/>
	<label class="ft-right-text"><?php _e('Main icon color', 'foxtool'); ?></label>
	</p>
	<p style="display:flex;align-items:center;">
	<input class="ft-input-color" name="foxtool_settings[chat-nav-c31]" type="text" data-coloris value="<?php if(!empty($foxtool_options['chat-nav-c31'])){echo $foxtool_options['chat-nav-c31'];} ?>"/>
	<label class="ft-right-text"><?php _e('Main icon text color', 'foxtool'); ?></label>
	</p>
	<p style="display:flex;align-items:center;">
	<input class="ft-input-color" name="foxtool_settings[chat-nav-c4]" type="text" data-coloris value="<?php if(!empty($foxtool_options['chat-nav-c4'])){echo $foxtool_options['chat-nav-c4'];} ?>"/>
	<label class="ft-right-text"><?php _e('Bar background color', 'foxtool'); ?></label>
	</p>
	<p style="display:flex;align-items:center;">
	<input class="ft-input-color" name="foxtool_settings[chat-nav-c5]" type="text" data-coloris value="<?php if(!empty($foxtool_options['chat-nav-c5'])){echo $foxtool_options['chat-nav-c5'];} ?>"/>
	<label class="ft-right-text"><?php _e('Chat background color', 'foxtool'); ?></label>
	</p>
	<p style="display:flex;align-items:center;">
	<input class="ft-input-color" name="foxtool_settings[chat-nav-c6]" type="text" data-coloris value="<?php if(!empty($foxtool_options['chat-nav-c6'])){echo $foxtool_options['chat-nav-c6'];} ?>"/>
	<label class="ft-right-text"><?php _e('Chat text color', 'foxtool'); ?></label>
	</p>
</div>		