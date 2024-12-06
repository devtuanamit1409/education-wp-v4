<?php
if ( ! $student ) {
	return;
}
if ( ! $course ) {
	return;
}
$show_avatar               = apply_filters( 'learn_press_students_list_avatar', true );
$students_list_avatar_size = apply_filters( 'learn_press_students_list_avatar_size', 32 );
$graduation                = $student_course ? $student_course->get_graduation() : false;
$is_passed                 = $student_course ? $student_course->is_passed() : false;
$result                    = $course_results['result'] ?? 0;
$passing_condition         = $course->get_passing_condition();
$process                   = ( $graduation == LP_COURSE_GRADUATION_IN_PROGRESS ) ? LP_COURSE_GRADUATION_IN_PROGRESS : LP_COURSE_FINISHED;
?>
<li class="students-enrolled <?php echo ( isset( $result ) ) ? 'user-login ' . $process : ''; ?>">
	<div class="user-info">
		<?php if ( $show_avatar ) : ?>
			<?php echo get_avatar( $student->get_id(), $students_list_avatar_size, '', $student->get_data( 'display_name' ), array( 'class' => 'students_list_avatar' ) ); ?>
		<?php endif; ?>
		<a class="name" href="<?php echo learn_press_user_profile_link( $student->get_id() ); ?>"
			title="<?php echo esc_attr( $student->get_data( 'display_name' ) . ' profile' ); ?>">
			<?php echo esc_attr( $student->get_data( 'display_name' ) ); ?>
		</a>
	</div>

	<div class="lp-course-status">
			<span class="number"><?php echo esc_attr( round( $result ?? 0, 2 ) ); ?>
				<span class="percentage-sign">%</span>
			</span>
		<?php if ( $graduation ) : ?>
			<span class="lp-graduation <?php echo esc_attr( $graduation ); ?>"
					style="color: #222; font-weight: 600;">
				<?php learn_press_course_grade_html( $graduation ); ?>
			</span>
		<?php endif; ?>
	</div>

	<div class="learn-press-progress lp-course-progress <?php echo $is_passed ? ' passed' : ''; ?>"
		data-value="<?php echo esc_attr( $result ?? 0 ); ?>"
		data-passing-condition="<?php echo esc_attr( $passing_condition ); ?>">
		<div class="progress-bg lp-progress-bar">
			<div class="progress-active lp-progress-value"
				style="left: <?php echo esc_attr( $result ?? 0 ); ?>%;">
			</div>
		</div>
		<div class="lp-passing-conditional"
			data-content="<?php printf( esc_html__( 'Passing condition: %s%%', 'learnpress' ), $passing_condition ); ?>"
			style="left: <?php echo esc_attr( $passing_condition ); ?>%;">
		</div>
	</div>
</li>