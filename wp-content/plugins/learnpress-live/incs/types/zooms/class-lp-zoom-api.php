<?php
defined( 'ABSPATH' ) || exit();


class LearnPress_Zoom_Setting_Api {

	private static $instance;
	/**
	 * @var string
	 */
	public $namespace = 'lp/zoom/v1';

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

	public function register_routes() {
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
			'get-all-events',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_all_events' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);

		// customize meetings
		register_rest_route(
			$this->namespace,
			'meetings/create',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'create_meeting' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);

		register_rest_route(
			$this->namespace,
			'meetings/edit/(?P<meeting_id>\d+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_meeting_by_id' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);

		register_rest_route(
			$this->namespace,
			'meetings/update/(?P<meeting_id>\d+)',
			array(
				'methods'             => 'PATCH',
				'callback'            => array( $this, 'update_meeting' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);

		register_rest_route(
			$this->namespace,
			'meetings/delete/(?P<meeting_id>\d+)',
			array(
				'methods'             => 'DELETE',
				'callback'            => array( $this, 'delete_meeting' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);

		//end custom meetings
	}

	public function authenticate( WP_REST_Request $request ) {
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

			update_user_meta( $user->get_id(), '_lp_zoom_connect', $user_meta );

		} catch ( Exception $e ) {
			$response->status  = 'error';
			$response->message = $e->getMessage();
		}

		return rest_ensure_response( $response );
	}

	public function get_config_connect( WP_REST_Request $request ) {
		$response = new stdClass();

		$user      = learn_press_get_current_user();
		$meta_data = get_user_meta( $user->get_id(), '_lp_zoom_connect', true );
		$token     = get_user_meta( $user->get_id(), '_lp_zoom_token', true );

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

	public function save_config_connect( WP_REST_Request $request ) {
		$response = new stdClass();
		$params   = $request->get_params();

		$client_id     = $params['client_id'] ?? '';
		$client_secret = $params['client_secret'] ?? '';
		$sdk_key       = $params['sdk_key'] ?? '';
		$sdk_secret    = $params['sdk_secret'] ?? '';
		$client_code   = $params['client_code'] ?? '';

		try {

			$user          = learn_press_get_current_user();
			$user_settings = get_user_meta( $user->get_id(), '_lp_zoom_connect', true );
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

			$token = LP_Zoom_Auth::instance()->generateAccessToken( $client_id, $client_secret, $client_code );

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
				// save pmi by user
				$user_pmi = get_user_meta( $user->get_id(), '_lp_zoom_meeting_pmi', true );
				if ( empty( $user_pmi ) ) {
					$request_url = 'https://api.zoom.us/v2/users/me';
					$auth        = 'Bearer ' . $token->access_token;
					$data_user   = LP_Zoom_Auth::instance()->learnpress_zoom_requests( '', 'GET', $auth, $request_url );
					if ( ! empty( $data_user['body'] ) ) {
						$user_pmi = $data_user['body']->pmi;
						update_user_meta( $user->get_id(), '_lp_zoom_meeting_pmi', $user_pmi );
					}
				}
				update_user_meta( $user->get_id(), '_lp_zoom_connect', $user_meta );
				update_user_meta( $user->get_id(), '_lp_zoom_token', $token );

				$response->status  = 'success';
				$response->message = 'Save config connect success!';
			}
		} catch ( Exception $e ) {
			$response->status  = 'error';
			$response->message = $e->getMessage();
		}
		return rest_ensure_response( $response );
	}

	public function get_all_events( WP_REST_Request $request ) {
		$response         = new stdClass();
		$response->status = 'success';

		$user       = learn_press_get_current_user();
		$data_token = get_user_meta( $user->get_id(), '_lp_zoom_token', true );

		try {
			if ( empty( $data_token ) ) {
				throw new Exception( 'Please connect to zoom' );
			}

			$params = $request->get_params();
			// $page            = $params['page'];
			// $next_page_token = $params['nextPageToken'];
			$type = $params['type'];

			$body = array(
				// 'page_number'     => $page + 1,
				'page_size' => 300,
				// 'next_page_token' => $next_page_token,
				'type'      => $type,
			);

			$request_url = 'https://api.zoom.us/v2/users/me/meetings/';
			$auth        = 'Bearer ' . $data_token->access_token;
			$results     = LP_Zoom_Auth::instance()->learnpress_zoom_requests( $body, 'GET', $auth, $request_url );

			if ( isset( $results['code'] ) && $results['code'] == 200 ) {
				$response->data = $results['body'];
				if ( empty( $results['body']->meetings ) ) {
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

	public function get_meeting_by_id( WP_REST_Request $request ) {
		$response   = new stdClass();
		$meeting_id = $request['meeting_id'];

		try {
			if ( ! $meeting_id ) {
				throw new Exception( __( 'Meeting ID not found.', 'learnpress-live' ) );
			}

			$user       = learn_press_get_current_user();
			$data_token = get_user_meta( $user->get_id(), '_lp_zoom_token', true );

			$body        = array();
			$request_url = 'https://api.zoom.us/v2/meetings/' . $meeting_id;
			$auth        = 'Bearer ' . $data_token->access_token;
			$results     = LP_Zoom_Auth::instance()->learnpress_zoom_requests( $body, 'GET', $auth, $request_url );

			if ( $results['code'] == 200 ) {
				$response->data = $results['body'];
			} else {
				$response->message = $results['error'];
			}
		} catch ( Exception $e ) {
			$response->status  = 'error';
			$response->message = $e->getMessage();
		}

		return rest_ensure_response( $response );
	}

	public function create_meeting( WP_REST_Request $request ) {
		$response         = new stdClass();
		$response->status = 'success';
		$params           = $request->get_params();
		//params
		$type_meetings = $params['typeMeeting'];
		$use_pmi       = $params['usePmi'] ?? '';
		$topic         = $params['topic'];
		$start_time    = $params['start_time'];
		$duration      = $params['duration'];
		$password      = $params['password'];
		$agenda        = $params['agenda'];
		$timezone      = $params['timezone'];
		//settings
		$setting_wating_room                     = $params['settings']['waiting_room'];
		$setting_participant_video               = $params['settings']['participant_video'];
		$setting_host_video                      = $params['settings']['host_video'];
		$setting_auto_recording                  = $params['settings']['auto_recording'];
		$setting_mute_participant                = $params['settings']['mute_upon_entry'];
		$setting_join_before_host                = $params['settings']['join_before_host'];
		$approved_or_denied_countries_or_regions = $params['settings']['approved_or_denied_countries_or_regions'];

		//recurrence
		$recurrence_enable          = $params['recurrence']['scheduleMeeting'];
		$recurrence_type_end        = $params['recurrence']['typeEndSchedule'];
		$recurrence_type            = $params['recurrence']['type'];
		$recurrence_repeat_interval = $params['recurrence']['repeat_interval'];
		$recurrence_weekly_days     = $params['recurrence']['weekly_days'];
		$recurrence_monthly_day     = $params['recurrence']['monthly_day'];
		$recurrence_end_times       = $params['recurrence']['end_times'];
		$recurrence_end_date_time   = $params['recurrence']['end_date_time'];

		try {

			if ( $duration <= 10 ) {
				throw new Exception( __( 'Duration must be greater than 10 minutes.', 'learnpress-live' ) );
			}

			if ( empty( $password ) ) {
				throw new Exception( __( 'Password is required.', 'learnpress-live' ) );
			}
			$user       = learn_press_get_current_user();
			$data_token = get_user_meta( $user->get_id(), '_lp_zoom_token', true );

			//settings
			$settings = array(
				'use_pmi'                                 => $use_pmi,
				'waiting_room'                            => $setting_wating_room,
				'participant_video'                       => $setting_participant_video,
				'host_video'                              => $setting_host_video,
				'auto_recording'                          => ! empty( $setting_auto_recording ) ? 'local' : 'none',
				'mute_upon_entry'                         => $setting_mute_participant,
				'join_before_host'                        => $setting_join_before_host,
				'approved_or_denied_countries_or_regions' => array(
					'enable'        => $approved_or_denied_countries_or_regions['enable'],
					'method'        => $approved_or_denied_countries_or_regions['method'] ?? '',
					'approved_list' => $approved_or_denied_countries_or_regions['approved_list'],
					'denied_list'   => $approved_or_denied_countries_or_regions['denied_list'],
				),
			);

			$body = array(
				'topic'      => $topic,
				'start_time' => $start_time,
				'duration'   => $duration,
				'password'   => $password,
				'agenda'     => $agenda,
				'timezone'   => $timezone,
				'settings'   => $settings,
			);

			//recurrence
			if ( $recurrence_enable ) {
				$recurrence = array(
					'type'            => $recurrence_type,
					'repeat_interval' => $recurrence_repeat_interval,
				);

				if ( $recurrence_type == 2 ) {
					if ( is_array( $recurrence_weekly_days ) ) {
						if ( count( $recurrence_weekly_days ) == 1 ) {
							$recurrence['weekly_days'] = $recurrence_weekly_days[0];
						} else {
							$recurrence['weekly_days'] = implode( ',', $recurrence_weekly_days );
						}
					}
				}

				if ( $recurrence_type == 3 ) {
					$recurrence['monthly_day'] = $recurrence_monthly_day;
				}

				$recurrence['end_times']     = $recurrence_end_times;
				$dt                          = new DateTime( $recurrence_end_date_time, new DateTimeZone( $timezone ) );
				$recurrence['end_date_time'] = $dt->format( 'Y-m-d\TH:i:s' );

				$body['recurrence'] = $recurrence;
			}

			if ( $type_meetings == 8 ) {
				$body['type'] = 8;
			}

			$request_url = 'https://api.zoom.us/v2/users/me/meetings';
			$auth        = 'Bearer ' . $data_token->access_token;
			$results     = LP_Zoom_Auth::instance()->learnpress_zoom_requests( json_encode( $body ), 'POST', $auth, $request_url );

			if ( $results['code'] == 201 ) {
				$response->message = __( 'Create meeting successfully!', 'learnpress-live' );
			} else {
				$response->message = $results['error'];
				$response->status  = 'error';
			}
		} catch ( Exception $e ) {
			$response->status  = 'error';
			$response->message = $e->getMessage();
		}

		return rest_ensure_response( $response );
	}

	public function update_meeting( WP_REST_Request $request ) {
		$response         = new stdClass();
		$response->status = 'success';
		$params           = $request->get_params();
		//params
		$type_meetings = $params['typeMeeting'];
		$meeting_id    = $params['meeting_id'];
		$topic         = $params['topic'];
		$start_time    = $params['start_time'];
		$duration      = $params['duration'];
		$password      = $params['password'];
		$agenda        = $params['agenda'];
		$timezone      = $params['timezone'];

		//settings
		$setting_wating_room                     = $params['settings']['waiting_room'];
		$setting_participant_video               = $params['settings']['participant_video'];
		$setting_host_video                      = $params['settings']['host_video'];
		$setting_auto_recording                  = $params['settings']['auto_recording'];
		$setting_mute_participant                = $params['settings']['mute_upon_entry'];
		$setting_join_before_host                = $params['settings']['join_before_host'];
		$approved_or_denied_countries_or_regions = $params['settings']['approved_or_denied_countries_or_regions'];

		//recurrence
		$recurrence_enable          = $params['recurrence']['scheduleMeeting'];
		$recurrence_type_end        = $params['recurrence']['typeEndSchedule'];
		$recurrence_type            = $params['recurrence']['type'];
		$recurrence_repeat_interval = $params['recurrence']['repeat_interval'];
		$recurrence_weekly_days     = $params['recurrence']['weekly_days'];
		$recurrence_monthly_day     = $params['recurrence']['monthly_day'];
		$recurrence_end_times       = $params['recurrence']['end_times'];
		$recurrence_end_date_time   = $params['recurrence']['end_date_time'];

		try {

			if ( $duration <= 10 ) {
				throw new Exception( __( 'Duration must be greater than 10 minutes.', 'learnpress-live' ) );
			}
			if ( empty( $password ) ) {
				throw new Exception( __( 'Password is required.', 'learnpress-live' ) );
			}

			if ( ! $meeting_id ) {
				throw new Exception( __( 'Meeting ID not found.', 'learnpress-live' ) );
			}

			$user       = learn_press_get_current_user();
			$data_token = get_user_meta( $user->get_id(), '_lp_zoom_token', true );

			$settings = array(
				'waiting_room'                            => $setting_wating_room,
				'participant_video'                       => $setting_participant_video,
				'host_video'                              => $setting_host_video,
				'auto_recording'                          => ! empty( $setting_auto_recording ) ? 'local' : 'none',
				'mute_upon_entry'                         => $setting_mute_participant,
				'join_before_host'                        => $setting_join_before_host,
				'approved_or_denied_countries_or_regions' => array(
					'enable'        => $approved_or_denied_countries_or_regions['enable'],
					'method'        => $approved_or_denied_countries_or_regions['method'] ?? '',
					'approved_list' => $approved_or_denied_countries_or_regions['approved_list'],
					'denied_list'   => $approved_or_denied_countries_or_regions['denied_list'],
				),
			);

			$body = array(
				'topic'      => $topic,
				'start_time' => $start_time,
				'duration'   => $duration,
				'password'   => $password,
				'agenda'     => $agenda,
				'timezone'   => $timezone,
				'settings'   => $settings,
			);

			//recurrence
			if ( $recurrence_enable ) {
				$recurrence = array(
					'type'            => $recurrence_type,
					'repeat_interval' => $recurrence_repeat_interval,
				);

				if ( $recurrence_type == 2 ) {
					if ( is_array( $recurrence_weekly_days ) ) {
						if ( count( $recurrence_weekly_days ) == 1 ) {
							$recurrence['weekly_days'] = $recurrence_weekly_days[0];
						} else {
							$recurrence['weekly_days'] = implode( ',', $recurrence_weekly_days );
						}
					}
				}

				if ( $recurrence_type == 3 ) {
					$recurrence['monthly_day'] = $recurrence_monthly_day;
				}

				$recurrence['end_times']     = $recurrence_end_times;
				$dt                          = new DateTime( $recurrence_end_date_time, new DateTimeZone( $timezone ) );
				$recurrence['end_date_time'] = $dt->format( 'Y-m-d\TH:i:s' );

				$body['recurrence'] = $recurrence;
			}

			if ( $type_meetings == 8 ) {
				$body['type'] = 8;
			}

			$request_url = 'https://api.zoom.us/v2/meetings/' . $meeting_id;
			$auth        = 'Bearer ' . $data_token->access_token;
			$results     = LP_Zoom_Auth::instance()->learnpress_zoom_requests( json_encode( $body ), 'PATCH', $auth, $request_url );

			if ( $results['code'] == 204 ) {
				$rq = new WP_REST_Request();
				$rq->set_param( 'meeting_id', $meeting_id );
				$res         = $this->get_meeting_by_id( $rq );
				$object_data = $res->get_data();

				if ( ! empty( $object_data->data ) ) {
					$db_live = LP_Live_Database::instance();
					$data    = array(
						'live_type'  => 'zoom_meet',
						'live_value' => json_encode( $object_data->data ),
					);
					$db_live->update( $meeting_id, $data );
				}

				$response->message = __( 'Update meeting successfully!', 'learnpress-live' );

			} else {
				$response->message = $results['error'];
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
		$params           = $request->get_params();

		//params
		$meeting_id = $params['meeting_id'];

		try {
			if ( ! $meeting_id ) {
				throw new Exception( __( 'Meeting ID not found.', 'learnpress-live' ) );
			}

			$user       = learn_press_get_current_user();
			$data_token = get_user_meta( $user->get_id(), '_lp_zoom_token', true );

			$request_url = 'https://api.zoom.us/v2/meetings/' . $meeting_id;
			$auth        = 'Bearer ' . $data_token->access_token;
			$results     = LP_Zoom_Auth::instance()->learnpress_zoom_requests( '', 'DELETE', $auth, $request_url );

			if ( $results['code'] == 204 ) {
				$response->message = __( 'Delete meeting successfully!', 'learnpress-live' );
			} else {
				$response->message = $results['error'];
				$response->status  = 'error';
			}
		} catch ( Exception $e ) {
			$response->status  = 'error';
			$response->message = $e->getMessage();
		}

		return rest_ensure_response( $response );
	}

}

LearnPress_Zoom_Setting_Api::instance();
