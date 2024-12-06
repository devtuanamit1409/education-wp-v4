<?php
/**
 * Template for displaying instructor tab in profile page.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/addons/co-instructor/profile-tab.php.
 *
 * @author ThimPress
 * @package LearnPress/Co-Instructor/Templates
 * @version 3.0.6
 */

use LearnPress\Helpers\Template;
use LearnPress\Models\CourseModel;
use LearnPress\TemplateHooks\Course\ListCoursesTemplate;

defined( 'ABSPATH' ) || exit;

global $post;

$profile   = LP_Profile::instance();
$user_id   = $profile->get_user()->get_id();
$limit     = LP_Settings::get_option( 'archive_course_limit', 8 );
$courses   = learn_press_get_course_of_user_instructor(
	array(
		'limit'   => $limit,
		'user_id' => $user_id,
	)
);
$num_pages = learn_press_get_num_pages( $courses['count'], $limit );
?>

<?php if ( $courses['rows'] ) : ?>
	<div class="lp-archive-courses">
		<ul class="learn-press-courses" data-layout="grid" data-size="3">
			<?php

			foreach ( $courses['rows'] as $post ) {
				$course = CourseModel::find( $post->ID, true );
				if ( ! $course ) {
					continue;
				}

				echo ListCoursesTemplate::render_course( $course );
			}
			?>
		</ul>
		<?php learn_press_paging_nav( array( 'num_pages' => $num_pages ) ); ?>
	</div>

	<?php else : ?>
		<?php
		Template::print_message(
			__( 'There isn\'t any courses created by you as an instructor.', 'learnpress-co-instructor' ),
			'info'
		);
		?>
<?php endif; ?>
