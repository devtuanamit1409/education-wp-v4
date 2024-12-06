<?php
/**
 * Template for displaying h5p tab in user profile page.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/addons/h5p/profile/tabs/h5p_items.php.
 *
 * @author   ThimPress
 * @package  Learnpress/H5p/Templates
 * @version  4.0.0
 */

defined( 'ABSPATH' ) || exit();

$profile = LP_Profile::instance();
$user    = $profile->get_user();

$filter_status = LP_Request::get_string( 'filter-status' );
$curd          = new LP_H5p_CURD();
$query         = $curd->profile_query_h5p_items( $profile->get_user_data( 'id' ), array( 'status' => $filter_status ) );
$filters       = $curd->get_h5p_items_filters( $profile, $filter_status );
?>

<div class="learn-press-subtab-content">
	<h3 class="profile-heading"><?php esc_html_e( 'My H5P Items', 'learnpress-h5p' ); ?></h3>

	<?php if ( $filters ) : ?>
		<ul class="lp-sub-menu learn-press-filters">
			<?php foreach ( $filters as $class => $link ) : ?>
				<li class="<?php echo esc_attr( $class ); ?>"><?php echo $link; ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<?php if ( $query->get_items() ) { ?>
		<table class="lp-list-table profile-list-h5p profile-list-table">
			<thead>
				<tr>
					<th class="column-course"><?php _e( 'Course', 'learnpress-h5p' ); ?></th>
					<th class="column-h5p"><?php _e( 'H5P Items', 'learnpress-h5p' ); ?></th>
					<th class="column-padding-grade"><?php _e( 'Passing Grade', 'learnpress-h5p' ); ?></th>
					<th class="column-status"><?php _e( 'Status', 'learnpress-h5p' ); ?></th>
					<th class="column-mark"><?php _e( 'Mark', 'learnpress-h5p' ); ?></th>
					<th class="column-time-interval"><?php _e( 'Interval', 'learnpress-h5p' ); ?></th>
				</tr>
			</thead>

			<tbody>
			<?php foreach ( $query->get_items() as $user_h5p ) { ?>
				<?php
				$h5p     = learn_press_get_h5p( $user_h5p->get_id() );
				$courses = learn_press_get_item_courses( array( $user_h5p->get_id() ) );

				// for case h5p was un-assign from course
				if ( ! $courses ) {
					continue;
				}

				$course      = learn_press_get_course( $courses[0]->ID );
				$course_data = $user->get_course_data( $course->get_id() );
				if ( ! $course_data ) {
					continue;
				}

				$user_item = $course_data->get_item( $h5p->get_id() );
				if ( ! $user_item ) {
					continue;
				}

				$user_item_id = $user_item->get_user_item_id();
				$mark         = learn_press_get_user_item_meta( $user_item_id, 'score', true );
				$completed    = $user->has_item_status( array( 'completed' ), $h5p->get_id(), $course->get_id() );
				?>

				<tr>
					<td class="column-course">
						<?php if ( $courses ) : ?>
							<a href="<?php echo esc_url( $course->get_permalink() ); ?>">
								<?php echo esc_html( $course->get_title( 'display' ) ); ?>
							</a>
						<?php endif; ?>
					</td>

					<td class="column-h5p">
						<?php if ( $courses ) : ?>
							<a href="<?php echo esc_url( $course->get_item_link( $user_h5p->get_id() ) ); ?>">
								<?php echo esc_html( $h5p->get_title( 'display' ) ); ?>
							</a>
						<?php endif; ?>
					</td>

					<td class="column-padding-grade">
						<?php echo $h5p->get_data( 'passing_grade' ); ?>
					</td>

					<td class="column-status">
						<?php echo $completed ? esc_html__( 'Completed', 'learnpress-h5p' ) : esc_html__( 'Not completed', 'learnpress-h5p' ); ?>
					</td>

					<td class="column-mark">
						<?php
						if ( $completed ) {
							echo $mark . '/' . learn_press_get_user_item_meta( $user_item_id, 'max_score', true );

							if ( ! $completed ) {
								$status = esc_html__( 'completed', 'learnpress-h5p' );
							} else {
								$status = ( $mark / learn_press_get_user_item_meta( $user_item_id, 'max_score', true ) ) * 100 >= $h5p->get_data( 'passing_grade' ) ? esc_html__( 'passed', 'learnpress-h5p' ) : esc_html__( 'failed', 'learnpress-h5p' );
							}
							?>
							<span class="lp-label label-<?php echo esc_attr( $status ); ?>"><?php echo esc_html( $status ); ?></span>
							<?php
						} else {
							echo '-';
						}
						?>
					</td>

					<td class="column-time-interval">
						<?php echo( $user_h5p->get_time_interval( 'display' ) ); ?>
					</td>
				</tr>
				<?php continue; ?>

				<tr>
					<td colspan="4"></td>
				</tr>
			<?php } ?>
			</tbody>

			<tfoot>
			<tr class="list-table-nav">
				<td colspan="2" class="nav-text">
					<?php echo $query->get_offset_text(); ?>
				</td>

				<td colspan="4" class="nav-pages">
					<?php $query->get_nav_numbers( true ); ?>
				</td>
			</tr>
			</tfoot>
		</table>

	<?php } else { ?>
		<?php learn_press_display_message( esc_html__( 'No h5p items!', 'learnpress-h5p' ) ); ?>
	<?php } ?>
</div>
