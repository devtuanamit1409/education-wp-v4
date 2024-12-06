<?php
/**
 * Class StudentsListTemplate
 *
 * Handle return template for students list
 * @since 4.0.2
 * @version 1.0.0
 */

namespace LearnPress\StudentsList;

use LearnPress\Helpers\Template;
use LearnPress\TemplateHooks\TemplateAJAX;
use LearnPress\Helpers\Singleton;
use LP_Course;
use LP_Helper;
use LP_User_Items_DB;
use LP_User_Items_Filter;
use stdClass;
use Throwable;
use Exception;
use LP_Filter;
use LP_Settings;
use LP_Database;
use LP_Addon_Students_List_Preload;

class StudentsListTemplate {

	use Singleton;

	private static $limit;

	/**
	 * init hooks...
	 */
	public function init() {
		self::$limit = LP_Settings::get_option( 'lp_students_per_page', 9 );
		add_filter( 'lp/rest/ajax/allow_callback', [ $this, 'allow_callback' ] );
		add_action( 'lp-addon-students-list/students-list/layout', [ $this, 'layout_students' ], 10, 2 );
	}

	public function allow_callback( $callbacks ) {
		/**
		 * @uses StudentsListTemplate::renderStudentsList
		 */
		$callbacks[] = get_class( $this ) . ':renderStudentsList';

		return $callbacks;
	}

	public function layout_students( $course ) {
		wp_enqueue_style( 'addon-lp-students-list' );
		wp_enqueue_script( 'addon-lp-students-list' );

		$html_wrapper = [
			'<div class="lp-course-students-list-wrapper">' => '</div>',
		];
		$callback     = [
			'class'  => get_class( $this ),
			'method' => 'renderStudentsList',
		];
		$args         = [
			'paged'    => 1,
			'courseID' => $course->get_id(),
			'status'   => 'all',
		];
		$content      = TemplateAJAX::load_content_via_ajax( $args, $callback );
		$html         = Template::instance()->nest_elements( $html_wrapper, $content );

		echo $html;
	}

	/**
	 * Render students list
	 *
	 * @param $args
	 *
	 * @return stdClass
	 */
	public static function renderStudentsList( $args ): stdClass {
		$content = new stdClass();

		try {
			$paged     = (int) ( $args['paged'] ?? 1 );
			$course_id = (int) ( $args['courseID'] ?? 0 );
			$course    = learn_press_get_course( $course_id );
			if ( ! $course ) {
				throw new Exception( 'Course not found' );
			}

			$total_students_enrolled = $course->get_total_user_enrolled();
			$total_students_fake     = $course->get_fake_students();
			$count_all_students      = $course->count_students();

			$total_students = 0;
			$students       = self::instance()->queryGetStudents( $args, $total_students );

			$total_pages         = LP_Database::get_total_pages( self::$limit, $total_students );
			$student_list_html   = static::instance()->html_students_list( $students, $course );
			$filter_student_html = static::instance()->html_filter_students( $args );

			$sections = [];

			if ( $total_students_enrolled > 0 ) {
				$sections['total_students_enrolled'] = [
					'text_html' => sprintf(
						'<p>There are %d students who are learning in this course.</p>',
						$total_students_enrolled
					)
				];

				if ( $course->get_fake_students() > 0 ) {
					$sections['fake_students'] = [
						'text_html' => sprintf(
							'<p>There are %d students enrolled in this course.</p>',
							$course->get_fake_students()
						)
					];
				}

				$sections['filter']   = [ 'text_html' => $filter_student_html ];
				$sections['students'] = [ 'text_html' => $student_list_html ];
				if ( $paged < $total_pages ) {
					$sections['pagination'] = [ 'text_html' => static::instance()->html_pagination( $args ) ];
				}
			} elseif ( $total_students_fake > 0 ) {
				$sections['fake_students'] = [
					'text_html' => sprintf(
						'<p>There have been %d students who have studied before</p>',
						$course->get_fake_students()
					)
				];
			} else {
				$sections['no_students'] = [ 'text_html' => '<p>There are no students</p>' ];
			}

			ob_start();
			Template::instance()->print_sections( $sections );
			$content->content     = ob_get_clean();
			$content->total_pages = $total_pages;
			$content->paged       = $args['paged'];
		} catch ( Throwable $e ) {
			$content->content = '<p>' . $e->getMessage() . '</p>';
		}

		return $content;
	}

	/**
	 * Query get list students
	 *
	 * @param array $args
	 * @param int $total_students
	 *
	 * @return array|int|string|null
	 * @throws Exception
	 */
	public function queryGetStudents( array $args = [], int &$total_students = 0 ) {
		$status              = LP_Helper::sanitize_params_submitted( $args['status'] ?? 'all' );
		$lp_user_items_db    = LP_User_Items_DB::getInstance();
		$filter              = new LP_User_Items_Filter();
		$filter->only_fields = [
			'DISTINCT ui.user_id as user_id',
			'ui.status as status',
		];
		$filter->limit       = self::$limit;
		$filter->page        = (int) ( $args['paged'] ?? 0 );
		$filter->item_id     = (int) ( $args['courseID'] ?? 0 );
		$filter->item_type   = LP_COURSE_CPT;
		$filter->order_by    = 'ui.start_time';
		$filter->field_count = 'ui.user_id';
		switch ( $status ) {
			case 'in-progress':
				$filter->where[] = $lp_user_items_db->wpdb->prepare( 'AND ui.graduation = %s', $status );
				break;
			case 'finished':
				$filter->where[] = $lp_user_items_db->wpdb->prepare( 'AND ui.status = %s', $status );
				break;
			default:
				$filter->where[] = $lp_user_items_db->wpdb->prepare( 'AND ui.status IN (%s, %s)', LP_COURSE_ENROLLED, LP_COURSE_FINISHED );
				break;
		}
		$filter->where[] = 'AND ui.user_id != 0';

		return $lp_user_items_db->get_user_items( $filter, $total_students );
	}

	/**
	 * pagination html(Load more method)
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	public function html_pagination( array $args = [] ): string {
		$html_wrapper = [
			'<button class="students-list-btn-load-more learn-press-pagination lp-button">' => '</button>',
		];
		$content      = sprintf(
			'%s<span class="lp-loading-circle lp-loading-no-css hide"></span>',
			__( 'Load more', 'learnpress-students-list' )
		);

		return Template::instance()->nest_elements( $html_wrapper, $content );
	}

	/**
	 * Get html filter students
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	public function html_filter_students( array $args = [] ): string {
		$status  = $args['status'] ?? 'all';
		$filters = apply_filters(
			'learn_press_get_students_list_filter',
			array(
				'all'         => esc_html__( 'Learning progress of students', 'learnpress-students-list' ),
				'in-progress' => esc_html__( 'In Progress', 'learnpress-students-list' ),
				'finished'    => esc_html__( 'Finished', 'learnpress-students-list' ),
			)
		);

		$filter_wrapper = [ '<div class="filter-students">' => '</div>' ];
		$content        = '<select class="students-list-filter" id="students-list-filter-select">';
		foreach ( $filters as $key => $val ) {
			$content .= sprintf(
				'<option value="%s" %s>%s</option>',
				esc_attr( $key ),
				( $status === $key ? 'selected' : '' ),
				esc_html( $val )
			);
		}
		$content .= '</select>';

		return Template::instance()->nest_elements( $filter_wrapper, $content );
	}

	/**
	 * list student html
	 *
	 * @param array $students
	 * @param LP_Course $course
	 *
	 * @return string
	 */
	public function html_students_list( array $students, LP_Course $course ): string {
		$student_list = '';
		if ( empty( $students ) ) {
			return '<p>There are no students</p>';
		}

		foreach ( $students as $student ) {
			$student_list .= self::renderStudent( $student->user_id, $course );
		}

		$html_students_list_wrapper = [
			'<ul class="students lp-students-list-wrapper" >' => '</ul>',
		];
		ob_start();
		echo Template::instance()->nest_elements( $html_students_list_wrapper, $student_list );

		return ob_get_clean();
	}

	/**
	 * Get single student html
	 *
	 * @param int $student_id LP user ID
	 * @param LP_Course $course LP COurse
	 *
	 * @return string html
	 */
	public static function renderStudent( int $student_id, LP_Course $course ): string {
		ob_start();
		$student               = learn_press_get_user( $student_id );
		$student_course        = $student->get_course_data( $course->get_id() );
		$student_course_result = $student_course->calculate_course_results();

		$html_wrapper = apply_filters(
			'lp/addon/students-list/student-course-sections-wrapper',
			[
				'<li class="lp-student-enrolled">'  => '</li>',
				'<div class="student-course-item">' => '</div>',
			],
			$student,
			$course,
			$student_course,
			$student_course_result
		);

		$student_name = $student->get_display_name();
		if ( current_user_can( ADMIN_ROLE ) ) {
			$student_name = sprintf(
				'<a href="%s" title="%s">%s</a>',
				learn_press_user_profile_link( $student->get_id() ),
				esc_attr( $student->get_display_name() . ' profile' ),
				$student->get_display_name()
			);
		}

		$sections = apply_filters(
			'lp/addon/students-list/student-course-sections',
			[
				'student-img'          => [
					'text_html' => $student->get_profile_picture(),
				],
				'div-student-info'     => [
					'text_html' => '<div class="student-info">',
				],
				'student-name'         => [
					'text_html' => $student_name,
				],
				'student-process'      => [
					'text_html' => sprintf(
						'<p>%s %s</p>',
						__( 'Progress', 'learnpress-students-list' ),
						$student_course_result['result'] . '%'
					),
				],
				'end-div-student-info' => [
					'text_html' => '</div>',
				]
			],
			$student,
			$course,
			$student_course,
			$student_course_result
		);

		Template::instance()->print_sections( $sections );

		return Template::instance()->nest_elements( $html_wrapper, ob_get_clean() );
	}
}
