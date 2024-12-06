<?php

use LearnPress\Models\CourseModel;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LP_PMS_Background_Single_Course' ) ) {
	/**
	 * Class LP_PMS_Background_Single_Course
	 *
	 * Single to run not schedule, run one time and done when be call
	 *
	 * @version 1.0.0
	 * @since 4.0.2
	 * @author minhpd
	 */
	class LP_PMS_Background_Single_Course extends LP_Async_Request {
		protected $prefix = 'lp_pms';
		protected $action = 'create_lp_order_when_change_membership';
		protected static $instance;

		protected function handle() {
			try {
				@set_time_limit( 0 );
				$params = array(
					'level_id'   => LP_Request::get_param( 'level_id', 0, 'int', 'post' ),
				);

				if ( ! empty( $_POST['level_course_ids'] ) ) {
					$params['lp_orders']        = LP_Helper::sanitize_params_submitted( $_POST['lp_orders'] ?? array() );
					$params['level_course_ids'] = LP_Helper::sanitize_params_submitted( $_POST['level_course_ids'] );

					$this->handleLevelChangeCourses( $params );
				} else {
					$params['user_id']     = LP_Request::get_param( 'user_id', 0, 'int', 'post' );
					$params['lp_order_id'] = LP_Request::get_param( 'lp_order_id', 0, 'int', 'post' );

					$this->handleAddItemsToLpOrder( $params );
				}
			} catch ( Throwable $e ) {
				error_log( __METHOD__ . ': ' . $e->getMessage() );
			}
		}

		/**
		 * handle add course to lp_order
		 *
		 * @param array $params
		 *
		 * @throws Exception
		 * @version 1.0.1
		 * @since 4.0.0
		 */
		protected function handleAddItemsToLpOrder( array $params ) {
			$lp_order_id  = $params['lp_order_id'] ?? 0;
			$pms_level_id = $params['level_id'] ?? 0;
			if ( empty( $lp_order_id ) || empty( $pms_level_id ) ) {
				return;
			}

			$lp_order = learn_press_get_order( $lp_order_id );
			if ( ! $lp_order ) {
				return;
			}

			$courses    = LP_PMS_DB::getInstance()->getCoursesByLevel( $pms_level_id );
			$course_ids = LP_Database::get_values_by_key( $courses );

			foreach ( $course_ids as $course_id ) {
				$course = CourseModel::find( $course_id, true );
				if ( ! $course ) {
					continue;
				}

				$item = array(
					'item_id'         => $course_id,
					'order_item_name' => $course->get_title(),
				);

				if ( ! $course->is_in_stock() && ! $course->has_no_enroll_requirement() ) {
					$course_out_stock[] = $course_id;
					$item['quantity']  = 0;
				}

				$lp_order->add_item( $item );
			}

			$lp_order->set_status( LP_ORDER_COMPLETED );
			$lp_order->save();

			$value_course_out_stock = ! empty( $course_out_stock ) ? implode( ',', $course_out_stock ) : '';
			update_post_meta( $lp_order_id, '_lp_course_out_stock', $value_course_out_stock );

			//$lp_pms_order->addItemsToLpOrder( $params, $course_ids );
		}

		/**
		 * Change courses on level
		 *
		 * @param array $params
		 *
		 * @throws Exception
		 */
		protected function handleLevelChangeCourses( array $params ) {
			$lp_pms_order     = new LP_PMS_Order();
			$level_course_ids = $params['level_course_ids'];
			$lp_orders        = $params['lp_orders'];

			foreach ( $lp_orders as $lp_order_data ) {
				$order_id = absint( $lp_order_data['order_id'] ?? 0 );
				$user_id  = absint( $lp_order_data['user_id'] ?? 0 );
				if ( empty( $order_id ) || empty( $user_id ) ) {
					continue;
				}

				$lp_order = learn_press_get_order( $order_id );

				// Get course ids on LP order
				$order_course_ids  = LP_PMS_DB::getInstance()->getCourseIdsOnLpOrder( $order_id );
				$order_course_ids  = array_keys( $order_course_ids );
				$remove_course_ids = array_diff( $order_course_ids, $level_course_ids );
				$add_course_ids    = array_diff( $level_course_ids, $order_course_ids );

				// Delete courses on Order
				if ( count( $remove_course_ids ) > 0 ) {
					foreach ( $remove_course_ids as $course_id ) {
						$lp_order->remove_item( $course_id );
					}
				}

				// Add courses to Order
				if ( count( $add_course_ids ) > 0 ) {
					foreach ( $add_course_ids as $course_id ) {
						$course = CourseModel::find( $course_id, true );
						if ( ! $course ) {
							continue;
						}

						$item = array(
							'item_id'         => $course_id,
							'order_item_name' => $course->get_title(),
						);

						$lp_order->add_item( $item );
					}
				}

				if ( $lp_order->get_status() === LP_ORDER_COMPLETED ) {
					$lp_order->set_status( LP_ORDER_PENDING );
					$lp_order->save();

					$lp_order->set_status( LP_ORDER_COMPLETED );
					$lp_order->save();
				}
			}
		}

		/**
		 * @return LP_PMS_Background_Single_Course
		 */
		public static function instance(): self {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}
	}

	// Must run instance to register ajax.
	LP_PMS_Background_Single_Course::instance();
}
