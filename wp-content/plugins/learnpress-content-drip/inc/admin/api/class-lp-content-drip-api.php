<?php
defined( 'ABSPATH' ) || exit();


class LearnPress_Content_Drip_Api {

	private static $instance;
	/**
	 * @var string
	 */
	public $namespace = 'lp/content-drip/v1';

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
			'update-settings',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'update_settings' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);
		register_rest_route(
			$this->namespace,
			'reset-settings',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'reset_settings' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);
	}

	public function update_settings( WP_REST_Request $request ) {
		$params   = $request->get_params();
		$response = new stdClass();

		$data_settings = $params['dataSettings'];
		$course_id     = $params['courseID'];

		try {

			if ( empty( $course_id ) ) {
				throw new Exception( __( 'Course ID is empty', 'learnpress-content-drip' ) );
			}

			if ( empty( $data_settings ) ) {
				throw new Exception( __( 'Drip items is empty', 'learnpress-content-drip' ) );
			}

			$drip_items = array();

			foreach ( $data_settings as $item ) {
				$item_id = $item['id'];

				if ( $item_id ) {
					$settings = $item['settings'];

					if ( ! empty( $settings ) ) {
						$drip_items[ $item['id'] ] = array(
							'prerequisite' => $settings['prerequisite'] ?? array(),
							'type'         => $settings['type'] ?? 'immediately',
							'date'         => $settings['date'] ?? '',
							'interval'     => array( $settings['interval'] ?? 0, $settings['interval_type'] ?? 'minute' ),
						);
					}
				}
			}

			$this->course_update_data_settings( $drip_items, $course_id );

			$response->status = 'success';

		} catch ( Exception $e ) {
			$response->status  = 'error';
			$response->message = $e->getMessage();
		}

		return rest_ensure_response( $response );
	}

	public function reset_settings( WP_REST_Request $request ) {
		$params   = $request->get_params();
		$response = new stdClass();

		$data_settings = $params['dataReset'];
		$course_id     = $params['courseID'];

		try {

			if ( empty( $course_id ) ) {
				throw new Exception( __( 'Course ID is empty', 'learnpress-content-drip' ) );
			}

			if ( empty( $data_settings ) ) {
				throw new Exception( __( 'Drip items is empty', 'learnpress-content-drip' ) );
			}

			$drip_items = array();

			foreach ( $data_settings as $id ) {
				$drip_items[ $id ] = array(
					'prerequisite' => array(),
					'type'         => 'immediately',
					'date'         => '',
					'interval'     => array( 0, 'minute' ),
				);
			}

			$this->course_update_data_settings( $drip_items, $course_id );

			$response->status = 'success';

		} catch ( Exception $e ) {
			$response->status  = 'error';
			$response->message = $e->getMessage();
		}

		return rest_ensure_response( $response );
	}

	public function course_update_data_settings( $drip_items = array(), $course_id = 0 ) {
		if ( ! $course_id ) {
			return;
		}
		// drip type
		$drip_type = get_post_meta( $course_id, '_lp_content_drip_drip_type', true );

		// present drip item meta
		$drip_meta = get_post_meta( $course_id, '_lp_drip_items', true );

		if ( ! empty( $drip_items ) ) {
			foreach ( $drip_items as $id => $item ) {
				if ( $drip_type == 'prerequisite' ) {
					$drip_items[ $id ]['prerequisite'] = $item['prerequisite'] ?? 0;
				} else {
					$drip_items[ $id ]['prerequisite'] = $drip_meta[ $id ]['prerequisite'] ?? 0;
				}

				if ( ( $item['type'] == 'interval' && ! $item['interval'][0] ) || ( $item['type'] == 'specific' && ! $item['date'] ) ) {
					$drip_items[ $id ]['type'] = 'immediately';
				}

				switch ( $item['type'] ) {
					case 'interval':
						$drip_items[ $id ]['interval'][2] = LP_Addon_Content_Drip_Preload::$addon->lpcd_data_to_seconds( $item['interval'][0], $item['interval'][1] );
						break;
					case 'specific':
						$drip_items[ $id ]['date'] = strtotime( get_gmt_from_date( $item['date'], 'Y-m-d H:i:s' ) );
						if ( empty( $drip_items[ $id ]['date'] ) ) {
							$drip_items[ $id ]['date'] = strtotime( get_gmt_from_date( str_replace( '/', '-', $item['date'] ), 'Y-m-d H:i:s' ) );
						}
						break;
					default:
						break;
				}
			}
		}

		update_post_meta( $course_id, '_lp_drip_items', $drip_items );

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
LearnPress_Content_Drip_Api::instance();
