<?php
/**
 * Class LP_H5Pcontent_Post_Type
 *
 * @author  ThimPress
 * @package LearnPress/H5P/Classes
 * @version 4.0.0
 * @author Nhamdv <Code is poetry>
 */

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'LP_H5pcontent_Post_Type' ) ) {
	/**
	 * Class LP_H5pcontent_Post_Type.
	 */
	final class LP_H5pcontent_Post_Type extends LP_Abstract_Post_Type {

		/**
		 * @var null
		 */
		protected static $_instance = null;

		/**
		 * @var array
		 */
		public static $metaboxes = array();

		/**
		 * LP_H5pcontent_Post_Type constructor.
		 *
		 * @param        $post_type
		 * @param string    $args
		 */
		public function __construct( $post_type, $args = '' ) {
			// posts where paged
			add_filter( 'posts_where_paged', array( $this, 'posts_where_paged' ), 10 );

			// view page
			add_filter( 'views_edit-' . LP_H5P_CPT, array( $this, 'views_pages' ), 10 );

			// add course assessment types
			add_filter( 'learn_press_course_assessment_metabox', array( $this, 'update_evaluate_options' ) );
			add_filter( 'learnpress/course-evaluation/methods', array( $this, 'evaluate_method' ) );

			// add h5p link in LP settings for course
			add_filter( 'learn-press/course-settings-fields/single', array( $this, 'add_setting_course_link' ) );
			// add h5p link in LP settings for profile
			add_filter( 'learn-press/profile-settings-fields/sub-tabs', array( $this, 'lp_h5p_add_setting_profile_link' ), 10, 2 );

			// add h5p publicity in LP settings for profile
			add_filter( 'learn-press/profile-settings-fields/publicity', array( $this, 'lp_h5p_add_setting_profile_publicity' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'learnpress_h5p_select2_enqueue' ) );
			add_action( 'wp_ajax_learnpress_search_h5p', array( __CLASS__, 'learnpress_search_h5p' ) );
			add_action( 'wp_ajax_lph5p_update_interacted_h5p', array( __CLASS__, 'lph5p_update_interacted_h5p' ) );
			// add_filter( 'query', array( $this, 'try_to_change_query' ) );

			parent::__construct( $post_type, $args );
		}

		public static function lph5p_update_interacted_h5p() {
			$item_id    = filter_input( INPUT_POST, 'item_id', FILTER_VALIDATE_INT );
			$chosen_h5p = filter_input( INPUT_POST, 'el_val', FILTER_VALIDATE_INT );
			$result     = array();
			echo '<pre>';
			print_r( $item_id );
			echo '<pre>';
			print_r( $chosen_h5p );
			if ( $item_id && $chosen_h5p && get_post_type( $item_id ) == LP_H5P_CPT ) {
				update_post_meta( $item_id, '_lp_h5p_interact', $chosen_h5p );
				$result['message'] = __( 'Connect to H5P Content success!', 'learnpress-h5p' );
			}
			$result['success'] = 1;
			echo json_encode( $result );
			die;
		}

		/**
		 *
		 */
		public static function learnpress_search_h5p() {
			global $wpdb;
			$return = array();

			$query = $wpdb->prepare(
				"
	            SELECT hc.title AS title, hl.title AS content_type, GROUP_CONCAT(DISTINCT CONCAT(t.id,',',t.name) ORDER BY t.id SEPARATOR ';') AS tags, hc.updated_at AS updated_at, hc.id AS id, hl.name AS content_type_id, hc.slug AS slug
	            FROM {$wpdb->prefix}h5p_contents hc
	            LEFT JOIN {$wpdb->prefix}h5p_libraries hl ON hl.id = hc.library_id
	            LEFT JOIN {$wpdb->prefix}h5p_contents_tags ct ON ct.content_id = hc.id
	            LEFT JOIN {$wpdb->prefix}h5p_tags t ON ct.tag_id = t.id
	            LEFT JOIN {$wpdb->prefix}h5p_contents_tags ct2 ON ct2.content_id = hc.id
	            WHERE hc.title LIKE '%%%s%%'
	            GROUP BY hc.id
	            ORDER BY hc.updated_at DESC
	            LIMIT 0, 10
	        ",
				LP_Helper::sanitize_params_submitted( $_GET['q'] ?? '' )
			);

			$search_results = $wpdb->get_results( $query, ARRAY_A );
			if ( count( $search_results ) > 0 ) {
				foreach ( $search_results as $search ) {
					// shorten the title a little
					$title    = ( mb_strlen( $search['title'] ) > 50 ) ? mb_substr( $search['title'], 0, 49 ) . '...' : $search['title'];
					$return[] = array( $search['id'], $title ); // array( Post ID, Post Title )
				}
			}

			echo json_encode( $return );
			die;
		}

		/**
		 *
		 */
		public function learnpress_h5p_select2_enqueue() {
			$screen = get_current_screen();

			if ( isset( $screen->id ) && $screen->id == 'lp_h5p' ) {
				wp_enqueue_script( 'learnpress-h5p', plugins_url( '/assets/js/admin-lph5p.js', LP_ADDON_H5P_FILE ), array( 'select2' ) );
			}
		}

		public function try_to_change_query( $query ) {
			if ( strpos( $query, 'SELECT hc.title AS title, hl.title AS content_type, GROUP_CONCAT(DISTINCT' ) !== false ) {
				echo '<pre>';
				print_r( $query );
				die;
			}

			return $query;
		}

		/**
		 * Register h5p post type.
		 */
		public function register() {
			register_post_type(
				LP_H5P_CPT,
				apply_filters(
					'lp_h5pcontent_post_type_args',
					array(
						'labels'             => array(
							'name'               => esc_html__( 'H5P Items', 'learnpress-h5p' ),
							'menu_name'          => esc_html__( 'H5P Item', 'learnpress-h5p' ),
							'singular_name'      => esc_html__( 'H5P Item', 'learnpress-h5p' ),
							'add_new_item'       => esc_html__( 'Add New H5P Item', 'learnpress-h5p' ),
							'edit_item'          => esc_html__( 'Edit H5P Item', 'learnpress-h5p' ),
							'all_items'          => esc_html__( 'H5P Items', 'learnpress-h5p' ),
							'view_item'          => esc_html__( 'View H5P Item', 'learnpress-h5p' ),
							'add_new'            => esc_html__( 'New H5P Item', 'learnpress-h5p' ),
							'update_item'        => esc_html__( 'Update H5P Item', 'learnpress-h5p' ),
							'search_items'       => esc_html__( 'Search H5P Items', 'learnpress-h5p' ),
							'not_found'          => sprintf( __( 'You have not got any H5P items yet. Click <a href="%s">Add new</a> to start', 'learnpress-h5p' ), admin_url( 'post-new.php?post_type=lp_h5p' ) ),
							'not_found_in_trash' => esc_html__( 'No H5P items found in Trash', 'learnpress-h5p' ),
						),
						'public'             => true,
						'publicly_queryable' => true,
						'show_ui'            => true,
						'has_archive'        => false,
						'capability_type'    => LP_COURSE_CPT,
						'map_meta_cap'       => true,
						'show_in_menu'       => 'learn_press',
						'show_in_admin_bar'  => true,
						'show_in_nav_menus'  => true,
						'supports'           => array( 'title', 'editor', 'revisions' ),
						'hierarchical'       => true,
						'rewrite'            => array(
							'slug'         => 'h5p',
							'hierarchical' => true,
							'with_front'   => false,
						),
					)
				)
			);
		}

		/**
		 * Add h5p meta box settings.
		 */
		public function add_meta_boxes() {
			parent::add_meta_boxes();
		}

		/**
		 * @return mixed
		 */
		public static function settings_meta_box() {
			$post_id    = LP_Request::get_int( 'post' );
			$h5p_chosen = array( '' => '' );
			$edit_link  = '';

			if ( ! lp_h5p_count_h5p_items() ) {
				$edit_link = __( 'There is no items to select. Create <a href="' . admin_url( 'admin.php?page=h5p_new' ) . '" target="_blank">here</a>.', 'learnpress-h5p' );
			}

			if ( $post_id ) {
				$appended_h5p = get_post_meta( $post_id, '_lp_h5p_interact', true );

				if ( ! empty( $appended_h5p ) ) {
					$h5p_chosen[ $appended_h5p ] = lp_h5p_get_content_title( $appended_h5p );
					$edit_link                   = '<a href="' . admin_url( 'admin.php?page=h5p&task=show&id=' ) . $appended_h5p . '" target="_blank">Edit the H5P item</a>.';
				}
			}

			$meta_box = array(
				'title'      => __( 'General Settings', 'learnpress-h5p' ),
				'post_types' => LP_H5P_CPT,
				'context'    => 'normal',
				'priority'   => 'high',
				'fields'     => array(
					array(
						'name'      => __( 'Interact H5P', 'learnpress-h5p' ),
						'id'        => '_lp_h5p_interact',
						'type'      => 'select',
						'multiple'  => false,
						'options'   => $h5p_chosen,
						'desc'      => $edit_link,
						'desc_none' => wp_kses(
							__( 'There is no items to select. Create <a href="' . admin_url( 'admin.php?page=h5p_new' ) . '" target="_blank">here</a>.', 'learnpress-h5p' ),
							array(
								'a' => array(
									'href'   => array(),
									'target' => array(),
								),
							)
						),
					),
					array(
						'name'     => __( 'Passing Grade (%)', 'learnpress-h5p' ),
						'desc'     => __( 'Requires user reached this point to pass this h5p content item.', 'learnpress-h5p' ),
						'id'       => '_lp_passing_grade',
						'type'     => 'number',
						'min'      => 0,
						'max'      => 100,
						'required' => true,
						'step'     => 1,
						'std'      => 50,
					),
				),
			);

			return apply_filters( 'learn_press_h5p_general_settings_meta_box', $meta_box );
		}

		/**
		 * Add H5P link in LP settings for profile.
		 *
		 * @param $settings
		 * @param $profile
		 *
		 * @return mixed
		 */
		public function lp_h5p_add_setting_profile_link( $settings, $profile ) {

			$lp_settings  = LearnPress::instance()->settings();
			$user         = wp_get_current_user();
			$username     = $user->user_login;
			$profile_slug = 'profile';
			$profile_id   = learn_press_get_page_id( 'profile' );

			if ( $profile_id ) {
				$profile_post = get_post( learn_press_get_page_id( 'profile' ) );
				$profile_slug = $profile_post->post_name;
			}
			$profile_url = site_url() . '/' . $profile_slug . '/' . $username;

			foreach ( $settings as $index => $setting ) {
				if ( isset( $setting['id'] ) && $setting['id'] == 'profile_endpoints[profile-quizzes]' ) {
					array_splice(
						$settings,
						$index + 1,
						0,
						array(
							array(
								'title'       => __( 'H5P Items', 'learnpress-h5p' ),
								'id'          => 'profile_endpoints[profile-h5p]',
								'type'        => 'text',
								'default'     => 'h5p',
								'placeholder' => 'h5p',
								'desc'        => sprintf( __( 'Example link is %s', 'learnpress-h5p' ), "<code>{$profile_url}/" . $lp_settings->get( 'profile_endpoints.h5p', 'h5p' ) . '</code>' ),
							),
						)
					);
					break;
				}
			}

			return $settings;
		}

		/**
		 * Add H5P publicity in LP settings for profile.
		 *
		 * @param $settings
		 *
		 * @return mixed
		 */
		public function lp_h5p_add_setting_profile_publicity( $settings ) {
			foreach ( $settings as $index => $setting ) {
				if ( isset( $setting['id'] ) && $setting['id'] == 'profile_publicity[quizzes]' ) {
					array_splice(
						$settings,
						$index + 1,
						0,
						array(
							array(
								'title'      => esc_html__( 'H5P Items', 'learnpress-h5p' ),
								'id'         => 'profile_publicity[h5p]',
								'default'    => 'no',
								'type'       => 'yes-no',
								'desc'       => esc_html__( 'Public user profile H5P items.', 'learnpress-h5p' ) . learn_press_quick_tip( __( 'Allow user to turn on/off sharing profile H5P Items option', 'learnpress-h5p' ), false ),
								'visibility' => array(
									'state'       => 'show',
									'conditional' => array(
										array(
											'field'   => 'profile_publicity[dashboard]',
											'compare' => '=',
											'value'   => 'yes',
										),
									),
								),
							),
						)
					);
					break;
				}
			}

			return $settings;
		}

		/**
		 * Add h5p content link in LP settings for course.
		 *
		 * @param $settings
		 *
		 * @return mixed
		 */
		public function add_setting_course_link( $settings ) {
			foreach ( $settings as $index => $setting ) {
				if ( isset( $setting['id'] ) && $setting['id'] == 'quiz_slug' ) {
					array_splice(
						$settings,
						$index + 1,
						0,
						array(
							array(
								'title'   => esc_html__( 'H5P Item', 'learnpress-h5p' ),
								'type'    => 'text',
								'id'      => 'h5p_slug',
								'desc'    => sprintf( '%s/course/sample-course/<code>h5p</code>/sample-h5p/', home_url() ),
								'default' => 'h5p',
							),
						)
					);
					break;
				}
			}

			return $settings;
		}

		/**
		 * Add course assessment types.
		 *
		 * @param $meta_box
		 *
		 * @return mixed
		 */
		public function update_evaluate_options( $meta_box ) {
			$course_result_option_desc = array(
				'evaluate_h5p_items'              => __( '<p>Evaluate by number of H5P items completed per number of total H5P items.</p>', 'learnpress-h5p' )
											   . __( '<p>=> Result = Completed / Total</p>', 'learnpress-h5p' ),
				'evaluate_h5p_passed_items'       => __( '<p>Evaluate by achieved points of H5P passed per total point of all H5P.</p>', 'learnpress-h5p' )
												. __( '<p>=> Result = Passed / Total</p>' ),
				'evaluate_h5p_quizz_passed_items' => __( '<p>Evaluate by achieved points of H5P + Quizzes passed per total point of all H5P and Quizzes.</p>', 'learnpress-h5p' )
												. __( '<p>=> Result = Passed / Total</p>' ),
			);

			if ( isset( $meta_box['fields'][0]['options'] ) ) {
				$meta_options                                    = $meta_box['fields'][0]['options'];
				$meta_options['evaluate_h5p_items']              = __( 'Evaluate via results of the H5P completed', 'learnpress-h5p' ) . learn_press_quick_tip( $course_result_option_desc['evaluate_h5p_items'], false );
				$meta_options['evaluate_h5p_passed_items']       = __( 'Evaluate via results of H5P passed', 'learnpress-h5p' ) . learn_press_quick_tip( $course_result_option_desc['evaluate_h5p_passed_items'], false );
				$meta_options['evaluate_h5p_quizz_passed_items'] = __( 'Evaluate via results of H5P and Quizzes passed', 'learnpress-h5p' ) . learn_press_quick_tip( $course_result_option_desc['evaluate_h5p_quizz_passed_items'], false );
				$meta_box['fields'][0]['options']                = $meta_options;
			}

			return $meta_box;
		}

		/**
		 * Add evaluation in LP4
		 *
		 * @param array $meta_box
		 * @return array
		 */
		public function evaluate_method( $meta_box ) {
			$course_result_option_desc = array(
				'evaluate_h5p_items'              => __( '<p>Evaluate by number of H5P items completed per number of total H5P items.</p>', 'learnpress-h5p' )
											   . __( '<p>=> Result = Completed / Total</p>', 'learnpress-h5p' ),
				'evaluate_h5p_passed_items'       => __( '<p>Evaluate by achieved points of H5P passed per total point of all H5P.</p>', 'learnpress-h5p' )
												. __( '<p>=> Result = Passed / Total</p>' ),
				'evaluate_h5p_quizz_passed_items' => __( '<p>Evaluate by achieved points of H5P + Quizzes passed per total point of all H5P and Quizzes.</p>', 'learnpress-h5p' )
												. __( '<p>=> Result = Passed / Total</p>' ),
			);

			$meta_box['evaluate_h5p_items']              = __( 'Evaluate via results of the H5P completed', 'learnpress-h5p' ) . learn_press_quick_tip( $course_result_option_desc['evaluate_h5p_items'], false );
			$meta_box['evaluate_h5p_passed_items']       = __( 'Evaluate via results of H5P passed', 'learnpress-h5p' ) . learn_press_quick_tip( $course_result_option_desc['evaluate_h5p_passed_items'], false );
			$meta_box['evaluate_h5p_quizz_passed_items'] = __( 'Evaluate via results of H5P and Quizzes passed', 'learnpress-h5p' ) . learn_press_quick_tip( $course_result_option_desc['evaluate_h5p_quizz_passed_items'], false );

			return $meta_box;
		}

		/**
		 * @param $join
		 *
		 * @return string
		 */
		public function posts_join_paged( $join ) {
			if ( ! $this->_is_archive() ) {
				return $join;
			}
			global $wpdb;
			if ( $this->_filter_course() || ( $this->_get_orderby() == 'course-name' ) || $this->_get_search() ) {
				$join .= " LEFT JOIN {$wpdb->prefix}learnpress_section_items si ON {$wpdb->posts}.ID = si.item_id";
				$join .= " LEFT JOIN {$wpdb->prefix}learnpress_sections s ON s.section_id = si.section_id";
				$join .= " LEFT JOIN {$wpdb->posts} c ON c.ID = s.section_course_id";
			}

			return $join;
		}

		/**
		 * @param $where
		 *
		 * @return mixed|null|string|string[]
		 */
		public function posts_where_paged( $where ) {
			if ( ! $this->_is_archive() ) {
				return $where;
			}

			global $wpdb;

			$course_id = $this->_filter_course();

			if ( $course_id ) {
				$where .= $wpdb->prepare( ' AND (c.ID = %d)', $course_id );
			}

			if ( isset( $_GET['s'] ) ) {
				$s     = $_GET['s'];
				$where = preg_replace(
					"/\.post_content\s+LIKE\s*(\'[^\']+\')\s*\)/",
					" .post_content LIKE '%$s%' ) OR (c.post_title LIKE '%$s%' )",
					$where
				);
			}

			if ( 'yes' === LP_Request::get( 'unassigned' ) ) {
				$where .= $wpdb->prepare(
					"
                    AND {$wpdb->posts}.ID NOT IN(
                        SELECT si.item_id
                        FROM {$wpdb->learnpress_section_items} si
                        INNER JOIN wp_posts p ON p.ID = si.item_id
                        WHERE p.post_type = %s
                    )
                ",
					LP_H5P_CPT
				);
			}

			return $where;
		}

		/**
		 * Add filters to lesson view.
		 *
		 * @since 3.0.0
		 *
		 * @param array $views
		 *
		 * @return mixed
		 */
		public function views_pages( array $views ) {
			$count_unassigned_h5p = LP_Course_DB::getInstance()->get_total_item_unassigned( LP_H5P_CPT );

			if ( $count_unassigned_h5p > 0 ) {
				$views['unassigned'] = sprintf(
					'<a href="%s" class="%s">%s <span class="count">(%d)</span></a>',
					admin_url( 'edit.php?post_type=' . LP_H5P_CPT . '&unassigned=yes' ),
					isset( $_GET['unassigned'] ) ? 'current' : '',
					__( 'Unassigned', 'learnpress' ),
					$count_unassigned_h5p
				);
			}

			return $views;
		}

		/**
		 * Add columns to admin manage h5p page.
		 *
		 * @param  array $columns
		 *
		 * @return array
		 */
		public function columns_head( $columns ) {
			$pos = array_search( 'title', array_keys( $columns ) );

			if ( false !== $pos && ! array_key_exists( 'lp_course', $columns ) ) {
				$columns = array_merge(
					array_slice( $columns, 0, $pos + 1 ),
					array(
						'author'       => __( 'Author', 'learnpress-h5p' ),
						'lp_course'    => __( 'Course', 'learnpress-h5p' ),
						'interact_h5p' => __( 'Interacted H5P', 'learnpress-h5p' ),
					),
					array_slice( $columns, $pos + 1 )
				);
			}

			unset( $columns['taxonomy-lesson-tag'] );
			$user = wp_get_current_user();

			if ( in_array( 'lp_teacher', $user->roles ) ) {
				unset( $columns['author'] );
			}

			return $columns;
		}


		/**
		 * @return bool
		 */
		private function _get_search() {
			return isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : false;
		}

		/**
		 * @return string
		 */
		private function _get_orderby() {
			return isset( $_REQUEST['orderby'] ) ? $_REQUEST['orderby'] : '';
		}

		/**
		 * @return bool
		 */
		private function _is_archive() {
			global $pagenow, $post_type;

			if ( ! is_admin() || ( $pagenow != 'edit.php' ) || ( LP_H5P_CPT != $post_type ) ) {
				return false;
			}

			return true;
		}

		/**
		 * @param $order_by_statement
		 *
		 * @return string
		 */
		public function posts_orderby( $order_by_statement ) {
			if ( ! $this->_is_archive() ) {
				return $order_by_statement;
			}

			global $wpdb;

			if ( isset( $_GET['orderby'] ) && isset( $_GET['order'] ) ) {
				switch ( $_GET['orderby'] ) {
					case 'course-name':
						$order_by_statement = "c.post_title {$_GET['order']}";
						break;
					default:
						$order_by_statement = "{$wpdb->posts}.post_title {$_GET['order']}";
				}
			}

			return $order_by_statement;
		}

		/**
		 * @param $columns
		 *
		 * @return mixed
		 */
		public function sortable_columns( $columns ) {
			$columns['author']    = 'author';
			$columns['lp_course'] = 'course-name';

			return $columns;
		}


		/**
		 * Display content for custom column
		 *
		 * @param string $name
		 * @param int    $post_id
		 */
		public function columns_content( $name, $post_id = 0 ) {
			// H5P curd
			$curd = new LP_H5p_CURD();

			switch ( $name ) {
				case 'lp_course':
					$courses = learn_press_get_item_courses( $post_id );
					if ( $courses ) {
						foreach ( $courses as $course ) {
							echo '<div><a href="' . esc_url( add_query_arg( array( 'filter_course' => $course->ID ) ) ) . '">' . get_the_title( $course->ID ) . '</a>';
							echo '<div class="row-actions">';
							printf( '<a href="%s">%s</a>', admin_url( sprintf( 'post.php?post=%d&action=edit', $course->ID ) ), __( 'Edit', 'learnpress-h5p' ) );
							echo '&nbsp;|&nbsp;';
							printf( '<a href="%s">%s</a>', get_the_permalink( $course->ID ), __( 'View', 'learnpress-h5p' ) );
							echo '</div></div>';
						}
					} else {
						esc_html_e( 'Not assigned yet', 'learnpress-h5p' );
					}
					break;
				case 'interact_h5p':
					$appended_h5p = get_post_meta( $post_id, '_lp_h5p_interact', true );
					if ( ! empty( $appended_h5p ) ) {
						$h5p_chosen_title = lp_h5p_get_content_title( $appended_h5p );
						printf( '<a href="%s" target="_blank">%s</a>', admin_url( sprintf( 'admin.php?page=h5p&task=show&id=%d', $appended_h5p ) ), $h5p_chosen_title );
					} else {
						echo esc_html__( 'Not Yet!', 'learnpress-h5p' );
					}
					break;
				case 'mark':
					$maximum_mark = ( get_post_meta( $post_id, '_lp_mark', true ) ) ? get_post_meta( $post_id, '_lp_mark', true ) : 10;
					echo $maximum_mark;
					break;
				case 'passing_grade':
					$passing_grade = ( get_post_meta( $post_id, '_lp_passing_grade', true ) ) ? get_post_meta( $post_id, '_lp_passing_grade', true ) : 7;
					echo $passing_grade;
					break;
				case 'duration':
					$duration = learn_press_human_time_to_seconds( get_post_meta( $post_id, '_lp_duration', true ) );
					if ( $duration > 86399 ) {
						echo get_post_meta( $post_id, '_lp_duration', true ) . '(s)';
					} elseif ( $duration >= 600 ) {
						echo gmdate( 'H:i:s', $duration );
					} elseif ( $duration > 0 ) {
						echo gmdate( 'i:s', $duration );
					} else {
						echo '-';
					}
					break;
				default:
					break;
			}
		}

		/**
		 * H5P assigned view.
		 *
		 * @since 3.0.0
		 */
		public static function h5p_assigned() {
			learn_press_admin_view( 'meta-boxes/course/assigned.php' );
		}

		/**
		 * @return bool|int
		 */
		private function _filter_course() {
			return ! empty( $_REQUEST['filter_course'] ) ? absint( $_REQUEST['filter_course'] ) : false;
		}

		/**
		 * @return LP_H5P_Post_Type|null
		 */
		public static function instance() {
			if ( ! self::$_instance ) {
				self::$_instance = new self( LP_H5P_CPT, array() );
			}

			return self::$_instance;
		}
	}

	// LP_Assignmen_Post_Type
	$h5pcontent_post_type = LP_H5pcontent_Post_Type::instance();

	// add meta box
	$h5pcontent_post_type
		->add_meta_box( 'h5p_assigned', esc_html__( 'Assigned', 'learnpress-h5p' ), 'h5p_assigned', 'side', 'high' )
		->add_meta_box( 'h5p_settings', esc_html__( 'General Settings', 'learnpress-h5p' ), 'LP_H5P_Meta_Box::output', 'normal', 'high' );
}
