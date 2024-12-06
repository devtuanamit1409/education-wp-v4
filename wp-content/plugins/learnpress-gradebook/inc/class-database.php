<?php
/**
 * Use for call database.
 *
 * @package LearnPress/Gradebook
 * @author Nhamdv
 */
class LP_Gradebook_Database extends LP_Database {
	private static $_instance = null;

	protected function __construct() {
		parent::__construct();
	}

	/**
	 * Nhamdv
	 *
	 * @param [type] $course_id
	 * @param [type] $user_id
	 * @param string $status
	 * @param string $item_type
	 * @return void
	 */
	public function get_item_id_in_user_items( $course_id, $user_id, $status = '', $item_type = '', $item_ids = array() ) {
		global $wpdb;

		$where = $wpdb->prepare( 'ref_id=%d AND user_id=%d', absint( $course_id ), absint( $user_id ) );

		if ( ! empty( $status ) ) {
			$where .= $wpdb->prepare( ' AND status=%s', $status );
		}

		if ( ! empty( $item_type ) ) {
			$where .= $wpdb->prepare( ' AND item_type=%s', $item_type );
		}
		// compare item in course with item in-progess
		$item_ids_format = LP_Helper::db_format_array( $item_ids, '%s' );
		$where          .= $wpdb->prepare( ' AND item_id IN (' . $item_ids_format . ')', $item_ids );
		//
		$where .= ' ORDER BY start_time DESC ';

		$query = "SELECT item_id, start_time, end_time, graduation, status FROM {$wpdb->learnpress_user_items} WHERE $where";

		return $wpdb->get_results( $query );
	}

	/**
	 * query get all students enroll by course ID
	 *
	 * @param int    $courseID
	 * @param int    $limit
	 * @param int    $page
	 * @param float  $time_ago
	 * @param string $graduation
	 * @param int    $average
	 * @param string $username
	 */
	public function get_all_students( $course_id, $limit, $page, $graduation = '', $username = '', $calc = false ) {
		global $wpdb;

		if ( ! $course_id ) {
			return false;
		}

		if ( $calc ) {
			$select = 'SQL_CALC_FOUND_ROWS ID';
		} else {
			$select = 'u.user_nicename, u.user_email, ui.user_id, ui.start_time, ui.graduation';
		}

		$where = $wpdb->prepare( 'ui.item_id=%d', absint( $course_id ) );

		if ( ! empty( $graduation ) ) {
			$where .= $wpdb->prepare( ' AND ui.graduation=%s', $graduation );
		}

		if ( ! empty( $username ) ) {
			$where .= $wpdb->prepare( ' AND u.user_nicename LIKE %s', '%' . $wpdb->esc_like( $username ) . '%' );
		}

		if ( $calc ) {
			$sql_limit = '';
		} else {
			$offset    = ( absint( $page ) - 1 ) * $limit;
			$sql_limit = $wpdb->prepare( 'LIMIT %d, %d', $offset, $limit );
		}

		$query = "SELECT $select FROM `{$this->tb_users}` as u INNER JOIN `{$this->tb_lp_user_items}` as ui ON u.ID = ui.user_id WHERE $where $sql_limit";

		$result = $wpdb->get_results( stripslashes( $query ), ARRAY_A );

		if ( $calc ) {
			$result = $wpdb->get_var( 'SELECT FOUND_ROWS()' );
		}

		return $result;
	}

	/**
	 * query get all students enroll by course ID for export
	 *
	 * @param int $courseID
	 */

	public function lp_gradebook_get_all_students_for_export( int $course_id ) {
		if ( ! $course_id ) {
			return false;
		}

		$query = $this->wpdb->prepare(
			"SELECT ui.user_id, u.user_nicename, u.user_email, ui.start_time, ui.graduation FROM `{$this->tb_lp_user_items }` as ui
			LEFT JOIN `{$this->tb_users }` as u ON u.ID = ui.user_id
			WHERE ui.item_id = '%d'",
			$course_id
		);

		return $this->wpdb->get_results( $query, ARRAY_A );
	}

	/**
	 * query get all students enroll by course ID for export
	 *
	 * @param int $courseID
	 */

	public function get_all_item_students_for_export( int $course_id, int $limit = 5, int $page = 1 ) {
		if ( ! $course_id ) {
			return false;
		}

		$offset    = ( absint( $page ) - 1 ) * $limit;
		$sql_limit = $this->wpdb->prepare( 'LIMIT %d, %d', $offset, $limit );

		$query = $this->wpdb->prepare(
			"SELECT ui.user_id, u.user_nicename, u.user_email, ui.start_time, ui.end_time, ui.graduation FROM `{$this->tb_lp_user_items }` as ui
			LEFT JOIN `{$this->tb_users }` as u ON u.ID = ui.user_id
			WHERE ui.item_id = '%d' {$sql_limit}",
			$course_id
		);

		return $this->wpdb->get_results( $query, ARRAY_A );
	}

	public function get_all_export_count( int $course_id ) {
		static $output;

		if ( ! isset( $output ) ) {
			$query  = $this->wpdb->prepare( "SELECT SQL_CALC_FOUND_ROWS user_item_id FROM {$this->tb_lp_user_items} WHERE item_id = '%d'", $course_id );
			$items  = $this->wpdb->get_results( $query );
			$output = $this->wpdb->get_var( 'SELECT FOUND_ROWS()' );
		}

		return $output;
	}

	public function get_all_status_item( int $course_id, int $user_id ) {
		$query = $this->wpdb->prepare(
			"SELECT status, item_id FROM `{$this->tb_lp_user_items }`
			WHERE ref_id = '%d' AND user_id = '%d'",
			$course_id,
			$user_id
		);

		return $this->wpdb->get_results( $query, ARRAY_A );
	}

	/**
	 * Get data about students to render in chart
	 *
	 * @param int   $courseID
	 * @param null  $from
	 * @param null  $by
	 * @param float $time_ago
	 */
	public function lp_gradebook_get_chart_students( int $course_id, $from = null, $by = null, $time_ago = 0 ) {
		$labels   = array();
		$datasets = array();

		if ( is_null( $from ) ) {
			$from = current_time( 'mysql', true );
		}

		if ( is_null( $by ) ) {
			$by = 'days';
		}

		$data_format = '';

		switch ( $by ) {
			case 'days':
				$date_format = 'M d';
				break;
			case 'months':
				$date_format = 'M Y';
				break;
			case 'years':
				$date_format = 'Y';
				break;
		}

		$results = array(
			'enrolled' => array(),
			'finished' => array(),
		);

		$from_time = is_numeric( $from ) ? $from : strtotime( $from );

		switch ( $by ) {
			case 'days':
				$date_format = 'M d Y';
				$_from       = - $time_ago + 1;
				$_from       = gmdate( 'Y-m-d', strtotime( "{$_from} {$by}", $from_time ) );
				$_to         = gmdate( 'Y-m-d', $from_time );
				$_sql_format = '%Y-%m-%d';
				$_key_format = 'Y-m-d';
				break;

			case 'months':
				$date_format = 'M Y';
				$_from       = - $time_ago + 1;
				$_from       = gmdate( 'Y-m-01', strtotime( "{$_from} {$by}", $from_time ) );
				$days        = gmdate( 't', mktime( 0, 0, 0, gmdate( 'm', $from_time ), 1, gmdate( 'Y', $from_time ) ) );
				$_to         = gmdate( 'Y-m-' . $days, $from_time );
				$_sql_format = '%Y-%m';
				$_key_format = 'Y-m';
				break;

			case 'years':
				$date_format = 'Y';
				$_from       = - $time_ago + 1;
				$_from       = gmdate( 'Y-01-01', strtotime( "{$_from} {$by}", $from_time ) );
				$days        = gmdate( 't', mktime( 0, 0, 0, gmdate( 'm', $from_time ), 1, gmdate( 'Y', $from_time ) ) );
				$_to         = gmdate( 'Y-12-' . $days, $from_time );
				$_sql_format = '%Y';
				$_key_format = 'Y';
				break;
		}

		$result_enroll = $this->wpdb->get_results(
			$this->wpdb->prepare(
				"
				SELECT count(u.user_id) as c, DATE_FORMAT(u.start_time, '%s') as d
				FROM {$this->tb_lp_user_items} u
				WHERE 1 AND u.item_id = '%d' AND u.status = 'enrolled'
				GROUP BY d
				HAVING d BETWEEN '%s' AND '%s'
				ORDER BY d ASC",
				$_sql_format,
				$course_id,
				$_from,
				$_to
			)
		);

		if ( $result_enroll ) {
			foreach ( $result_enroll as $k => $v ) {
				$results['enrolled'][ $v->d ] = $v;
			}
		}

		$result_finished = $this->wpdb->get_results(
			$this->wpdb->prepare(
				"
				SELECT count(u.user_id) as c, DATE_FORMAT(u.start_time, '%s') as d
				FROM {$this->tb_lp_user_items} u
				WHERE 1 AND u.item_id = '%d' AND u.status = 'finished'
				GROUP BY d
				HAVING d BETWEEN '%s' AND '%s'
				ORDER BY d ASC",
				$_sql_format,
				$course_id,
				$_from,
				$_to
			)
		);

		if ( $result_finished ) {
			foreach ( $result_finished as $k => $v ) {
				$results['finished'][ $v->d ] = $v;
			}
		}

		for ( $i = - $time_ago + 1; $i <= 0; $i ++ ) {
			$date     = strtotime( "$i $by", $from_time );
			$labels[] = gmdate( $date_format, $date );
			$key      = gmdate( $_key_format, $date );

			$enrolled = ! empty( $results['enrolled'][ $key ] ) ? $results['enrolled'][ $key ]->c : 0;
			$finished = ! empty( $results['finished'][ $key ] ) ? $results['finished'][ $key ]->c : 0;

			$datasets[0]['data'][] = $enrolled;
			$datasets[1]['data'][] = $finished;
		}

		$dataset_params = array(
			array(
				'color1' => 'rgba(54, 162, 235, %s)',
				'color2' => '#FFF',
				'label'  => esc_html__( 'Enrolled', 'learnpress-gradebook' ),
			),
			array(
				'color1' => 'rgba(255, 205, 86, %s)',
				'color2' => '#FFF',
				'label'  => esc_html__( 'Finished', 'learnpress-gradebook' ),
			),
		);

		foreach ( $dataset_params as $k => $v ) {
			$datasets[ $k ]['backgroundColor'] = sprintf( $v['color1'], '0.2' );
			$datasets[ $k ]['borderColor']     = sprintf( $v['color1'], '1' );
			$datasets[ $k ]['label']           = $v['label'];
		}

		return array(
			'labels'   => $labels,
			'datasets' => $datasets,
		);
	}

	/**
	 * Get data about students to render in chart
	 *
	 * @param int $courseID
	 */

	public function lp_gradebook_get_pie_chart_students( int $course_id ) {
		$labels   = array( esc_html__( 'Enrolled', 'learnpress-gradebook' ), esc_html__( 'Finished', 'learnpress-gradebook' ) );
		$datasets = array();

		$result_enrolled = $this->wpdb->get_var(
			$this->wpdb->prepare(
				"SELECT count(user_id)
				FROM {$this->tb_lp_user_items}
				WHERE item_id=%d AND status=%s",
				$course_id,
				'enrolled'
			)
		);

		$result_finished = $this->wpdb->get_var(
			$this->wpdb->prepare(
				"SELECT count(user_id)
				FROM {$this->tb_lp_user_items}
				WHERE item_id=%d AND status=%s",
				$course_id,
				'finished'
			)
		);

		$datasets['data']            = array( $result_enrolled, $result_finished );
		$datasets['backgroundColor'] = array(
			'rgba(255, 205, 86, 0.2)',
			'rgba(54, 162, 235, 0.2)',
		);
		$datasets['borderColor']     = array(
			'rgba(255, 205, 86, 1)',
			'rgba(54, 162, 235, 1)',
		);

		return array(
			'labels'   => $labels,
			'datasets' => $datasets,
		);
	}

	/**
	 * query get all items by student ID and course ID for export
	 *
	 * @param int $student_id
	 * @param int $course_id
	 */

	public function lp_gradebook_get_all_items_for_export( int $student_id, int $course_id ) {
		if ( ! $student_id || ! $course_id ) {
			return false;
		}

		$query = $this->wpdb->prepare(
			"SELECT DISTINCT post_title, item_type, start_time, end_time, graduation, status FROM `{$this->tb_lp_user_items}` as ui
			INNER JOIN `{$this->tb_posts}` as p ON p.ID = ui.item_id
			WHERE ui.user_id = '%d'  AND ref_id = '%d'
			",
			$student_id,
			$course_id
		);

		return $this->wpdb->get_results( $query, ARRAY_A );
	}

	/**
	 * query get all result question
	 *
	 * @param int $quiz_id
	 * @param int $student_id
	 */

	public function lp_gradebook_get_all_questions_result( int $quiz_id, int $student_id ) {
		if ( ! $quiz_id || ! $student_id ) {
			return false;
		}

		$query = $this->wpdb->prepare(
			"SELECT uir.result FROM `{$this->tb_lp_user_item_results}` as uir
			LEFT JOIN `{$this->tb_lp_user_items}` as ui ON uir.user_item_id=ui.user_item_id
			WHERE ui.item_id='%d'
			AND ui.user_id = '%d'",
			$quiz_id,
			$student_id
		);

		$result = $this->wpdb->get_results( $query );

		return $result;
	}

	/**
	 * query get all result qizz
	 *
	 * @param int $quiz_id
	 * @param int $student_id
	 * @param int $limit
	 */

	public function lp_gradebook_get_qizz_result( int $quiz_id, int $student_id, $limit = 1 ) {
		if ( ! $quiz_id || ! $student_id ) {
			return false;
		}
		$output = array();

		$query = $this->wpdb->prepare(
			"SELECT uir.result, ui.end_time FROM `{$this->tb_lp_user_item_results}` as uir
		    LEFT JOIN `{$this->tb_lp_user_items}` as ui ON uir.user_item_id=ui.user_item_id
			WHERE ui.item_id='%d'
			AND ui.user_id = '%d'
			ORDER BY id DESC LIMIT %d",
			$quiz_id,
			$student_id,
			$limit
		);

		$result = $this->wpdb->get_results( $query );

		if ( ! empty( $result ) ) {
			$output['result']   = $result[0]->result;
			$output['end-time'] = $result[0]->end_time;
		}

		return $output;
	}

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

}

LP_Gradebook_Database::instance();
