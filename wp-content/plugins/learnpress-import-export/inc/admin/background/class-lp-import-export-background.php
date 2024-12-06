<?php
/**
 * Class LP_Background_Single_Import_Export
 *
 * Single to run not schedule, run one time and done when be call
 *
 * @since 4.1.1
 * @author tungnx
 * @version 1.0.1
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LP_Background_Single_Import_Export' ) ) {
	class LP_Background_Single_Import_Export extends LP_Async_Request {
		protected $action = 'background_single_import_export';

		protected $offset = 0;
		protected $limit  = 0;
		protected $total_page  = 0;
		protected $data_import_order = [];

		protected static $instance;

		
		protected function handle() {
			try {
				$handle_name             = LP_Helper::sanitize_params_submitted( $_POST['handle_name'] ?? '' );
				$this->data_import_order = LP_Helper::sanitize_params_submitted( $_POST['data_import_order'] ?? [] );
				$this->offset            = LP_Helper::sanitize_params_submitted( $_POST['offset'] ?? 0 );
				$this->limit	         = LP_Helper::sanitize_params_submitted( $_POST['limit'] ?? 0 );
				$this->total_page        = LP_Helper::sanitize_params_submitted( $_POST['total_page'] ?? 0 );

				if ( empty( $handle_name ) ) {
					return;
				}

				switch ( $handle_name ) {
					case 'create_order':
						$data_import_order = array_slice( $this->data_import_order, $this->offset, $this->limit, true);
						$this->create_order( $data_import_order );
						break;
					default:
						break;
				}
			} catch ( Throwable $e ) {
				error_log( $e->getMessage() );
			}
		}

		/**
		 * Create order for user
		 *
		 * @throws Exception
		 */
		protected function create_order( $data_import_order ) {
			$this->offset++;
			try{
				$order_total    = 0;
				$order_subtotal = 0;

				foreach( $data_import_order as $user_id => $courses_id ) {
					if ( ! empty( $courses_id ) ) {
						$order_data = array(
							'post_author' => $user_id,
							'post_parent' => '0',
							'post_type'   => LP_ORDER_CPT,
							'post_status' => 'publish',
							'ping_status' => 'closed',
							'post_title'  => __( 'Order on', 'learnpress-import-export' ) . ' ' . current_time( 'l jS F Y h:i:s A' ),
							'meta_input'  => array(
								'_user_ip_address'      => learn_press_get_ip(),
								'_user_agent'           => $_SERVER['HTTP_USER_AGENT'] ?? '',
								'_user_id'              => $user_id,
								'_order_key'            => apply_filters( 'learn_press_generate_order_key', uniqid( 'order' ) ),
								'_created_via'          => 'manual',
								'user_note'             => '',
							),
						);
		
						$lp_order_id = wp_insert_post( $order_data );
		
						if ( is_wp_error( $lp_order_id ) ) {
							throw new Exception( __( 'Can not create order', 'learnpress-import-export' ) );
						}
		
						$lp_order = learn_press_get_order( $lp_order_id );
		
						foreach ( $courses_id as $id ) {
							$course = learn_press_get_course( $id );
							if ( ! $course ) {
								continue;
							}
							//using set to order
							$order_total    += floatval( $course->get_price() );
							$order_subtotal += floatval( $course->get_price() );
		
							$item_total    = floatval( $course->get_price() );
							$item_subtotal = floatval( $course->get_price() );
		
							$item = array(
								'item_id'         => $id,
								'order_item_name' => get_the_title( $id ),
								'subtotal'        => $item_total,
								'total'           => $item_subtotal,
							);
							$lp_order->add_item( $item );
						}

						//set meta key order
						$lp_order->set_total( ! empty( $order_total ) ? $order_total : $order_subtotal);
						$lp_order->set_subtotal( $order_subtotal );
						$lp_order->set_status( 'lp-completed' );
						$lp_order->save();
					}
					
				}

				if( $this->offset < $this->total_page ) {
					$this->data( array(
						'handle_name'       => 'create_order',
						'data_import_order' => $this->data_import_order,
						'offset'            => $this->offset,
						'limit'             => $this->limit,
						'total_page'        => $this->total_page,
					) )->dispatch();
				}

			} catch (Throwable $e) {
				error_log($e->getMessage());
			}
			
		}
	
		/**
		 * @return LP_Background_Single_Import_Export
		 */
		public static function instance(): self {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}
	}

	// Must run instance to register ajax.
	LP_Background_Single_Import_Export::instance();
}
