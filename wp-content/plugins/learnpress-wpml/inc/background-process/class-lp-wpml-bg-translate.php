<?php

/**
 * Class LP_WPML_BG_Translate
 *
 * Handle run background sync data to translate
 * Single to run not schedule, run one time and done when be call
 *
 * @since 4.0.0
 * @author thimpress
 * @version 1.0.1
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LP_WPML_BG_Translate' ) ) {
	class LP_WPML_BG_Translate extends LP_Async_Request {
		protected $prefix = 'lp_wpml';
		protected $action = 'background_translate';
		protected static $instance;
		/**
		 * @var array
		 */
		protected $data = array();

		protected function handle() {
			if ( empty( $_POST['id_new'] ) || empty( $_POST['job'] ) ) {
				return;
			}

			@set_time_limit( 0 );
			$params = array(
				'id_new' => LP_Helper::sanitize_params_submitted( $_POST['id_new'] ),
				'job'    => LP_Helper::sanitize_params_submitted( $_POST['job'] ),
			);

			switch ( get_post_type( $params['id_new'] ) ) {
				case LP_COURSE_CPT:
					$this->async_course( $params );
					break;
				case LP_QUIZ_CPT:
					$this->async_quiz( $params );
					break;
				case LP_QUESTION_CPT:
					$this->async_question( $params );
					break;
				default:
					return;
			}
			die;
		}

		/**
		 * Translate sections, items of course.
		 *
		 * @param array $params
		 *
		 * @return void
		 */
		protected function async_course( array $params ) {
			$course_curd = new LP_Course_CURD();

			try {
				// Create section
				$course_id_new    = $params['id_new'];
				$course_new       = learn_press_get_course( $course_id_new );
				$job              = $params['job'];
				$id_origin        = $job['original_doc_id'];
				$lang_code        = $job['language_code'];
				$source_lang_code = $job['source_language_code'];

				$course_origin = learn_press_get_course( $id_origin );

				// Check sections origin exist, if not delete sections translated with lang.
				$args_sync_sections_deleted = compact( 'course_new', 'course_origin', 'lang_code', 'source_lang_code' );
				$this->sync_sections_deleted( $args_sync_sections_deleted );

				// Check items origin exist, if not delete items translated with lang.
				$this->async_items_deleted( $course_new, $course_origin, $lang_code, $source_lang_code );

				// Duplicate sections of course.
				$course_curd->duplicate_sections( $course_id_new, $course_origin );

				// Save course new
				LP_Course_Post_Type::instance()->save_post( $course_id_new, null, true );
			} catch ( Throwable $e ) {
				error_log( $e->getMessage() );
			}
		}

		/**
		 * Delete sections clone of sections(default lang) deleted.
		 *
		 * @param array $args
		 *
		 * @return void
		 * @version 1.0.0
		 * @since 4.0.1
		 */
		public function sync_sections_deleted( array $args = [] ) {
			$lp_wpml_db       = LP_WPML_DB::getInstance();
			$element_type     = 'post_lp_section';
			$course_origin    = $args['course_origin'];
			$course_new       = $args['course_new'];
			$lang_code        = $args['lang_code'];
			$source_lang_code = $args['source_lang_code'];

			try {
				$sections_origin = $course_origin->get_sections();
				$sections_origin = array_keys( $sections_origin );
				$sections_clone  = $course_new->get_sections();
				$sections_clone  = array_keys( $sections_clone );

				// Get translated ids of sections origin.
				$filer_trids_origin                = new LP_WPML_Filter();
				$filer_trids_origin->element_type  = $element_type;
				$filer_trids_origin->language_code = $source_lang_code;
				$trids_origin                      = $lp_wpml_db->get_trids_by_element_ids( $filer_trids_origin, $sections_origin );

				// Get translated ids of sections clone.
				$filer_trids_clone               = new LP_WPML_Filter();
				$filer_trids_clone->element_type = $element_type;
				$trids_clone                     = $lp_wpml_db->get_trids_by_element_ids( $filer_trids_clone, $sections_clone );

				$trids_will_delete = array_diff( $trids_clone, $trids_origin );

				// Not any translate of section clone.
				if ( empty( $trids_clone ) && ! empty( $sections_clone ) ) {
					foreach ( $sections_clone as $section_clone_id ) {
						$section_curd_clone = new LP_Section_CURD( $course_new->get_id() );
						$section_curd_clone->delete( $section_clone_id );
					}
				}

				if ( ! empty( $trids_will_delete ) ) {
					// Get clone section ids .
					$sections_clone_filer                = new LP_WPML_Filter();
					$sections_clone_filer->element_type  = $element_type;
					$sections_clone_filer->language_code = $lang_code;
					$sections_clone_translated           = $lp_wpml_db->get_element_ids_by_trids( $sections_clone_filer, $trids_will_delete );

					// Delete sections, items of sections clone.
					foreach ( $sections_clone_translated as $section_clone_id ) {
						$section_curd_clone = new LP_Section_CURD( $course_new->get_id() );
						$section_curd_clone->delete( $section_clone_id );
					}

					// Delete sections translated.
					$filer_del = new LP_WPML_Filter();
					$lp_wpml_db->delete_by_trids( $filer_del, $trids_will_delete );
				}
			} catch ( Throwable $e ) {
				error_log( $e->getMessage() );
			}
		}

		/**
		 * Delete sections clone of sections(default lang) deleted.
		 *
		 * @param LP_Course $course_new
		 * @param LP_Course $course_origin
		 * @param string $lang_code
		 * @param string $source_lang_code
		 *
		 * @return void
		 * @version 1.0.0
		 * @since 4.0.1
		 */
		public function async_items_deleted( $course_new, $course_origin, $lang_code, $source_lang_code ) {
			$lp_wpml_db          = LP_WPML_DB::getInstance();
			$lp_section_items_db = LP_Section_Items_DB::getInstance();

			try {
				$items_origin  = $course_origin->get_items();
				$items_clone   = $course_new->get_items();
				$item_types    = learn_press_get_course_item_types();
				$element_types = array_map(
					function ( $item_type ) {
						return 'post_' . $item_type;
					},
					$item_types
				);

				// Get translated ids of sections origin.
				$filer_trids_origin                = new LP_WPML_Filter();
				$filer_trids_origin->element_types = $element_types;
				$filer_trids_origin->language_code = $source_lang_code;
				$trids_origin                      = $lp_wpml_db->get_trids_by_element_ids( $filer_trids_origin, $items_origin );

				// Get translated ids of sections clone.
				$filer_trids_clone                = new LP_WPML_Filter();
				$filer_trids_clone->element_types = $element_types;
				$trids_clone                      = $lp_wpml_db->get_trids_by_element_ids( $filer_trids_clone, $items_clone );

				$trids_will_delete = array_diff( $trids_clone, $trids_origin );
				// Not any translate of items clone.
				if ( empty( $trids_clone ) && ! empty( $items_clone ) ) {
					$filer_del           = new LP_Section_Items_Filter();
					$filer_del->item_ids = $items_clone;
					$lp_section_items_db->delete_items( $filer_del );
				}

				if ( ! empty( $trids_will_delete ) ) {
					// Get clone items ids .
					$items_clone_filer                = new LP_WPML_Filter();
					$items_clone_filer->element_types = $element_types;
					$items_clone_filer->language_code = $lang_code;
					$items_clone_translated           = $lp_wpml_db->get_element_ids_by_trids( $items_clone_filer, $trids_will_delete );

					// Delete items clone on section clone.
					$filer_del           = new LP_Section_Items_Filter();
					$filer_del->item_ids = $items_clone_translated;
					$lp_section_items_db->delete_items( $filer_del );

					// Delete items translated.
					$filer_del = new LP_WPML_Filter();
					$lp_wpml_db->delete_by_trids( $filer_del, $trids_will_delete );
				}
			} catch ( Throwable $e ) {
				error_log( $e->getMessage() );
			}
		}

		/**
		 * Sync quiz.
		 *
		 * @param array $params
		 *
		 * @return void
		 */
		protected function async_quiz( array $params ) {
			try {
				$quiz_id_new      = $params['id_new'] ?? 0;
				$quiz_new         = learn_press_get_quiz( $quiz_id_new );
				$job              = $params['job'];
				$quiz_id_origin   = $job['original_doc_id'] ?? 0;
				$quiz_origin      = learn_press_get_quiz( $quiz_id_origin );
				$lang_code        = $job['language_code'];
				$source_lang_code = $job['source_language_code'];

				$question_ids_origin = $quiz_origin->get_question_ids();
				$question_ids_clone  = $quiz_new->get_question_ids();

				// Check questions origin exist, if not delete questions translated with lang.
				$args = compact( 'quiz_origin', 'quiz_new', 'question_ids_origin', 'question_ids_clone', 'lang_code', 'source_lang_code' );
				$this->sync_questions_deleted( $args );

				// Duplicate questions of quiz.
				$quiz_curd = new LP_Quiz_CURD();
				$quiz_curd->duplicate_questions( $quiz_id_origin, $quiz_id_new );
			} catch ( Throwable $e ) {
				error_log( $e->getMessage() );
			}
		}

		public function sync_questions_deleted( array $args ) {
			$lp_wpml_db = LP_WPML_DB::getInstance();

			try {
				/**
				 * @var LP_Quiz $quiz_new
				 */
				$quiz_new            = $args['quiz_new'];
				$question_ids_clone  = $args['question_ids_clone'];
				$question_ids_origin = $args['question_ids_origin'];
				$lang_code           = $args['lang_code'];
				$source_lang_code    = $args['source_lang_code'];
				$element_type        = 'post_' . LP_QUESTION_CPT;

				// Get translated ids of questions origin.
				$filer_questions_origin                = new LP_WPML_Filter();
				$filer_questions_origin->element_type  = $element_type;
				$filer_questions_origin->language_code = $source_lang_code;
				$trids_origin                          = $lp_wpml_db->get_trids_by_element_ids( $filer_questions_origin, $question_ids_origin );

				// Get translated ids of questions clone.
				$filer_questions_clone               = new LP_WPML_Filter();
				$filer_questions_clone->element_type = $element_type;
				$trids_clone                         = $lp_wpml_db->get_trids_by_element_ids( $filer_questions_clone, $question_ids_clone );

				// Not any translate of questions clone.
				if ( empty( $trids_clone ) && ! empty( $question_ids_clone ) ) {
					// Delete $questions assign for quiz.
					$filter_del             = new LP_Quiz_Questions_Filter();
					$filter_del->quiz_ids   = $question_ids_clone;
					$filter_del->collection = $lp_wpml_db->tb_lp_quiz_questions;
					$question_ids_format    = LP_Helper::db_format_array( $question_ids_clone, '%d' );
					$filter_del->where[]    = $lp_wpml_db->wpdb->prepare( 'AND question_id IN (' . $question_ids_format . ')', $question_ids_clone );

					$lp_wpml_db->delete_execute( $filter_del );

					return;
				}

				$trids_will_delete = array_diff( $trids_clone, $trids_origin );

				if ( empty( $trids_will_delete ) ) {
					return;
				}

				// Get all questions clone with lang need deleted
				$questions_clone_filer                = new LP_WPML_Filter();
				$questions_clone_filer->element_type  = $element_type;
				$questions_clone_filer->language_code = $lang_code;
				$questions_clone_translated           = $lp_wpml_db->get_element_ids_by_trids( $questions_clone_filer, $trids_will_delete );
				if ( ! empty( $questions_clone_translated ) ) {
					// Delete $questions assign for quiz.
					$filter_del             = new LP_Quiz_Questions_Filter();
					$filter_del->quiz_ids   = $questions_clone_translated;
					$filter_del->collection = $lp_wpml_db->tb_lp_quiz_questions;
					$question_ids_format    = LP_Helper::db_format_array( $questions_clone_translated, '%d' );
					$filter_del->where[]    = $lp_wpml_db->wpdb->prepare( 'AND question_id IN (' . $question_ids_format . ')', $questions_clone_translated );

					$lp_wpml_db->delete_execute( $filter_del );
				}
			} catch ( Throwable $e ) {
				error_log( $e->getMessage() );
			}
		}

		/**
		 * Sync translate question.
		 * @param array $params
		 *
		 * @return void
		 * @since 4.0.1
		 * @version 1.0.0
		 */
		public function async_question( array $params ) {
			try {
				$question_id_new      = $params['id_new'] ?? 0;
				$question_new         = learn_press_get_question( $question_id_new );
				$job              = $params['job'];
				$question_id_origin   = $job['original_doc_id'] ?? 0;
				$question_origin = learn_press_get_question( $question_id_origin );

				// set data
				$question_new->set_type( $question_origin->get_type() );
				$question_new->set_data( 'answer_options', $question_origin->get_data( 'answer_options' ) );

				// delete answer translated if exists
				$lp_question_curd = new LP_Question_CURD();
				$question_answers = $question_new->get_answers();
				foreach ( $question_answers as $answer ) {
					$lp_question_curd->delete_answer( $question_id_new, $answer->get_id() );
				}

				// duplicate answer
				$lp_question_curd = new LP_Question_CURD();
				$lp_question_curd->duplicate_answer( $question_id_origin, $question_id_new );
			} catch ( Throwable $e ) {
				error_log( $e->getMessage() );
			}
		}

		/**
		 * @return LP_WPML_BG_Translate
		 */
		public static function instance(): self {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}
	}

	// Must run instance to register ajax.
	LP_WPML_BG_Translate::instance();
}
