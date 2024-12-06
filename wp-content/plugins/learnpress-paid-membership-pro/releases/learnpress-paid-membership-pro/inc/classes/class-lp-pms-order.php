<?php
/**
 * Class LP_PMS_Order
 */

class LP_PMS_Order {
	public static $_instance;
	public static $_payment_method       = 'paid-memberships-pro';
	public static $_payment_method_title = '';
	public static $_key_lp_pmpro_level   = '_lp_pmpro_level'; // meta key for lp_order
	public static $_key_lp_pmpro_levels  = '_lp_pmpro_levels'; // meta key for lp_course
	public static $_loaded               = false;
	public static $_LIMIT_COURSES;
	public static $_LIMIT_ORDERS;
	public static $_CALL_CRON_JOB_AFTER_SECOND;
	public $_MBS_USER_OLD_LEVELS = array();

	public $redirect    = false;
	public $user_orders = array();

	public function __construct() {
		self::$_LIMIT_COURSES              = apply_filters( 'learn-press/pmspro/limit-courses', 10 );
		self::$_LIMIT_ORDERS               = apply_filters( 'learn-press/pmspro/limit-orders', 10 );
		self::$_CALL_CRON_JOB_AFTER_SECOND = apply_filters(
			'learn-press/pmspro/time-call-cron-job-after-second',
			5
		);

		add_filter( 'learn_press_display_payment_method_title', array( $this, 'display_payment_method_title' ), 10, 2 );

		// creat order membership success
		add_action(
			'pmpro_membership_post_membership_expiry',
			array( $this, 'membership_expire' ),
			10,
			2
		);
		add_action(
			'pmpro_before_change_membership_level',
			array( $this, 'before_update_lp_orders_when_change_membership_level' ),
			11,
			4
		);
		add_action(
			'pmpro_after_change_membership_level',
			array( $this, 'update_lp_orders_when_change_membership_level' ),
			11,
			3
		);

		// pmprommpu_addMembershipLevel | case multiple level

		// Admin add order
		//add_action( 'pmpro_add_order', array( $this, 'pms_added_order_will_add_lp_order' ) );

		// Admin change status order
		//add_action( 'pmpro_update_order', array( $this, 'pms_updated_order_will_add_lp_order' ) );
	}


	public function display_payment_method_title( $title, $payment_method ) {
		if ( self::$_payment_method == $payment_method ) {
			return $this->get_payment_method_title();
		}

		return $title;
	}

	/**
	 * Get payment method title
	 *
	 * @return string
	 */
	public function get_payment_method_title(): string {
		if ( ! self::$_payment_method_title ) {
			self::$_payment_method_title = __(
				'Pay via <strong>Paid Memberships Pro</strong>',
				'learnpress-paid-membership-pro'
			);
		}

		return self::$_payment_method_title;
	}

	/**
	 * @deprecated 4.0.6
	 */
	/*private function hasMembershipLevel( $level_id, $user_id ) {
		return learn_press_pmpro_hasMembershipLevel( $level_id, $user_id );
	}*/

	/**
	 * Create Lp order
	 *
	 * @param int $user_id
	 * @param int $level_id
	 *
	 * @return bool|int|WP_Error
	 */
	public function create_lp_order( $user_id = 0, $level_id = 0 ) {
		global $action;

		$action = 'no_editpost'; // learnpress\inc\custom-post-types\order.php search editpost
		$user   = learn_press_get_user( $user_id );

		// Check user, if Admin or User same is valid
		if ( current_user_can( 'administrator' ) ) {
			// Check user exists
			if ( ! $user ) {
				return new WP_Error( 'lp_pms_create_order', 'User not exists' );
			}
		}
		//Todo: Check why something didn't get current user_id
		/*elseif ( get_current_user_id() != $user_id ) { // Temporary not check
			return new WP_Error( 'lp_pms_create_order', 'Invalid User! You can\'t create LP Order' );
		}*/

		// get total courses by level
		$totalCourses = $this->get_total_courses_by_level( $level_id );

		if ( empty( $totalCourses ) || ! isset( $totalCourses->total ) || $totalCourses->total == 0 ) {
			return new WP_Error( 'lp_pms_create_order', 'Don\'t have any courses on level PMS' );
		}

		$totalPage = ceil( $totalCourses->total / self::$_LIMIT_COURSES );

		// create order
		$level      = pmpro_getLevel( $level_id );
		$level_cost = learn_press_pmpro_getLevelCost( $level, $user_id );
		$post_title = sprintf(
			__( 'Order on %s', 'learnpress-paid-membership-pro' ),
			current_time( 'l jS F Y h:i:s A' )
		);
		$order_data = array(
			'post_author' => $user_id,
			'post_parent' => '0',
			'post_type'   => LP_ORDER_CPT,
			'post_status' => LP_ORDER_PENDING_DB,
			'ping_status' => 'closed',
			'post_title'  => $post_title,
			'meta_input'  => array(
				'_user_id'                 => $user_id,
				'_created_via'             => 'lp_pms',
				'_payment_method'          => self::$_payment_method,
				'_payment_method_title'    => __( 'Memberships', 'learnpress-paid-membership-pro' ),
				'_order_total'             => $level_cost,
				'_checkout_email'          => $user->get_email(),
				self::$_key_lp_pmpro_level => $level_id,
			),
		);

		if ( isset( $_SESSION['wc_order_change_completed'] ) ) {
			/**
			 * @var WC_Order $_wc_order
			 */
			$_wc_order = $_SESSION['wc_order_change_completed'];

			$order_data['meta_input']['_woo_order_id']         = $_wc_order->get_id();
			$order_data['meta_input']['_payment_method']       = $_wc_order->get_payment_method();
			$order_data['meta_input']['_payment_method_title'] = 'Woocommerce: ' . $_wc_order->get_payment_method_title();

			unset( $_SESSION['wc_order_change_completed'] );
		}

		$order_id = wp_insert_post( $order_data );
		// End create order

		if ( $order_id instanceof WP_Error ) {
			return $order_id;
		}

		$params = array(
			'lp_order_id' => $order_id,
			'level_id'    => $level_id,
			'user_id'     => $user_id,
		);

		$this->handleAddItemsToLpOrderRemotePost( $params );

		return $order_id;
	}

	/**
	 * Run wp_remote_post add course to lp_order
	 *
	 * @param array $params
	 *
	 * @return void
	 * @author minhpd
	 * @version 1.0.0
	 * @sicne 4.0.2
	 */
	protected function handleAddItemsToLpOrderRemotePost( array $params ) {
		$bg = LP_PMS_Background_Single_Course::instance();
		$bg->data( $params )->dispatch();
	}

	/**
	 * Get total courses by MemberShip level
	 *
	 * @param int $level_id
	 *
	 * @return array|object|null
	 */
	public function get_total_courses_by_level( int $level_id = 0 ) {
		global $wpdb;
		$tabel_post     = $wpdb->posts;
		$tabel_postmeta = $wpdb->postmeta;

		$sql = $wpdb->prepare(
			"SELECT COUNT(p.ID) as total
			FROM $tabel_post AS p
			INNER JOIN $tabel_postmeta AS pm
			ON p.ID = pm.post_id
			WHERE pm.meta_key = %s
			AND pm.meta_value = %s
			AND p.post_type = %s
			AND p.post_status = 'publish'",
			self::$_key_lp_pmpro_levels,
			$level_id,
			LP_COURSE_CPT
		);

		$result = $wpdb->get_row( $sql );

		return $result;
	}

	/**
	 * Add courses to Lp order
	 *
	 * @param array $params | data example array('lp_order_id' => 1, 'level_id' => 2, 'total_page' => 2, 'p', 'user_id' => 2, 'limit' => 1)
	 * @param array $new_course_ids | data example array(1, 2, 3)
	 *
	 * @return array
	 * @deprecated 4.0.6
	 */
	/*public function addItemsToLpOrder( array $params = array(), array $new_course_ids = array() ) {
		if ( ! isset( $params['lp_order_id'] ) || ! isset( $params['user_id'] ) ) {

			debugLogLpPms( 'addItemsToLpOrder: Params is invalid!' );
			$params['code'] = 0;

			return $params;
		}

		$lp_order_id = $params['lp_order_id'];
		$lp_order    = learn_press_get_order( $lp_order_id );
		$user_id     = $params['user_id'];

		// set status user item
		if ( LP_Settings::is_auto_start_course() ) {
			$status     = LP_COURSE_ENROLLED;
			$graduation = LP_COURSE_GRADUATION_IN_PROGRESS;
		} else {
			$status     = LP_COURSE_PURCHASED;
			$graduation = null;
		}

		foreach ( $new_course_ids as $course_id ) {

			// Add item to order
			$result_add_course = $lp_order->add_item( $course_id );

			if ( ! $result_add_course ) {
				debugLogLpPms( 'Errors add course ' . $course_id . ' to order' );
				$params['code'] = 0;

				return $params;
			}

			// Update user item
			$result_update_user_item_field = learn_press_update_user_item_field(
				array(
					'user_id'    => $user_id,
					'item_id'    => $course_id,
					'start_time' => current_time( 'mysql', 1 ),
					'status'     => $status,
					'graduation' => $graduation,
					'end_time'   => '',
					'ref_id'     => $lp_order->get_id(),
					'item_type'  => LP_COURSE_CPT,
					'ref_type'   => LP_ORDER_CPT,
					'parent_id'  => 0,
				)
			);

			if ( ! $result_update_user_item_field ) {
				debugLogLpPms( 'Errors update user item field with course id: ' . $course_id );
				$params['code'] = 0;

				return $params;
			}

			$params['code'] = 1;
		}

		return $params;
	}*/

	/**
	 * Handle when membership expire
	 *
	 * @param int $user_id
	 * @param int $membership_id
	 *
	 * @since
	 * @author tungnx
	 */
	public function membership_expire( $user_id, $membership_id ) {
		// Get last order of user has level
		$result = LP_PMS_DB::getInstance()->getLastOrderMembershipOfUser( $user_id, $membership_id );

		if ( $result ) {
			$order_id = $result->post_id;
			$lp_order = learn_press_get_order( $order_id );
			if ( $lp_order ) {
				$lp_order->update_status( LP_ORDER_CANCELLED );
			}
		}
	}

	public function before_update_lp_orders_when_change_membership_level(
		$level_id = 0,
		$user_id = 0,
		$old_levels = array(),
		$cancel_level = 0
	) {
		if ( ! empty( $old_levels ) ) {
			$this->_MBS_USER_OLD_LEVELS = $old_levels;
		}
	}

	/**
	 * 1. Create LP order when subscription level membership (or via Woo) and delete old level if exits
	 * 2. Cancel level will delete LP order
	 *
	 * @param int $level_id
	 * @param int $user_id
	 * @param int $cancel_level
	 */
	public function update_lp_orders_when_change_membership_level( $level_id = 0, $user_id = 0, $cancel_level = 0 ) {
		if ( ! empty( $cancel_level ) ) { // Cancel level
			$this->LpOrderCancel( $user_id, $cancel_level );
		} elseif ( ! empty( $level_id ) && ! empty( $this->_MBS_USER_OLD_LEVELS ) && ! empty( $user_id ) ) {
			// Cancel orders old
			foreach ( $this->_MBS_USER_OLD_LEVELS as $membership_user_level ) {
				if ( isset( $membership_user_level->ID ) ) {
					$this->LpOrderCancel( $user_id, $membership_user_level->ID );
				}
			}

			// Create LP order and add items
			$order_id = $this->create_lp_order( $user_id, $level_id );
			if ( $order_id instanceof WP_Error ) {
				debugLogLpPms( $order_id->get_error_message() );
			}
		} elseif ( ! empty( $level_id ) && ! empty( $user_id ) ) { // Create LP order and add items
			$order_id = $this->create_lp_order( $user_id, $level_id );

			if ( $order_id instanceof WP_Error ) {
				debugLogLpPms( $order_id->get_error_message() );
			}
		} elseif ( ! empty( $user_id ) && ! empty( $this->_MBS_USER_OLD_LEVELS ) ) {
			// Admin cancel level of user
			if ( isset( $_REQUEST['membership_level'] )
				&& ( $_REQUEST['membership_level'] === '0' || $_REQUEST['membership_level'] == '' ) ) {
				foreach ( $this->_MBS_USER_OLD_LEVELS as $membership_user_level ) {
					if ( isset( $membership_user_level->ID ) ) {
						$this->LpOrderCancel( $user_id, $membership_user_level->ID );
					}
				}
			}
		}
	}

	public function LpOrderCancel( $user_id, $cancel_level_id ) {
		if ( empty( $user_id ) || empty( $cancel_level_id ) ) {
			return;
		}

		$lpOrderLast = LP_PMS_DB::getInstance()->getLastOrderMembershipOfUser( $user_id, $cancel_level_id );
		$lp_order    = learn_press_get_order( $lpOrderLast->post_id ?? 0 );
		if ( ! $lp_order ) {
			return;
		}

		$lp_order->update_status( LP_ORDER_CANCELLED );
	}

	/**
	 * @deprecated 4.0.6
	 */
	/*public function LpOrderDelete( $user_id = 0, $cancel_level_id = 0 ) {
		global $action;
		$result = false;

		$action = 'no_editpost'; // learnpress\inc\custom-post-types\order.php search editpost

		if ( empty( $user_id ) || empty( $cancel_level_id ) ) {
			return false;
		}

		$lpOrder = LP_PMS_DB::getInstance()->getLastOrderMembershipOfUser( $user_id, $cancel_level_id );

		if ( $lpOrder && isset( $lpOrder->post_id ) ) {
			$result = wp_delete_post( $lpOrder->post_id, true );

			if ( $result instanceof WP_Post ) {
				$result = true;
			}

			// Delete in lp_order_items
			// Delete in lp_order_itemmeta
			// Delete in lp_user_items
			// Delete in lp_user_itemmeta
		}

		return $result;
	}*/

	/**
	 * Delete courses in lp orders
	 *
	 * @param array $order_ids | data example array(1 ,2, 3)
	 * @param array $del_course_ids
	 * @deprecated 4.0.6
	 */
	/*public function deleteCoursesOnOrders( $order_ids = array(), $del_course_ids = array() ) {
		// get user_item_ids
		$user_item_ids = LP_PMS_DB::getInstance()->getUserItemIds( $order_ids, $del_course_ids );

		// get user item ids
		if ( is_array( $user_item_ids ) && count( $user_item_ids ) > 0 ) {
			$user_item_ids = array_keys( $user_item_ids );

			$rsDeleteUserItemmeta = LP_PMS_DB::getInstance()->deleteUserItemmeta( $user_item_ids );
			$rsDeleteUserItems    = LP_PMS_DB::getInstance()->deleteUserItems( $user_item_ids );
		}

		// get order item ids
		$order_item_ids = LP_PMS_DB::getInstance()->getOrderItemIds( $order_ids, $del_course_ids );

		if ( is_array( $order_item_ids ) && count( $order_item_ids ) > 0 ) {
			$order_item_ids = array_keys( $order_item_ids );

			$rsDeleteOrderItemmeta = LP_PMS_DB::getInstance()->deleteOrderItemmeta( $order_item_ids );
			$rsDeleteOrderItems    = LP_PMS_DB::getInstance()->deleteOrderItems( $order_item_ids );
		}
	}*/

	/**
	 * Delete courses in lp order
	 *
	 * @param int $order_id
	 * @param array $del_course_ids
	 *
	 * @throws Exception
	 * @deprecated 4.0.6
	 */
	/*public function deleteCoursesOnOrder( $order_id = 0, $del_course_ids = array() ) {
		// get user item ids
		$user_item_ids = LP_PMS_DB::getInstance()->getUserItemIds( array( $order_id ), $del_course_ids );

		$lp_user_items_db     = LP_User_Items_DB::getInstance();
		$lp_user_item_results = LP_User_Items_Result_DB::instance();

		// get user item ids
		if ( is_array( $user_item_ids ) && count( $user_item_ids ) > 0 ) {
			$user_item_ids = array_keys( $user_item_ids );

			$course_tmp = new LP_Course( array() );
			// Delete on tb lp_user_items, lp_user_itemmeta, lp_user_item_results
			$course_tmp->delete_user_item_and_result( $user_item_ids );

		}

		// get order item ids
		$order_item_ids = LP_PMS_DB::getInstance()->getOrderItemIds( array( $order_id ), $del_course_ids );

		if ( is_array( $order_item_ids ) && count( $order_item_ids ) > 0 ) {
			$order_item_ids = array_keys( $order_item_ids );
			$lp_order_db    = LP_Order_DB::getInstance();

			// Delete lp_order_item, lp_order_itemmeta
			$filter_delete                 = new LP_Order_Filter();
			$filter_delete->order_item_ids = $order_item_ids;
			$lp_order_db->delete_order_item( $filter_delete );
			$lp_order_db->delete_order_itemmeta( $filter_delete );
			// End
		}
	}*/

	// Find orders of users has level and add courses

	/**
	 * Admin update status PMS
	 *
	 * @param MemberOrder $pms_order
	 */
	public function pms_updated_order_will_add_lp_order( MemberOrder $pms_order ) {
		if ( ! isset( $pms_order->id ) || empty( $_REQUEST['save'] ) || ! current_user_can( 'pmpro_orders' ) ) {
			return;
		}

		$pmsOrder = LP_PMS_DB::getInstance()->getPmsOrderById( $pms_order->id );

		if ( is_object( $pmsOrder ) ) {
			if ( isset( $pms_order->user_id ) && isset( $pms_order->membership_id ) && isset( $pms_order->status ) ) {

				if ( $pmsOrder->status == $pms_order->status ) {
					return;
				}

				if ( $pms_order->status == 'success' || $pms_order->status == 'completed' ) {
					// Create LP order and add items
					$order_id = $this->create_lp_order( $pms_order->user_id, $pms_order->membership_id );
				} else {
					$this->update_lp_orders_when_change_membership_level(
						$pms_order->membership_id,
						$pms_order->user_id,
						$pms_order->membership_id
					);
				}
			}
		}
	}

	/**
	 * Admin create new order PMS
	 *
	 * @param MemberOrder $pms_order
	 */
	public function pms_added_order_will_add_lp_order( $pms_order ) {
		if ( empty( $_REQUEST['save'] ) || ! current_user_can( 'pmpro_orders' ) ) {
			return;
		}

		if ( $pms_order->status == 'success' || $pms_order->status == 'completed' ) {
			// Create LP order and add items
			$order_id = $this->create_lp_order( $pms_order->user_id, $pms_order->membership_id );
		}
	}

	/**
	 * Update list courses on Orders has level when save level
	 *
	 * @param array $lp_orders
	 * @param array $level_course_ids | get value from $_POST['_lp_pmpro_courses']
	 * @param int $level_id
	 */
	public function handleLevelChangeCourses(  $lp_orders = [], $level_course_ids = [], $level_id = 0 ) {
		$params = array(
			'level_id'         => $level_id,
			'level_course_ids' => $level_course_ids,
			'lp_orders'        => array_values( $lp_orders ),
		);

		$this->handleAddItemsToLpOrderRemotePost( $params );
	}

	public static function getInstance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}
}

LP_PMS_Order::getInstance();
