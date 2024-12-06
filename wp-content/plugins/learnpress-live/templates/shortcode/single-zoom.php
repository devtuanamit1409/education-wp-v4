<?php if ( empty( $data ) ) {
	return;
}

$settings  = (array) $data['settings'] ?? array();
$time_zone = $data['timezone'] ?? wp_timezone_string();
$dt        = new DateTime( "now", new DateTimeZone( $time_zone ) );

//show all occurrences
$type       = $data['type'] ?? 2;
$list_times = array();

if ( $type == 8 ) {
	$list_times = $data['occurrences'] ?? array();
} elseif ( $type == 2 ) {
	if ( ! empty( $data['start_time'] ) ) {
		$timestamp = strtotime( $data['start_time'] );
		$dt->setTimestamp($timestamp);
	}
}

?>
<h4><?php echo __('Zoom Meeting Information', 'learnpress-live') ?></h4>
<div class="z-form-row">
	<div class="form-group">
		<label for="meeting_title"><?php echo __('Meeting Title', 'learnpress-live'); ?></label>
		<div class="content">
			<?php echo $data['topic'] ?? __('My meeting', 'learnpress-live'); ?>
		</div>
	</div>
	<?php if ( $type == 2 ) { ?>
		<div class="form-group">
			<label for="meeting_start"><?php echo __('Date start', 'learnpress-live'); ?></label>
			<div class="content">
				<?php echo $dt->format( 'Y-m-d H:i:s' ); ?>
			</div>
		</div>
	<?php }; ?>
	<?php if ( empty( $list_times ) ) { ?>
	<div class="form-group">
		<label for="meeting_start"><?php echo __('Duration', 'learnpress-live'); ?></label>
		<div class="content">
			<?php echo $data['duration'] ?? 60; ?> <?php echo __('minutes', 'learnpress-live'); ?>
		</div>
	</div>
	<?php } else { ?>
		<div class="form-group">
			<label for="meeting_start"><?php echo __('Date start', 'learnpress-live'); ?></label>
			<div class="time-occurrences content">
				<?php foreach( $list_times as $time ) { ?>
					<?php 
					$timestamp = strtotime( $time->start_time );
					$time_occurrences = new DateTime("now", new DateTimeZone($time_zone));
					$time_occurrences->setTimestamp($timestamp); 
					?>
						<div class="detail-occurrences">
							<?php echo $time_occurrences->format( 'Y-m-d H:i:s' ); ?>
							- 
							<?php echo $time->duration; ?> <?php echo __('minutes', 'learnpress-live'); ?>
						</div>
				<?php }; ?>
			</div>
		</div>
	<?php } ?>
	<div class="form-group">
		<label for="meeting_id"><?php echo __('Meeting ID', 'learnpress-live'); ?></label>
		<div class="content">
			<?php echo $data['id'] ?? 0; ?>
		</div>
	</div>
	<div class="form-group">
		<label for="meeting_url"><?php echo __('Join Link', 'learnpress-live'); ?></label>
		<div class="content">
			<span><?php echo $data['join_url'] ?? '#'; ?></span>
		</div>
	</div>
	<div class="form-group">
		<label for="label_option_password"><?php echo __('Security', 'learnpress-live'); ?></label>
		<div class="content">
			<div class="z-form-row-action">
				<label for="label_option_password"><?php echo __('Password', 'learnpress-live'); ?></label>
				<span class="hidePassword"><?php echo $data['password'] ?? '123456'; ?></span>
			</div>
			<?php if( $settings['waiting_room'] ) : ?>
			<label for="meeting_waiting" class="meeting_waiting">
				<span class="icon-checked"></span>
				<?php echo __('Waiting Room', 'learnpress-live'); ?>
			</label>
			<?php endif; ?>
		</div>
	</div>
	<?php if ( ! empty( $settings ) ) :  ?>
		<div class="form-group">
			<label for="meeting-label"><?php echo __('Settings', 'learnpress-live'); ?></label>
			<div class="content">
				<?php if ( ! empty($settings['host_video']) && $settings['host_video'] == 1 ) { ?>
					<div class="controls col-md-10">
						<label class="checkbox">
							<span class="icon-checked"></span>
							<?php echo __('Start video when the host joins the meeting', 'learnpress-live'); ?>
						</label>
					</div>
				<?php }; ?>
				<?php if ( !empty($settings['participant_video']) && $settings['participant_video'] == 1 ) { ?>
					<div class="controls col-md-10">
						<label class="checkbox ">
							<span class="icon-checked"></span>
							<?php echo __('Allow participants to join the meeting before the host starts the meeting. Only used for scheduled or recurring meetings', 'learnpress-live'); ?>
						</label>
					</div>
				<?php }; ?>
				<?php if( !empty($settings['auto_recording']) && $settings['auto_recording'] != 'none' ) { ?>
					<div class="controls col-md-10">
						<label class="checkbox ">
							<span class="icon-checked"></span>
							<?php echo __('Allow automatic recording', 'learnpress-live'); ?>
						</label>
					</div>
				<?php }; ?>
				<?php if( ! empty( $settings['join_before_host'] ) ) { ?>
					<div class="controls col-md-10">
						<label class="checkbox ">
							<span class="icon-checked"></span>
							<?php echo __('Allow participants to enter at any time', 'learnpress-live'); ?>
						</label>
					</div>
				<?php }; ?>
				<?php if( ! empty( $settings['mute_upon_entry'] ) ) { ?>
					<div class="controls col-md-10">
						<label class="checkbox ">
							<span class="icon-checked"></span>
							<?php echo __('Mute participants when entering a meeting', 'learnpress-live'); ?>
						</label>
					</div>
				<?php }; ?>
				<?php if( ! empty( $settings['approved_or_denied_countries_or_regions'] ) && $settings['approved_or_denied_countries_or_regions']->enable == 1 ) { ?>
					<div class="controls col-md-10">
						<label class="checkbox ">
							<span class="icon-checked"></span>
							<?php echo __('Approve or block user access from specific countries/regions', 'learnpress-live'); ?>
						</label>
					</div>
				<?php }; ?>
			</div>
		</div>
	<?php endif; ?>
</div>
<div class="z-form-button text-center">
	<a href="<?php echo $data['join_url'] ?? '#'; ?>" target="_blank" class="lp-button button">
		<?php echo __( 'Join Now', 'learnpress-live' ); ?>
	</a>
</div>
