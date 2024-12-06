<?php
/**
 * Class LP_WC_Subscription
 *
 * Handle for WooCommerce Subscription plugin
 * https://woo.com/products/woocommerce-subscriptions/
 *
 * Require: enable option "Buy courses via Product"
 *
 * @since 4.1.4
 * @version 1.0.0
 */

use LearnPress\Helpers\Singleton;
use LearnPress\Models\UserItemMeta\UserItemMetaModel;
use LearnPress\Models\UserItems\UserCourseModel;

defined( 'ABSPATH' ) || exit();

class LP_WC_Subscription {
	use Singleton;

	public function init() {
		$this->hooks();
	}

	protected function hooks() {
		// Update LP Order when woocommerce subscription's status is changed
		add_action(
			'woocommerce_subscription_status_updated',
			[
				$this,
				'update_lp_order_status',
			],
			10,
			3
		);
	}

	/**
	 * Check status of WooCommerce Subscription plugin impact to LP Order
	 *
	 * @param WC_Subscription $subscription Subscriptions object
	 * @param string $new_status new subscription's status
	 * @param string $old_status old subscription's status
	 *
	 * @since 4.1.4
	 * @version 1.0.0
	 */
	public function update_lp_order_status( $subscription, $new_status, $old_status ) {
		try {
			$wc_order_parent = $subscription->get_parent();
			if ( ! $wc_order_parent ) {
				return;
			}

			$lp_order_id = $wc_order_parent->get_meta( '_learn_press_order_id' );
			if ( empty( $lp_order_id ) ) {
				return;
			}

			$lp_order = learn_press_get_order( $lp_order_id );
			if ( ! $lp_order ) {
				error_log( 'Invalid LP Order.', 'learnpress-woo-payment' );

				return;
			}

			$lp_user_id = $subscription->get_customer_id();
			if ( ! $lp_user_id ) {
				error_log( 'Invalid LP UserID.', 'learnpress-woo-payment' );

				return;
			}

			$filter = new LP_User_Items_Filter();
			$filter->user_id = $lp_user_id;
			$filter->ref_id = $lp_order_id;
			$filter->ref_type = LP_ORDER_CPT;
			$filter->item_type = LP_COURSE_CPT;
			$user_course = UserCourseModel::get_user_item_model_from_db( $filter );

			if ( ! empty( $user_course ) ) {
				$user_course_meta = $user_course->get_meta_model_from_key( '_lp_allow_repurchase_type' );
				if ( ! empty( $user_course_meta ) ) {
					$user_course_meta->meta_value = 'keep';
					$user_course_meta->save();
				} else {
					$new_user_course_meta = new UserItemMetaModel();
					$new_user_course_meta->learnpress_user_item_id = $user_course->get_user_item_id();
					$new_user_course_meta->meta_key = '_lp_allow_repurchase_type';
					$new_user_course_meta->meta_value = 'keep';
					$new_user_course_meta->save();
				}
			}

			$allow_statuses = array( 'active', 'completed' );
			if ( ! in_array( $new_status, $allow_statuses ) ) {
				$lp_order->update_status( LP_ORDER_CANCELLED );
			}
		} catch ( Throwable $e ) {

		}
	}
}
