<?php

/**
 * Update compatible with LP4
 *
 * @package LearnPress/H5P.
 * @author Nhamdv <Code is poetry>
 */

if ( ! function_exists( 'lp_h5p_re_sort_video_actions' ) ) {
	function lp_h5p_re_sort_video_actions( $actions ) {
		$after_sort = array();

		if ( count( $actions ) < 1 ) {
			return $after_sort;
		}

		foreach ( $actions as $action ) {
			$duration_from                           = $action->duration->from;
			$sub_action                              = $action->action;
			$library                                 = strtok( $sub_action->library, ' ' );
			$after_sort[ $duration_from ]['library'] = $library;
			$html                                    = '';

			if ( $library != '' && in_array( $library, lp_h5p_can_summary_types_list() ) ) {
				$library_file_name = strtolower( str_replace( 'H5P.', '', $library ) ) . '.php';
				$content['params'] = $sub_action->params;

				ob_start();

				learn_press_h5p_get_template(
					'content-h5p/summary/' . $library_file_name,
					array(
						'h5p_content'        => $content,
						'not_show_head_text' => 1,
					)
				);

				$html = ob_get_clean();
			}

			$after_sort[ $duration_from ]['html'] = $html;
		}

		ksort( $after_sort );

		return $after_sort;
	}
}

if ( ! function_exists( 'learn_press_h5p_start' ) ) {
	/**
	 * @param LP_User $user
	 * @param $h5p_id
	 * @param $course_id
	 * @param $action
	 * @param $meta_data
	 * @param $wp_error
	 *
	 * @return int|mixed|string|void|WP_Error
	 */
	function learn_press_h5p_start( $user, $h5p_id, $course_id, $action = 'start', $meta_data = array(), $wp_error = false ) {
		try {
			$course = learn_press_get_course( $course_id );
			// Validate course and quiz
			if ( false === ( $course->has_item( $h5p_id ) ) ) {
				throw new Exception( esc_html__( 'Course does not exist or does not contain this h5p', 'learnpress-h5p' ), LP_INVALID_H5P_OR_COURSE );
			}

			// If user has already finished the course
			if ( ! $user->has_enrolled_course( $course_id ) ) {
				throw new Exception( esc_html__( 'User has not already enrolled the course of this H5P item', 'learnpress-h5p' ) );
			}

			// If user has already finished the course
			if ( $user->has_finished_course( $course_id ) ) {
				throw new Exception( esc_html__( 'User has already finished the course of this H5P item', 'learnpress-h5p' ), LP_COURSE_IS_FINISHED );
			}

			if ( $action == 'start' ) {
				// Check if user has already completed item
				if ( $user->has_item_status( array( 'completed' ), $h5p_id, $course_id ) ) {
					throw new Exception( esc_html__( 'User has completed this H5P item', 'learnpress-h5p' ), LP_H5P_HAS_STARTED_OR_COMPLETED );
				}
			}

			if ( $course->is_required_enroll() && $user->is( 'guest' ) ) {
				throw new Exception( esc_html__( 'You have to login for starting h5p item.', 'learnpress-h5p' ), LP_REQUIRE_LOGIN );
			}

			$user_item_id = 0;
			$course_data  = $user->get_course_data( $course->get_id() );
			if ( $course_data ) {
				$user_item = $course_data->get_item( $h5p_id );
				if ( $user_item ) {
					$user_item_id = $user_item->get_user_item_id();
				}
			}

			$action = $user_item_id ? 'h5p_doing' : 'started';

			if ( $meta_data['score'] == $meta_data['max_score'] ) {
				$action = 'completed';
			}

			$return = learn_press_update_h5p_item( $h5p_id, $course_id, $user, $action, $user_item_id );

			if ( ! $return ) {
				do_action( 'learn-press/user/doing-h5p-failed', $h5p_id, $course_id, $user->get_id() );
				throw new Exception( __( 'Doing H5p failed!', 'learnpress-h5p' ), 99 );
			}

			learn_press_update_user_item_meta( $return, 'score', $meta_data['score'] );
			learn_press_update_user_item_meta( $return, 'max_score', $meta_data['max_score'] );

			if ( $action == 'completed' ) {
				$h5p               = LP_H5p::get_h5p( $h5p_id );
				$conditional_grade = $h5p->get_data( 'passing_grade' );
				$grade             = ( $meta_data['score'] / $meta_data['max_score'] ) * 100 >= $conditional_grade ? 'passed' : 'failed';

				learn_press_update_user_item_field(
					array( 'graduation' => $grade ),
					array( 'user_item_id' => $return )
				);
			}
		} catch ( Exception $ex ) {
			$return = $wp_error ? new WP_Error( $ex->getCode(), $ex->getMessage() ) : false;
		}

		return $return;
	}
}

if ( ! function_exists( 'learn_press_update_h5p_item' ) ) {
	function learn_press_update_h5p_item( $h5p_id, $course_id, $user, $status, $user_itemid = '' ) {
		global $wpdb;

		$course_data = $user->get_course_data( $course_id );
		if ( ! $course_data ) {
			return false;
		}

		$user_id = $user->get_id();

		$item_data = array(
			'user_id'      => $user_id,
			'item_id'      => $h5p_id,
			'user_item_id' => $user_itemid,
			'end_time'     => '0000-00-00 00:00:00',
			'item_type'    => LP_H5P_CPT,
			'status'       => $status,
			'ref_id'       => $course_id,
			'ref_type'     => LP_COURSE_CPT,
			'parent_id'    => $course_data->get_user_item_id(),
		);

		if ( $status == 'started' || ! $user_itemid ) {
			$start_time              = new LP_Datetime( current_time( 'mysql' ) );
			$item_data['start_time'] = $start_time->toSql();
		} elseif ( $status == 'completed' ) {
			$end_time              = new LP_Datetime( current_time( 'mysql' ) );
			$item_data['end_time'] = $end_time->toSql();
		}

		// if ( $status != 'started' ) {

		$query = $wpdb->prepare(
			"
            SELECT ui.*
            FROM {$wpdb->learnpress_user_items} ui
            WHERE item_type = %s
                AND user_id = %d
                AND item_id = %d
            ORDER BY user_item_id DESC
            LIMIT 0, 1
        ",
			LP_H5P_CPT,
			$user->get_id(),
			$h5p_id
		);

		$item = $wpdb->get_row( $query, ARRAY_A );
		if ( $item ) {
			/*** TEST CACHE */
			// $this->_read_course_items( $result, $force );
		} else {
			$item = LP_User_Item::get_empty_item();
		}

		// Table fields.
		$table_fields = array(
			'user_id'    => '%d',
			'item_id'    => '%d',
			'ref_id'     => '%d',
			'start_time' => '%s',
			'end_time'   => '%s',
			'item_type'  => '%s',
			'status'     => '%s',
			'ref_type'   => '%s',
			'parent_id'  => '%d',
		);

		// Data and format
		$data        = array();
		$data_format = array();

		// Update it later...
		$new_status = false;
		if ( array_key_exists( 'status', $item_data ) && $item_data['status'] != $item['status'] ) {
			$new_status = $item_data['status'];
			// unset( $item_data['status'] );
		}

		// Build data and data format
		foreach ( $item_data as $field => $value ) {
			if ( ! empty( $table_fields[ $field ] ) ) {
				$data[ $field ]        = $value;
				$data_format[ $field ] = $table_fields[ $field ];
			}
		}

		$data['user_id'] = $user_id;
		$data['item_id'] = $h5p_id;

		$data['item_type'] = LP_H5P_CPT;

		foreach ( $data as $k => $v ) {
			$data_format[ $k ] = $table_fields[ $k ];
		}

		$data_format = array_values( $data_format );

		if ( ! $item || ! $user_itemid ) {
			$wpdb->insert(
				$wpdb->learnpress_user_items,
				$data,
				$data_format
			);
			$user_itemid = $wpdb->insert_id;
			$item        = learn_press_get_user_item( array( 'user_item_id' => $user_itemid ) );
		} else {
			$wpdb->update(
				$wpdb->learnpress_user_items,
				$data,
				array( 'user_item_id' => $user_itemid ),
				$data_format,
				array( '%d' )
			);
		}

		if ( $user_itemid ) {
			if ( is_object( $item ) ) {
				$item = (array) $item;
			}
			// Track last status if it is updated new status.
			if ( $new_status !== false ) {
				learn_press_update_user_item_meta( $user_itemid, '_last_status', $item['status'] );
				learn_press_update_user_item_meta( $user_itemid, '_current_status', $new_status );
			}
		}

		return $user_itemid;
	}
}

if ( ! function_exists( 'learn_press_h5p_single_args' ) ) {
	function learn_press_h5p_single_args() {
		$args   = array();
		$course = learn_press_get_course();
		if ( empty( $course ) ) {
			return $args;
		}
		$current_h5p = LP_Global::course_item();

		if ( $current_h5p ) {
			$user = learn_press_get_current_user();
			$args = array(
				'id'              => $current_h5p->get_id(),
				'course_id'       => $course->get_id(),
				'status'          => $user->get_item_status( $current_h5p->get_id(), $course->get_id() ),
				'ajax_url'        => admin_url( 'admin-ajax.php' ),
				'conditional_h5p' => get_post_meta( $current_h5p->get_id(), '_lp_h5p_interact', true ),
			);
		}

		return $args;
	}
}

if ( ! function_exists( 'lp_h5p_default_types_list' ) ) {
	function lp_h5p_default_types_list() {
		return apply_filters(
			'learnpress/h5p/default_types_list',
			array(
				'H5P.ArithmeticQuiz',
				'H5P.Dictation',
				'H5P.DragNDrop',
				'H5P.DragText',
				'H5P.Blanks',
				'H5P.ImageMultipleHotspotQuestion',
				'H5P.ImageHotspotQuestion',
				'H5P.FindTheWords',
				'H5P.Flashcards',
				'H5P.ImagePair',
				'H5P.ImageSequencing',
				'H5P.MarkTheWords',
				'H5P.QuestionSet',
				'H5P.MultiChoice',
				'H5P.SingleChoiceSet',
				'H5P.SpeakTheWords',
				'H5P.SpeakTheWordsSet',
				'H5P.Summary',
				'H5P.InteractiveVideo',
				'H5P.CoursePresentation',
				'H5P.AdvancedBlanks',
				'H5P.MemoryGame',
				'H5P.Column',
				'H5P.TrueFalse',
			)
		);
	}
}

if ( ! function_exists( 'lp_h5p_can_summary_types_list' ) ) {

	function lp_h5p_can_summary_types_list() {
		return apply_filters(
			'learnpress/h5p/can_summary_types_list',
			array(
				// 'H5P.ArithmeticQuiz',
				// 'H5P.Dictation',
				// 'H5P.DragNDrop',
				'H5P.DragText',
				// 'H5P.Blanks',
				// 'H5P.ImageMultipleHotspotQuestion',
				// 'H5P.ImageHotspotQuestion',
				// 'H5P.FindTheWords',
				// 'H5P.Flashcards',
				// 'H5P.ImagePair',
				// 'H5P.ImageSequencing',
				'H5P.MarkTheWords',
				// 'H5P.QuestionSet',
				'H5P.MultiChoice',
				'H5P.SingleChoiceSet',
				// 'H5P.SpeakTheWords',
				// 'H5P.SpeakTheWordsSet',
				// 'H5P.Summary',
				'H5P.InteractiveVideo',
				// 'H5P.CoursePresentation',
				'H5P.AdvancedBlanks',
				// 'H5P.MemoryGame',
				// 'H5P.Column',
				// 'H5P.TrueFalse',
			)
		);
	}
}

if ( ! function_exists( 'lp_h5p_get_content_title' ) ) {
	function lp_h5p_get_content_title( $content_id ) {
		global $wpdb;

		$title = $wpdb->get_var(
			$wpdb->prepare(
				"
        	SELECT title
         	FROM {$wpdb->prefix}h5p_contents
        	WHERE id = %d
        ",
				$content_id
			)
		);

		return $title;
	}
}

if ( ! function_exists( 'lp_h5p_count_h5p_items' ) ) {
	function lp_h5p_count_h5p_items() {
		global $wpdb;

		$count = $wpdb->get_var(
			$wpdb->prepare(
				"
        	SELECT COUNT(*)
         	FROM {$wpdb->prefix}h5p_contents
         	WHERE %d
        ",
				1
			)
		);

		return $count;
	}
}

if ( ! function_exists( 'learn_press_h5p_locate_template' ) ) {
	function learn_press_h5p_locate_template( $template_name, $template_path = '', $default_path = '' ) {
		if ( ! $template_path ) {
			$template_path = learn_press_template_path();
		}

		if ( ! $default_path ) {
			$default_path = LP_ADDON_H5P_PATH . '/templates/';
		}

		// Look within passed path within the theme - this is priority
		$template = locate_template(
			array(
				trailingslashit( $template_path ) . $template_name,
				$template_name,
			)
		);

		// Get default template
		if ( ! $template ) {
			$template = trailingslashit( $default_path ) . $template_name;
		}

		// Return what we found
		return apply_filters( 'learn-press/h5p/locate-template', $template, $template_name, $template_path );
	}
}

if ( ! function_exists( 'learn_press_can_view_h5p' ) ) {
	function learn_press_can_view_h5p( $h5p_id, $course_id, $user_id ) {
		$course = false;
		$view   = false;
		$user   = learn_press_get_user( $user_id );

		// Disable preview course when course status is pending
		if ( get_post_status( $course_id ) == 'pending' ) {
			$view = false;
		} else {
			if ( $course_id ) {
				$course = learn_press_get_course( $course_id );
			}

			if ( $course ) {
				if ( $user->has_enrolled_course( $course_id ) || $user->has_finished_course( $course_id ) ) {
					$view = 'enrolled';
				} elseif ( $user->is_admin() || ( $user->is_instructor() && $course->get_instructor( 'id' ) == $user->get_id() ) ) {
					$view = 'preview';
				} elseif ( ! $course->is_required_enroll() ) {
					$view = 'no-required-enroll';
				}
			}
		}

		return apply_filters( 'learn-press/can-view-h5p', $view, $h5p_id, $user->get_id(), $course_id );
	}
}

if ( ! function_exists( 'learn_press_h5p_get_template' ) ) {
	/**
	 * @param $template_name
	 * @param array $args
	 * @param string $template_path
	 * @param string $default_path
	 */
	function learn_press_h5p_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
		learn_press_get_template( $template_name, $args, learn_press_template_path() . '/addons/h5p/', LP_ADDON_H5P_PATH . '/templates/' );
	}
}

if ( ! function_exists( 'learn_press_h5p_locate_template' ) ) {
	/**
	 * @param $template_name
	 * @param string $template_path
	 * @param string $default_path
	 *
	 * @return mixed
	 */
	function learn_press_h5p_locate_template( $template_name, $template_path = '', $default_path = '' ) {
		if ( ! $template_path ) {
			$template_path = learn_press_template_path();
		}

		if ( ! $default_path ) {
			$default_path = LP_ADDON_H5P_PATH . '/templates/';
		}

		// Look within passed path within the theme - this is priority
		$template = locate_template(
			array(
				trailingslashit( $template_path ) . $template_name,
				$template_name,
			)
		);

		// Get default template
		if ( ! $template ) {
			$template = trailingslashit( $default_path ) . $template_name;
		}

		// Return what we found
		return apply_filters( 'learn-press/h5p/locate-template', $template, $template_name, $template_path );
	}
}

if ( ! function_exists( 'learn_press_h5p_get_template_part' ) ) {
	function learn_press_h5p_get_template_part( $slug, $name = '' ) {
		$template = '';

		if ( $name ) {
			$template = locate_template(
				array(
					"{$slug}-{$name}.php",
					learn_press_h5p_template_path() . "/{$slug}-{$name}.php",
				)
			);
		}

		// Get default slug-name.php
		if ( ! $template && $name && file_exists( LP_ADDON_H5P_PATH . "/templates/{$slug}-{$name}.php" ) ) {
			$template = LP_ADDON_H5P_PATH . "/templates/{$slug}-{$name}.php";
		}

		// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/learnpress/slug.php
		if ( ! $template ) {
			$template = locate_template(
				array(
					"{$slug}.php",
					learn_press_h5p_template_path() . "/{$slug}.php",
				)
			);
		}

		// Allow 3rd party plugin filter template file from their plugin
		if ( $template ) {
			$template = apply_filters( 'learn_press_h5p_get_template_part', $template, $slug, $name );
		}

		return $template;
	}
}

if ( ! function_exists( 'learn_press_h5p_template_path' ) ) {
	function learn_press_h5p_template_path() {
		return 'learnpress/addons/h5p';
	}
}

if ( ! function_exists( 'lp_h5p_action' ) ) {
	function lp_h5p_action( $action, $h5p_id, $course_id, $ajax = false ) {
		?>
		<input type="hidden" name="h5p-id" value="<?php echo esc_attr( $h5p_id ); ?>">
		<input type="hidden" name="course-id" value="<?php echo esc_attr( $course_id ); ?>">
		<?php if ( $ajax ) : ?>
			<input type="hidden" name="lp-ajax" value="<?php echo $action; ?>-h5p">
		<?php else : ?>
			<input type="hidden" name="lp-<?php echo $action; ?>-h5p" value="<?php echo esc_attr( $h5p_id ); ?>">
		<?php endif; ?>
		<input type="hidden" name="<?php echo $action; ?>-h5p-nonce" value="<?php echo wp_create_nonce( sprintf( 'learn-press/h5p/%s/%s-%s-%s', $action, get_current_user_id(), $course_id, $h5p_id ) ); ?>">
		<?php
	}
}

if ( ! function_exists( 'learn_press_h5p_get_result' ) ) {
	function learn_press_h5p_get_result( $item_id, $user_id, $course_id ) {
		$h5p    = new LP_H5p( $item_id );
		$user   = learn_press_get_user( $user_id );
		$status = $user->get_item_status( $item_id, $course_id );
		$result = array(
			'mark'      => 1,
			'user_mark' => 1,
			'status'    => $status,
			'grade'     => '',
			'result'    => 0,
		);

		if ( ! $item_id ) {
			return $result;
		}

		$course_data = $user->get_course_data( $course_id );
		if ( ! $course_data ) {
			return $result;
		}

		$course_data_item = $course_data->get_item( $item_id );
		if ( ! $course_data_item ) {
			return $result;
		}

		$user_item_id        = $course_data_item->get_user_item_id();
		$result['user_mark'] = ( learn_press_get_user_item_meta( $user_item_id, 'score', true ) ) ? learn_press_get_user_item_meta( $user_item_id, 'score', true ) : 0;
		$result['mark']      = ( learn_press_get_user_item_meta( $user_item_id, 'max_score', true ) ) ? learn_press_get_user_item_meta( $user_item_id, 'max_score', true ) : 0;
		$percent             = $result['mark'] ? ( $result['user_mark'] / $result['mark'] ) * 100 : 0;
		$passing_condition   = $h5p->get_data( 'passing_grade' );
		$result['result']    = $percent;
		$result['grade']     = $status === 'completed' ? ( $percent >= $passing_condition ? 'passed' : 'failed' ) : '';

		learn_press_update_user_item_field(
			array( 'graduation' => $result['grade'] ),
			array( 'user_item_id' => $user_item_id )
		);

		return $result;
	}
}

if ( ! function_exists( '_evaluate_course_by_passed_h5p_quizzes_items' ) ) {
	function _evaluate_course_by_passed_h5p_quizzes_items( $user_course ) {
		$cache_key = 'user-course-' . $user_course->get_user_id() . '-' . $user_course->get_id();

		$data = array(
			'result' => 0,
			'grade'  => LP_COURSE_GRADUATION_FAILED,
			'status' => $user_course->get_status(),
			'pass'   => 0,
		);

		$result          = 0;
		$result_of_items = 0;

		$cached_data = LP_Object_Cache::get( $cache_key, 'learn-press/course-results' );

		if ( false === $cached_data || ! array_key_exists( 'passed_h5p_quizzes', $cached_data ) ) {
			/**
			 * @var LP_User_Item_Course $user_course
			 */
			$items = $user_course->get_items();

			if ( $items ) {
				foreach ( $items as $item ) {
					if ( $item->get_type() !== LP_H5P_CPT && $item->get_type() !== LP_QUIZ_CPT ) {
						continue;
					}

					if ( $item->get_type() === LP_H5P_CPT ) {
						$h5p = learn_press_h5p_get_result( $item->get_item_id(), $user_course->get_user_id(), $user_course->get_id() );

						$result += $h5p['grade'] === 'passed' ? $item->get_result( 'result' ) : 0;
						++$result_of_items;
					}

					if ( $item->get_type() === LP_QUIZ_CPT ) {
						if ( $item->get_quiz()->get_data( 'passing_grade' ) ) {
							$result += $item->is_passed() ? $item->get_results( 'result' ) : 0;
							++$result_of_items;
						}
					}
				}

				$result         = $result_of_items ? $result / $result_of_items : 0;
				$data['result'] = $result;

				$passing_condition = $user_course->get_passing_condition();
				if ( $result >= $passing_condition ) {
					$data['pass']  = 1;
					$data['grade'] = LP_COURSE_GRADUATION_PASSED;
				}
			}

			settype( $cached_data, 'array' );
			$cached_data['passed_h5p_quizzes'] = $data;
			LP_Object_Cache::set( $cache_key, $cached_data, 'learn-press/course-results' );
		}

		return isset( $cached_data['passed_h5p_quizzes'] ) ? $cached_data['passed_h5p_quizzes'] : array();
	}
}

if ( ! function_exists( '_evaluate_course_by_passed_h5p_items' ) ) {
	function _evaluate_course_by_passed_h5p_items( $user_course ) {
		$data = array(
			'result' => 0,
			'grade'  => LP_COURSE_GRADUATION_FAILED,
			'status' => $user_course->get_status(),
			'pass'   => 0,
		);

		$result          = 0;
		$result_of_items = 0;

		/**
		 * @var LP_User_Item_Course $user_course
		 */
		$items = $user_course->get_items();
		if ( $items ) {
			foreach ( $items as $item ) {
				if ( $item->get_type() !== LP_H5P_CPT ) {
					continue;
				}

				$h5p = learn_press_h5p_get_result( $item->get_item_id(), $user_course->get_user_id(), $user_course->get_id() );

				$result += $h5p['grade'] === 'passed' ? $item->get_result( 'result' ) : 0;
				++$result_of_items;
			}

			$result         = $result_of_items ? $result / $result_of_items : 0;
			$data['result'] = $result;

			$passing_condition = $user_course->get_passing_condition();
			if ( $result >= $passing_condition ) {
				$data['pass']  = 1;
				$data['grade'] = LP_COURSE_GRADUATION_PASSED;
			}
		}

		return $data;
	}
}

if ( ! function_exists( '_evaluate_course_by_h5p_items' ) ) {
	function _evaluate_course_by_h5p_items( $user_course ) {
			$completing = $user_course->get_completed_items( LP_H5P_CPT, true );

		if ( $completing[1] ) {
			$result = $completing[0] / $completing[1];
		} else {
			$result = 0;
		}

			$result *= 100;
			$data    = array(
				'result' => $result,
				'pass'   => 0,
				'grade'  => LP_COURSE_GRADUATION_FAILED,
				'status' => $user_course->get_status(),
			);

			$passing_condition = $user_course->get_passing_condition();
			if ( $result >= $passing_condition ) {
				$data['pass']  = 1;
				$data['grade'] = LP_COURSE_GRADUATION_PASSED;
			}

			return $data;
	}
}

if ( ! function_exists( 'learn_press_get_h5p' ) ) {
	/**
	 * @param $assignment
	 *
	 * @return bool|LP_Assignment
	 */
	function learn_press_get_h5p( $h5p ) {
		return LP_H5p::get_h5p( $h5p );
	}
}

if ( ! function_exists( 'lp_h5p_check_interacted' ) ) {
	function lp_h5p_check_interacted( $h5pitem_id ) {
		return get_post_meta( $h5pitem_id, '_lp_h5p_interact', true );
	}
}

function learn_press_h5p_item_slugs( $slugs ) {
	$slugs[ LP_H5P_CPT ] = 'h5p';

	return $slugs;
}

add_filter( 'learn-press/course/custom-item-slugs', 'learn_press_h5p_item_slugs' );

add_filter( 'menu_order', 'lp_h5p_submenu_order' );
function lp_h5p_submenu_order( $menu_order ) {
	global $submenu;

	try {
		$lp_menu           = $submenu['learn_press'];
		$temp_arr          = array();
		$index_of_menu_h5p = array_search( 'edit.php?post_type=' . LP_H5P_CPT, array_column( $lp_menu, 2 ) );

		foreach ( $lp_menu as $key => $details ) {
			$link = $details[2];
			if ( $key === $index_of_menu_h5p ) {
				continue;
			}
			$temp_arr[] = $details;
			if ( $link == 'edit.php?post_type=' . LP_QUIZ_CPT ) {
				$temp_arr[] = $lp_menu[ $index_of_menu_h5p ];
			}
		}

		$submenu['learn_press'] = $temp_arr;
	} catch ( Throwable $e ) {
	}

	return $menu_order;
}

add_action( 'learn_press_after_display_item_actions', 'lp_h5p_show_interacted_item' );

function lp_h5p_show_interacted_item() {
	?>
	<div class="action connect-h5p lp-title-attr-tip" data-content-tip="<?php esc_attr_e( 'Connect to H5P content', 'learnpress-h5p' ); ?>" v-if="item.type == 'lp_h5p'">
		<select @click="ClickSelect2" :data-itemid="item.id" class="lp_h5p_select_interact" :name="item.element_name" :data-selected="item.interacted_h5p" v-html="item.interacted_html">
			<option v-if="!item.interacted_html"><?php _e( 'Select H5P Content!', 'learnpress-h5p' ); ?></option>
		</select>
	</div>
	<?php
}

add_action( 'learn_press_after_section_item_script', 'lp_h5p_add_more_vue_method' );

function lp_h5p_add_more_vue_method() {
	?>
,
		ClickSelect2: function(event){
			$('.lp_h5p_select_interact').select2({
				placeholder: 'Select a H5P item',
				minimumInputLength: 0,
				ajax: {
					url: ajaxurl,
					dataType: 'json',
					allowClear: true,
					delay: 250,
					data: function (params) {
						return {
							q: params.term, // search term
							action: 'learnpress_search_h5p'
						};
					},
					processResults: function( data ) {
						var options = [];
						if ( data ) {

							// data is the array of arrays, and each of them contains ID and the Label of the option
							$.each( data, function( index, text ) { // do not forget that "index" is just auto incremented value
								options.push( { id: text[0], text: text[1]  } );
							});

						}
						return {
							results: options
						};
					},
					cache: true
				},
				escapeMarkup: function(markup) {
				return markup;
				},
			}).trigger('change')
			// emit event on change.
			.on('change', function () {
				var el_name = this.name;
				var el_data_itemid = this.getAttribute('data-itemid');console.log(el_data_itemid);
				var data = {
					action: 'lph5p_update_interacted_h5p',
					item_id: el_data_itemid ? parseInt(el_data_itemid) : parseInt(el_name.replace('select_h5p_lp_', '')),
					el_val: parseInt(this.value),
				};
				$.ajax({
					type: 'post',
					url: ajaxurl,
					data: data,
					dataType: 'json',
					success: function (res, textStatus, xhr) {
						console.log(res);
					}
				});
			});
		}
	<?php
}
