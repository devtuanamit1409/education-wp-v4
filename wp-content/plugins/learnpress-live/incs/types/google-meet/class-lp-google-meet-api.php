<?php
defined( 'ABSPATH' ) || exit();


class LearnPress_Google_Setting_Api {

	private static $instance;
	/**
	 * @var string
	 */
	public $namespace = 'lp/google/v1';

	/**
	 * @var string
	 */
	public $rest_base = '';

	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function register_routes() {

		register_rest_route(
			$this->namespace,
			'save-config-connect',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'save_config_connect' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);
		register_rest_route(
			$this->namespace,
			'get-config-connect',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_config_connect' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);

		register_rest_route(
			$this->namespace,
			'authenticate',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'authenticate' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);

		// customize meetings
		register_rest_route(
			$this->namespace,
			'get-all-events',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_all_events' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);
		register_rest_route(
			$this->namespace,
			'meetings/get',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_meeting' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);
		register_rest_route(
			$this->namespace,
			'meetings/create-or-update',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'create_or_update_meeting' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);
		register_rest_route(
			$this->namespace,
			'meetings/delete/(?P<meeting_id>\S+)',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'delete_meeting' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);

		//end custom meetings
	}

	public function save_config_connect( WP_REST_Request $request ) {
		$params           = $request->get_params();
		$response         = new stdClass();
		$response->status = 'success';

		$client_id     = $params['client_id'] ?? '';
		$client_secret = $params['client_secret'] ?? '';

		try {

			if ( empty( $client_id ) || empty( $client_secret ) ) {
				throw new Exception( 'Client ID or Client Secret is empty.' );
			}

			$user      = learn_press_get_current_user();
			$user_meta = array(
				'client_id'     => $client_id,
				'client_secret' => $client_secret,
			);

			update_user_meta( $user->get_id(), '_lp_google_connect', $user_meta );

		} catch ( Exception $e ) {
			$response->status  = 'error';
			$response->message = $e->getMessage();
		}

		return rest_ensure_response( $response );
	}

	public function authenticate( WP_REST_Request $request ) {
		$response = new stdClass();
		$params   = $request->get_params();

		$client_id     = $params['client_id'] ?? '';
		$client_secret = $params['client_secret'] ?? '';
		$client_code   = $params['client_code'] ?? '';

		try {

			$user          = learn_press_get_current_user();
			$user_settings = get_user_meta( $user->get_id(), '_lp_google_connect', true );
			//use in re-authenticate
			if ( ! empty( $user_settings ) ) {
				if ( empty( $client_id ) ) {
					$client_id = $user_settings['client_id'];
				}
				if ( empty( $client_secret ) ) {
					$client_secret = $user_settings['client_secret'];
				}
			}

			if ( empty( $client_code ) || empty( $client_id ) || empty( $client_secret ) ) {
				throw new Exception( __( 'Please do not leave any fields blank.', 'learnpress-live' ) );
			}

			$token = LP_Google_Auth::instance()->generateAccessToken( $client_id, $client_secret, $client_code );

			if ( is_wp_error( $token ) ) {
				$response->status  = 'error';
				$response->message = $token->get_error_message();
			} else {
				//update option
				$user_meta = array(
					'client_id'     => $client_id,
					'client_secret' => $client_secret,
					'client_code'   => $client_code,
				);

				update_user_meta( $user->get_id(), '_lp_google_connect', $user_meta );
				update_user_meta( $user->get_id(), '_lp_google_token', $token );
				update_user_meta( $user->get_id(), '_lp_refresh_token', $token->refresh_token );//using for re-authenticate

				$response->status  = 'success';
				$response->message = 'Save config connect success.';
			}
		} catch ( Exception $e ) {
			$response->status  = 'error';
			$response->message = $e->getMessage();
		}
		return rest_ensure_response( $response );
	}

	public function get_config_connect( WP_REST_Request $request ) {
		$response = new stdClass();

		$user      = learn_press_get_current_user();
		$meta_data = get_user_meta( $user->get_id(), '_lp_google_connect', true );
		$token     = get_user_meta( $user->get_id(), '_lp_google_token', true );

		if ( ! empty( $meta_data ) ) {
			$response->data = $meta_data;
			if ( ! empty( $token->access_token ) ) {
				$response->authenticated = true;
			} else {
				$response->authenticated = false;
			}
		} else {
			$response->data = array();
		}

		return rest_ensure_response( $response );
	}

	public function get_all_events( WP_REST_Request $request ) {
		$response         = new stdClass();
		$response->status = 'success';

		$user       = learn_press_get_current_user();
		$data_token = get_user_meta( $user->get_id(), '_lp_google_token', true );

		try {
			if ( empty( $data_token ) ) {
				throw new Exception( __( 'Please connect to Google Meet.', 'learnpress-live' ) );
			}

			$body        = array();
			$request_url = 'https://www.googleapis.com/calendar/v3/calendars/primary/events';
			$auth        = 'Bearer ' . $data_token->access_token;
			$results     = LP_Google_Auth::instance()->learnpress_google_requests( $body, 'GET', $auth, $request_url );

			if ( isset( $results['code'] ) && $results['code'] == 200 ) {
				$response->data = $results['body'];
				if ( empty( $results['body']->items ) ) {
					$response->message = __( 'No meeting found!', 'learnpress-live' );
					$response->status  = 'error';
				}
			} else {
				$response->message = $results['error'] ?? __( 'Error meetings!', 'learnpress-live' );
				$response->status  = 'error';
			}
		} catch ( Exception $e ) {
			$response->status  = 'error';
			$response->message = $e->getMessage();
		}

		return rest_ensure_response( $response );
	}

	public function get_meeting( WP_REST_Request $request ) {
		$response         = new stdClass();
		$response->status = 'success';

		$user       = learn_press_get_current_user();
		$data_token = get_user_meta( $user->get_id(), '_lp_google_token', true );

		try {
			if ( empty( $data_token ) ) {
				throw new Exception( __( 'Please connect to Google Meet.', 'learnpress-live' ) );
			}

			$params = $request->get_params();
			$id     = $params['ID'] ?: '';
			$body   = array();

			$request_url = 'https://www.googleapis.com/calendar/v3/calendars/primary/events/' . $id;
			$auth        = 'Bearer ' . $data_token->access_token;
			$results     = LP_Google_Auth::instance()->learnpress_google_requests( $body, 'GET', $auth, $request_url );

			if ( isset( $results['code'] ) && $results['code'] == 200 ) {
				$response->data = $results['body'];
			} else {
				$response->message = $results['error'] ?? __( 'Error meetings!', 'learnpress-live' );
				$response->status  = 'error';
			}
		} catch ( Exception $e ) {
			$response->status  = 'error';
			$response->message = $e->getMessage();
		}

		return rest_ensure_response( $response );
	}

	public function create_or_update_meeting( WP_REST_Request $request ) {
		$response         = new stdClass();
		$response->status = 'success';

		$params     = $request->get_params();
		$user       = learn_press_get_current_user();
		$data_token = get_user_meta( $user->get_id(), '_lp_google_token', true );

		try {

			$id               = $params['ID'] ?: '';
			$summary          = $params['summary'] ?: '';
			$description      = $params['description'] ?: '';
			$duration         = $params['duration'] ?: 0;
			$guest_can_modify = $params['guestsCanModify'] ?: false;
			$transparency     = $params['transparency'] ?: false;

			if ( empty( $summary ) ) {
				throw new Exception( __( 'Please enter meeting name.', 'learnpress-live' ) );
			}
			if ( empty( $duration ) ) {
				throw new Exception( __( 'Please enter meeting duration.', 'learnpress-live' ) );
			}

			if ( $duration < 0 ) {
				throw new Exception( __( 'Please enter meeting duration greater than 0.', 'learnpress-live' ) );
			}

			if ( $transparency ) {
				$duration = 6000;
			}

			$time_zone  = wp_timezone_string();
			$now_tz     = new DateTime( 'now', new DateTimeZone( $time_zone ) );
			$start_date = $now_tz->format( 'Y-m-d\\TH:i:s' );
			//end_date
			$time_stamp_end = $now_tz->setTimestamp( $now_tz->getTimestamp() + $duration * 60 );
			$end_date       = $time_stamp_end->format( 'Y-m-d\\TH:i:s' );
			$body           = array(
				'start'           => array(
					'dateTime' => $start_date,
					'timeZone' => $time_zone,
				),
				'end'             => array(
					'dateTime' => $end_date,
					'timeZone' => $time_zone,
				),
				'kind'            => 'calendar#event',
				'summary'         => $summary,
				'description'     => $description,
				'conferenceData'  => array(
					'createRequest' => array(
						'requestId'             => uniqid(),
						'conferenceSolutionKey' => array(
							'type' => 'hangoutsMeet',
						),
						'status'                => array(
							'statusCode' => 'success',
						),
					),
				),
				'guestsCanModify' => $guest_can_modify,
				'transparency'    => $transparency ? 'transparent' : 'opaque',
			);

			$method      = 'POST';
			$request_url = 'https://www.googleapis.com/calendar/v3/calendars/primary/events?conferenceDataVersion=1';
			if ( $id ) {
				$method      = 'PUT';
				$request_url = 'https://www.googleapis.com/calendar/v3/calendars/primary/events/' . $id;
				unset( $body['conferenceData'] );
			}
			$auth    = 'Bearer ' . $data_token->access_token;
			$results = LP_Google_Auth::instance()->learnpress_google_requests( json_encode( $body ), $method, $auth, $request_url );
			if ( isset( $results['code'] ) && $results['code'] == 200 ) {
				$response->message = __( 'Successfully!', 'learnpress-live' );
				if ( $id ) {
					//save to db after update;
					$rq = new WP_REST_Request();
					$rq->set_param( 'ID', $id );
					$res         = $this->get_meeting( $rq );
					$object_data = $res->get_data();

					if ( ! empty( $object_data->data ) ) {
						$db_live = LP_Live_Database::instance();
						$data    = array(
							'live_type'  => 'google_meet',
							'live_value' => json_encode( $object_data->data ),
						);
						$db_live->update( $id, $data );
					}
				}
			} else {
				$response->message = $results['message'];
				$response->status  = 'error';
			}
		} catch ( Exception $e ) {
			$response->status  = 'error';
			$response->message = $e->getMessage();
		}

		return rest_ensure_response( $response );
	}

	public function delete_meeting( WP_REST_Request $request ) {
		$response         = new stdClass();
		$response->status = 'success';

		$id         = $request['meeting_id'] ?: '';
		$user       = learn_press_get_current_user();
		$data_token = get_user_meta( $user->get_id(), '_lp_google_token', true );

		try {

			if ( empty( $id ) ) {
				throw new Exception( __( 'Google Meet ID Invalid.', 'learnpress-live' ) );
			}

			$body = array();

			$method      = 'DELETE';
			$request_url = 'https://www.googleapis.com/calendar/v3/calendars/primary/events/' . $id;

			$auth    = 'Bearer ' . $data_token->access_token;
			$results = LP_Google_Auth::instance()->learnpress_google_requests( $body, $method, $auth, $request_url );

			if ( isset( $results['code'] ) && $results['code'] == 204 ) {
				$response->message = __( 'Remove Successfully!', 'learnpress-live' );
			} else {
				$response->message = $results['message'];
				$response->status  = 'error';
			}
		} catch ( Exception $e ) {
			$response->status  = 'error';
			$response->message = $e->getMessage();
		}

		return rest_ensure_response( $response );
	}

	public function is_instructor() {
		$user_id = get_current_user_id();

		if ( ! $user_id ) {
			return false;
		}

		$user = learn_press_get_user( $user_id );

		if ( ! $user ) {
			return false;
		}

		if ( $user->is_instructor() || $user->is_admin() ) {
			return true;
		}

		return false;
	}

}
LearnPress_Google_Setting_Api::instance();
