<?php
/**
 * LearnPress Instamojo Payment Hooks
 *
 * @since 4.0.0
 * @version 1.0.0
 */
defined( 'ABSPATH' ) || exit();

class LP_Instamojo_Hooks {
	private static $instance;

	protected function __construct() {
		$this->hooks();
	}

	protected function hooks() {
		//show order id payment instamojo in page lp checkout
		add_action( 'learn-press/order/received/items-table', array( $this, 'show_order_id_instamojo_page_checkout' ), 10, 1 );
		//show order id payment instamojo in admin lp order
		add_action( 'lp/admin/order/detail/after-order-key', array( $this, 'show_order_id_instamojo_admin' ), 10, 1 );

	}

	/**
	 * Show order id instamojo on the page checkout
	 *
	 * @param $order_received
	 *
	 * @return void
	 */
	public function show_order_id_instamojo_page_checkout( $order_received ) {
		LP_Addon_Instamojo_Payment_Preload::$addon->get_template( 'orders/order-received.php', compact( 'order_received' ) );
	}

	/**
	 * Show order id instamojo in admin lp order
	 *
	 * @param LP_Order $order
	 *
	 * @return void
	 */
	public function show_order_id_instamojo_admin( $order ) {
		$order_id = $order->get_id();
		if ( $order_id ) {
			$instamojo_payment_id = get_post_meta( $order_id, '_lp_instamojo_payment_id', true );
			LP_Addon_Instamojo_Payment_Preload::$addon->get_template( 'orders/order-detail.php', compact( 'instamojo_payment_id' ) );
		}
	}

	/**
	 * Singleton.
	 *
	 * @return LP_Instamojo_Hooks
	 */
	public static function instance(): LP_Instamojo_Hooks {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
