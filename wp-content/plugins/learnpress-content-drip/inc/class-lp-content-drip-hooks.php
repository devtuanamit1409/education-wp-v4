<?php
/**
 * LearnPress Content Drip Hooks
 *
 * @package LearnPress/Content-Drip/Hooks
 * @version 4.0.3
 */

use LearnPress\Models\CourseModel;

defined( 'ABSPATH' ) || exit();

class LP_Content_Drip_Hooks {

	private static $instance;

	protected function __construct() {
		$this->hooks();
	}

	protected function hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ), 20 );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_assets' ), 20 );

		add_action( 'admin_menu', array( $this, 'admin_menu' ), 30 );
		add_action( 'save_post_lp_course', array( $this, 'save_post' ) );

		// update drip item when add new items to course
		add_action( 'learn-press/after-add-items-section', array( $this, 'after_add_items_section' ), 10, 4 );

		// New metabox - nhamdv
		add_filter( 'learnpress/course/metabox/tabs', array( $this, 'course_metabox' ), 60, 2 );

		// Check can view item.
		add_action( 'learnpress/course/item/can-view', array( $this, 'can_view_item' ), 10, 2 );

		add_action( 'learn-press/after-course-item-content', array( $this, 'after_course_item_content' ), 10, 3 );
	}

	/**
	 * This function checks if a course item is part of a drip content schedule and displays a countdown
	 * timer if the content is not yet available.
	 *
	 */
	public function after_course_item_content( $user, $course, $course_item ) {
		$item_id = $course_item->get_id();
		if ( $item_id ) {
			$course_id = $course->get_id();
			if ( $course_id ) {
				$course_data = $user->get_course_data( $course_id );
				if ( ! $course_data ) {
					return;
				}

				$drip_items = get_post_meta( $course_id, '_lp_drip_items', true );
				if ( ! empty( $drip_items ) && is_array( $drip_items ) && array_key_exists( $item_id, $drip_items ) ) {
					$now             = time();
					$type            = $drip_items[ $item_id ]['type'];
					$time_remaining  = 0;
					$timestamp_point = $course_data->get_start_time()->getTimestamp();

					switch ( $type ?? '' ) {
						case 'specific':
							$timestamp_end = $drip_items[ $item_id ]['date'] ?? 0;
							if ( time() < $timestamp_end ) {
								$time_remaining = $timestamp_end - $now;
							}
							break;
						case 'interval':
							$timestamp_duration = $drip_items[ $item_id ]['interval'][2] ?? 0;
							if ( time() - $timestamp_point < $timestamp_duration ) {
								$time_remaining = ( $timestamp_duration + $timestamp_point ) - $now;
							}
							break;
						default:
							break;
					}

					if ( ! empty( $time_remaining ) ) {
						wp_enqueue_script( 'lp-content-drip' );
						$time_remaining = $time_remaining * 1000;
						echo '<input type="hidden" id="ctd-time-remaining" value="' . $time_remaining . '">';
					}
				}
			}
		}
	}

	/**
	 * Admin assets.
	 */
	public function admin_assets() {
		$min = '.min';
		$ver = LP_ADDON_CONTENT_DRIP_VER;
		if ( LP_Debug::is_debug() ) {
			$min = '';
			$ver = uniqid();
		}

		// Register scripts
		/*wp_register_script(
			'lp-content-drip-dpf',
			LP_Addon_Content_Drip_Preload::$addon->get_plugin_url( 'assets/lib/datetimepicker/jquery.datetimepicker.full.min.js' ),
			[ 'jquery' ]
		);*/
		/*wp_register_script(
			'lp-content-drip',
			LP_Addon_Content_Drip_Preload::$addon->get_plugin_url( "/assets/dist/js/backend/admin{$min}.js" ),
			[ 'jquery', 'select2', 'lp-content-drip-dpf' ],
			$ver,
			true
		);*/
		wp_register_script(
			'lp-content-drip-v2',
			LP_Addon_Content_Drip_Preload::$addon->get_plugin_url( "/assets/dist/js/backend/admin-v2{$min}.js" ),
			[ 'wp-api-fetch' ],
			$ver,
			[ 'strategy' => 'defer' ]
		);

		// Register styles
		/*wp_register_style(
			'lp-content-drip-dpf',
			LP_Addon_Content_Drip_Preload::$addon->get_plugin_url( '/assets/lib/datetimepicker/jquery.datetimepicker.min.css' )
		);*/

		wp_register_style(
			'lp-content-drip',
			LP_Addon_Content_Drip_Preload::$addon->get_plugin_url( "/assets/dist/css/admin{$min}.css" )
		);

		if ( isset( $_GET['page'] ) && 'content-drip-items' === $_GET['page'] ) {
			//wp_enqueue_script( 'lp-content-drip' );
			wp_enqueue_script( 'lp-content-drip-v2' );
			wp_enqueue_style( 'lp-content-drip' );
			//wp_enqueue_style( 'select2' );
			//wp_enqueue_style( 'lp-content-drip-dpf' );

			wp_localize_script(
				'lp-content-drip',
				'lpContentDrip',
				array(
					'confirm_reset_items'      => __( 'Are you sure you want to reset drip items.', 'learnpress-content-drip' ),
					'prerequisite_placeholder' => __( 'After enrolled item', 'learnpress-content-drip' ),
				)
			);
		}

		//check if we are on course edit page
		if ( isset( $_GET['post'] ) && get_post_type( $_GET['post'] ) === LP_COURSE_CPT ||
			isset( $_GET['post_type'] ) && $_GET['post_type'] === LP_COURSE_CPT ) {
			wp_enqueue_script( 'lp-content-drip-v2' );
			wp_enqueue_style( 'lp-content-drip' );
		}
	}

	/**
	 * Front-end assets.
	 */
	public function frontend_assets() {
		$min = '.min';
		$ver = LP_ADDON_CONTENT_DRIP_VER;
		if ( LP_Debug::is_debug() ) {
			$min = '';
			$ver = uniqid();
		}

		wp_register_script(
			'lp-content-drip',
			LP_Addon_Content_Drip_Preload::$addon->get_plugin_url( "/assets/dist/js/frontend/lp-content-drip{$min}.js" ),
			[],
			$ver,
			true
		);
	}

	/**
	 * Add admin menu.
	 */
	public function admin_menu() {
		if ( 'content-drip-items' === LP_Request::get_param( 'page' ) ) {
			check_admin_referer( 'content-drip-items', 'drip-items-nonce' );
			$course_cap = LP_COURSE_CPT . 's';
			add_submenu_page(
				'learn_press',
				__( 'Drip Items', 'learnpress-content-drip' ),
				__( 'Drip Items', 'learnpress-content-drip' ),
				'edit_' . $course_cap,
				'content-drip-items',
				array( $this, 'display_items' )
			);
		}
	}

	/**
	 * Admin drip items view.
	 */
	public function display_items() {
		LP_Addon_Content_Drip_Preload::$addon->get_admin_template( 'drip-items' );
	}

	/**
	 * Init drip item when first save post.
	 *
	 * @param $post_id
	 *
	 * @editor tungnx
	 *
	 */
	public function save_post( $post_id ) {
		$drip_type = LP_Request::get_param( $_POST['_lp_content_drip_drip_type'] ?? '' );
		if ( empty( $drip_type ) ) {
			return;
		}

		//$new_arr = array();
		$course = learn_press_get_course( $post_id );
		if ( ! $course ) {
			return;
		}

		$course_items = $course->get_item_ids();
		$course_items = array_map(
			function ( $item_id ) {
				return (string) $item_id;
			},
			$course_items
		);

		$old_drip_type = get_post_meta( $post_id, '_lp_content_drip_drip_type', true );
		if ( $old_drip_type == 'prerequisite' && $drip_type != 'prerequisite' ) {
			delete_post_meta( $post_id, '_lp_drip_items', '' );
		}

		$drip_items = get_post_meta( $post_id, '_lp_drip_items', true );
		$new_drip   = array();
		if ( ! $drip_items || $drip_items == '' ) {
			if ( $course_items ) {
				foreach ( $course_items as $item_id ) {
					$new_drip[ $item_id ] = array(
						'type'     => 'immediately',
						'interval' => array( '0', 'minute' ),
						'date'     => gmdate( get_option( 'date_format', 'F j, Y' ) ),
					);

					$index                                = array_search( $item_id, $course_items );
					$new_drip[ $item_id ]['prerequisite'] = $index ? array( $course_items[ $index - 1 ] ) : array( 0 );

				}
				update_post_meta( $post_id, '_lp_drip_items', $new_drip );
			}
		}
	}

	/**
	 * Update drip item when add new items to course
	 *
	 * @param $items
	 * @param $section_id
	 * @param $course_id
	 * @param $result
	 */
	public function after_add_items_section( $items, $section_id, $course_id, $result ) {

		$course_items = wp_cache_get( 'course-' . $course_id, 'lp-course-items' );
		$drip_items   = get_post_meta( $course_id, '_lp_drip_items' ) ? get_post_meta( $course_id, '_lp_drip_items', true ) : array();

		if ( $items ) {
			foreach ( $items as $item ) {
				$index = count( $course_items );

				$new_item = array(
					'prerequisite' => $index ? array( $course_items[ $index - 1 ] ) : array( 0 ),
					'type'         => 'immediately',
					'interval'     => array( '0', 'minute' ),
					'date'         => date( get_option( 'date_format' ) ),
				);

				$drip_items[ $item['id'] ] = $new_item;
				update_post_meta( $course_id, '_lp_drip_items', $drip_items );
			}
		}
	}

	/**
	 * It adds a new tab to the course edit screen, and adds a checkbox and a radio field to that tab
	 *
	 * @param array $tabs The tabs array.
	 * @param int $post_id The ID of the post being edited.
	 *
	 * @return array.
	 */
	public function course_metabox( $tabs, $post_id ) {
		$course = CourseModel::find( $post_id, true );
		if ( ! $course ) {
			return $tabs;
		}

		if ( $course->is_offline() ) {
			return $tabs;
		}

		$url                    = wp_nonce_url( admin_url( 'admin.php?page=content-drip-items&course-id=' . absint( $post_id ) ), 'content-drip-items', 'drip-items-nonce' );
		$content_dip_type       = get_post_meta( $post_id, '_lp_content_drip_drip_type', true );
		$is_enable_content_drip = get_post_meta( $post_id, '_lp_content_drip_enable', true ) === 'yes';

		$tabs['content_drip'] = array(
			'label'    => esc_html__( 'Content drip', 'learnpress-content-drip' ),
			'icon'     => 'dashicons-filter',
			'target'   => 'lp_contentdrip_course_data',
			'priority' => 60,
			'content'  => array(
				'_lp_content_drip_enable'    => new LP_Meta_Box_Checkbox_Field(
					esc_html__( 'Enable', 'learnpress-content-drip' ),
					esc_html__( 'All settings in items of this course will become locked if turn off.', 'learnpress-content-drip' ),
					'no'
				),
				'_lp_content_drip_drip_type' => new LP_Meta_Box_Radio_Field(
					esc_html__( 'Drip type', 'learnpress-content-drip' ),
					sprintf(
						'<div class="_lp_content_drip_drip_type--description"><div>%s</div></div>',
						implode(
							'</div><div>',
							array(
								wp_kses_post( '<strong>Drip type:</strong>', 'learnpress-content-drip' ),
								esc_html__( '1. Open course item after enrolled course specific time.', 'learnpress-content-drip' ),
								esc_html__( '2. Open lesson #2 after completed lesson #1, open lesson #3 after completed lesson #2, and so on...', 'learnpress-content-drip' ),
								esc_html__( '3. Open course item after completed prerequisite items.', 'learnpress-content-drip' ),
								sprintf(
									'<p style="color:red"><i>* %s<br/>* %s</i></>',
									esc_html__( 'You must save the post before beginning content settings or changing the drip type Content Drip!', 'learnpress-content-drip' ),
									esc_html__( 'Content drip do not apply for Admin, author of course, author of item', 'learnpress-content-drip' )
								),
								sprintf(
									'<a class="button save-post-ctd button-primary %s">%s</a>
									 <a class="button button-primary settings-ctd %s" href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
									empty( $content_dip_type ) ? 'd-none' : '',
									esc_html__( 'Save', 'learnpress-content-drip' ),
									empty( $content_dip_type ) ? 'd-none' : '',
									$url,
									esc_html__( 'Settings', 'learnpress-content-drip' )
								),
							)
						)
					),
					'specific_date',
					array(
						'options'    => LP_Addon_Content_Drip_Preload::$addon->lp_content_drip_types(),
						'dependency' => [
							'name'       => '_lp_content_drip_enable',
							'is_disable' => ! $is_enable_content_drip
						],
					)
				),
			),
		);

		return $tabs;
	}

	/**
	 * Set user can view content of item.
	 *
	 * @param LP_Model_User_Can_View_Course_Item $view
	 * @param LP_Course_Item $item
	 *
	 * @return LP_Model_User_Can_View_Course_Item
	 */
	public function can_view_item( LP_Model_User_Can_View_Course_Item $view, LP_Course_Item $item ): LP_Model_User_Can_View_Course_Item {
		$course = learn_press_get_course( $item->get_course_id() );
		if ( ! $course ) {
			return $view;
		}

		if ( ! $view->flag ) {
			return $view;
		}

		$user = learn_press_get_current_user();
		$rs   = LP_Addon_Content_Drip_Preload::$addon->check_drip_item( $item, $course, $user );
		// If locked = 1, flag will be false.
		$view->flag    = ! ( isset( $rs['locked'] ) && $rs['locked'] ? 1 : 0 );
		$view->message = $rs['message'] ?? '';

		return $view;
	}

	public static function instance(): LP_Content_Drip_Hooks {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
