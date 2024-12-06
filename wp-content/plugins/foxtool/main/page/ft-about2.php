<?php 
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $foxtool_options; ?>
<h2><?php _e('DATABASE', 'foxtool'); ?></h2>
  <h3><i class="fa-regular fa-database"></i> <?php _e('Your database', 'foxtool') ?></h3>
	<div class="ft-card-note">
		<div class="ft-showcsdl ft-showcsdl-tit">
			<div><?php _e('Table name', 'foxtool') ?></div>
			<div><?php _e('Field', 'foxtool') ?></div>
			<div><?php _e('MB', 'foxtool') ?></div>
		</div>
		<div class="ft-scdl-scrool">
			<?php foxtool_display_wp_tables(); ?>
		</div>
		<div class="ft-csdl-tatol">
			<p class="cdata-tatol">
			<?php echo __('Total data', 'foxtool') .': '. esc_html(foxtool_get_database_size()); ?>
			</p>
			<p class="cdata-data">
			 <span id="cdata1"></span> <?php _e('System', 'foxtool') ?>
			 <span id="cdata2"></span> <?php _e('Empty', 'foxtool') ?>
			 <span id="cdata3"></span> <?php _e('Data', 'foxtool') ?>
			</p>
		</div>
	</div>
  