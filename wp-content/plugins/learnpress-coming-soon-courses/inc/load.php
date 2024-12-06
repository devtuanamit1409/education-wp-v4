<?php
/**
 * Plugin load class.
 *
 * @author   ThimPress
 * @package  LearnPress/Coming-Soon-Courses/Classes
 * @version  3.0.0
 */

use LP_Addon_Coming_Soon\Elementor\ComingSoonElementorHandler;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LP_Addon_Coming_Soon_Courses' ) ) {
	/**
	 * Class LP_Addon_Coming_Soon_Courses.
	 */
	class LP_Addon_Coming_Soon_Courses extends LP_Addon {

		/**
		 * Hold the course ids is coming soon
		 *
		 * @var array
		 */
		protected $_coming_soon_courses = array();

		/**
		 * @var null
		 */
		protected $_course_coming_soon = null;

		/**
		 * @var string
		 */
		public $version = LP_ADDON_COMING_SOON_COURSES_VER;

		/**
		 * @var string
		 */
		public $require_version = LP_ADDON_COMING_SOON_COURSES_REQUIRE_VER;

		/**
		 * Path file addon .
		 *
		 * @var string
		 */
		public $plugin_file = LP_ADDON_COMING_SOON_COURSES_FILE;


		/**
		 * LP_Addon_Coming_Soon_Courses constructor.
		 */
		public function __construct() {
			parent::__construct();
		}

		/**
		 * Define constants.
		 */
		protected function _define_constants() {
			define( 'LP_ADDON_COMING_SOON_COURSES_PATH', dirname( LP_ADDON_COMING_SOON_COURSES_FILE ) );
			define( 'LP_ADDON_COMING_SOON_COURSES_TEMP', LP_ADDON_COMING_SOON_COURSES_PATH . '/templates/' );
		}

		/**
		 * Includes files.
		 */
		protected function _includes() {
			require_once LP_ADDON_COMING_SOON_COURSES_PATH . '/inc/functions.php';
			if ( is_plugin_active( 'elementor/elementor.php' ) ) {
				require_once LP_ADDON_COMING_SOON_COURSES_PATH . '/inc/Elementor/ComingSoonElementorHandler.php';
				ComingSoonElementorHandler::instance();
			}
		}

		/**
		 * Init hooks.
		 */
		protected function _init_hooks() {
			add_action( 'learn-press/course-content-summary', array( $this, 'coming_soon_message' ), 10 );
			add_action( 'learn-press/course-content-summary', array( $this, 'coming_soon_countdown' ), 10 );
			add_action( 'template_redirect', array( $this, 'coming_soon_option_v4' ), 10 );
			remove_action(
				'learn-press/course-meta-secondary-left',
				LearnPress::instance()->template( 'course' )->callback( 'single-course/meta/duration' ),
				10
			);

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
			// Check can purchase course via product of Woo (Assign courses to product feature)
			add_filter( 'learnpress/wc-order/can-purchase-product', array( $this, 'can_purchase_product_has_courses' ), 10, 2 );
			// Check can purchase course
			add_filter( 'learn-press/user/can-purchase-course', array( $this, 'can_purchase_course' ), 20, 3 );

			add_filter( 'learnpress/course/metabox/tabs', array( $this, 'add_course_metabox' ), 10, 2 );
		}

		public function add_course_metabox( $data, $post_id ) {
			$data['course_coming_soon'] = array(
				'label'    => esc_html__( 'Coming soon', 'learnpress-collections' ),
				'icon'     => 'dashicons-clock',
				'target'   => 'lp_coming_soon_course_data',
				'priority' => 60,
				'content'  => array(
					'_lp_coming_soon'           => new LP_Meta_Box_Checkbox_Field(
						esc_html__( 'Enable', 'learnpress-coming-soon-courses' ),
						esc_html__( 'Enable coming soon mode.', 'learnpress-coming-soon-courses' ),
						'no'
					),
					'_lp_coming_soon_msg'       => new LP_Meta_Box_Textarea_Field(
						esc_html__( 'Message', 'learnpress-coming-soon-courses' ),
						esc_html__( 'Message to display when course is in coming soon mode.', 'learnpress-coming-soon-courses' ),
						esc_html__( 'This course will be coming soon', 'learnpress-coming-soon-courses' ),
						array(
							'show' => array( '_lp_coming_soon', '=', 'yes' ),
						)
					),
					'_lp_coming_soon_end_time'  => new LP_Meta_Box_Date_Field(
						esc_html__( 'End time', 'learnpress-coming-soon-courses' ),
						esc_html__( 'End time of coming soon mode.', 'learnpress-coming-soon-courses' ),
						'',
						array(
							'show'          => array( '_lp_coming_soon', '=', 'yes' ),
							'wrapper_class' => '',
							'placeholder'   => _x( 'Date', 'placeholder', 'learnpress-coming-soon-courses' ),
						)
					),
					'_lp_coming_soon_countdown' => new LP_Meta_Box_Checkbox_Field(
						esc_html__( 'Countdown', 'learnpress-coming-soon-courses' ),
						esc_html__( 'Enable countdown.', 'learnpress-coming-soon-courses' ),
						'no',
						array(
							'show' => array( '_lp_coming_soon', '=', 'yes' ),
						)
					),
					'_lp_coming_soon_showtext'  => new LP_Meta_Box_Checkbox_Field(
						esc_html__( 'Show date time text', 'learnpress-coming-soon-courses' ),
						esc_html__( 'Show date and time text (days, hours, minutes, seconds) on single course page.', 'learnpress-coming-soon-courses' ),
						'no',
						array(
							'show' => array( '_lp_coming_soon', '=', 'yes' ),
						)
					),
					'_lp_coming_soon_metadata'  => new LP_Meta_Box_Checkbox_Field(
						esc_html__( 'Show metadata', 'learnpress-coming-soon-courses' ),
						esc_html__( 'Show meta data (such as info about Instructor, price, so on) of the course.', 'learnpress-coming-soon-courses' ),
						'no',
						array(
							'show' => array( '_lp_coming_soon', '=', 'yes' ),
						)
					),
					'_lp_coming_soon_details'   => new LP_Meta_Box_Checkbox_Field(
						esc_html__( 'Show details', 'learnpress-coming-soon-courses' ),
						esc_html__( 'Show details content of the course.', 'learnpress-coming-soon-courses' ),
						'no',
						array(
							'show' => array( '_lp_coming_soon', '=', 'yes' ),
						)
					),
				),
			);

			return $data;
		}

		/**
		 * Check can purchase product has courses - LP Woo
		 *
		 * @param bool $can_purchase
		 * @param int  $course_id
		 * @return bool
		 * @since 4.0.3
		 * @editor minhpd
		 */
		public function can_purchase_product_has_courses( $can_purchase, $course_id ) {
			if ( $this->is_coming_soon( $course_id ) ) {
				$can_purchase = false;
			}

			return $can_purchase;
		}

		/**
		 * Filer user can purchase course when show button by course is comingsoon
		 *
		 * @param bool $can_purchase
		 * @param int  $user_id
		 * @param int  $course_id
		 *
		 * @return bool
		 * @since 4.0.0
		 * @editor minhpd
		 */
		public function can_purchase_course( $can_purchase, $user_id, $course_id ) {
			if ( $this->is_coming_soon( $course_id ) ) {
				$can_purchase = false;
			}

			return $can_purchase;
		}

		/**
		 * @return string
		 */
		public function get_plugin_file() {
			return $this->plugin_file;
		}

		/**
		 * @param string $plugin_file
		 */
		public function coming_soon_option_v4() {

			$course = learn_press_get_course();

			if ( ! $course ) {
				return;
			}

			$course_id = $course->get_id();

			$check_coming_soon          = get_post_meta( $course_id, '_lp_coming_soon', true );
			$check_coming_soon_metadata = get_post_meta( $course_id, '_lp_coming_soon_metadata', true );
			$check_coming_soon_details  = get_post_meta( $course_id, '_lp_coming_soon_details', true );
			if ( empty( $check_coming_soon ) || $check_coming_soon == 'no' ) {
				return;
			}
			if ( $this->is_coming_soon( $course_id ) ) {
				remove_all_actions( 'learn-press/course-buttons' );
				if ( $check_coming_soon_metadata == 'no' ) {
					// Check conditions before execution
					remove_action(
						'learn-press/course-meta-secondary-left',
						LearnPress::instance()->template( 'course' )->func( 'count_object' ),
						20
					);
					remove_action(
						'learn-press/course-content-summary',
						LearnPress::instance()->template( 'course' )->func( 'course_extra_boxes' ),
						40
					);
					add_action( 'learn_press_course_price_html', array( $this, 'set_course_price_html_empty' ) );
					LearnPress::instance()->template( 'course' )->remove_callback(
						'learn-press/course-meta-secondary-left',
						'single-course/meta/level',
						20
					);
					LearnPress::instance()->template( 'course' )->remove_callback(
						'learn-press/course-meta-secondary-left',
						'single-course/meta/category',
						20
					);
					LearnPress::instance()->template( 'course' )->remove_callback(
						'learn-press/course-meta-secondary-left',
						'single-course/meta/instructor',
						20
					);
					LearnPress::instance()->template( 'course' )->remove_callback(
						'learn-press/course-meta-secondary-left',
						'single-course/meta/duration',
						10
					);
					LearnPress::instance()->template( 'course' )->remove_callback(
						'learn-press/course-meta-primary-left',
						'single-course/meta/category',
						20
					);

					// Remove add_filter group
					add_filter(
						'learn-press/course-tabs',
						function ( $defaults ) {
							unset( $defaults['instructor'] );

							return $defaults;
						},
						10,
						1
					);

					add_filter(
						'learn-press/course-tabs',
						function ( $defaults ) {
							unset( $defaults['faqs'] );

							return $defaults;
						},
						10,
						1
					);
				}

				if ( $check_coming_soon_details == 'no' ) {
					// Remove add_filter group
					add_filter(
						'learn-press/course-tabs',
						function ( $defaults ) {
							unset( $defaults['overview'] );
							unset( $defaults['curriculum'] );

							return $defaults;
						},
						10,
						1
					);
				}
			}
		}

		/**
		 * Assets.
		 */
		public function enqueue_scripts() {
			wp_register_style( 'lp-coming-soon-course', $this->get_plugin_url( 'assets/css/coming-soon-course.css' ) );
			wp_register_script( 'lp-jquery-mb-coming-soon', $this->get_plugin_url( 'assets/js/jquery.mb-coming-soon.min.js' ), array( 'jquery' ) );
			wp_register_script( 'lp-coming-soon-course', $this->get_plugin_url( 'assets/js/coming-soon-course.js' ) );

			$translation_array = array(
				'days'    => esc_html__( 'days', 'learnpress-coming-soon-courses' ),
				'hours'   => esc_html__( 'hours', 'learnpress-coming-soon-courses' ),
				'minutes' => esc_html__( 'minutes', 'learnpress-coming-soon-courses' ),
				'seconds' => esc_html__( 'seconds', 'learnpress-coming-soon-courses' ),
			);

			if ( LP_PAGE_SINGLE_COURSE === LP_Page_Controller::page_current() ) {
				$course = learn_press_get_course();

				if ( ! $course ) {
					return;
				}

				if ( ! $this->is_coming_soon( $course->get_id() ) ) {
					return;
				}

				wp_enqueue_style( 'lp-coming-soon-course' );
				wp_enqueue_script( 'lp-jquery-mb-coming-soon' );
				wp_enqueue_script( 'lp-coming-soon-course' );

				wp_localize_script( 'lp-coming-soon-course', 'lp_coming_soon_translation', $translation_array );

			} elseif ( LP_PAGE_COURSES === LP_Page_Controller::page_current() ) {
				wp_enqueue_style( 'lp-coming-soon-course' );
				wp_enqueue_script( 'lp-jquery-mb-coming-soon' );
				wp_enqueue_script( 'lp-coming-soon-course' );

				$all_courses = learn_press_get_all_courses();
				if ( count( $all_courses ) ) {
					foreach ( $all_courses as $course_item ) {
						if ( learn_press_is_coming_soon( $course_item ) ) {
							wp_localize_script(
								'lp-coming-soon-course',
								'lp_coming_soon_translation',
								$translation_array
							);
							break;
						}
					}
				}
			}
		}

		public function admin_enqueue_scripts() {
			$screen    = get_current_screen();
			$screen_id = $screen ? $screen->id : '';

			if ( in_array( $screen_id, array( 'lp_course', 'edit-lp_course' ) ) ) {
				wp_enqueue_style( 'lp-coming-soon-date-style', $this->get_plugin_url( 'assets/css/jquery.datetimepicker.min.css' ) );
				wp_enqueue_script( 'lp-coming-soon-date-script', $this->get_plugin_url( 'assets/js/jquery.datetimepicker.full.min.js' ) );
				wp_enqueue_script( 'lp-coming-soon-admin-script', $this->get_plugin_url( 'assets/js/admin.js' ) );
				wp_enqueue_style( 'lp-coming-soon-admin-style', $this->get_plugin_url( 'assets/css/admin.css' ) );
			}
		}

		/**
		 * @param $located
		 * @param $template_name
		 * @param $args
		 * @param $template_path
		 * @param $default_path
		 *
		 * @return string
		 */
		public function change_default_template( $located, $template_name, $args, $template_path, $default_path ) {
			remove_filter( 'learn_press_get_template', array( $this, 'change_default_template' ), 100, 5 );
			if ( $template_name == 'content-single-course.php' ) {
				$course = learn_press_get_course();
				if ( $course ) {
					$course_id = $course->get_id();
					if ( $this->is_coming_soon( $course_id ) ) {
						$located = learn_press_coming_soon_course_locate_template( $template_name );
					}
				}
			}
			add_filter( 'learn_press_get_template', array( $this, 'change_default_template' ), 100, 5 );

			return $located;
		}

		/**
		 * @param $template
		 * @param $slug
		 * @param $name
		 *
		 * @return string
		 */
		public function change_content_course_template( $template, $slug, $name ) {
			if ( $slug == 'content' && $name == 'course' ) {
				$course    = learn_press_get_course();
				$course_id = $course->get_id();
				if ( $this->is_coming_soon( $course_id ) ) {
					remove_filter(
						'learn_press_get_template_part',
						array(
							$this,
							'change_content_course_template',
						),
						100,
						3
					);
					$template = learn_press_coming_soon_course_locate_template( 'content-course.php' );
					add_filter(
						'learn_press_get_template_part',
						array(
							$this,
							'change_content_course_template',
						),
						100,
						3
					);
				}
			}

			return $template;
		}


		/**
		 * Display coming soon message
		 */
		public function coming_soon_message() {
			$course    = learn_press_get_course();
			$course_id = $course->get_id();
			$message   = get_post_meta(
				$course_id,
				'_lp_coming_soon_msg',
				true
			);
			if ( $this->is_coming_soon( $course_id ) && '' !== $message ) {
				// enable shortcode in coming message
				$message = do_shortcode( $message );
				LP_Addon_Coming_Soon_Courses_Preload::$addon->get_template(
					'single-course/message.php',
					array( 'message' => $message )
				);
			}
		}

		/**
		 * Display coming soon countdown
		 */
		public function coming_soon_countdown() {
			$course    = learn_press_get_course();
			$course_id = $course->get_id();
			if ( get_post_meta( $course_id, '_lp_coming_soon', true ) != 'yes' ) {
				return;
			}
			$end_time = $this->get_coming_soon_end_time( $course_id, 'Y-m-d H:i:s' );
			$datetime = new DateTime( $end_time );
			$timezone = get_option( 'gmt_offset' );
			$showtext = get_post_meta( $course_id, '_lp_coming_soon_showtext', true );
			LP_Addon_Coming_Soon_Courses_Preload::$addon->get_template(
				'single-course/countdown.php',
				array(
					'datetime' => $datetime,
					'timezone' => $timezone,
					'showtext' => $showtext,
				)
			);
		}

		/**
		 * /**
		 * Check all options and return TRUE if a course has 'Coming Soon'
		 *
		 * @param int $course_id
		 *
		 * @return mixed
		 */
		public function is_coming_soon( $course_id = 0 ) {
			if ( ! $course_id && LP_COURSE_CPT == get_post_type() ) {
				$course_id = get_the_ID();
			}
			if ( empty( $this->_coming_soon_courses[ $course_id ] ) ) {
				$this->_coming_soon_courses[ $course_id ] = false;
				if ( $this->is_enable_coming_soon( $course_id ) ) {
					$end_time     = $this->get_coming_soon_end_time( $course_id );
					$current_time = current_time( 'timestamp' );

					if ( $end_time == 0 || $end_time > $current_time ) {
						$this->_coming_soon_courses[ $course_id ] = true;
					}
				}
			}

			return $this->_coming_soon_courses[ $course_id ];
		}

		/**
		 * Return TRUE if 'Coming Soon' is enabled
		 *
		 * @param int $course_id
		 *
		 * @return bool
		 */
		public function is_enable_coming_soon( $course_id = 0 ): bool {
			if ( ! $course_id && LP_COURSE_CPT == get_post_type() ) {
				$course_id = get_the_ID();
			}

			return 'yes' == get_post_meta( $course_id, '_lp_coming_soon', true );
		}

		/**
		 * Return expiration time of 'Coming Soon'
		 *
		 * @param int    $course_id
		 * @param string
		 *
		 * @return int
		 */
		public function get_coming_soon_end_time( $course_id = 0, $format = 'timestamp' ) {
			if ( ! $course_id && LP_COURSE_CPT == get_post_type() ) {
				$course_id = get_the_ID();
			}
			$end_time = 0;
			if ( $this->is_enable_coming_soon( $course_id ) ) {
				$end_time           = get_post_meta( $course_id, '_lp_coming_soon_end_time', true );
				$current_time       = time();
				$end_time_timestamp = strtotime( $end_time, $current_time );
				if ( $format == 'timestamp' ) {
					$end_time = $end_time_timestamp;
				} elseif ( $format ) {
					$end_time = gmdate( $format, $end_time_timestamp );
				}
			}

			return $end_time;
		}

		/**
		 * Return TRUE if a course is enabled countdown
		 *
		 * @param int $course_id
		 *
		 * @return bool
		 */
		public function is_show_coming_soon_countdown( $course_id = 0 ): bool {
			if ( ! $course_id && LP_COURSE_CPT == get_post_type() ) {
				$course_id = get_the_ID();
			}

			return 'yes' == get_post_meta( $course_id, '_lp_coming_soon_countdown', true );
		}

		/**
		 * Set course price html to empty if not enable "Show Meta"
		 *
		 * @return string
		 */
		public function set_course_price_html_empty( $price ): string {
			return '';
		}
	}
}
