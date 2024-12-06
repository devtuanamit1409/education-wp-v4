<?php if ( empty( $results ) || empty( $results['live_value'] ) ) {
	return;
}
$data       = json_decode( $results['live_value'] );
$start_time = $data->start->dateTime;
$end_time   = $data->end->dateTime;
$duration   = ( strtotime( $end_time ) - strtotime( $start_time ) ) / 60 ;
?>

<h4><?php echo __('Google Meet Information', 'learnpress-live') ?></h4>
<div class="z-form-row">
	<div class="form-group">
			<label for="meeting_title"><?php echo __('Meeting Title', 'learnpress-live'); ?></label>
			<div class="content">
				<?php echo $data->summary; ?>
			</div>
		</div>
		<?php if ( ! empty( $data->description ) ) { ?>
			<div class="form-group">
				<label for="meeting_start"><?php echo __('Description', 'learnpress-live'); ?></label>
				<div class="content">
					<?php echo $data->description; ?>
				</div>
			</div>
		<?php }; ?>
		<div class="form-group">
			<label for="meeting_title"><?php echo __('Duration', 'learnpress-live'); ?></label>
			<div class="content">
				<?php echo $duration; ?>
			</div>
		</div>
	<div class="form-group">
		<label for="meeting-label"><?php echo __('Settings', 'learnpress-live'); ?></label>
		<div class="content">
			<?php if ( ! empty( $data->guestsCanModify ) ) { ?>
			<div class="controls col-md-10">
				<label class="checkbox">
					<span class="icon-checked"></span>
					<?php echo __('Attendees other than the organizer can modify the event', 'learnpress-live'); ?>
				</label>
			</div>
			<?php }; ?>
			<?php if( ! empty( $data->transparency ) && $data->transparency == 'transparent' ) { ?>
			<div class="controls col-md-10">
				<label class="checkbox ">
					<span class="icon-checked"></span>
					<?php echo __('Unlimited time (for the long conference)', 'learnpress-live'); ?>
				</label>
			</div>
			<?php }; ?>
		</div>
	</div>
</div>
<div class="z-form-button text-center">
	<a href="<?php echo $data->hangoutLink; ?>" target="_blank" class="lp-button button">
		<?php echo __( 'Join Now', 'learnpress-live' ); ?>
	</a>
</div>
