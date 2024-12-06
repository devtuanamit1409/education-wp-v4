<?php
use LearnPress\Models\CourseModel;

/**
 * Class LP_PMPro_User_Hooks
 */
class LP_PMPro_User_Hooks {

	public function __construct() {
		// add hook for CAN ENROLL COURSE
		add_filter( 'learn_press_user_can_enroll_course', array( $this, 'can_enroll_course_callback' ), 11, 3 );
		add_filter( 'learnpress/course/template/button-enroll/can-show', array( $this, 'can_show_button_enroll_course_callback' ), 11, 3 );

		// add hook for CAN PURCHASE COURSE
		//add_filter( 'learnpress/course/template/button-purchase/can-show', array( $this, 'can_show_button_purchase_course_callback' ), 11, 3 );
		add_filter( 'learn-press/user/can-purchase-course', array( $this, 'can_purchase_course_callback' ), 11, 3 );

		// add hook for can_retake_course
		// add hook for can_retake_course
		// add_filter( 'learn_press_user_can_retake_course', array( $this, 'can_retake_course_callback' ), 11, 3 );
		// add_filter( 'learn_press_user_has_purchased_course', array( $this, 'has_purchased_course_callback' ), 11, 3 );
		// tungnx

		add_filter( 'learn_press/order_item_name', array( $this, 'name_course_order' ), 10, 3 );
		add_filter( 'learn_pres_pmpro_course_header_level', array( $this, 'name_course_membership_checkout' ), 10, 3 );
	}

	/**
	 * @param $can_purchase
	 * @param $user_id
	 * @param $course_id
	 * @return bool
	 */
	public function can_purchase_course_callback( $can_purchase, $user_id, $course_id ) {
		if ( ! $course_id ) {
			return $can_purchase;
		}

		$course_levels  = learn_press_pmpro_get_course_levels( $course_id );
		$buy_membership = ( LearnPress::instance()->settings()->get( 'buy_through_membership' ) === 'yes' );

		$has_membership_level = learn_press_pmpro_hasMembershipLevel( $course_levels, $user_id );

		if ( $has_membership_level ) {
			return $can_purchase;
		}

		if ( ! empty( $course_levels ) ) {
			if ( $buy_membership ) {
				$can_purchase = new WP_Error( 'course_buy_through_mbs', __( 'Buy course only via membership', 'learnpress-paid-membership-pro' ) );
			}
		}

		return $can_purchase;
	}

	/**
	 * @param $can_show
	 * @param $user
	 * @param $course
	 * @return bool
	 */
	public function can_show_button_enroll_course_callback( $can_show, $user, $course ) {

		if ( ! $course || ! $user ) {
			return $can_show;
		}

		$user_id        = $user->get_id();
		$course_id      = $course->get_id();
		$course_levels  = learn_press_pmpro_get_course_levels( $course_id );
		$buy_membership = ( LearnPress::instance()->settings()->get( 'buy_through_membership' ) === 'yes' );

		$has_membership_level = learn_press_pmpro_hasMembershipLevel( $course_levels, $user_id );

		if ( $has_membership_level ) {
			return $can_show;
		}

		if ( ! empty( $course_levels ) ) {
			if ( $buy_membership ) {
				$can_show = false;
			}
		}

		return $can_show;
	}

	// /**
	//  * @param $can_purchase
	//  * @param $user
	//  * @param $course
	//  * @return bool
	//  */
	// public function can_show_button_purchase_course_callback( $can_purchase, $user, $course ) {

	// 	if ( ! $course || ! $user ) {
	// 		return $can_purchase;
	// 	}
	// 	$user_id        = $user->get_id();
	// 	$course_id      = $course->get_id();
	// 	$course_levels  = learn_press_pmpro_get_course_levels( $course_id );
	// 	$buy_membership = LearnPress::instance()->settings()->get( 'buy_through_membership' ) === 'yes';

	// 	$has_membership_level = learn_press_pmpro_hasMembershipLevel( $course_levels, $user_id );

	// 	if ( $has_membership_level ) {
	// 		return $can_purchase;
	// 	}

	// 	if ( ! empty( $course_levels ) ) {
	// 		if ( $buy_membership ) {
	// 			$can_purchase = false;
	// 		}
	// 	}

	// 	return $can_purchase;
	// }

	public function can_retake_course_callback( $can_retake, $course_id, $user_id ) {
		if ( $can_retake ) {
			return $can_retake;
		}

		$course_levels = learn_press_pmpro_get_course_levels( $course_id );
		if ( empty( $course_levels ) ) {
			return $can_retake;
		}

		$has_membership_level = learn_press_pmpro_hasMembershipLevel( $course_levels, $user_id );
		if ( $has_membership_level ) {
			if ( ! $can_retake || $can_retake < 1 ) {
				$can_retake = 1;
			}
		}

		return $can_retake;
	}

	public function has_purchased_course_callback( $has_purchased, $course_id, $user_id ) {
		if ( $has_purchased ) {
			$user          = learn_press_get_user( $user_id );
			$course_status = $user->get_course_status( $course_id );

			if ( $course_status === 'canceled' ) {
				$has_purchased = false;
			} elseif ( $course_status === 'finished' && ! $user->can_retake_course( $course_id ) ) {
				$has_purchased = false;
			}
		}

		return $has_purchased;
	}

	/**
	 * Display label course full students.
	 *
	 * @param $name
	 * @param $item
	 * @param $order
	 *
	 * @return mixed|string
	 */
	public function name_course_order( $name, $item, $order ) {
		$course_id         = $item['course_id'] ?? '';
		$courses_out_stock = $this->get_items_out_stock( $order->get_id() );
		if ( empty( $courses_out_stock ) ) {
			return $name;
		}

		if ( in_array( $course_id, $courses_out_stock ) ) {
			$name = sprintf( '%s - %s', $name, esc_html__( 'The course is full of students.', 'learnpress' ) );
		}

		return $name;
	}

		/**
	 * Get items out stock in order
	 *
	 * @param int $order_id
	 *
	 * @return array
	 * @author  VuxMinhThanh
	 */
	public function get_items_out_stock( int $order_id ): array {
		$course_ids_out_of_stock = [];
		if ( empty( $order_id ) ) {
			return $course_ids_out_of_stock;
		}

		$key                         = '_lp_course_out_stock';
		$course_ids_out_of_stock_str = get_post_meta( $order_id, $key, true );
		if ( ! empty( $course_ids_out_of_stock_str ) ) {
			$course_ids_out_of_stock = explode( ',', $course_ids_out_of_stock_str );
		}

		return $course_ids_out_of_stock;
	}

	public function name_course_membership_checkout( $link, $course_item, $key ) {
		$course_id = $key['id'];
		$course    = CourseModel::find( $course_id );

		if ( ! $course->is_in_stock() && ! $course->has_no_enroll_requirement() ) {
			$link = '<td class="list-main item-td">' . wp_kses_post( $course_item ) . ' - ' . esc_html__( 'The course is full of students.', 'learnpress' ) . '</td>';
		}

		return $link;
	}
}

$pmpro_user = new LP_PMPro_User_Hooks();
