<?php
/**
 * Admin View: Drip items
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

$course_id = LP_Request::get_param( 'course-id', 0, 'int' );
$course    = learn_press_get_course( $course_id );
if ( ! $course ) {
	return;
}

$list_table = new LP_Drip_Items_List_Table( $course_id );
$drip_type  = get_post_meta( $course_id, '_lp_content_drip_drip_type', true );
$types      = LP_Addon_Content_Drip_Preload::$addon->lp_content_drip_types();

//get timezone local
$wp_timezone = wp_timezone_string();
$is_utc      = (int) $wp_timezone !== 0;

if ( $is_utc ) {
	$wp_timezone = 'UTC' . $wp_timezone;
}

?>

<div class="wrap" id="learn-press-content-drip">
	<h2><?php esc_html_e( 'Drip items', 'learnpress-content-drip' ); ?></h2>

	<?php
	if ( ! empty( $drip_type ) ) {
		?>

		<?php _e( 'Course:', 'learnpress-content-drip' ); ?>
		<a href="<?php echo $course->get_permalink(); ?>" target="_blank"><?php echo $course->get_title(); ?></a>
		(<a href="<?php echo $course->get_edit_link(); ?>"
			target="_blank"><?php _e( 'Edit', 'learnpress-content-drip' ); ?></a>)

		<?php echo __( '- Drip type: ', 'learnpress-content-drip' ) . '<strong>' . $drip_type ? substr( $types[ $drip_type ], 3 ) : '' . '</strong>'; ?>

		<?php echo sprintf( '<p style="color:red">%s: %s</p>', __( 'You are configuring in Time zone', 'learnpress-content-drip' ), $wp_timezone ); ?></p>

		<!-- show message when update and reset settings false -->
		<div id="content-drip-settings-message"></div>

		<form method="post">
			<?php $list_table->display(); ?>
		</form>

		<?php
	} else {
		echo sprintf( '<p>%s</p>', __( 'Please select "Drip type" and save', 'learnpress-content-drip' ) );
	}
	?>
</div>
