<?php
/**
 * Class LP_H5p_CURD
 *
 * @author  ThimPress
 * @package LearnPress/H5p/Classes/CURD
 * @since   4.0.0
 */

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'LP_H5p_CURD' ) ) {

	/**
	 * Class LP_H5p_CURD
	 */
	class LP_H5p_CURD extends LP_Object_Data_CURD implements LP_Interface_CURD {

		/**
		 * Create h5p content item, with default meta.
		 *
		 * @param $args
		 *
		 * @return int|WP_Error
		 */
		public function create( &$args ) {
			$args = wp_parse_args(
				$args,
				array(
					'id'      => '',
					'status'  => 'publish',
					'title'   => esc_html__( 'New H5P Item', 'learnpress-h5p' ),
					'content' => '',
					'author'  => learn_press_get_current_user_id(),
				)
			);

			$h5p_id = wp_insert_post(
				array(
					'ID'           => $args['id'],
					'post_type'    => LP_H5P_CPT,
					'post_status'  => $args['status'],
					'post_title'   => $args['title'],
					'post_content' => $args['content'],
					'post_author'  => $args['author'],
				)
			);

			if ( $h5p_id ) {
				$default_meta = LP_H5p::get_default_meta();

				if ( is_array( $default_meta ) ) {
					foreach ( $default_meta as $key => $value ) {
						update_post_meta( $h5p_id, '_lp_' . $key, $value );
					}
				}
			}

			return $h5p_id;
		}

		/**
		 * @param object $h5p
		 */
		public function update( &$h5p ) {
			// TODO: Implement update() method.
		}

		/**
		 * Delete h5p.
		 *
		 * @param object $h5p_id
		 *
		 * @since 3.0.0
		 *
		 */
		public function delete( &$h5p_id ) {
			$curd = new LP_Course_CURD();

			// allow hook
			do_action( 'learn-press/before-delete-h5p', $h5p_id );

			// remove h5p from course items
			$curd->remove_item( $h5p_id );
		}

		/**
		 * Duplicate h5p.
		 *
		 * @param       $h5p_id
		 * @param array $args
		 *
		 * @return mixed|WP_Error
		 * @since 3.0.0
		 *
		 */
		public function duplicate( &$h5p_id, $args = array() ) {

			if ( ! $h5p_id ) {
				return new WP_Error( __( '<p>Op! ID not found</p>', 'learnpress-h5p' ) );
			}

			if ( get_post_type( $h5p_id ) != LP_H5P_CPT ) {
				return new WP_Error( __( '<p>Op! The h5p does not exist</p>', 'learnpress-h5p' ) );
			}

			// ensure that user can create h5p
			if ( ! current_user_can( 'edit_posts' ) ) {
				return new WP_Error(
					__(
						'<p>Sorry! You don\'t have permission to duplicate this h5p</p>',
						'learnpress-h5p'
					)
				);
			}

			// duplicate h5p
			$new_h5p_id = learn_press_duplicate_post( $h5p_id, $args );

			if ( ! $new_h5p_id || is_wp_error( $new_h5p_id ) ) {
				return new WP_Error( __( '<p>Sorry! Failed to duplicate h5p!</p>', 'learnpress-h5p' ) );
			} else {
				return $new_h5p_id;
			}
		}

		/**
		 * Load h5p data.
		 *
		 * @param object $h5p
		 *
		 * @return object
		 * @throws Exception
		 * @since 3.0.0
		 *
		 */
		public function load( &$h5p ) {
			$id = $h5p->get_id();

			if ( ! $id || get_post_type( $id ) !== LP_H5P_CPT ) {
				throw new Exception( sprintf( __( 'Invalid h5p with ID "%d".', 'learnpress-h5p' ), $id ) );
			}

			$h5p->set_data_via_methods(
				array(
					'passing_grade' => get_post_meta( $h5p->get_id(), '_lp_passing_grade', true ),
					'h5p_interact'  => get_post_meta( $h5p->get_id(), '_lp_h5p_interact', true ),
				)
			);

			return $h5p;
		}

		/**
		 * @param $h5p
		 *
		 * @return array|null|object
		 */
		public function get_students( $h5p ) {
			global $wpdb;

			$h5p   = LP_H5p::get_h5p( $h5p );
			$query = $wpdb->prepare(
				"
				SELECT DISTINCT student.* FROM {$wpdb->users} AS student
				INNER JOIN {$wpdb->prefix}learnpress_user_items AS user_item
				ON user_item.user_id = student.ID
				WHERE user_item.item_id = %d AND user_item.item_type = %s AND user_item.status IN (%s, %s)
			",
				$h5p->get_id(),
				LP_H5P_CPT,
				'completed',
				'evaluated'
			);

			$students = $wpdb->get_results( $query, ARRAY_A );

			return $students;
		}

		/**
		 * @param int    $user_id
		 * @param string $args
		 *
		 * @return LP_Query_List_Table
		 */
		public function profile_query_h5p_items( $user_id = 0, $args = '' ) {
			global $wpdb, $wp;

			$paged = 1;

			if ( ! empty( $wp->query_vars['view_id'] ) ) {
				$paged = absint( $wp->query_vars['view_id'] );
			}

			$paged = max( $paged, 1 );
			$args  = wp_parse_args(
				$args,
				array(
					'paged'  => $paged,
					'limit'  => 10,
					'status' => '',
				)
			);

			if ( ! $user_id ) {
				$user_id = get_current_user_id();
			}

			$cache_key = sprintf( 'h5p-%d-%s', $user_id, md5( build_query( $args ) ) );
			$h5p       = wp_cache_get( $cache_key, 'lp-user-h5p' );

			if ( false === $h5p ) {
				$user_curd = new LP_User_CURD();
				$orders    = $user_curd->get_orders( $user_id );

				$query = array(
					'total' => 0,
					'pages' => 0,
					'items' => false,
				);

				$h5pitems = array(
					'total' => 0,
					'paged' => $args['paged'],
					'limit' => $args['limit'],
					'pages' => 0,
					'items' => array(),
				);

				try {
					if ( ! $orders ) {
						throw new Exception( '', 0 );
					}

					$course_ids   = array_keys( $orders );
					$query_args   = $course_ids;
					$query_args[] = $user_id;

					$select  = 'SELECT * ';
					$from    = "FROM {$wpdb->learnpress_user_items}";
					$join    = $wpdb->prepare(
						"INNER JOIN {$wpdb->posts} c ON c.ID = item_id AND c.post_type = %s",
						LP_H5P_CPT
					);
					$where   = $wpdb->prepare( 'WHERE 1 AND user_id = %d', $user_id );
					$orderby = 'ORDER BY item_id, user_item_id DESC';

					if ( ! empty( $args['status'] ) ) {
						switch ( $args['status'] ) {
							case 'completed':
							case 'passed':
							case 'failed':
								$where .= $wpdb->prepare( ' AND status IN( %s )', array( 'completed' ) );

								if ( $args['status'] !== 'completed' ) {
									if ( 'passed' === $args['status'] ) {
										$where .= $wpdb->prepare( '  AND graduation = %s', 'passed' );
									} else {
										$where .= $wpdb->prepare(
											'  AND ( graduation IS NULL OR graduation <> %s )',
											'passed'
										);
									}
								}

								break;
							case 'doing':
								$where .= $wpdb->prepare( ' AND status IN( %s )', array( 'h5p_doing' ) );
						}
					}

					$limit  = $args['limit'];
					$offset = ( $args['paged'] - 1 ) * $limit;

					$query_parts = apply_filters(
						'learn-press/query/user-h5pitemss',
						compact( 'select', 'from', 'join', 'where', 'orderby' ),
						$user_id,
						$args
					);

					list( $select, $from, $join, $where, $orderby ) = array_values( $query_parts );

					$sql = "
							{$select}
							{$from}
							{$join}
							{$where}
							{$orderby}
							LIMIT {$offset}, {$limit}
						";

					$items = $wpdb->get_results( $sql, ARRAY_A );

					if ( $items ) {
						$count      = $wpdb->get_var( 'SELECT FOUND_ROWS()' );
						$course_ids = wp_list_pluck( $items, 'item_id' );
						LP_Helper::cache_posts( $course_ids );

						$h5pitems['total'] = $count;
						$h5pitems['pages'] = ceil( $count / $args['limit'] );
						foreach ( $items as $item ) {
							$h5pitems['items'][] = new LP_User_Item_H5p( $item );
						}
					}
				} catch ( Exception $ex ) {

				}

				wp_cache_set( $cache_key, $h5pitems, 'lp-user-h5p' );
			}

			$h5pitems['single'] = esc_html__( 'H5P', 'learnpress-h5p' );
			$h5pitems['plural'] = esc_html__( 'H5P Items', 'learnpress-h5p' );

			return new LP_Query_List_Table( $h5pitems );
		}

		/**
		 * @param        $profile LP_Profile
		 * @param string $current_filter
		 *
		 * @return mixed
		 */
		public function get_h5p_items_filters( $profile, $current_filter = '' ) {
			$url = $profile->get_tab_link( 'h5p' );

			$defaults = array(
				'all'       => sprintf( '<a href="%s">%s</a>', esc_url( $url ), esc_html__( 'All', 'learnpress-h5p' ) ),
				'completed' => sprintf(
					'<a href="%s">%s</a>',
					esc_url( add_query_arg( 'filter-status', 'completed', $url ) ),
					esc_html__( 'Completed', 'learnpress-h5p' )
				),
				'doing'     => sprintf(
					'<a href="%s">%s</a>',
					esc_url( add_query_arg( 'filter-status', 'doing', $url ) ),
					esc_html__( 'Doing', 'learnpress-h5p' )
				),
				'passed'    => sprintf(
					'<a href="%s">%s</a>',
					esc_url( add_query_arg( 'filter-status', 'passed', $url ) ),
					esc_html__( 'Passed', 'learnpress-h5p' )
				),
				'failed'    => sprintf(
					'<a href="%s">%s</a>',
					esc_url( add_query_arg( 'filter-status', 'failed', $url ) ),
					esc_html__( 'Failed', 'learnpress-h5p' )
				),
			);

			if ( ! $current_filter ) {
				$keys           = array_keys( $defaults );
				$current_filter = reset( $keys );
			}

			foreach ( $defaults as $k => $v ) {
				if ( $k === $current_filter ) {
					$defaults[ $k ] = sprintf( '<span>%s</span>', strip_tags( $v ) );
				}
			}

			return apply_filters( 'learn-press/profile/h5p_items-filters', $defaults );
		}
	}

}
