<?php
/**
 * Class LP_Random_Quiz_Hooks
 */
defined( 'ABSPATH' ) || exit();

class LP_Random_Quiz_Hooks {
	private static $instance;

	/**
	 * Singleton
	 *
	 * @return LP_Random_Quiz_Hooks
	 */
	public static function instance(): LP_Random_Quiz_Hooks {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	protected function __construct() {
		$this->hooks();
	}

	/**
	 * Hooks.
	 *
	 * @return void
	 */
	protected function hooks() {
		add_filter( 'lp/metabox/quiz/lists', array( $this, 'admin_meta_box' ), 10, 2 );
		add_filter( 'learn-press/quiz/get-question-ids', array( $this, 'get_question_ids_random' ), 10, 3 );
		add_filter( 'learn-press/quiz/questions', array( $this, 'get_question_ids_random' ), 10, 3 );
		add_action( 'learn-press/user/quiz-started', [ $this, 'set_question_ids_random' ], 11, 3 );
		add_action( 'learn-press/user/quiz-retried', [ $this, 'set_question_ids_random' ], 11, 3 );
		add_action( 'learn-press/quiz/number-questions-show', [ $this, 'number_questions_show' ], 11, 3 );
	}

	/**
	 * Set list question ids random.
	 *
	 * @param $quiz_id
	 * @param $course_id
	 * @param $user_id
	 *
	 * @return void
	 */
	public function set_question_ids_random( $quiz_id, $course_id, $user_id ) {
		try {
			// Check enable random questions.
			if ( ! LP_Addon_Random_Quiz_Preload::$addon->is_enable_questions_rand( $quiz_id ) ) {
				return;
			}

			$number_question_rand = LP_Addon_Random_Quiz_Preload::$addon->get_number_question_rand( $quiz_id );
			if ( $number_question_rand === 0 ) {
				return;
			}

			$quiz_curd       = new LP_Quiz_CURD();
			$quiz_ids_origin = $quiz_curd->read_question_ids( $quiz_id );
			$user            = learn_press_get_user( $user_id );
			if ( ! $user ) {
				return;
			}

			$user_course = $user->get_course_data( $course_id );
			if ( ! $user_course ) {
				return;
			}

			$user_quiz = $user_course->get_item( $quiz_id );
			if ( ! $user_quiz ) {
				return;
			}

			$questions_shuffle = $this->shuffle_questions( $quiz_ids_origin, $number_question_rand );
			// $result            = learn_press_update_user_item_meta( $user_quiz->get_user_item_id(), LP_Addon_Random_Quiz::$key_quiz_ids_random, $questions_shuffle );
			LP_User_Items_DB::getInstance()->update_extra_value( $user_quiz->get_user_item_id(), LP_Addon_Random_Quiz::$key_quiz_ids_random, json_encode( $questions_shuffle ) );
			// if ( ! $result ) {
			// 	throw new Exception( __( 'Error when update user item meta.', 'learnpress-random-quiz' ) );
			// }
		} catch ( Throwable $e ) {
			error_log( $e->getMessage() );
		}
	}

	/**
	 * It shuffles the questions in a quiz
	 *
	 * @param array $question_ids.
	 * @param int $quiz_id.
	 * @param int $course_id.
	 *
	 * @return array.
	 */
	public function get_question_ids_random( $question_ids, $quiz_id, $course_id ): array {
		$number_question_rand = LP_Addon_Random_Quiz_Preload::$addon->get_number_question_rand( $quiz_id );

		if ( ! LP_Addon_Random_Quiz_Preload::$addon->is_enable_questions_rand( $quiz_id ) ) {
			return $question_ids;
		}

		if ( $number_question_rand === 0 ) {
			return $this->shuffle_questions( $question_ids, $number_question_rand );
		}

		$course = learn_press_get_course( $course_id );
		if ( ! $course ) {
			return $question_ids;
		}

		// For case No require enroll course.
		if ( $course->is_no_required_enroll() ) {
			return $this->shuffle_questions( $question_ids, $number_question_rand );
		}

		$user        = learn_press_get_current_user();
		$user_course = $user->get_course_data( $course_id );
		if ( ! $user_course ) {
			return $question_ids;
		}

		$user_quiz = $user_course->get_item( $quiz_id );
		// Count total questions random before start quiz.
		if ( ! $user_quiz ) {
			return $this->shuffle_questions( $question_ids, $number_question_rand );
		}

		/*if ( LP_ORDER_COMPLETED === $user_quiz->get_status() ) {
			return $question_ids;
		}*/
		// $question_ids = learn_press_get_user_item_meta( $user_quiz->get_user_item_id(), LP_Addon_Random_Quiz::$key_quiz_ids_random );
		$user_item_id = $user_quiz->get_user_item_id();
		$question_ids = LP_User_Items_DB::getInstance()->get_extra_value( $user_item_id, LP_Addon_Random_Quiz::$key_quiz_ids_random );
		$question_ids = json_decode( $question_ids, true );
		if ( json_last_error() !== JSON_ERROR_NONE || ! is_array( $question_ids ) ) {
			$question_ids = [];
		}

		return $question_ids;
	}

	/**
	 * Get shuffle question ids
	 *
	 * @param array $quiz_ids_origin Quiz IDs.
	 * @param int $limit number Quiz ids return.
	 */
	public function shuffle_questions( array $quiz_ids_origin, int $limit = 0 ): array {
		shuffle( $quiz_ids_origin );
		$questions_shuffle = [];

		if ( $limit === 0 ) {
			return $quiz_ids_origin;
		}

		for ( $i = 0; $i < $limit; $i++ ) {
			$questions_shuffle[] = $quiz_ids_origin[ $i ];
		}

		return $questions_shuffle;
	}

	/**
	 * Get number question random show
	 *
	 * @param int $number_questions
	 * @param LP_Quiz $quiz
	 *
	 * @return int
	 */
	public function number_questions_show( $number_questions, $quiz ) {
		$quiz_id = $quiz->get_id();

		// Check enable random questions.
		if ( LP_Addon_Random_Quiz_Preload::$addon->is_enable_questions_rand( $quiz_id ) ) {
			$number_question_rand = LP_Addon_Random_Quiz_Preload::$addon->get_number_question_rand( $quiz_id );
			if ( $number_question_rand !== 0 ) {
				return $number_question_rand;
			}
		}

		return $number_questions;
	}

	/**
	 * Add field to quiz settings
	 *
	 * @param array $meta_boxes.
	 * @param int $post_id ID of the post being editing.
	 *
	 * @return array
	 */
	public function admin_meta_box( array $meta_boxes, $post_id ) {
		$random_quiz = [
			LP_Addon_Random_Quiz::$key_quiz_random_enable => new LP_Meta_Box_Checkbox_Field(
				esc_html__( 'Random Questions', 'learnpress-random-quiz' ),
				esc_html__( 'Mix all available questions in this quiz.', 'learnpress-random-quiz' ),
				'no'
			),
			LP_Addon_Random_Quiz::$key_number_questions_random => new LP_Meta_Box_Random_Questions(
				'',
				esc_html__( 'Randomised question bank: Limit the question to show random for the student. Set 0 to show all.', 'learnpress-random-quiz' ),
				0,
				array(
					'show' => array( LP_Addon_Random_Quiz::$key_quiz_random_enable, '=', 'yes' ),
				)
			),
		];

		return array_merge( $random_quiz, $meta_boxes );
	}
}
