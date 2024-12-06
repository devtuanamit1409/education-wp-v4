<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
function foxtool_about_options_page() {
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
			<button class="sotab sotab-select" onclick="fttab(event, 'tab1')"><i class="fa-regular fa-bomb"></i> <?php _e('ABOUT', 'foxtool'); ?></button>
			<button class="sotab" onclick="fttab(event, 'tab2')"><i class="fa-regular fa-database"></i> <?php _e('DATABASE', 'foxtool'); ?></button>
		</div>
		<div class="ft-main">
			<!-- about web -->
			<div class="sotab-box ftbox" id="tab1" style="margin-bottom:-60px;">
				<?php include( FOXTOOL_DIR . 'main/page/ft-about1.php'); ?> 
			</div>
			<!-- about database -->
			<div class="sotab-box ftbox" id="tab2" style="display:none;margin-bottom:-60px;">
				<?php include( FOXTOOL_DIR . 'main/page/ft-about2.php'); ?> 
			</div>
		</div>
	  </div>
      <div class="ft-sidebar">
		<?php include( FOXTOOL_DIR . 'main/page/ft-aff.php'); ?>
	  </div>
	</div>	
	</div>
	<?php
	// style foxtool
	require_once( FOXTOOL_DIR . 'main/style.php');
	echo ob_get_clean();
}
function foxtool_about_options_link() {
	add_submenu_page ('foxtool-options', 'About', '<i class="fa-regular fa-bomb" style="width:20px;"></i> '. __('About', 'foxtool'), 'manage_options', 'foxtool-about-options', 'foxtool_about_options_page');
}
add_action('admin_menu', 'foxtool_about_options_link');

