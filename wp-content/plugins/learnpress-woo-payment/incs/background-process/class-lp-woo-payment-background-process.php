<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LP_Woo_Payment_Background_Process' ) ) {
	/**
	 * Class LP_Background_Single_Course
	 *
	 * Single to run not schedule, run one time and done when be call
	 *
	 * @since 4.1.1
	 * @author tungnx
	 */
	class LP_Woo_Payment_Background_Process extends LP_Async_Request {
		protected $prefix = 'lp_woo_payment';
		protected $action = 'create_lp_order_when_payment_woocommerce';
		protected static $instance;

		/**
		 * @throws Exception
		 */
		protected function handle() {
			$params = array(
				'lp_order_id'    => LP_Request::get_param( 'lp_order_id', 0, 'int' ),
				'lp_order_items' => LP_Request::get_param( 'lp_order_items', array(), 'int' ),
				'wc_order_id'    => LP_Request::get_param( 'wc_order_id', 0, 'int' ),
			);

			$this->handleAddItemsToLpOrderBackground( $params );
		}

		/**
		 * handle add course to lp_order
		 *
		 * @param array $params
		 *
		 * @throws Exception
		 */
		protected function handleAddItemsToLpOrderBackground( array $params ) {
			try {
				$order_id       = $params['lp_order_id'] ?? 0;
				$wc_order_id    = $params['wc_order_id'] ?? 0;
				$lp_order_items = (array) $params['lp_order_items'] ?? array();
				$lp_order       = learn_press_get_order( $order_id );
				if ( ! $lp_order ) {
					throw new Exception( __( 'LP order is invalid!', 'learnpress-woo-payment' ) );
				}

				$lp_woo_order = new LP_Woo_Order( $order_id, $wc_order_id );
				$lp_woo_order->add_item_to_order( $lp_order_items );
			} catch ( Throwable $e ) {
				error_log( __FUNCTION__ . ': ' . $e->getMessage() );
			}
		}

		/**
		 * @return LP_Woo_Payment_Background_Process
		 */
		public static function instance(): self {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}
	}

	// Must run instance to register ajax.
	LP_Woo_Payment_Background_Process::instance();
}
