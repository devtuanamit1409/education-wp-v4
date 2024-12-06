<?php
/**
 * Use for register REST-API.
 *
 * @package LearnPress/Gradebook
 * @author Nhamdv
 */
class LP_Gradebook_Rest_Controller {
	private static $_instance = null;

	public $namespace = 'lp-gradebook';

	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_endpoints' ) );
	}

	public function register_endpoints() {
		// Run {SITE_URL} /wp-json/lp-gradebook/get-students
		register_rest_route(
			$this->namespace,
			'/get-students',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_students' ),
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/get-chart-student',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_chart_students' ),
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/get-pie-chart-student',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_pie_chart_students' ),
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);
		register_rest_route(
			$this->namespace,
			'/export-students',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'export_list_students' ),
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/export-students-full',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'export_all_list_students' ),
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/get-items',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_items' ),
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/export-items',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'export_list_items' ),
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/get-questions',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'lp_gradebook_get_questions' ),
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);
		register_rest_route(
			$this->namespace,
			'/get-chart-questions',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_chart_questions' ),
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);
		register_rest_route(
			$this->namespace,
			'/export-questions',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'export_list_questions' ),
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);
		register_rest_route(
			$this->namespace,
			'/questions-attempted',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'attempted_questions' ),
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);
	}

	public function permission_callback( $request ) {
		$permission = current_user_can( LP_TEACHER_ROLE ) || current_user_can( 'administrator' );

		return apply_filters( 'learnpress/gradebook/rest-api/permission', $permission );
	}

	/**
	 * get list student enroll by courseID
	 */

	public function get_students( $request ) {
		$response       = new LP_REST_Response();
		$response->data = new stdClass();

		$course_id  = ! empty( $request['courseID'] ) ? $request['courseID'] : '';
		$course     = learn_press_get_course( $course_id );
		$limit      = ! empty( $request['limit'] ) ? $request['limit'] : 10;
		$page       = ! empty( $request['page'] ) ? $request['page'] : 1;
		$graduation = ! empty( $request['graduation'] ) ? $request['graduation'] : '';
		$average    = ! empty( $request['average'] ) ? $request['average'] : '';
		$username   = ! empty( $request['userName'] ) ? $request['userName'] : '';

		try {
			if ( empty( $course_id ) ) {
				throw new Exception( esc_html__( 'No Course ID param.', 'learnpress-gradebook' ) );
			}

			$results = LP_Gradebook_Database::instance()->get_all_students( $course_id, $limit, $page, $graduation, $username, false );

			$output = array();

			if ( ! empty( $results ) ) {
				foreach ( $results as $result ) {
					$user_id = absint( $result['user_id'] );
					$user    = learn_press_get_user( $user_id );
					if ( ! $user ) {
						continue;
					}

					$course_data = $user->get_course_data( absint( $course_id ) );
					if ( ! $course_data ) {
						continue;
					}

					$course_results = $course_data->calculate_course_results();
					$output[]       = array(
						'user_id'       => $user_id,
						'graduation'    => learn_press_get_graduation_text( $result['graduation'] ),
						'start_time'    => ! empty( $result['start_time'] ) ? get_date_from_gmt( gmdate( 'Y-m-d H:i:s', strtotime( $result['start_time'] ) ), 'Y-m-d H:i:s' ) : '--:--',
						'result'        => $course_results['result'],
						'user_email'    => $result['user_email'] ?? '',
						'user_nicename' => $result['user_nicename'] ?? '',
					);
				}
			}

			$response->data->result = $output ?? array();
			$response->data->count  = absint( LP_Gradebook_Database::instance()->get_all_students( $course_id, $limit, $page, $graduation, $username, true ) );
			$response->data->title  = get_the_title( $course_id );
			$response->status       = 'success';
		} catch ( Exception $e ) {
			$response->message = $e->getMessage();
		}

		return rest_ensure_response( $response );
	}

	/**
	 * get list student enroll by courseID for export
	 */

	public function export_list_students( $request ) {
		$response       = new LP_REST_Response();
		$response->data = '';
		$course_id      = $request->get_param( 'courseID' );

		try {
			if ( empty( $course_id ) ) {
				throw new Exception( esc_html__( 'No Course ID param.', 'learnpress-gradebook' ) );
			}

			$result = LP_Gradebook_Database::instance()->lp_gradebook_get_all_students_for_export( $course_id );

			$output = array();
			if ( $result ) {
				foreach ( $result as $value ) {
					$data = array();

					$user_id = absint( $value['user_id'] );
					$user    = learn_press_get_user( $user_id );
					if ( ! $user ) {
						continue;
					}

					$course_data = $user->get_course_data( absint( $course_id ) );
					if ( ! $course_data ) {
						continue;
					}

					$course_results = $course_data->calculate_course_results();

					$value['average'] = ! empty( $course_results['result'] ) ? round( (float) $course_results['result'], 2 ) . '%' : '0%';

					foreach ( $value as $k => $v ) {
						if ( $k === 'start_time' ) {
							$v = ! empty( $v ) ? get_date_from_gmt( gmdate( 'Y-m-d H:i:s', strtotime( $v ) ), 'Y-m-d H:i:s' ) : '--:--';
						} elseif ( $k === 'graduation' ) {
							$v = ! empty( $v ) ? learn_press_get_graduation_text( $v ) : '';
						}

						$object          = new stdClass();
						$object->display = $k;
						$object->value   = $v;
						$data[]          = $object;
					}
					$output[] = $data;
				}
			}

			$response->data   = $output;
			$response->status = 'success';

		} catch ( Exception $e ) {
			$response->message = $e->getMessage();
		}

		return rest_ensure_response( $response );
	}

	/**
	 * Export for all( course, lesson, quiz )
	 *
	 * @param [type] $request
	 * @return void
	 */
	public function export_all_list_students( $request ) {
		$response       = new LP_REST_Response();
		$response->data = '';
		$course_id      = $request->get_param( 'courseID' );
		$limit          = $request->get_param( 'limit' );
		$offset         = $request->get_param( 'offset' );

		try {
			if ( empty( $course_id ) ) {
				throw new Exception( esc_html__( 'No Course ID param.', 'learnpress-gradebook' ) );
			}

			$status_text = array(
				'started'     => esc_html__( 'Started', 'learnpress-gradebook' ),
				'completed'   => esc_html__( 'Completed', 'learnpress-gradebook' ),
				'in-progress' => esc_html__( 'In Progress', 'learnpress-gradebook' ),
				'evaluated'   => esc_html__( 'Evaluated', 'learnpress-gradebook' ),
			);

			$result = LP_Gradebook_Database::instance()->get_all_item_students_for_export( $course_id, $limit, $offset );
			$count  = LP_Gradebook_Database::instance()->get_all_export_count( $course_id );

			$course     = learn_press_get_course( $course_id );
			$curriculum = $course->get_curriculum();

			$output = array();
			$header = array();

			if ( $result ) {
				foreach ( $result as $value ) {
					$data = array();

					$user_id = absint( $value['user_id'] );
					$user    = learn_press_get_user( $user_id );
					if ( ! $user ) {
						continue;
					}

					$course_data = $user->get_course_data( absint( $course_id ) );
					if ( ! $course_data ) {
						continue;
					}

					$course_results   = $course_data->calculate_course_results();
					$value['average'] = ! empty( $course_results['result'] ) ? round( (float) $course_results['result'], 2 ) . '%' : '0%';

					foreach ( $value as $k => $v ) {
						if ( in_array( $k, array( 'start_time', 'end_time' ) ) ) {
							$v = ! empty( $v ) ? get_date_from_gmt( gmdate( 'Y-m-d H:i:s', strtotime( $v ) ), 'Y-m-d H:i:s' ) : '--:--';
						} elseif ( $k === 'graduation' ) {
							$v = ! empty( $v ) ? learn_press_get_graduation_text( $v ) : '';
						}

						$object          = new stdClass();
						$object->display = $k;
						$object->value   = $v;
						$data[]          = $object;
					}

					$items  = LP_Gradebook_Database::instance()->get_all_status_item( $course_id, $user_id );
					$status = ! empty( $items ) ? wp_list_pluck( $items, 'status', 'item_id' ) : array();

					if ( ! empty( $curriculum ) ) {
						foreach ( $curriculum as $section ) {
							$section_items = $section->get_items();

							if ( ! empty( $section_items ) ) {
								foreach ( $section_items as $section_item ) {
									$obj_item          = new stdClass();
									$obj_item->display = $section_item->get_id();
									$obj_item->value   = ! empty( $status[ $section_item->get_id() ] ) ? $status_text[ $status[ $section_item->get_id() ] ] : '';
									$data[]            = $obj_item;
								}
							}
						}
					}

					$output[] = $data;
				}

				if ( ! empty( $curriculum ) ) {
					foreach ( $curriculum as $section ) {
						$section_items = $section->get_items();

						if ( ! empty( $section_items ) ) {
							foreach ( $section_items as $section_item ) {
								$header_obj        = new stdClass();
								$header_obj->key   = $section_item->get_id();
								$header_obj->label = get_the_title( $section_item->get_id() );
								$header[]          = $header_obj;
							}
						}
					}
				}
			}

			$response->header = $header;
			$response->data   = $output;
			$response->count  = absint( $count );
			$response->status = 'success';

		} catch ( Exception $e ) {
			$response->message = $e->getMessage();
		}

		return rest_ensure_response( $response );
	}

	/**
	 *  get list student enroll course and display chart bar
	 */

	public function get_chart_students( $request ) {
		$response       = new LP_REST_Response();
		$response->data = '';
		$course_id      = $request->get_param( 'courseID' );
		$type_chart     = $request->get_param( 'typeChart' ) ?? 'last-7-days';

		try {
			if ( empty( $course_id ) ) {
				throw new Exception( esc_html__( 'No Course ID param.', 'learnpress-gradebook' ) );
			}

			switch ( $type_chart ) {
				case 'last-7-days':
					$data = LP_Gradebook_Database::instance()->lp_gradebook_get_chart_students( $course_id, null, 'days', 7 );
					break;
				case 'last-30-days':
					$data = LP_Gradebook_Database::instance()->lp_gradebook_get_chart_students( $course_id, null, 'days', 30 );
					break;
				case 'last-12-month':
					$data = LP_Gradebook_Database::instance()->lp_gradebook_get_chart_students( $course_id, null, 'months', 12 );
					break;
			}

			$response->data   = $data;
			$response->status = 'success';

		} catch ( Exception $e ) {
			$response->message = $e->getMessage();
		}

		return rest_ensure_response( $response );
	}

	/**
	 *  get list student enroll course and display chart pie
	 */

	public function get_pie_chart_students( $request ) {
		$response       = new LP_REST_Response();
		$response->data = '';
		$course_id      = $request->get_param( 'courseID' );

		try {
			if ( empty( $course_id ) ) {
				throw new Exception( esc_html__( 'No Course ID param.', 'learnpress-gradebook' ) );
			}

			$data             = LP_Gradebook_Database::instance()->lp_gradebook_get_pie_chart_students( $course_id );
			$response->data   = $data;
			$response->status = 'success';

		} catch ( Exception $e ) {
			$response->message = $e->getMessage();
		}

		return rest_ensure_response( $response );
	}

	/**
	 *  get list items courseID by StudentID
	 */

	public function get_items( $request ) {
		$student_id = $request->get_param( 'studentID' );
		$course_id  = $request->get_param( 'courseID' );
		$item_type  = $request->get_param( 'itemType' ) ?? '';
		$status     = $request->get_param( 'status' ) ?? '';
		$post_title = $request->get_param( 'postTitle' ) ?? '';
		$page       = $request->get_param( 'page' ) ?? 1;
		$limit      = $request->get_param( 'limit' ) ?? 10;

		$status_text = array(
			'started'     => esc_html__( 'Started', 'learnpress-gradebook' ),
			'completed'   => esc_html__( 'Completed', 'learnpress-gradebook' ),
			'in-progress' => esc_html__( 'In Progress', 'learnpress-gradebook' ),
			'evaluated'   => esc_html__( 'Evaluated', 'learnpress-gradebook' ),
			'viewed'      => esc_html__( 'Viewed', 'learnpress-gradebook' ),
		);

		$response       = new LP_REST_Response();
		$response->data = new stdClass();

		try {
			if ( empty( $course_id ) || empty( $student_id ) ) {
				throw new Exception( esc_html__( 'No params invalid', 'learnpress-gradebook' ) );
			}

			$posts       = array();
			$total_posts = 0;
			// compare item in course with item in-progess
			$course   = learn_press_get_course( $course_id );
			$item_ids = $course->get_item_ids();

			$course_items = LP_Gradebook_Database::instance()->get_item_id_in_user_items( $course_id, $student_id, $status, $item_type, $item_ids );

			if ( ! empty( $course_items ) ) {
				$course_item_ids = ! empty( $course_items ) ? array_map( 'absint', wp_list_pluck( $course_items, 'item_id' ) ) : array();
				$start_times     = ! empty( $course_items ) ? wp_list_pluck( $course_items, 'start_time', 'item_id' ) : array();
				$end_times       = ! empty( $course_items ) ? wp_list_pluck( $course_items, 'end_time', 'item_id' ) : array();
				$graduations     = ! empty( $course_items ) ? wp_list_pluck( $course_items, 'graduation', 'item_id' ) : array();
				$statuses        = ! empty( $course_items ) ? wp_list_pluck( $course_items, 'status', 'item_id' ) : array();

				$query_args = array(
					'post_type'      => learn_press_get_course_item_types(),
					'posts_per_page' => $limit,
					's'              => $post_title,
					'paged'          => $page,
					'post__in'       => $course_item_ids,
					'orderby'        => 'post__in',
				);

				$query = new WP_Query();

				$result = $query->query( $query_args );

				$total_posts = $query->found_posts;

				if ( $total_posts < 1 ) {
					unset( $query_args['paged'] );
					$count_query = new WP_Query();
					$count_query->query( $query_args );
					$total_posts = $count_query->found_posts;
				}

				foreach ( $result as $post ) {
					$posts[] = array(
						'id'         => $post->ID,
						'title'      => $post->post_title,
						'status'     => array(
							'raw'    => ! empty( $statuses[ $post->ID ] ) ? $statuses[ $post->ID ] : '',
							'render' => ! empty( $statuses[ $post->ID ] ) ? $status_text[ $statuses[ $post->ID ] ] : '',
						),
						'type'       => array(
							'raw'    => $post->post_type,
							'render' => learn_press_course_get_support_item_types()[ $post->post_type ],
						),
						'graduation' => ! empty( $graduations[ $post->ID ] ) ? learn_press_get_graduation_text( $graduations[ $post->ID ] ) : '',
						'start_time' => ! empty( $start_times[ $post->ID ] ) ? get_date_from_gmt( gmdate( 'Y-m-d H:i:s', strtotime( $start_times[ $post->ID ] ) ), 'Y-m-d H:i:s' ) : '--:--',
						'end_time'   => ! empty( $end_times[ $post->ID ] ) && $end_times[ $post->ID ] != '0000-00-00 00:00:00' ? get_date_from_gmt( gmdate( 'Y-m-d H:i:s', strtotime( $end_times[ $post->ID ] ) ), 'Y-m-d H:i:s' ) : '--:--',
					);
				}
			}

			$student      = get_userdata( $student_id );
			$student_name = $student->user_nicename;

			$response->data->studentName = ! empty( $student_name ) ? $student_name : '';
			$response->data->titleCourse = get_the_title( $course_id );
			$response->data->types       = learn_press_course_get_support_item_types();
			$response->data->posts       = $posts;
			$response->data->total_posts = $total_posts;
			$response->status            = 'success';
		} catch ( Exception $e ) {
			$response->message = $e->getMessage();
		}

		return rest_ensure_response( $response );
	}

	/**
	 * get list items CourseID by StudenID for export
	 */

	public function export_list_items( $request ) {
		$response       = new LP_REST_Response();
		$response->data = '';
		$student_id     = $request->get_param( 'studentID' );
		$course_id      = $request->get_param( 'courseID' );

		$status_text = array(
			'started'     => esc_html__( 'Started', 'learnpress-gradebook' ),
			'completed'   => esc_html__( 'Completed', 'learnpress-gradebook' ),
			'in-progress' => esc_html__( 'In Progress', 'learnpress-gradebook' ),
			'evaluated'   => esc_html__( 'Evaluated', 'learnpress-gradebook' ),
		);

		try {
			if ( empty( $course_id ) || empty( $student_id ) ) {
				throw new Exception( esc_html__( 'No params invalid', 'learnpress-gradebook' ) );
			}

			$db = LP_Gradebook_Database::instance()->lp_gradebook_get_all_items_for_export( $student_id, $course_id );

			$output = array();
			if ( $db ) {
				foreach ( $db as $value ) {
					$data = array();

					foreach ( $value as $k => $v ) {
						if ( $k === 'item_type' ) {
							$v = learn_press_course_get_support_item_types()[ $v ];
						} elseif ( $k === 'start_time' || $k === 'end_time' ) {
							$v = ! empty( $v ) ? get_date_from_gmt( gmdate( 'Y-m-d H:i:s', strtotime( $v ) ), 'Y-m-d H:i:s' ) : '--:--';
						} elseif ( $k === 'graduation' ) {
							$v = ! empty( $v ) ? learn_press_get_graduation_text( $v ) : '';
						} elseif ( $k === 'status' ) {
							$v = ! empty( $v ) ? $status_text[ $v ] : '';
						}

						$object          = new stdClass();
						$object->display = $k;
						$object->value   = $v;
						$data[]          = $object;
					}
					$output[] = $data;
				}
			}

			$response->data   = $output;
			$response->status = 'success';

		} catch ( Exception $e ) {
			$response->message = $e->getMessage();
		}

		return rest_ensure_response( $response );
	}

	/**
	 *  get all list question by QuizID and StudentID
	 */

	public function lp_gradebook_get_questions( $request ) {
		$response       = new LP_REST_Response();
		$response->data = new stdClass();

		$quiz_id    = $request->get_param( 'quizzID' );
		$course_id  = $request->get_param( 'courseID' );
		$student_id = $request->get_param( 'studentID' );
		$limit      = $request->get_param( 'limit' );
		$page       = $request->get_param( 'page' );
		$search     = $request->get_param( 'questionTitle' );

		try {
			if ( empty( $course_id ) || empty( $student_id ) || empty( $quiz_id ) ) {
				throw new Exception( esc_html__( 'No params invalid', 'learnpress-gradebook' ) );
			}

			$course = learn_press_get_course( $course_id );
			if ( ! $course ) {
				throw new Exception( esc_html__( 'Course not found', 'learnpress-gradebook' ) );
			}

			$quiz = learn_press_get_quiz( $quiz_id );
			if ( ! $quiz ) {
				throw new Exception( esc_html__( 'Quiz not found', 'learnpress-gradebook' ) );
			}

			$quiz->set_course( $course );
			$quiz_title       = get_the_title( $quiz_id );
			$course_title     = get_the_title( $course_id );
			$list_id_question = $quiz->get_question_ids();
			$b                = array_values( $list_id_question );
			$query            = new WP_Query();

			$query_args = array(
				'post_type'      => LP_QUESTION_CPT,
				'posts_per_page' => $limit,
				's'              => $search,
				'paged'          => $page,
				'post__in'       => $b,
				'orderby'        => 'post__in',
			);

			$result = $query->query( $query_args );

			$total_posts = $query->found_posts;

			if ( $total_posts < 1 ) {
				unset( $query_args['paged'] );
				$count_query = new WP_Query();
				$count_query->query( $query_args );
				$total_posts = $count_query->found_posts;
			}

			$user_item_id = 0;
			$user         = learn_press_get_user( $student_id );
			if ( ! $user ) {
				throw new Exception( esc_html__( 'No user found', 'learnpress-gradebook' ) );
			}

			$course_data = $user->get_course_data( $course_id );
			if ( $course_data ) {
				$item = $course_data->get_item( $quiz_id );
				if ( $item ) {
					$user_item_id = $item->get_user_item_id();
				}
			}

			$get_results = LP_User_Items_Result_DB::instance()->get_results( absint( $user_item_id ), 6, false );

			$data = array();

			foreach ( $result as $post ) {

				$question_id                          = $post->ID;
				$data[ $question_id ]['title']        = $post->post_title;
				$data[ $question_id ]['type']         = learn_press_question_name_from_slug( get_post_meta( $question_id, '_lp_type', true ) );
				$data[ $question_id ]['retake_count'] = ! empty( $get_results ) ? ( count( $get_results ) - 1 ) : 0;

				foreach ( $get_results as $key => $get_result ) {
					$get_result = json_decode( $get_result, true );

					if ( $key === 0 ) {
						$data[ $question_id ]['correct'] = empty( $get_result['questions'][ $question_id ]['correct'] ) ? esc_html__( 'False', 'learnpress-gradebook' ) : esc_html__( 'True', 'learnpress-gradebook' );
						continue;
					}

					$data[ $question_id ]['retake'][] = empty( $get_result['questions'][ $question_id ]['correct'] ) ? esc_html__( 'False', 'learnpress-gradebook' ) : esc_html__( 'True', 'learnpress-gradebook' );
				}
			}

			$student      = get_userdata( $student_id );
			$student_name = $student->user_nicename;

			$response->data->item        = $data;
			$response->data->count       = $total_posts;
			$response->data->studentName = $student_name;
			$response->data->courseTitle = $course_title;
			$response->data->quizTitle   = $quiz_title;
			$response->status            = 'success';

		} catch ( Exception $e ) {
			$response->message = $e->getMessage();
		}

		return rest_ensure_response( $response );
	}

	/**
	 * get attempted_questions by quiz id and student id
	 */
	public function attempted_questions( $request ) {

		$response       = new LP_REST_Response();
		$response->data = new stdClass();

		$quiz_id    = $request->get_param( 'quizzID' );
		$student_id = $request->get_param( 'studentID' );

		try {

			if ( empty( $student_id ) || empty( $quiz_id ) ) {
				throw new Exception( esc_html__( 'No params invalid', 'learnpress-gradebook' ) );
			}

			$result                 = LP_Gradebook_Database::instance()->lp_gradebook_get_qizz_result( $quiz_id, $student_id );
			$response->data->result = $result;
			$response->status       = 'success';

		} catch ( Exception $e ) {
			$response->message = $e->getMessage();
		}

		return rest_ensure_response( $response );
	}

	/**
	 * get all list question by QuizID and StudentID for chart
	 */
	public function get_chart_questions( WP_REST_Request $request ) {
		$response         = new LP_REST_Response();
		$response->data   = '';
		$response->status = 'error';

		try {
			$quiz_id    = $request->get_param( 'quizzID' ) ?? 0;
			$student_id = $request->get_param( 'studentID' ) ?? 0;
			$course_id  = $request->get_param( 'courseID' ) ?? 0;
			$student    = learn_press_get_user( $student_id );

			if ( ! $student ) {
				throw new Exception( 'User invalid!' );
			}

			$course_data = $student->get_course_data( $course_id );
			if ( ! $course_data ) {
				throw new Exception( 'Course data is invalid!' );
			}

			$quiz = $course_data->get_item( $quiz_id );

			if ( empty( $student_id ) || empty( $quiz_id ) ) {
				throw new Exception( esc_html__( 'No params invalid', 'learnpress-gradebook' ) );
			}
			if ( ! $quiz->is_completed() ) {
				throw new Exception( esc_html__( 'Quiz is in-progress', 'learnpress-gradebook' ) );
			}

			$query  = LP_Gradebook_Database::instance()->lp_gradebook_get_qizz_result( $quiz_id, $student_id );
			$result = ! empty( $query['result'] ) ? json_decode( $query['result'] ) : '';
			$labels = array( esc_html__( 'True', 'learnpress-gradebook' ), esc_html__( 'False', 'learnpress-gradebook' ) );

			$count_answer_true  = ! empty( $result->question_correct ) ? (int) $result->question_correct : 0;
			$count_answer_false = ! empty( $result->question_count ) ? (int) ( $result->question_count - $count_answer_true ) : 0;

			$datasets['data']            = array( $count_answer_true, $count_answer_false );
			$datasets['backgroundColor'] = array(
				'rgba(255, 205, 86, 0.2)',
				'rgba(54, 162, 235, 0.2)',
			);
			$datasets['borderColor']     = array(
				'rgba(255, 205, 86, 1)',
				'rgba(54, 162, 235, 1)',
			);

			$data = array(
				'labels'   => $labels,
				'datasets' => $datasets,
			);

			$response->data   = $data;
			$response->status = 'success';

		} catch ( Exception $e ) {
			$response->message = $e->getMessage();
		}

		return rest_ensure_response( $response );
	}

	/**
	 *  export list question by QuizID and StudentID
	 */

	public function export_list_questions( $request ) {
		$course_id  = $request->get_param( 'courseID' );
		$quiz_id    = $request->get_param( 'quizzID' );
		$student_id = $request->get_param( 'studentID' );

		$response       = new LP_REST_Response();
		$response->data = '';

		try {
			if ( empty( $course_id ) || empty( $student_id ) || empty( $quiz_id ) ) {
				throw new Exception( esc_html__( 'No params invalid', 'learnpress-gradebook' ) );
			}

			$quiz             = learn_press_get_quiz( $quiz_id );
			$list_id_question = $quiz->get_questions();

			$retake = LP_Gradebook_Database::instance()->lp_gradebook_get_all_questions_result( $quiz_id, $student_id );

			$user_item_id = 0;
			$user         = learn_press_get_user( $student_id );
			if ( ! $user ) {
				throw new Exception( 'User is invalid!' );
			}

			$course_data = $user->get_course_data( $course_id );
			if ( ! $course_data ) {
				throw new Exception( 'Course data of user invalid!' );
			}

			$item = $course_data->get_item( $quiz_id );
			if ( ! $item ) {
				throw new Exception( 'Item is invalid!' );
			}

			$user_item_id = $item->get_user_item_id();

			$get_results = LP_User_Items_Result_DB::instance()->get_results( absint( $user_item_id ), 6, false );

			$data = array();

			foreach ( $list_id_question as $question_id ) {
				$data[ $question_id ]['title']        = get_the_title( $question_id );
				$data[ $question_id ]['type']         = get_post_meta( $question_id, '_lp_type', true );
				$data[ $question_id ]['retake_count'] = count( $get_results ) - 1;

				foreach ( $get_results as $key => $get_result ) {
					$get_result = json_decode( $get_result, true );

					if ( $key === 0 ) {
						$data[ $question_id ]['correct'] = empty( $get_result['questions'][ $question_id ]['correct'] ) ? esc_html__( 'False', 'learnpress-gradebook' ) : esc_html__( 'True', 'learnpress-gradebook' );
						continue;
					}

					$data[ $question_id ]['retake'][] = empty( $get_result['questions'][ $question_id ]['correct'] ) ? esc_html__( 'False', 'learnpress-gradebook' ) : esc_html__( 'True', 'learnpress-gradebook' );
				}
			}

			if ( $data ) {
				foreach ( $data as $value ) {
					$array    = json_decode( json_encode( $value ), true );
					$data_exp = array();

					foreach ( $array as $k => $v ) {
						$object          = new stdClass();
						$object->display = $k;
						$object->value   = $v;
						$data_exp[]      = $object;

					}
					$data['export'][] = $data_exp;
				}
			}

			$response->data   = $data;
			$response->status = 'success';

		} catch ( Exception $e ) {
			$response->message = $e->getMessage();
		}

		return rest_ensure_response( $response );
	}

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}
}

LP_Gradebook_Rest_Controller::instance();
