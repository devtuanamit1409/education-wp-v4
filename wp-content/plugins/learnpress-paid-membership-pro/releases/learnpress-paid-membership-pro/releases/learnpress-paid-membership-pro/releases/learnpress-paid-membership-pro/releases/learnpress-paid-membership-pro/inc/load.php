<?php

use LearnPress\Models\CourseModel;

defined( 'ABSPATH' ) || exit;

/**
 * Class LP_Addon_Paid_Memberships_Pro
 */
class LP_Addon_Paid_Memberships_Pro extends LP_Addon {
	public $settings = array();

	/**
	 * page id of the Membership Levels page
	 *
	 * @var int
	 */
	public $pmpro_levels_page_id = null;

	/**
	 * @var string
	 */
	public $version = LP_ADDON_PMPRO_VER;

	/**
	 * @var string
	 */
	public $require_version = LP_ADDON_PMPRO_REQUIRE_VER;

	/**
	 * Path file addon
	 *
	 * @var string
	 */
	public $plugin_file = LP_ADDON_PMPRO_FILE;

	/**
	 * @var
	 */
	public $pmpro_levels;

	/**
	 * @var
	 */
	protected $user;

	/**
	 * @var
	 */
	protected $user_level;

	/**
	 * @var bool
	 */
	private $_meta_box = false;

	/**
	 * LP_Addon_Paid_Memberships_Pro constructor.
	 */
	public function __construct() {
		$this->text_domain = 'learnpress-paid-membership-pro'; // pre-set text domain

		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
		parent::__construct();
	}

	protected function _includes() {
		require_once LP_ADDON_PMPRO_PATH . '/inc/functions.php';
	}

	public function plugins_loaded() {
		include 'background-process/class-lp-pms-background-process.php';
		include 'classes/class-lp-pms-order.php';
		include 'user-hooks.php';

		$this->user = learn_press_get_current_user();

		if ( is_admin() ) {
			$this->admin_require();
		}
	}

	protected function _define_contants() {
		define( 'LP_PMPRO_FILE', __FILE__ );
		define( 'LP_PMPRO_URI', plugins_url( '/', LP_PMPRO_FILE ) );
		define( 'LP_PMPRO_VER', $this->version );
		define( 'LP_PMPRO_REQUIRE_VER', $this->require_version );
	}

	public function admin_require() {
		require_once __DIR__ . DIRECTORY_SEPARATOR . 'pmpro-admin' . DIRECTORY_SEPARATOR . 'pmpro-admin.php';
	}


	public function admin_notices() {
		?>
		<div class="notice notice-error">
			<p>
			<?php
				echo wp_kses(
					sprintf(
						'<strong>Paid Membership Pro</strong> addon for <strong>LearnPress</strong> requires %s plugin is <strong>installed</strong> and <strong>activated</strong>.',
						sprintf(
							'<a href="%s" target="_blank">Paid Membership Pro</a>',
							admin_url( 'plugin-install.php?tab=search&type=term&s=paid memberships pro' )
						)
					),
					array(
						'a'      => array(
							'href'   => array(),
							'target' => array(),
						),
						'strong' => array(),
					)
				);
			?>
				</p>
		</div>
		<?php
	}

	function get_pmpro_levels_page_id() {
		if ( null === $this->pmpro_levels_page_id ) {
			$this->pmpro_levels_page_id = pmpro_getOption( 'levels_page_id' );
		}

		return $this->pmpro_levels_page_id;
	}

	/**
	 * @param array $levels array level_id
	 *
	 * @return bool
	 */
	private function checkUserHasLevel( $levels ) {
		$levels = (array) $levels;

		if ( ! $this->user_level ) {
			return false;
		}

		foreach ( $levels as $l ) {
			if ( $l == $this->user_level->ID ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Init hooks
	 */
	protected function _init_hooks() {
		// get all memberships
		add_action( 'learn-press/content-single', array( $this, 'get_pmpro_get_all_levels' ), - 1 );

		// Add the Memberships tab in LearnPress Settings page
		add_filter( 'learn-press/admin/settings-tabs-array', array( $this, 'admin_settings' ) );

		// build externaml link buy course
		add_filter( 'learn-press/course-external-link', array( $this, 'external_link_buy_course' ), 10, 2 );

		add_action( 'learn-press/course-buttons', array( $this, 'add_buy_membership_button' ), 10 );

		add_filter( 'learn-press/retake-course-button-text', array( $this, 'retake_course_text_calback' ), 10 );

		// add scripts to frontend
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_enqueue_scripts' ) );

		// add scripts to admin
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		/* Override page list levels Templates */
		add_filter( 'pmpro_pages_custom_template_path', array( $this, 'learn_press_pmpro_pages_custom_template_path' ), 10, 5 );
		add_action( 'pmpro_checkout_after_pricing_fields', array( $this, 'learn_press_pmpro_checkout_after_pricing_fields' ) );

		//add_filter( 'pmpro_email_data', array( $this, 'learn_press_pmpro_email_data' ), 10, 2 );

		add_filter( 'learn-press/purchase-course-button-text', array( $this, 'learn_press_pmpro_purchase_button_text' ), 10, 2 );

		add_shortcode( 'lp_pmpro_courses', array( $this, 'learn_press_page_levels_short_code' ) );

		// Add the Memberships tab to Course settings in edit Course page
		add_action(
			'learnpress/course/metabox/tabs',
			function( $tabs, $post_id ) {
				$course = CourseModel::find( $post_id, true );
				if ( ! $course ) {
					return $tabs;
				}

				if ( $course->is_offline() ) {
					return $tabs;
				}

				$options_levels = array();
				$pmpro_levels   = lp_pmpro_get_all_levels();

				foreach ( $pmpro_levels as $pmpro_level ) {
					$options_levels[ $pmpro_level->id ] = $pmpro_level->name;
				}

				$values = get_post_meta( $post_id, '_lp_pmpro_levels', false ) ?? array();

				$tabs['paid-memberships-pro'] = array(
					'label'    => esc_html__( 'Course Memberships', 'learnpress-paid-membership-pro' ),
					'target'   => 'lp-course-membership-pro',
					'icon'     => 'dashicons-groups',
					'priority' => 90,
					'content'  => array(
						'_lp_pmpro_levels' => new LP_Meta_Box_Select_Field(
							esc_html__( 'Select membership levels', 'learnpress-paid-membership-pro' ),
							'',
							array(),
							[
								'value'       => $values,
								'options'     => $options_levels,
								'tom_select'  => true,
								'multiple'    => true,
								'style'       => 'min-width:200px;',
								'custom_save' => true, // Custom for save metabox
							]
						),
					),
				);

				return $tabs;
			},
			10,
			2
		);

		add_action(
			'learnpress/admin/metabox/select/save',
			function( $id, $raw_values, $post_id = 0 ) {
				if ( $id === '_lp_pmpro_levels' ) {
					$values       = get_post_meta( $post_id, '_lp_pmpro_levels', false ) ?? array();
					$pmpro_levels = isset( $raw_values ) ? (array) wp_unslash( $raw_values ) : array();

					$array_values = ! empty( $values ) ? array_values( $values ) : array();
					$level_values = ! empty( $pmpro_levels ) ? array_values( $pmpro_levels ) : array();

					$del_course_ids = array_diff( $array_values, $level_values );
					$new_course_ids = array_diff( $level_values, $array_values );

					$ob_postid = array(
						'ID'    => $post_id,
						'title' => get_the_title( $post_id ),
					);

					foreach ( $del_course_ids as $level_id ) {
						$coursesOnLevel = LP_PMS_DB::getInstance()->getCoursesOnLevel( $level_id );
						unset( $coursesOnLevel[ $post_id ] );
						delete_post_meta( $post_id, '_lp_pmpro_levels', $level_id );
						$this->update_order_single_course_when_update_level( $level_id, $coursesOnLevel );
					}

					foreach ( $new_course_ids as $level_id ) {
						$coursesOnLevel             = LP_PMS_DB::getInstance()->getCoursesOnLevel( $level_id );
						$coursesOnLevel[ $post_id ] = ConvertArrayToObject( $ob_postid );
						add_post_meta( $post_id, '_lp_pmpro_levels', $level_id, false );
						$this->update_order_single_course_when_update_level( $level_id, $coursesOnLevel );
					}
				}
			},
			10,
			3
		);
	}

	/**
	 * update order in single course when update level.
	 *
	 * @param $level_id , $coursesOnLevel
	 */

	public function update_order_single_course_when_update_level( $level_id, $coursesOnLevel ) {

		$level_course_ids = array();

		foreach ( $coursesOnLevel as $value ) {
			$level_course_ids[] = $value->ID;
		}

		$auto_update_courses_on_level = LP_Settings::get_option( 'pmpro_update_access_course', 'no' );
		if ( $auto_update_courses_on_level != 'yes' ) {
			return;
		}

		$lp_orders = LP_PMS_DB::getInstance()->getLastOrderOfUsersHasLevel( $level_id );
		if ( is_array( $lp_orders ) && count( $lp_orders ) > 0 ) {
			LP_PMS_Order::getInstance()->handleLevelChangeCourses( $lp_orders, $level_course_ids, $level_id );
		}
	}

	public function get_pmpro_get_all_levels() {
		$this->pmpro_levels = lp_pmpro_get_all_levels();
	}

	/**
	 * callback function for hook 'learn-press/admin/settings-tabs-array'.
	 * it add new tab in LearnPress Settings page.
	 *
	 * @param array $tabs
	 *
	 * @return array
	 */
	public function admin_settings( $tabs ) {
		$tabs['membership'] = include_once LP_ADDON_PMPRO_PATH . '/inc/classes/class-lp-pms-setting.php';

		return $tabs;
	}

	/**
	 * build content of settings tab for Paid Memberships Pro in edit course page
	 * NOT USE IN LP4
	 *
	 * @return mixed
	 */
//	function meta_box() {
//		$prefix         = '_lp_pmpro_';
//		$options_levels = array();
//		$pmpro_levels   = lp_pmpro_get_all_levels();
//
//		foreach ( $pmpro_levels as $pmpro_level ) {
//			$options_levels[ $pmpro_level->id ] = $pmpro_level->name;
//		}
//
//		$meta_box = array(
//			'id'     => 'course_pmpro',
//			'title'  => __( 'Course Memberships', 'learnpress-paid-membership-pro' ),
//			'icon'   => 'dashicons-groups',
//			'pages'  => array( LP_COURSE_CPT ),
//			'fields' => array(
//				array(
//					'name'        => __( 'Select Membership Levels', 'learnpress-paid-membership-pro' ),
//					'id'          => "{$prefix}levels",
//					'type'        => 'select_advanced',
//					'options'     => $options_levels,
//					'multiple'    => true,
//					'placeholder' => __( 'Select membership levels', 'learnpress-paid-membership-pro' ),
//				),
//			),
//		);
//
//		/*** Check if on frontend editor set type is select */
//		$current_url = LP_Helper::getUrlCurrent();
//
//		preg_match( '/wp-admin/', $current_url, $match );
//
//		if ( count( $match ) == 0 ) {
//			$meta_box = array(
//				'id'     => 'course_pmpro',
//				'title'  => __( 'Course Memberships', 'learnpress-paid-membership-pro' ),
//				'icon'   => 'dashicons-groups',
//				'pages'  => array( LP_COURSE_CPT ),
//				'fields' => array(
//					array(
//						'name'        => __( 'Select Membership Levels', 'learnpress-paid-membership-pro' ),
//						'id'          => "{$prefix}levels",
//						'type'        => 'select',
//						'options'     => $options_levels,
//						'multiple'    => true,
//						'placeholder' => __( 'Select membership levels', 'learnpress-paid-membership-pro' ),
//					),
//				),
//			);
//		}
//
//		/*** End */
//
//		return apply_filters( 'learn_press_pmpro_meta_box_args', $meta_box );
//	}

	/**
	 * add the "Buy Membership" button in to single course page
	 * To do: need check again @tungnx
	 */
	public function add_buy_membership_button() {

		global $post;
		// get course levels
		$course_id = learn_press_get_course_id();

		if ( ! $course_id ) {
			$course_id = $post->ID;
		}

		$course_levels = learn_press_pmpro_get_course_levels( $course_id );

		if ( ! $course_levels || empty( $course_levels ) ) {
			return;
		}
		// get user levels
		$current_user = learn_press_get_current_user();
		$course       = CourseModel::find( $course_id, true );

		if ( ! $course ) {
			return;
		}

		if ( ! $course->is_in_stock() && ! $course->has_no_enroll_requirement() ) {
			return;
		}

		if ( ! $current_user || $current_user instanceof LP_User_Guest ) {

			$pmpro_levels_page_id        = $this->get_pmpro_levels_page_id();
			$redirect                    = add_query_arg(
				'course_id',
				$course_id,
				get_the_permalink( $pmpro_levels_page_id )
			);
			$redirect                    = apply_filters(
				'learn_press_pmpro_redirect_levels_page',
				$redirect,
				$course,
				$pmpro_levels_page_id,
				$current_user
			);
			$buy_through_membership_text = LearnPress::instance()->settings()->get( 'button_buy_membership' );

			if ( empty( $buy_through_membership_text ) ) {
				$buy_through_membership_text = esc_html__( 'Buy Membership', 'learnpress-paid-membership-pro' );
			}

			$buy_through_membership_text = apply_filters(
				'learn_press_buy_through_membership_text',
				$buy_through_membership_text,
				$redirect,
				$course,
				$pmpro_levels_page_id,
				$current_user
			);

			$args = array(
				'buy_through_membership_text' => $buy_through_membership_text,
				'redirect'                    => $redirect,
				'course'                      => $course,
				'levels_page_id'              => $pmpro_levels_page_id,
				'current_user'                => $current_user,
			);

			learn_press_get_template(
				'button-buy-membership.php',
				$args,
				learn_press_template_path() . '/addons/paid-membership-pro/',
				LP_ADDON_PMPRO_TEMP
			);

			return;
		}

		$course_status = $current_user->get_course_status( $course_id );
		$has_purchased = $current_user->has_purchased_course( $course_id );

		$can_retake = $current_user->can_retry_course( $course_id );
		// $can_enroll   = $current_user->can_enroll_course( $course_id );
		// $can_purchase = $current_user->can_purchase_course( $course_id );
		// $current_user->has_purchased_course( $course_id );

		if ( is_user_logged_in() && $can_retake && $has_purchased ) {
			return;
		}

		if ( ! $this->hasMembershipLevel( $course_levels ) && ( empty( $course_status ) || in_array( $course_status, array( 'finished', 'cancelled' ) ) ) ) {

			$pmpro_levels_page_id   = $this->get_pmpro_levels_page_id();
			$redirect               = add_query_arg(
				'course_id',
				$course_id,
				get_the_permalink( $pmpro_levels_page_id )
			);
			$redirect               = apply_filters(
				'learn_press_pmpro_redirect_levels_page',
				$redirect,
				$course,
				$pmpro_levels_page_id,
				$current_user
			);
			$buy_through_membership = LearnPress::instance()->settings()->get( 'buy_through_membership' );

			$buy_through_membership_text = LearnPress::instance()->settings()->get( 'button_buy_membership' );

			if ( empty( $buy_through_membership_text ) ) {
				$buy_through_membership_text = esc_html__( 'Buy Membership', 'learnpress-paid-membership-pro' );
			}

			$buy_through_membership_text = apply_filters(
				'learn_press_buy_through_membership_text',
				$buy_through_membership_text,
				$redirect,
				$course,
				$pmpro_levels_page_id,
				$current_user
			);

			$args = array(
				'buy_through_membership_text' => $buy_through_membership_text,
				'redirect'                    => $redirect,
				'course'                      => $course,
				'levels_page_id'              => $pmpro_levels_page_id,
				'current_user'                => $current_user,
			);

			learn_press_get_template(
				'button-buy-membership.php',
				$args,
				learn_press_template_path() . '/addons/paid-membership-pro/',
				LP_ADDON_PMPRO_TEMP
			);
		}
	}


	public function retake_course_text_calback( $text ) {
		global $post;
		// get course levels
		$course_id = learn_press_get_course_id();

		if ( ! $course_id ) {
			$course_id = $post->ID;
		}

		$course_levels = learn_press_pmpro_get_course_levels( $course_id );

		if ( ! empty( $course_levels ) && $this->hasMembershipLevel( $course_levels ) ) {
			$text = esc_html__( 'Retake course', 'learnpress-paid-membership-pro' );
		}

		return $text;
	}


	private function hasMembershipLevel( $levels = array() ) {
		return learn_press_pmpro_hasMembershipLevel( $levels );
	}


	public function frontend_enqueue_scripts() {
		$v_rand = uniqid();

		if ( LP_Debug::is_debug() ) {
			wp_register_style(
				'learn-press-pmpro-style',
				LP_ADDON_PMPRO_URL . 'assets/lp-pms-style.css',
				array(),
				$v_rand
			);
		} else {
			wp_register_style(
				'learn-press-pmpro-style',
				LP_ADDON_PMPRO_URL . 'assets/lp-pms-style.min.css',
				array(),
				LP_ADDON_PMPRO_VER
			);
		}

		if ( is_archive() || learn_press_is_course() ) {
			wp_enqueue_style( 'learn-press-pmpro-style' );
		}
	}

	public function admin_enqueue_scripts() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		$ver = LP_ADDON_PMPRO_VER;
		$min = '.min';
		if ( LP_Debug::is_debug() ) {
			$min = '';
			$ver = uniqid();
		}

		if ( LP_Debug::is_debug() ) {
			$ver = uniqid();
		}

		wp_register_script(
			'learn-press-pms-script',
			LP_ADDON_PMPRO_URL . "assets/js/dist/admin-lp-pms{$min}.js",
			array(),
			$ver,
			[ 'strategy' => 'async' ]
		);

		if ( $screen ) {
			$check_valid = in_array( $screen->id, [ 'memberships_page_pmpro-membershiplevels', LP_COURSE_CPT ] );
			if ( $check_valid ) {
				wp_enqueue_script( 'learn-press-pms-script' );
			}
		}
	}

	/**
	 * Load template overwrite for Paid Memberships Pro
	 *
	 * @param $default_templates
	 * @param $page_name
	 * @param $type
	 * @param $where
	 * @param $ext
	 *
	 * @return mixed
	 * @since 3.0.0
	 * @version 1.0.1
	 */
	public function learn_press_pmpro_pages_custom_template_path( $default_templates, $page_name, $type, $where, $ext ) {
		// Set info for file levels.php to not out of date
		try {
			if ( $page_name === 'levels' && is_admin() ) {
				$path_file                 = LP_ADDON_PMPRO_TEMP . '/pages/levels-version.php';
				$page_levels_original_info = get_file_data( PMPRO_DIR . '/pages/levels.php', array( 'Version' => 'Version' ) );
				//$page_levels_override_info = get_file_data( $path_file, array( 'Version' => 'Version' ) );

				//if ( $page_levels_override_info['Version'] !== $page_levels_original_info['Version'] ) {
				$folder_name_rewrite = learn_press_template_path();
				$from_theme_path     = sprintf(
					'%s/%s/%s/%s/%s',
					get_template_directory(),
					$folder_name_rewrite,
					'addons',
					str_replace( 'learnpress-', '', $this->plugin_folder_name ),
					'levels.php'
				);

				$path_load = LP_ADDON_PMPRO_TEMP . '/pages/levels.php';
				if ( file_exists( $from_theme_path ) ) {
					$path_load = $from_theme_path;
				}

				$content = "<?php
/**
 * Template: Levels
 * Version: {$page_levels_original_info['Version']}
 * Version of PMS, for not out of date
 */
include '{$path_load}';
";

				file_put_contents( $path_file, $content );
				//}

				$default_templates[] = $path_file;

				return $default_templates;
			}
		} catch ( Exception $e ) {

		}
		// End set

		$template = learn_press_pmpro_locate_template( "{$type}/{$page_name}.{$ext}" );

		if ( file_exists( $template ) ) {
			$default_templates[] = $template;
		}

		return $default_templates;
	}

	public function learn_press_pmpro_email_data( $data, $email ) {
		$path_email = LP_ADDON_PMPRO_PATH . '/templates/email/';
		$path_email = apply_filters( 'learn_press_pmpro_email_custom_template_path', $path_email, $data, $email );

		if ( ! empty( $email->body ) && ! empty( $email->template ) && file_exists( $path_email . $email->template . '.html' ) ) {
			$email->body = file_get_contents( $path_email . $email->template . '.html' );
		}

		return $data;
	}

	public function learn_press_pmpro_checkout_after_pricing_fields() {
		LP_Addon_Paid_Memberships_Pro_Preload::$addon->get_template( 'pages/checkout-custom-pricing' );
	}

	/**
	 * @param int $course_id
	 *
	 * @todo: remove this function in next version
	 */
	public function learn_press_before_course_buttons( $course_id ) {
		$this->add_buy_membership_button();
	}

	public function external_link_buy_course( $external_link_buy_course, $course_id ) {

		$buy_through_membership = LearnPress::instance()->settings()->get( 'buy_through_membership' );
		$is_required            = learn_press_pmpro_check_require_template( $course_id );

		if ( ! empty( $buy_through_membership ) && $buy_through_membership === 'yes' && $is_required ) {
			return '';
		}

		return $external_link_buy_course;
	}

	public function learn_press_pmpro_purchase_button_text( $purchase_text, $course_id ) {
		$is_required            = learn_press_pmpro_check_require_template( $course_id );
		$buy_through_membership = LearnPress::instance()->settings()->get( 'buy_through_membership' );
		$new_text               = LearnPress::instance()->settings()->get( 'button_buy_course' );

		if ( ! empty( $buy_through_membership ) && $buy_through_membership == 'no' && ! empty( $new_text ) && $is_required ) {
			return $new_text;
		}

		return $purchase_text;
	}

	public function learn_press_page_levels_short_code() {
		echo do_shortcode( '[pmpro_levels]' );
	}
}
