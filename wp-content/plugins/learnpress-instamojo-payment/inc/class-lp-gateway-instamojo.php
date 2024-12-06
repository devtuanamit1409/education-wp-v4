<?php

require LP_ADDON_INSTAMOJO_PAYMENT_PATH . '/vendor/autoload.php';

/**
 * Instamojo payment gateway class.
 *
 * @author   ThimPress
 * @package  LearnPress/Instamojo/Classes
 * @version  4.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LP_Gateway_Instamojo' ) ) {
	/**
	 * Class LP_Gateway_Instamojo
	 */
	class LP_Gateway_Instamojo extends LP_Gateway_Abstract {

		/**
		 * @var string
		 */
		private $api_endpoint = '';

		/**
		 * @var string
		 */
		private $auth_endpoint = '';

		/**
		 * @var null
		 */
		protected $client_id = null;

		/**
		 * @var null
		 */
		protected $client_secret = null;

		/**
		 * @var bool
		 */
		protected $test_mode = false;

		/**
		 * @var bool
		 */
		protected $enable = true;

		/**
		 * @var bool
		 */
		protected $floating_checkout = true;

		public function __construct() {
			$this->id = 'instamojo';

			$this->method_title       = 'Instamojo';
			$this->method_description = esc_html__( 'Make a payment with Instamojo.', 'learnpress-instamojo-payment' );
			$this->icon               = 'https://im-testing.im-cdn.com/assets/images/favicon.6d3d153d920c.png';

			// Get settings.
			$this->title       = LearnPress::instance()->settings()->get( "{$this->id}.title", $this->method_title );
			$this->description = LearnPress::instance()->settings()->get( "{$this->id}.description", $this->method_description );

			$lp_settings = LearnPress::instance()->settings();

			//enable settings if supports Rupee currency.
			$currency = learn_press_get_currency();

			if ( $currency != 'INR' ) {
				$this->enable = false;
			}

			// Add default values for fresh installs.
			if ( $lp_settings->get( "{$this->id}.enable" ) == 'yes' ) {
				$this->test_mode         = $lp_settings->get( "{$this->id}.test_mode" ) == 'yes' ? true : false;
				$this->client_id         = $lp_settings->get( "{$this->id}.client_id" );
				$this->client_secret     = $lp_settings->get( "{$this->id}.client_secret" );
				$this->floating_checkout = $lp_settings->get( "{$this->id}.floating_checkout" ) == 'yes' ? true : false;

				// API Info.
				$this->api_endpoint  = $this->test_mode == 'yes' ? 'https://test.instamojo.com/v2/' : 'https://www.instamojo.com/v2/';
				$this->auth_endpoint = $this->test_mode == 'yes' ? 'https://test.instamojo.com/oauth2/token/' : 'https://www.instamojo.com/oauth2/token/';
			}

			// check payment gateway enable.
			add_filter( 'learn-press/payment-gateway/' . $this->id . '/available', array( $this, 'instamojo_available' ), 10, 2 );
			//webhook callback update status lp-order
			add_action( 'template_redirect', array( $this, 'update_order' ) );

			parent::__construct();

			if ( $this->floating_checkout ) {

				wp_enqueue_script( 'instamojo', 'https://js.instamojo.com/v1/checkout.js', '', '3.0', true );
				wp_enqueue_script( 'learn-press-instamojo', LP_ADDON_INSTAMOJO_PAYMENT_URL . 'assets/instamojo.js', array( 'instamojo', 'wp-api-fetch' ), LP_ADDON_INSTAMOJO_PAYMENT_VER, true );
				wp_localize_script(
					'learn-press-instamojo',
					'LearnPressInstamojo',
					array(
						'insta_client_id'     => $this->client_id,
						'insta_client_secret' => $this->client_secret,
						'insta_test_mode'     => $this->test_mode,
					)
				);
			}

		}

		/**
		 * Payment form.
		 */
		public function get_payment_form() {
			return LP()->settings->get( $this->id . '.description' );
		}

		/**
		 * Admin payment settings.
		 *
		 * @return array
		 */
		public function get_settings() {
			$datas = apply_filters(
				'learn-press/gateway-payment/instamojo/settings',
				array(
					array(
						'type' => 'title',
					),
					array(
						'title'   => esc_html__( 'Enable', 'learnpress-instamojo-payment' ),
						'id'      => '[enable]',
						'default' => 'no',
						'type'    => 'yes-no',
					),
					array(
						'type'       => 'text',
						'title'      => esc_html__( 'Title', 'learnpress-instamojo-payment' ),
						'default'    => esc_html__( 'Instamojo', 'learnpress-instamojo-payment' ),
						'id'         => '[title]',
						'visibility' => array(
							'state'       => 'show',
							'conditional' => array(
								array(
									'field'   => '[enable]',
									'compare' => '=',
									'value'   => 'yes',
								),
							),
						),
					),
					array(
						'type'       => 'textarea',
						'title'      => esc_html__( 'Description', 'learnpress-instamojo-payment' ),
						'default'    => esc_html__( 'Make a payment with Instamojo', 'learnpress-instamojo-payment' ),
						'id'         => '[description]',
						'editor'     => array(
							'textarea_rows' => 5,
						),
						'css'        => 'height: 100px;',
						'visibility' => array(
							'state'       => 'show',
							'conditional' => array(
								array(
									'field'   => '[enable]',
									'compare' => '=',
									'value'   => 'yes',
								),
							),
						),
					),
					array(
						'title'      => esc_html__( 'Client ID', 'learnpress-instamojo-payment' ),
						'id'         => '[client_id]',
						'type'       => 'text',
						'class'      => 'regular-text',
						'visibility' => array(
							'state'       => 'show',
							'conditional' => array(
								array(
									'field'   => '[enable]',
									'compare' => '=',
									'value'   => 'yes',
								),
							),
						),
					),
					array(
						'type'       => 'text',
						'title'      => esc_html__( 'Client Secret', 'learnpress-instamojo-payment' ),
						'default'    => '',
						'id'         => '[client_secret]',
						'class'      => 'regular-text',
						'visibility' => array(
							'state'       => 'show',
							'conditional' => array(
								array(
									'field'   => '[enable]',
									'compare' => '=',
									'value'   => 'yes',
								),
							),
						),
					),
					array(
						'title'      => esc_html__( 'Enable test mode', 'learnpress-instamojo-payment' ),
						'id'         => '[test_mode]',
						'default'    => 'no',
						'type'       => 'yes-no',
						'visibility' => array(
							'state'       => 'show',
							'conditional' => array(
								array(
									'field'   => '[enable]',
									'compare' => '=',
									'value'   => 'yes',
								),
							),
						),
					),
					array(
						'title'      => esc_html__( 'Enable Floating Checkout', 'learnpress-instamojo-payment' ),
						'id'         => '[floating_checkout]',
						'default'    => 'no',
						'type'       => 'yes-no',
						'desc'       => esc_html__( 'Enable payment on the current site, if disabled will redirect to the Instamojo website.', 'learnpress-instamojo-payment' ),
						'visibility' => array(
							'state'       => 'show',
							'conditional' => array(
								array(
									'field'   => '[enable]',
									'compare' => '=',
									'value'   => 'yes',
								),
							),
						),
					),
					array(
						'type' => 'sectionend',
					),
				)
			);

			if ( ! $this->enable ) {
				$url_settings_payment = admin_url( 'admin.php?page=learn-press-settings&tab=general' );
				$mess_error           = sprintf(
					'<p style="color:red;font-size: 14px;font-weight:600;">%s<span>%s<a href="%s">here</a></span></p>',
					__( 'The site\'s currency is not supported by Instamojo and the system only supports Rupee currency. ', 'learnpress-instamojo-payment' ),
					__( 'Please change the currency ', 'learnpress-instamojo-payment' ),
					$url_settings_payment
				);
				array_unshift(
					$datas,
					array(
						'type' => 'title',
						'desc' => $mess_error,
					)
				);

			}

			return $datas;
		}

		/**
		 * Check gateway available.
		 *
		 * @return bool
		 */
		public function instamojo_available() {
			if ( LearnPress::instance()->settings->get( "{$this->id}.enable" ) != 'yes' ) {
				return false;
			}

			if ( ! $this->enable ) {
				return false;
			}

			if ( LearnPress::instance()->settings->get( "{$this->id}.enable" ) == 'yes' ) {
				if ( ! LearnPress::instance()->settings->get( "{$this->id}.client_id" ) || ! LearnPress::instance()->settings->get( "{$this->id}.client_secret" ) ) {
					return false;
				}
			}

			return true;
		}

		/**
		 * Instamojo payment process.
		 *
		 * @param $order
		 *
		 * @return array
		 * @throws string
		 */
		public function process_payment( $order_id ) {
			$result = array(
				'result'   => 'error',
				'redirect' => '',
			);

			try {
				$order = learn_press_get_order( $order_id );

				if ( ! $order ) {
					throw new Exception( __( 'Invalid order', 'learnpress-instamojo-payment' ) );
				}

				$instaobj = Instamojo\Instamojo::init(
					'app',
					[
						'client_id'     => $this->client_id,
						'client_secret' => $this->client_secret,
					],
					$this->test_mode
				);

				if ( $instaobj ) {
					$order_items = $order->get_item_ids();
					if ( ! empty( $order_items ) ) {
						$course_id    = $order_items[0];
						$course       = learn_press_get_course( $course_id );
						$course_title = $course->get_title();

						$total   = $order->get_total();
						$user_id = $order->get_user_id();
						$user    = learn_press_get_user( $user_id );
						$email   = '';
						if ( $user instanceof LP_User ) {
							$email = $user->get_email();
						} else {
							$email = $order->get_email_checkout();
						}

						$response = $instaobj->createPaymentRequest(
							array(
								'purpose'      => $course_title,
								'amount'       => $total,
								'send_email'   => true,
								'email'        => $email,
								'redirect_url' => $this->get_return_url( $order ),
							)
						);

						if ( ! empty( $response ) && isset( $response['longurl'] ) ) {
							$result['result']   = 'success';
							$result['redirect'] = $response['longurl'];
						}
					}
				}
			} catch ( Throwable $e ) {
				$result['messages'] = $e->getMessage();
			}

			return $result;
		}

		public function update_order() {

			if ( ! isset( $_GET['payment_id'] ) ) {
				return;
			}

			if ( ! isset( $_GET['payment_request_id'] ) ) {
				return;
			}

			global $wp;

			try {
				$order_id = $wp->query_vars['lp-order-received'];

				if ( ! $order_id ) {
					throw new Exception( __( 'Error: OrderID Invalid.', 'learnpress-instamojo-payment' ) );
				}

				$lp_order = learn_press_get_order( $order_id );

				$instaobj = Instamojo\Instamojo::init(
					'app',
					[
						'client_id'     => $this->client_id,
						'client_secret' => $this->client_secret,
					],
					$this->test_mode
				);

				if ( $instaobj ) {
					$payment_request_id = $_GET['payment_request_id'];
					$payment_id         = $_GET['payment_id'];

					$payment_data = $instaobj->getPaymentRequestDetails( $payment_request_id );

					if ( ! empty( $payment_data ) && isset( $payment_data['status'] ) ) {
						$lp_order_status = '';
						$status          = $payment_data['status'];

						switch ( $status ) {
							case 'Completed':
								$lp_order_status = LP_ORDER_COMPLETED;
								break;
							default:
								$lp_order_status = LP_ORDER_PENDING;
						}

						$lp_order->update_status( $lp_order_status );
					}
				}
			} catch ( Throwable $e ) {
				error_log( $e->getMessage() );
			}
		}
	}
}
