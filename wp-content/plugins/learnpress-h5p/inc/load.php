<?php
/**
 * Plugin load class.
 *
 * @author   ThimPress
 * @package  LearnPress/H5p/Classes
 * @version  4.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LP_Addon_H5p' ) ) {
	/**
	 * Class LP_Addon_Assignment
	 */
	class LP_Addon_H5p extends LP_Addon {

		/**
		 * Addon version
		 *
		 * @var string
		 */
		public $version = LP_ADDON_H5P_VER;

		/**
		 * @var int flag to get the error
		 */
		protected static $_error = 0;

		/**
		 * Require LP version
		 *
		 * @var string
		 */
		public $require_version = LP_ADDON_H5P_REQUIRE_VER;

		/**
		 * Path file addon
		 *
		 * @var string
		 */
		public $plugin_file = LP_ADDON_H5P_FILE;

		/**
		 * LP_Addon_Assignment constructor.
		 */
		public function __construct() {
			parent::__construct();

			add_action( 'admin_enqueue_scripts', array( $this, 'lp_h5p_admin_assets' ), 20 );
			add_action( 'wp_enqueue_scripts', array( $this, 'lp_h5p_enqueue_scripts' ) );
			add_action( 'init', array( $this, 'lp_h5p_init' ) );
			add_filter( 'learn-press/course-support-items', array( $this, 'lp_h5p_put_type_here' ), 10, 2 );
			add_filter( 'learn-press/new-section-item-data', array( $this, 'new_h5p_item' ), 10, 4 );
			add_filter( 'learn-press/course-item-object-class', array( $this, 'h5p_object_class' ), 10, 4 );
			add_filter( 'learn-press/modal-search-items/exclude', array( $this, 'exclude_h5p_items' ), 10, 4 );
			// update h5p item in single course template
			add_filter( 'learn_press_locate_template', array( $this, 'update_h5p_template' ), 10, 2 );
			add_filter( 'learn-press/can-view-item', array( $this, 'learnpress_h5p_can_view_item' ), 10, 4 );
			add_filter(
				'learn-press/evaluate_passed_conditions',
				array( $this, 'learnpress_h5p_evaluate' ),
				10,
				3
			);
			add_filter( 'learn-press/get-course-item', array( $this, 'learnpress_h5p_get_item' ), 10, 3 );
			add_filter(
				'learn-press/default-user-item-status',
				array(
					$this,
					'learnpress_h5p_default_user_item_status',
				),
				10,
				2
			);
			add_filter(
				'learn-press/user-item-object',
				array(
					$this,
					'learnpress_h5p_user_item_object',
				),
				10,
				2
			);
			add_filter(
				'learn-press/course-item-type',
				array(
					$this,
					'learnpress_h5p_course_item_type',
				),
				10,
				1
			);
			add_filter(
				'learn-press/block-course-item-types',
				array(
					$this,
					'learnpress_h5p_block_course_item_type',
				),
				10,
				1
			);
			// get grade
			add_filter( 'learn-press/user-item-grade', array( $this, 'learnpress_h5p_get_grade' ), 10, 4 );
			// AJAX for logging results
			add_action( 'wp_ajax_lph5p_process', array( $this, 'lph5p_process' ) );
			// add filter user access admin view assignment
			add_filter( 'learn-press/filter-user-access-types', array( $this, 'lp_h5p_add_filter_access' ) );
			// add user profile page tabs
			add_filter( 'learn-press/profile-tabs', array( $this, 'lp_h5p_add_profile_tabs' ) );
			// add profile setting publicity fields
			add_filter( 'learn-press/get-publicity-setting', array( $this, 'lp_h5p_add_publicity_setting' ) );
			// check profile setting publicity fields
			add_filter(
				'learn-press/check-publicity-setting',
				array( $this, 'lp_h5p_check_publicity_setting' ),
				10,
				2
			);
			// add user profile page setting publicity fields
			add_action(
				'learn-press/end-profile-publicity-fields',
				array(
					$this,
					'lp_h5p_add_profile_publicity_fields',
				)
			);

			// add user profile page setting publicity in LP4.
			add_action(
				'learn-press/profile-privacy-settings',
				function ( $privacy ) {
					$privacy[] = array(
						'name'        => esc_html__( 'H5P', 'learnpress' ),
						'id'          => 'h5p',
						'default'     => 'no',
						'type'        => 'checkbox',
						'description' => esc_html__( 'Public your profile H5P items', 'learnpress' ),
					);

					return $privacy;
				}
			);

			add_filter( 'learn-press/item/to_array', array( $this, 'lp_h5p_get_more_data' ), 10, 1 );
			add_action( 'learn-press/course-item-slugs/for-rewrite-rules', array( $this, 'add_item_slug_for_rewrite_rules' ), 10, 1 );
			add_filter( 'learn-press/course/custom-item-prefixes', [ $this, 'h5p_slug_link' ], 10, 2 );
		}

		public function lp_h5p_get_more_data( $item ) {
			if ( $item['type'] == LP_H5P_CPT ) {
				$item['interacted_h5p'] = get_post_meta( $item['id'], '_lp_h5p_interact', true );

				if ( $item['interacted_h5p'] ) {
					$selected_option = '<option value="' . $item['interacted_h5p'] . '"  selected="selected">' . lp_h5p_get_content_title( $item['interacted_h5p'] ) . '</option>';
				} else {
					$selected_option = '';
				}

				$item['element_name']    = 'select_h5p_lp_' . $item['id'];
				$item['interacted_html'] = '<option value="" >' . __(
					'Select a H5P item',
					'learnpress-h5p'
				) . '</option>' . $selected_option;
			}

			return $item;
		}

		/**
		 * @param $profile LP_Profile
		 *
		 * not use in LP4
		 */
		public function lp_h5p_add_profile_publicity_fields( $profile ) {
			if ( LearnPress::instance()->settings()->get( 'profile_publicity.h5p' ) === 'yes' ) { ?>
				<li class="form-field">
					<label for="my-h5p"><?php _e( 'My H5P items', 'learnpress-h5p' ); ?></label>
					<div class="form-field-input">
						<input name="publicity[h5p]" value="yes" type="checkbox"
							id="my-h5p" <?php checked( $profile->get_publicity( 'h5p' ), 'yes' ); ?>/>
						<p class="description"><?php _e( 'Public your profile H5P items', 'learnpress-h5p' ); ?></p>
					</div>
				</li>
				<?php
			}
		}

		/**
		 * @param $publicities
		 * @param $profile LP_Profile
		 *
		 * @return mixed
		 */
		public function lp_h5p_check_publicity_setting( $publicities, $profile ) {
			$publicities['view-tab-h5p'] = $profile->get_publicity( 'h5p' ) == 'yes';

			return $publicities;
		}

		/**
		 * @param $settings
		 *
		 * @return mixed
		 */
		public function lp_h5p_add_publicity_setting( $settings ) {
			$settings['h5p'] = LearnPress::instance()->settings()->get( 'profile_publicity.h5p' );

			return $settings;
		}

		/**
		 * Add user profile tabs.
		 *
		 * @param $tabs
		 *
		 * @return mixed
		 */
		public function lp_h5p_add_profile_tabs( $tabs ) {
			$settings = LearnPress::instance()->settings();

			$tabs['h5p'] = array(
				'title'    => esc_html__( 'H5P Items', 'learnpress-h5p' ),
				'slug'     => $settings->get( 'profile_endpoints.profile-h5p', 'h5p' ),
				'callback' => array( $this, 'tab_h5p' ),
				'icon'     => '<i class="fas fa-file-code"></i>',
				'priority' => 25,
			);

			return $tabs;
		}

		public function tab_h5p() {
			learn_press_h5p_get_template( 'profile/tabs/h5p_items.php' );
		}

		/**
		 * @param $types
		 *
		 * @return array
		 */
		public function lp_h5p_add_filter_access( $types ) {
			$types[] = LP_H5P_CPT;

			return $types;
		}

		/**
		 *
		 */
		public function lp_h5p_init() {
			$actions = array(
				'complete-h5p' => 'complete_h5p',
			);

			foreach ( $actions as $action => $function ) {
				LP_Request::register_ajax( $action, array( __CLASS__, $function ) );
				LP_Request::register( "lp-{$action}", array( __CLASS__, $function ) );
			}
		}

		public static function complete_h5p() {
			$course_id = LP_Request::get_int( 'course-id' );
			$h5p_id    = LP_Request::get_int( 'h5p-id' );
			$user      = learn_press_get_current_user();
			$result    = array(
				'message'  => '',
				'result'   => esc_html__( 'Success', 'learnpress-h5p' ),
				'redirect' => learn_press_get_current_url(),
			);

			$course_data = $user->get_course_data( $course_id );
			if ( ! $course_data ) {
				$result['result']  = esc_html__( 'Error', 'learnpress-h5p' );
				$result['message'] = esc_html__( 'You did not enroll course', 'learnpress-h5p' );

				learn_press_maybe_send_json( $result );

				if ( ! empty( $result['redirect'] ) ) {
					wp_redirect( $result['redirect'] );
					exit();
				}

				return;
			}

			$course_item = $course_data->get_item( $h5p_id );
			if ( ! $course_item ) {
				$result['result']  = esc_html__( 'Error', 'learnpress-h5p' );
				$result['message'] = esc_html__( 'You did not enroll course', 'learnpress-h5p' );

				learn_press_maybe_send_json( $result );

				if ( ! empty( $result['redirect'] ) ) {
					wp_redirect( $result['redirect'] );
					exit();
				}

				return;
			}

			$user_item_id = $course_item->get_user_item_id();
			$h5p          = LP_H5p::get_h5p( $h5p_id );
			$score        = learn_press_get_user_item_meta( $user_item_id, 'score', true );
			$max_score    = learn_press_get_user_item_meta( $user_item_id, 'max_score', true );
			$mark         = floatval( $score / $max_score ) * 100;

			learn_press_update_user_item_field(
				array( 'graduation' => $mark >= $h5p->get_data( 'passing_grade' ) ? 'passed' : 'failed' ),
				array( 'user_item_id' => $user_item_id )
			);

			learn_press_update_h5p_item( $h5p_id, $course_id, $user, 'completed', $user_item_id );
			$result['message'] = esc_html__( 'Congratulation! You completed this!', 'learnpress-h5p' );

			learn_press_maybe_send_json( $result );

			if ( function_exists( 'learn_press_evaluate_course_results' ) ) {
				learn_press_evaluate_course_results( $h5p_id, $course_id, $user->get_id() );
			}

			if ( ! empty( $result['redirect'] ) ) {
				wp_redirect( $result['redirect'] );
				exit();
			}
		}

		function lph5p_process() {
			$content_id = filter_input( INPUT_POST, 'contentId', FILTER_VALIDATE_INT );
			$data_meta  = array(
				'score'     => filter_input( INPUT_POST, 'score', FILTER_VALIDATE_INT ),
				'max_score' => filter_input( INPUT_POST, 'maxScore', FILTER_VALIDATE_INT ),
			);
			$item_id    = filter_input( INPUT_POST, 'item_id', FILTER_VALIDATE_INT );
			$course_id  = filter_input( INPUT_POST, 'course_id', FILTER_VALIDATE_INT );
			$user       = learn_press_get_current_user();

			$result = array( 'result' => 'success' );

			try {
				$data = learn_press_h5p_start( $user, $item_id, $course_id, 'start', $data_meta, true );

				if ( is_wp_error( $data ) ) {
					throw new Exception( $data->get_error_message() );
				} else {
					$h5p                = LP_H5p::get_h5p( $item_id );
					$conditional_grade  = $h5p->get_data( 'passing_grade' );
					$result['result']   = floatval( $data_meta['score'] / $data_meta['max_score'] ) * 100 >= $conditional_grade ? 'reached' : 'not_reached';
					$result['reload']   = $data_meta['score'] == $data_meta['max_score'] ? 1 : 0;
					$result['redirect'] = LP_Helper::getUrlCurrent();
				}
			} catch ( Exception $ex ) {
				$result['message']  = $ex->getMessage();
				$result['result']   = 'error';
				$result['redirect'] = LP_Helper::getUrlCurrent();
			}

			learn_press_maybe_send_json( $result );

			if ( ! empty( $result['redirect'] ) ) {
				H5PCore::ajaxSuccess( $result );
				exit();
			}
		}

		/**
		 * Include files.
		 */
		protected function _includes() {
			require_once LP_ADDON_H5P_INC_PATH . 'custom-post-types' . DIRECTORY_SEPARATOR . 'metabox.php';
			require_once LP_ADDON_H5P_INC_PATH . 'custom-post-types' . DIRECTORY_SEPARATOR . 'h5pcontent.php';
			require_once LP_ADDON_H5P_INC_PATH . 'class-lp-h5p-curd.php';
			require_once LP_ADDON_H5P_INC_PATH . 'class-lp-h5p.php';
			require_once LP_ADDON_H5P_INC_PATH . 'functions.php';
			//require_once LP_ADDON_H5P_INC_PATH . 'lp-h5p-template-functions.php';
			require_once LP_ADDON_H5P_INC_PATH . 'lp-h5p-template-hooks.php';
			require_once LP_ADDON_H5P_INC_PATH . 'user-item/class-lp-user-item-h5p.php';
		}

		public function learnpress_h5p_get_grade( $grade, $item_id, $user_id, $course_id ) {
			if ( LP_H5P_CPT == get_post_type( $item_id ) ) {
				$result = learn_press_h5p_get_result( $item_id, $user_id, $course_id );
				$grade  = isset( $result['grade'] ) ? $result['grade'] : false;
			}

			return $grade;
		}

		public function learnpress_h5p_block_course_item_type( $types ) {
			$types[] = LP_H5P_CPT;

			return $types;
		}

		public function learnpress_h5p_course_item_type( $item_types ) {
			$item_types[] = 'lp_h5p';

			return $item_types;
		}

		public function learnpress_h5p_user_item_object( $item, $data ) {
			if ( is_array( $data ) && isset( $data['item_id'] ) ) {
				$item_id = $data['item_id'];
			} elseif ( is_object( $data ) && isset( $data->item_id ) ) {
				$item_id = $data->item_id;
			} elseif ( is_numeric( $data ) ) {
				$item_id = absint( $data );
			} elseif ( $data instanceof LP_User_Item ) {
				$item_id = $data->get_id();
			}

			if ( LP_H5P_CPT == get_post_type( $item_id ) ) {
				$item = new LP_User_Item( $data );
			}

			return $item;
		}

		/**
		 * @param        $exclude
		 * @param        $type
		 * @param string  $context
		 * @param null    $context_id
		 *
		 * @return array
		 */
		public function exclude_h5p_items( $exclude, $type, $context = '', $context_id = null ) {
			if ( $type != 'lp_h5p' ) {
				return $exclude;
			}

			global $wpdb;

			$used_items = array();

			$query = $wpdb->prepare(
				"
						SELECT item_id
						FROM {$wpdb->prefix}learnpress_section_items si
						INNER JOIN {$wpdb->prefix}learnpress_sections s ON s.section_id = si.section_id
						INNER JOIN {$wpdb->posts} p ON p.ID = s.section_course_id
						WHERE %d
						AND p.post_type = %s
					",
				1,
				LP_COURSE_CPT
			);

			$used_items = $wpdb->get_col( $query );

			if ( $used_items && $exclude ) {
				$exclude = array_merge( $exclude, $used_items );
			} elseif ( $used_items ) {
				$exclude = $used_items;
			}

			return is_array( $exclude ) ? array_unique( $exclude ) : array();
		}

		/**
		 * @param $item
		 * @param $args
		 *
		 * @return int|WP_Error
		 */
		public function new_h5p_item( $item_id, $item, $args, $course_id ) {
			if ( $item['type'] == LP_H5P_CPT ) {
				$h5p_curd = new LP_H5p_CURD();
				$item_id  = $h5p_curd->create( $args );
			}

			return $item_id;
		}

		/**
		 * @param $status
		 * @param $type
		 * @param $item_type
		 * @param $item_id
		 *
		 * @return string
		 */
		public function h5p_object_class( $status, $type, $item_type, $item_id ) {
			$status['h5p'] = 'LP_H5p';

			return $status;
		}

		/**
		 * @param $types
		 * @param $key
		 *
		 * @return array
		 */
		public function lp_h5p_put_type_here( $types, $key ) {
			if ( $key ) {
				$types[] = 'lp_h5p';
			} else {
				$types['lp_h5p'] = esc_html__( 'H5P Item', 'learnpress-h5p' );
			}

			return $types;
		}

		/**
		 * Define constants.
		 */
		protected function _define_constants() {
			define( 'LP_ADDON_H5P_TEMPLATE', LP_ADDON_H5P_PATH . '/templates/' );
			define( 'LP_INVALID_H5P_OR_COURSE', 270 );
			define( 'LP_H5P_HAS_STARTED_OR_COMPLETED', 280 );
			define( 'LP_H5P_CPT', 'lp_h5p' );
		}

		/**
		 *
		 */
		public function lp_h5p_enqueue_scripts() {
			if ( function_exists( 'learn_press_is_course' ) && learn_press_is_course() ) {
				wp_enqueue_style( 'learn-press-h5p', plugins_url( '/assets/css/h5p.css', LP_ADDON_H5P_FILE ) );
				wp_enqueue_script(
					'learn-press-h5p',
					plugins_url( '/assets/js/lph5p.js', LP_ADDON_H5P_FILE ),
					array(
						'jquery',
						'plupload-all',
						'h5p-core-js-jquery',
						'h5p-core-js-h5p',
					)
				);

				$scripts = learn_press_assets();
				$scripts->add_script_data( 'learn-press-h5p', learn_press_h5p_single_args() );
			}
		}

		/**
		 * Admin asset and localize script.
		 */
		public function lp_h5p_admin_assets() {
			// TODO: add css and js
			wp_enqueue_style( 'learn-press-h5p', plugins_url( '/assets/css/admin-h5p.css', LP_ADDON_H5P_FILE ) );

			if ( LP_Request::get( 'post_type' ) == 'lp_h5p' ) {
				wp_enqueue_style(
					'learn-press-h5p-edit',
					plugins_url( '/assets/css/admin-edit-h5p.css', LP_ADDON_H5P_FILE )
				);
			}
		}

		/**
		 * Update single course h5p item template files.
		 *
		 * @param $located
		 * @param $template_name
		 *
		 * @return mixed|string
		 */
		public function update_h5p_template( $located, $template_name ) {
			if ( $template_name == 'single-course/section/item-h5p.php' ) {
				$located = learn_press_h5p_locate_template( 'single-course/section/item-h5p.php' );
			} elseif ( $template_name == 'single-course/content-item-lp_h5p.php' ) {
				$located = learn_press_h5p_locate_template( 'single-course/content-item-lp_h5p.php' );
			}

			return $located;
		}

		public function learnpress_h5p_can_view_item( $return, $item_id, $userid, $course_id ) {
			if ( get_post_type( $item_id ) == 'lp_h5p' ) {

				$return = learn_press_can_view_h5p( $item_id, $course_id, $userid );
			}

			return $return;
		}

		/**
		 * @param $results
		 * @param string $evaluate_type
		 * @param $user_course
		 *
		 * @return array|bool|int|mixed
		 */
		public function learnpress_h5p_evaluate( $results, string $evaluate_type, $user_course ) {
			if ( $user_course->is_finished() ) {
				return false;
			}
			switch ( $evaluate_type ) {
				case 'evaluate_h5p_items':
					$results = _evaluate_course_by_h5p_items( $user_course );
					break;
				case 'evaluate_h5p_passed_items':
					$results = _evaluate_course_by_passed_h5p_items( $user_course );
					break;
				case 'evaluate_h5p_quizz_passed_items':
					$results = _evaluate_course_by_passed_h5p_quizzes_items( $user_course );
					break;
				default:
					break;
			}

			return $results;
		}

		/**
		 * @param $item
		 * @param $item_type
		 * @param $item_id
		 *
		 * @return bool|LP_H5p
		 */
		public function learnpress_h5p_get_item( $item, $item_type, $item_id ) {
			if ( LP_H5P_CPT === $item_type ) {
				$item = LP_H5p::get_h5p( $item_id );
			}

			return $item;
		}

		public function learnpress_h5p_default_user_item_status( $status, $item_id ) {
			if ( get_post_type( $item_id ) === LP_H5P_CPT ) {
				$status = 'viewed';
			}

			return $status;
		}

		/**
		 * Add rewrite rules for single assignment.
		 * To compatible with LP v4.2.2.2 and higher.
		 *
		 * @param $item_slugs array
		 * @since 4.0.3
		 * @return array
		 */
		public function add_item_slug_for_rewrite_rules( array $item_slugs = array() ) {
			$item_slugs[ LP_H5P_CPT ] = urldecode( sanitize_title_with_dashes( LP_Settings::get_option( 'h5p_slug', 'h5p' ) ) );

			return $item_slugs;
		}

		/**
		 * Set slug link item when view on frontend.
		 *
		 * @param $custom_prefixes
		 * @param $course_id
		 *
		 * @since 4.0.3
		 * @version 1.0.0
		 * @return mixed
		 */
		public function h5p_slug_link( $custom_prefixes, $course_id ) {
			$custom_prefixes[ LP_H5P_CPT ] = sanitize_title_with_dashes( LP_Settings::get_option( 'h5p_slug', 'h5p' ) );

			return $custom_prefixes;
		}
	}

}
