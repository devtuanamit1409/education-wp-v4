<?php

/**
 * Class LP_Addon_WPML_Hooks
 *
 * @since 4.0.0
 * @version  1.0.0
 * @author Minhpd
 */
class LP_Addon_WPML_Hooks {
	private static $instance;
	/**
	 * @var array $wpml_setting
	 */
	public $wpml_setting = array();
	/**
	 * @var int $wpml_setting_course
	 */
	public $wpml_setting_course = 0;
	/**
	 * @var SitePress $sitepress
	 */
	public $sitepress;

	public static function instance(): self {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();

			self::$instance->wpml_setting        = get_option( 'icl_sitepress_settings', array() );
			self::$instance->wpml_setting_course = self::$instance->wpml_setting['custom_posts_sync_option']['lp_course'] ?? 0;
		}

		return self::$instance;
	}

	/**
	 * LP_Addon_WPML_Hooks constructor.
	 */
	protected function __construct() {
		global $sitepress;
		$this->sitepress = $sitepress;

		// Add param lang when call api course page profile
		add_filter( 'lp/profile/args/user_courses_created', array( $this, 'lp_wpml_add_params_lang_restapi' ), 10 );
		add_filter( 'lp/profile/args/user_courses_attend', array( $this, 'lp_wpml_add_params_lang_restapi' ), 10 );
		add_filter( 'lp/user/course/query/filter', array( $this, 'lp_wpml_filter_user_course' ), 10 );

		// Hook get query courses by lang
		add_filter( 'lp/template/archive-course/skeleton/args', array( $this, 'lp_wpml_add_params_lang_restapi' ), 10 );
		add_filter( 'learn-press/courses/handle_params_for_query_courses', array(
			$this,
			'lp_wpml_filter_query_courses'
		), 10, 2 );

		// Hook get statistic info on profile
		add_filter( 'lp/profile/args/user_courses_statistic', array( $this, 'lp_wpml_add_params_lang_restapi' ), 10 );

		// Hook change link
		add_filter( 'learn_press_get_page_id', array( $this, 'get_page_id' ), 10, 2 );

		// Set language default for course, items' course when created course via LP Tool
		add_action( 'lp/background/course/save', array( $this, 'set_course_sample_default_lang' ), 10, 2 );
		add_action( 'lp/section/clone/success', array( $this, 'set_section_lang' ), 10, 1 );

		// Set save translation completed
		add_filter( 'wpml_translation_editor_save_job_data', array( $this, 'save_job_data_to_complete' ), 20, 1 );

		// Auto create translate items of courses
		add_filter( 'wpml_pro_translation_completed', array( $this, 'sync_lp_items_translate' ), 10, 3 );

		// Check sections and items of course was translate.
		add_filter( 'lp/section/can-clone', array( $this, 'check_section_translated' ), 10, 4 );
		add_filter( 'lp/section/item/can-clone', array( $this, 'check_section_item_translated' ), 10, 2 );
		add_filter( 'lp/quiz/question/can-clone', array( $this, 'check_quiz_question_translated' ), 10, 2 );

		// create item translate when duplicate post
		add_action( 'learn-press/item/after-duplicate', array( $this, 'translate_item_after_duplicated' ), 10, 2 );

		// change item when change setting format url wpml
		add_filter( 'learn-press/course/item-link', array( $this, 'lp_wpml_course_item_link' ), 10, 2 );

		// change rest url wpml
		add_filter( 'rest_url', array( $this, 'lp_wpml_rest_url' ), 9999, 4 );

		// Search items by lang in single course admin section
		add_filter( 'learn-press/modal-search-items/args', array( $this, 'lp_wpml_modal_search_items_args' ), 10, 1 );
	}

	/**
	 * Add param lang when call api course tabs , statistic,... page profile
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public function lp_wpml_add_params_lang_restapi( array $args ): array {
		$current_lang = apply_filters( 'wpml_current_language', null );
		$default_lang = apply_filters( 'wpml_default_language', null );

		// url format setting
		$language_negotiation_type = $this->wpml_setting['language_negotiation_type'];

		if ( $language_negotiation_type == 1 ) {
			$args['lang'] = $current_lang;
		} elseif ( $current_lang != $default_lang ) {
			$args['lang'] = $current_lang;
		}

		return $args;
	}

	/**
	 * custom filter tab purchased with current language
	 *
	 * @param LP_User_Items_Filter $filter
	 *
	 * @return LP_User_Items_Filter $filter
	 * @throws Exception
	 */
	public function lp_wpml_filter_user_course( LP_User_Items_Filter $filter ): LP_User_Items_Filter {
		$wpml_default_lang = apply_filters( 'wpml_default_language', null );
		$wpml_current_lang = $_REQUEST['lang'] ?? $wpml_default_lang;
		if ( ! isset( $wpml_current_lang ) ) {
			return $filter;
		}

		$lp_wpml_db = LP_WPML_DB::getInstance();

		$filter->join[] = "INNER JOIN $lp_wpml_db->tb_icl_translations as wpml ON ui.item_id = wpml.element_id";

		if ( $this->wpml_setting_course == 1 ) { // Get courses has translated by lang
			$filter->where[] = $lp_wpml_db->wpdb->prepare( 'AND wpml.language_code = %s AND wpml.element_type = %s', $wpml_current_lang, LP_WPML_COURSE_CPT );
		} elseif ( $this->wpml_setting_course == 2 ) { // Get courses has translated, if not get course by default lang
			if ( $wpml_current_lang === $wpml_default_lang ) {
				$filter->where[] = $lp_wpml_db->wpdb->prepare( 'AND wpml.language_code = %s AND wpml.element_type = %s', $wpml_current_lang, LP_WPML_COURSE_CPT );
			} else {
				// Get courses id by lang
				$wpml_filter                      = new LP_WPML_Filter();
				$wpml_filter->return_string_query = true;
				$wpml_filter->language_code       = $wpml_current_lang;
				$wpml_filter->element_type        = LP_WPML_COURSE_CPT;
				$query_course_wpml_by_lang        = $lp_wpml_db->get_courses_by_lang( $wpml_filter );

				// Get trid by lang
				$wpml_filter                      = new LP_WPML_Filter();
				$wpml_filter->language_code       = $wpml_current_lang;
				$wpml_filter->element_type        = LP_WPML_COURSE_CPT;
				$wpml_filter->return_string_query = true;
				$query_trid_course_translated     = $lp_wpml_db->get_trid_by_lang( $wpml_filter );

				// Query get courses not translate lang, will get default lang
				$wpml_filter                             = new LP_WPML_Filter();
				$wpml_filter->return_string_query        = true;
				$wpml_filter->element_type               = LP_WPML_COURSE_CPT;
				$wpml_filter->language_code              = $wpml_default_lang;
				$query_course_wpml_not_translate_by_lang = $lp_wpml_db->get_courses_by_default_lang_not_translate_by_lang( $wpml_filter, $query_trid_course_translated );

				$filter->where[] = 'AND (ui.item_id IN (' . $query_course_wpml_by_lang . ') OR ui.item_id IN (' . $query_course_wpml_not_translate_by_lang . '))';
			}
		}

		return $filter;
	}

	/**
	 * custom filter courses page archive course
	 *
	 * @param LP_Course_Filter $filter
	 * @param array $params
	 *
	 * @return LP_Course_Filter
	 * @throws Exception
	 */
	public function lp_wpml_filter_query_courses( LP_Course_Filter $filter, array $params ): LP_Course_Filter {
		$lp_wpml_db        = LP_WPML_DB::getInstance();
		$wpml_default_lang = apply_filters( 'wpml_default_language', null );
		$wpml_current_lang = $params['lang'] ?? $_REQUEST['lang'] ?? apply_filters( 'wpml_current_language', NULL ) ?? $wpml_default_lang;

		if ( ! isset( $wpml_current_lang ) ) {
			return $filter;
		}

		$filter->join[] = "INNER JOIN {$lp_wpml_db->tb_icl_translations} as wpml ON p.ID = wpml.element_id";

		if ( $this->wpml_setting_course == 1 ) { // Get courses has translated by lang
			$filter->where[] = $lp_wpml_db->wpdb->prepare( 'AND wpml.language_code = %s AND wpml.element_type = %s', $wpml_current_lang, LP_WPML_COURSE_CPT );
		} elseif ( $this->wpml_setting_course == 2 ) { // Get courses has translated, if not get course by default lang
			if ( $wpml_current_lang === $wpml_default_lang ) {
				$filter->where[] = $lp_wpml_db->wpdb->prepare( 'AND wpml.language_code = %s AND wpml.element_type = %s', $wpml_current_lang, LP_WPML_COURSE_CPT );
			} else {
				// Get courses id by lang
				$wpml_filter                      = new LP_WPML_Filter();
				$wpml_filter->return_string_query = true;
				$wpml_filter->language_code       = $wpml_current_lang;
				$wpml_filter->element_type        = LP_WPML_COURSE_CPT;
				$query_course_wpml_by_lang        = $lp_wpml_db->get_courses_by_lang( $wpml_filter );

				// Get trid by lang
				$wpml_filter                      = new LP_WPML_Filter();
				$wpml_filter->language_code       = $wpml_current_lang;
				$wpml_filter->element_type        = LP_WPML_COURSE_CPT;
				$wpml_filter->return_string_query = true;
				$query_trid_course_translated     = $lp_wpml_db->get_trid_by_lang( $wpml_filter );

				// Query get courses not translate lang, will get default lang
				$wpml_filter                             = new LP_WPML_Filter();
				$wpml_filter->return_string_query        = true;
				$wpml_filter->language_code              = $wpml_default_lang;
				$wpml_filter->element_type               = LP_WPML_COURSE_CPT;
				$query_course_wpml_not_translate_by_lang = $lp_wpml_db->get_courses_by_default_lang_not_translate_by_lang( $wpml_filter, $query_trid_course_translated );

				$filter->where[] = 'AND (p.ID IN (' . $query_course_wpml_by_lang . ') OR p.ID IN (' . $query_course_wpml_not_translate_by_lang . '))';
			}
		}

		return $filter;
	}

	/**
	 * Get page_id config on LP Settings
	 *
	 * @param int $page_id
	 * @param string $name
	 *
	 * @return int
	 */
	public function get_page_id( int $page_id = 0, string $name = '' ): int {
		$wpml_default_lang = apply_filters( 'wpml_default_language', null );
		$wpml_current_lang = ICL_LANGUAGE_CODE;

		if ( $wpml_current_lang != $wpml_default_lang ) {
			$page_id = absint( LP_Settings::get_option( "{$name}_page_id_{$wpml_current_lang}" ) );
		}

		return $page_id;
	}

	/**
	 * Set language default for course when created course via LP Tool
	 *
	 * @param LP_Course $course
	 * @param array $data
	 */
	public function set_course_sample_default_lang( $course, $data ) {
		if ( ! isset( $data['data_sample'] ) ) {
			return;
		}

		$post_id = $course->get_id();
		$this->set_default_lang_for_item( $post_id );

		$course_item_ids = $course->get_items();
		foreach ( $course_item_ids as $item ) {
			$this->set_default_lang_for_item( $item->item_id );
		}
	}

	/**
	 * Set language default for section's course.
	 * Save text lang code after name section.
	 *
	 * @param array $args
	 *
	 * @return int
	 */
	public function set_section_lang( array $args ): int {
		$lp_wpml_db        = LP_WPML_DB::getInstance();
		$section_new       = $args['section_new'] ?? [];
		$section_origin    = $args['section_origin'] ?? [];
		$course_id_new     = $args['course_id_new'] ?? [];
		$section_id_new    = $section_new['id'] ?? $section_new['section_id'] ?? 0;
		$translation_id    = 0;
		$section_id_origin = $section_origin['id'] ?? $section_origin['section_id'] ?? 0;
		$element_type      = 'post_lp_section';

		if ( ! isset( $_POST['id_new'] ) || ! isset( $_POST['job'] ) ) {
			return $translation_id;
		}

		try {
			$job              = LP_Helper::sanitize_params_submitted( $_POST['job'] );
			$source_lang_code = $job['source_language_code'] ?? '';
			$lang_code        = $job['language_code'] ?? '';
			if ( empty( $source_lang_code ) || empty( $lang_code ) ) {
				return $translation_id;
			}

			// Get trid of section origin.
			$filter                = new LP_WPML_Filter();
			$filter->element_type  = $element_type;
			$filter->element_id    = [ $section_id_origin ];
			$filter->language_code = $source_lang_code;
			$section_trid          = $lp_wpml_db->get_trid( $filter );
			if ( ! $section_trid ) {
				return $translation_id;
			}

			/**
			 * @var SitePress $sitepress
			 */
			global $sitepress;
			// Set language translate for new section.
			$set_language_args = array(
				'element_id'           => $section_id_new,
				'element_type'         => $element_type,
				'trid'                 => $section_trid,
				'language_code'        => $lang_code,
				'source_language_code' => $source_lang_code,
			);
			$sitepress->set_element_language_details_action( $set_language_args );

			// Update title section.
			$lp_section_curd             = new LP_Section_CURD( $course_id_new );
			$section_new['section_name'] = $section_new['section_name'] . ' - ' . $lang_code;
			$lp_section_curd->update( $section_new );

			return $translation_id;
		} catch ( Throwable $e ) {
			error_log( $e->getMessage() );
		}

		return $translation_id;
	}

	/**
	 * Set default lang for section.
	 *
	 * @param int $section_id
	 *
	 * @return int
	 */
	public function set_section_default_lang( int $section_id ): int {
		/**
		 * @var SitePress $sitepress
		 */
		$translation_id = 0;
		$sitepress      = $this->sitepress;
		$lp_wpml_db     = LP_WPML_DB::getInstance();

		try {
			$default_language = $sitepress->get_default_language();

			$filter_trid_max = new LP_WPML_Filter();
			$trid_max        = $lp_wpml_db->get_trid_max( $filter_trid_max );
			if ( ! $trid_max ) {
				return $translation_id;
			}
			$trid_max ++;

			$data = array(
				'element_id'           => $section_id,
				'element_type'         => 'post_lp_section',
				'trid'                 => $trid_max,
				'language_code'        => $default_language,
				'source_language_code' => null,
			);
			$lp_wpml_db->wpdb->insert( $lp_wpml_db->tb_icl_translations, $data, array( '%d', '%s', '%d', '%s', '%s' ) );

			$filter_get_trid                = new LP_WPML_Filter();
			$filter_get_trid->element_id    = [ $section_id ];
			$filter_get_trid->trid          = [ $trid_max ];
			$filter_get_trid->language_code = $default_language;
			$translation_id                 = $lp_wpml_db->get_trid( $filter_get_trid );
		} catch ( Throwable $e ) {
			error_log( $e->getMessage() );
		}

		return $translation_id;
	}

	/**
	 * Set language default for post(course, item's course) when created course via LP Tool
	 *
	 * @param int $post_id
	 */
	public function set_default_lang_for_item( int $post_id = 0 ) {
		$wpml_settings = array();
		global $wpdb;

		if ( ! $post_id ) {
			return;
		}

		$wpml = new WPML_Admin_Post_Actions( $wpml_settings, $wpdb );
		$wpml->save_post_actions( $post_id, get_post( $post_id ) );
	}

	/**
	 * Set 100% completed translation, to pass condition and can call hook 'wpml_pro_translation_completed'
	 *
	 * @param array $data
	 *
	 * @return array
	 * @see WPML_Save_Translation_Data_Action::save_translation
	 * Line 85 if ( ! empty( $data['complete'] ) && ! $is_incomplete )
	 *
	 */
	public function save_job_data_to_complete( array $data ): array {
		if ( ! empty( $data ) && ! empty( $data['job_post_type'] ) && 'post_' . LP_COURSE_CPT === $data['job_post_type'] ) {
			$data['complete'] = 'on';

			foreach ( $data['fields'] as $key => $field ) {
				$data['fields'][ $key ]['finished'] = 'on';
			}
		}

		return $data;
	}

	/**
	 * Auto create translate items of courses
	 *
	 * @param int $id_new
	 * @param array $data_fields
	 * @param object $job
	 */
	public function sync_lp_items_translate( $id_new, $data_fields, $job ) {
		if ( empty( $id_new ) || empty( $job ) ) {
			return;
		}

		if ( LP_COURSE_CPT !== get_post_type( $id_new )
		     && LP_QUIZ_CPT !== get_post_type( $id_new )
		     && LP_QUESTION_CPT !== get_post_type( $id_new ) ) {
			return;
		}

		$params = array(
			'id_new' => $id_new,
			'job'    => $job,
		);

		$bg = LP_WPML_BG_Translate::instance();

		$bg->data( $params )->dispatch();
	}

	/**
	 * Check clone section, items of section.
	 * Update order of section and items.
	 *
	 * @param bool $can_clone
	 * @param int $course_id_new
	 * @param LP_Course $course_origin
	 * @param array $section_origin
	 *
	 * @return bool
	 */
	public function check_section_translated( bool $can_clone, int $course_id_new, LP_Course $course_origin, array $section_origin ): bool {
		$lp_wpml_db        = LP_WPML_DB::getInstance();
		$section_trid      = 0;
		$section_id_origin = $section_origin['id'] ?? 0;
		if ( ! $section_id_origin || ! $course_id_new ) {
			return $can_clone;
		}

		if ( ! isset( $_POST['id_new'] ) || ! isset( $_POST['job'] ) ) {
			return $can_clone;
		}

		$job              = LP_Helper::sanitize_params_submitted( $_POST['job'] );
		$source_lang_code = $job['source_language_code'] ?? '';
		$lang_code        = $job['language_code'] ?? '';
		$element_type     = 'post_lp_section';

		try {
			// Get trid of section origin.
			$filter                = new LP_WPML_Filter();
			$filter->element_type  = $element_type;
			$filter->element_id    = [ $section_id_origin ];
			$filter->language_code = $source_lang_code;
			$section_trid          = $lp_wpml_db->get_trid( $filter );

			// Set default lang for origin section if not exists.
			if ( ! $section_trid ) {
				$section_trid = $this->set_section_default_lang( $section_id_origin );
			}

			// Check section has translated
			$filter                       = new LP_WPML_Filter();
			$filter->element_type         = $element_type;
			$filter->language_code        = $lang_code;
			$filter->source_language_code = $source_lang_code;
			$filter->trid                 = [ $section_trid ];
			$section_translated           = $lp_wpml_db->get_icl_translations( $filter );

			if ( $section_translated ) {
				$can_clone = false;

				// Update order of section.
				$section_curd = new LP_Section_CURD( $course_id_new );

				$section_id_new = $section_translated[0]->element_id;
				$course         = learn_press_get_course( $course_id_new );
				$section_new    = $course->get_sections_data_arr( $section_id_new );
				if ( ! isset( $section_new['section_order'] ) || $section_new['section_order'] !== $section_origin['order'] ) {
					$section_new['section_order'] = $section_origin['order'];
					$section_curd->update( $section_new );
				}

				// Update items for section
				$course_curd = new LP_Course_CURD();
				$course_curd->duplicate_section_items( $section_id_new, $section_curd, $section_origin );
			}
		} catch ( Throwable $e ) {
			error_log( $e->getMessage() );
		}

		return $can_clone;
	}

	/**
	 * Check item of section is translated.
	 * Update order of section and items.
	 *
	 * @param bool $can_clone
	 * @param array $args
	 *
	 * @return bool
	 */
	public function check_section_item_translated( bool $can_clone, array $args ): bool {
		$lp_wpml_db          = LP_WPML_DB::getInstance();
		$lp_section_items_db = LP_Section_Items_DB::getInstance();

		if ( ! isset( $_POST['id_new'] ) || ! isset( $_POST['job'] ) ) {
			return $can_clone;
		}

		$job = LP_Helper::sanitize_params_submitted( $_POST['job'] );

		$source_lang_code = $job['source_language_code'] ?? '';
		$lang_code        = $job['language_code'] ?? '';

		/** @var array $item_origin */
		$item_origin = $args['item_origin'] ?? [];
		/** @var int $section_id_new */
		$section_id_new = $args['section_id_new'] ?? 0;
		/** @var LP_Section_CURD $section_curd_new */
		$section_curd_new = $args['section_curd_new'] ?? 0;
		/** @var array $course */
		$section_origin = $args['section_origin'] ?? 0;

		if ( empty( $item_origin ) ) {
			return $can_clone;
		}

		$item_id_origin = $item_origin['id'] ?? 0;
		try {
			$item_type    = get_post_type( $item_id_origin );
			$element_type = 'post_' . $item_type;

			// Check item has default lang.
			$filter                = new LP_WPML_Filter();
			$filter->element_type  = $element_type;
			$filter->element_id    = [ $item_id_origin ];
			$filter->language_code = $source_lang_code;
			$item_trid             = $lp_wpml_db->get_trid( $filter );
			if ( ! $item_trid ) {
				return $can_clone;
			}

			// Check section has translated.
			$filter                       = new LP_WPML_Filter();
			$filter->element_type         = $element_type;
			$filter->language_code        = $lang_code;
			$filter->source_language_code = $source_lang_code;
			$filter->trid                 = [ $item_trid ];
			$item_translated              = $lp_wpml_db->get_icl_translations( $filter );
			if ( $item_translated ) {
				// Update item (translated) order.
				$item_id_translated = $item_translated[0]->element_id;
				if ( ! $item_id_translated ) {
					$filter_del             = new LP_WPML_Filter();
					$filter_del->collection = $lp_wpml_db->tb_icl_translations;
					$filter_del->where[]    = $lp_wpml_db->wpdb->prepare( 'AND translation_id = %d', $item_translated[0]->translation_id );
					$lp_wpml_db->delete_execute( $filter_del );
				} else {
					$can_clone = false;

					// Update order of item.
					$filter_update          = new LP_Section_Items_Filter();
					$filter_update->where[] = $lp_section_items_db->wpdb->prepare( 'AND item_id = %d', $item_id_translated );
					$filter_update->set[]   = $lp_section_items_db->wpdb->prepare( 'item_order = %d', $item_origin['order'] );
					$lp_section_items_db->update( $filter_update );

					// Assign item to translated section if not.
					$item = [
						'item_id'    => $item_id_translated,
						'item_order' => $item_origin['order'],
						'item_type'  => get_post_type( $item_id_translated ),
					];
					$section_curd_new->assign_item_section( $section_id_new, $item );

					// If item is quiz, sync questions of quiz.
					if ( LP_QUIZ_CPT === $item_type ) {
						// Set quiz id origin for sync.
						$job['original_doc_id'] = $item_id_origin;

						$params = array(
							'id_new' => $item_id_translated,
							'job'    => $job,
						);

						$translate_questions_of_quiz = LP_WPML_BG_Translate::instance();
						$translate_questions_of_quiz->data( $params )->dispatch();
					}
				}
			}
		} catch ( Throwable $e ) {
			error_log( $e->getMessage() );
		}

		return $can_clone;
	}

	/**
	 * Check item of section is translated.
	 * Update order of section and items.
	 *
	 * @param bool $can_clone
	 * @param array $args
	 *
	 * @return bool
	 * @version 1.0.0
	 * @since 4.0.1
	 */
	public function check_quiz_question_translated( bool $can_clone, array $args ): bool {
		$lp_wpml_db           = LP_WPML_DB::getInstance();
		$lp_section_items_db  = LP_Section_Items_DB::getInstance();
		$lp_quiz_questions_db = LP_Quiz_Questions_DB::getInstance();

		if ( ! isset( $_POST['id_new'] ) || ! isset( $_POST['job'] ) ) {
			return $can_clone;
		}

		$job = LP_Helper::sanitize_params_submitted( $_POST['job'] );

		$source_lang_code = $job['source_language_code'] ?? '';
		$lang_code        = $job['language_code'] ?? '';

		/** @var int $question_id_origin */
		$question_id_origin = $args['question_id_origin'] ?? 0;
		/** @var LP_Quiz $quiz_origin */
		$quiz_origin = $args['quiz_origin'] ?? null;
		/** @var int $quiz_id_new */
		$quiz_id_new = $args['quiz_id_new'] ?? 0;

		if ( ! $quiz_origin ) {
			return $can_clone;
		}

		$quiz_id_origin = $quiz_origin->get_id();
		try {
			$element_type = 'post_' . LP_QUESTION_CPT;

			// Check question has default lang.
			$filter                = new LP_WPML_Filter();
			$filter->element_type  = $element_type;
			$filter->element_id    = [ $question_id_origin ];
			$filter->language_code = $source_lang_code;
			$question_trid         = $lp_wpml_db->get_trid( $filter );
			if ( ! $question_trid ) {
				return $can_clone;
			}

			// Check question has translated.
			$filter                       = new LP_WPML_Filter();
			$filter->element_type         = $element_type;
			$filter->language_code        = $lang_code;
			$filter->source_language_code = $source_lang_code;
			$filter->trid                 = [ $question_trid ];
			$item_translated              = $lp_wpml_db->get_icl_translations( $filter );
			if ( $item_translated ) {
				$can_clone              = false;
				$question_id_translated = $item_translated[0]->element_id;

				// Assign question translated to quiz translated if not.
				$quiz_clone_curd = new LP_Quiz_CURD();
				$quiz_clone_curd->add_question( $quiz_id_new, $question_id_translated );

				// Get order of question origin.
				$filter_get_question_origin               = new LP_Quiz_Questions_Filter();
				$filter_get_question_origin->quiz_ids     = [ $quiz_id_origin ];
				$filter_get_question_origin->question_ids = [ $question_id_origin ];
				$filter_get_question_origin->field_count  = 'quiz_question_id';
				$questions                                = $lp_quiz_questions_db->get_quiz_questions( $filter_get_question_origin );
				if ( $questions ) {
					$question_order_origin = $questions[0]->question_order;

					// Update question (translated) order.
					$filter_update             = new LP_Quiz_Questions_Filter();
					$filter_update->collection = $lp_quiz_questions_db->tb_lp_quiz_questions;
					$filter_update->set[]      = $lp_section_items_db->wpdb->prepare( 'question_order = %d', $question_order_origin );
					$filter_update->where[]    = $lp_section_items_db->wpdb->prepare( 'AND question_id = %d', $question_id_translated );
					$lp_quiz_questions_db->update_execute( $filter_update );
				}
			}
		} catch ( Throwable $e ) {
			error_log( $e->getMessage() );
		}

		return $can_clone;
	}

	/**
	 * Translate item of course when clone success.
	 *
	 * @param int $id_origin
	 * @param int $id_new
	 */
	public function translate_item_after_duplicated( int $id_origin, int $id_new ) {
		$sitepress = $this->sitepress;

		try {
			if ( ! isset( $_POST['id_new'] ) || ! isset( $_POST['job'] ) ) {
				$this->set_default_lang_for_item( $id_origin );

				return;
			}

			$job              = LP_Helper::sanitize_params_submitted( $_POST['job'] );
			$source_lang_code = $job['source_language_code'] ?? '';
			$lang_code        = $job['language_code'] ?? '';

			// Update item title
			$post_type    = get_post_type( $id_new );
			$trid         = $sitepress->get_element_trid( $id_origin, 'post_' . $post_type );
			$element_type = 'post_' . get_post_type( $id_new );
			$title_new    = get_the_title( $id_origin ) . ' - ' . $lang_code;
			wp_update_post(
				array(
					'ID'         => $id_new,
					'post_title' => $title_new,
				)
			);

			$set_language_args = array(
				'element_id'           => $id_new,
				'element_type'         => $element_type,
				'trid'                 => $trid,
				'language_code'        => $lang_code,
				'source_language_code' => $source_lang_code,
			);

			$sitepress->set_element_language_details_action( $set_language_args );
		} catch ( Throwable $e ) {
			error_log( $e->getMessage() );
		}
	}

	/**
	 * It takes the course link and adds the item slug to it
	 *
	 * @param item_link The link to the item.
	 * @param item_id The ID of the item.
	 */
	public function lp_wpml_course_item_link( $item_link, $item_id ) {
		global $sitepress;

		$item_type   = get_post_type( $item_id );
		$course_curd = new LP_Course_CURD();
		$course_id   = $course_curd->get_course_by_item( $item_id );

		if ( ! empty( $course_id ) ) {
			if ( (int) $this->wpml_setting['language_negotiation_type'] == 3 ) {
				$trid_item    = $sitepress->get_element_trid( $course_id[0], 'post_' . LP_COURSE_CPT );
				$translations = apply_filters( 'wpml_get_element_translations', null, $trid_item, 'post_' . LP_COURSE_CPT );
				$default_lang = apply_filters( 'wpml_default_language', null );

				foreach ( $translations as $lang => $translation ) {

					if ( ! $translation->original && $translation->element_id == $course_id[0] ) {
						$course      = get_post( $course_id[0] );
						$course_link = $course->guid;
						$item_slug   = get_post_field( 'post_name', $item_id );

						$slug_prefixes = apply_filters(
							'learn-press/course/custom-item-prefixes',
							array(
								LP_QUIZ_CPT   => sanitize_title_with_dashes( LearnPress::instance()->settings->get( 'quiz_slug', 'quiz' ) ),
								LP_LESSON_CPT => sanitize_title_with_dashes( LearnPress::instance()->settings->get( 'lesson_slug', 'lesson' ) ),
							),
							$course->ID
						);

						$slug_prefix = trailingslashit( $slug_prefixes[ $item_type ] ?? '' );
						$item_link   = $course_link . $slug_prefix . $item_slug . '/?lang=' . $lang;
					}
				}
			}
		}

		return $item_link;
	}

	public function lp_wpml_rest_url( $url, $path, $blog_id, $scheme ) {
		if ( empty( $path ) ) {
			$path = '/';
		}

		$path = '/' . ltrim( $path, '/' );

		if ( is_multisite() && get_blog_option( $blog_id, 'permalink_structure' ) || get_option( 'permalink_structure' ) ) {
			global $wp_rewrite;

			if ( $wp_rewrite->using_index_permalinks() ) {
				$url = get_home_url( $blog_id, $wp_rewrite->index . '/' . rest_get_url_prefix(), $scheme );
			} else {
				$url = get_home_url( $blog_id, rest_get_url_prefix(), $scheme );
			}

			$url .= $path;
		} else {
			$url = trailingslashit( get_home_url( $blog_id, '', $scheme ) );
			// nginx only allows HTTP/1.0 methods when redirecting from / to /index.php.
			// To work around this, we manually add index.php to the URL, avoiding the redirect.
			if ( 'index.php' !== substr( $url, 9 ) ) {
				$url .= 'index.php';
			}

			$url = add_query_arg( 'rest_route', $path, $url );
		}

		if ( is_ssl() && isset( $_SERVER['SERVER_NAME'] ) ) {
			// If the current host is the same as the REST URL host, force the REST URL scheme to HTTPS.
			if ( parse_url( get_home_url( $blog_id ), PHP_URL_HOST ) === $_SERVER['SERVER_NAME'] ) {
				$url = set_url_scheme( $url, 'https' );
			}
		}

		if ( is_admin() && force_ssl_admin() ) {
			/*
			 * In this situation the home URL may be http:, and `is_ssl()` may be false,
			 * but the admin is served over https: (one way or another), so REST API usage
			 * will be blocked by browsers unless it is also served over HTTPS.
			 */
			$url = set_url_scheme( $url, 'https' );
		}

		return $url;
	}

	/**
	 * It allows you to search for posts in the modal search box
	 *
	 * @param array $args .
	 *
	 * @return array
	 */
	public function lp_wpml_modal_search_items_args( array $args ): array {
		$args['suppress_filters'] = false;

		return $args;
	}
}

LP_Addon_WPML_Hooks::instance();
