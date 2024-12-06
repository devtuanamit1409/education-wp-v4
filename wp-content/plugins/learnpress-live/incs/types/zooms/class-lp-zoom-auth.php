<?php
/**
 * class Zoom Auth
 *
 * @author   ThimPress
 * @version  4.0.0
 */

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'LP_Zoom_Auth' ) ) {
	class LP_Zoom_Auth {

		private static $instance = null;

		public static $OAuth_revalidate_attempts = 0;

		public static function instance(): LP_Zoom_Auth {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * It takes the client_id, client_secret, code, and refresh_token as parameters and returns the
		 * access token
		 *
		 * @param string $client_id The client ID you received from Zoom when you created the OAuth app.
		 * @param string $client_secret The client secret you received when you registered your application.
		 * @param string $code The authorization code you received from the previous step.
		 * @param string $refresh_token The refresh token that you received in the previous step.
		 *
		 * @return The response is a JSON object containing the following fields:
		 */
		public function generateAccessToken( $client_id = '', $client_secret = '', $code = '', $refresh_token = '' ) {

			$base64Encoded = base64_encode( $client_id . ':' . $client_secret );
			$result        = new \WP_Error( 0, 'Something went wrong' );
			$user          = learn_press_get_current_user();

			$body = array(
				'grant_type'   => 'authorization_code',
				'code'         => $code,
				'redirect_uri' => LP_Addon_Live_Preload::$addon->url_page_setting() . 'settings/',
			);

			if ( ! empty( $refresh_token ) ) {
				$body = array(
					'grant_type'    => 'refresh_token',
					'refresh_token' => $refresh_token,
				);
			}

			$args = [
				'method'  => 'POST',
				'headers' => [
					'Authorization' => "Basic $base64Encoded",
				],
				'body'    => $body,
			];

			$request_url      = 'https://zoom.us/oauth/token';
			$response         = wp_remote_post( $request_url, $args );
			$responseCode     = wp_remote_retrieve_response_code( $response );
			$response_message = wp_remote_retrieve_response_message( $response );
			$responseBody     = wp_remote_retrieve_body( $response );

			if ( $responseCode == 200 && strtolower( $response_message ) == 'ok' ) {
				$decoded_response_body = json_decode( $responseBody );
				if ( isset( $decoded_response_body->access_token ) && ! empty( $decoded_response_body->access_token ) ) {
					$result = $decoded_response_body;
				} else {
					$result = new \WP_Error( $responseCode, $responseBody );
				}
			} else {
				$result = new \WP_Error( $responseCode, $responseBody );
			}

			return $result;
		}

		/**
		 * @param boolean $refesh
		 *
		 * @return mixed
		 */
		public function generateAndSaveAccessToken( $refesh = false ) {
			$user = learn_press_get_current_user();
			if ( empty( $user ) ) {
				return false;
			}
			$user_id           = $user->get_id();
			$user_data_connect = get_user_meta( $user_id, '_lp_zoom_connect', true );

			$client_id     = isset( $user_data_connect['client_id'] ) ? $user_data_connect['client_id'] : '';
			$client_secret = isset( $user_data_connect['client_secret'] ) ? $user_data_connect['client_secret'] : '';
			$code          = isset( $user_data_connect['client_code'] ) ? $user_data_connect['client_code'] : '';
			$refresh_token = '';

			if ( $refesh ) {
				$token_data    = get_user_meta( $user_id, '_lp_zoom_token', true );
				$refresh_token = isset( $token_data->refresh_token ) ? $token_data->refresh_token : '';
			}

			$result = $this->generateAccessToken( $client_id, $client_secret, $code, $refresh_token );

			//update user meta
			if ( ! is_wp_error( $result ) ) {
				update_user_meta( $user_id, '_lp_zoom_token', $result );
			}
			return $result;
		}

		/**
		 * It takes in an array of data, a method, an authorization string, and a request URL, and returns
		 * the response body as an object
		 *
		 * @param array $body The body of the request.
		 * @param string $method The HTTP method to use for the request.
		 * @param string $auth The authorization token you got from the previous step.
		 * @param string $request_url The URL to which you are sending the request.
		 *
		 * @return The response body is being returned.
		 */
		public function learnpress_zoom_requests( $body = [], $method = 'GET', $auth = '', $request_url = '' ) {

			$args = [
				'method'  => $method,
				'headers' => [
					'Authorization' => $auth,
					'Content-Type'  => 'application/json',
				],
				'body'    => $body,
			];

			$request         = wp_remote_request( $request_url, $args );
			$responseCode    = wp_remote_retrieve_response_code( $request );
			$responseMessage = wp_remote_retrieve_response_message( $request );
			$responseBody    = wp_remote_retrieve_body( $request );

			$result = array(
				'code'    => $responseCode,
				'message' => $responseMessage,
				'body'    => json_decode( $responseBody ),
				'error'   => $responseBody,
			);

			if ( $responseCode == 401 ) {
				$token = $this->generateAndSaveAccessToken( true );
				if ( ! is_wp_error( $token ) ) {
					if ( self::$OAuth_revalidate_attempts <= 2 ) {
						self::$OAuth_revalidate_attempts ++;

						$auth = 'Bearer ' . $token->access_token;
						//resend the request after regenerating access token
						return $this->learnpress_zoom_requests( $body, $method, $auth, $request_url );
					} else {
						self::$OAuth_revalidate_attempts = 0;
					}
				}
			} else {
				return $result;
			}
		}
	}
}
