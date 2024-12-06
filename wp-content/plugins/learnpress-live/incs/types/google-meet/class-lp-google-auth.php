<?php
/**
 * class Zoom Auth
 *
 * @author   ThimPress
 * @version  4.0.0
 */

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'LP_Google_Auth' ) ) {
	class LP_Google_Auth {

		private static $instance = null;

		public static function instance(): LP_Google_Auth {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * It takes the client_id, client_secret, code, and refresh_token as parameters and returns the
		 * access token
		 *
		 * @param client_id The client ID you received from Zoom when you created the OAuth app.
		 * @param client_secret The client secret you received when you registered your application.
		 * @param code The authorization code you received from the previous step.
		 * @param refresh_token The refresh token that you received in the previous step.
		 *
		 * @return The response is a JSON object containing the following fields:
		 */
		public function generateAccessToken( $client_id, $client_secret, $code, $refresh_token = '' ) {

			$result = new \WP_Error( 0, 'Something went wrong' );
			$body   = array(
				'grant_type'    => 'authorization_code',
				'code'          => $code,
				'redirect_uri'  => LP_Addon_Live_Preload::$addon->url_page_setting() . 'settings/',
				'client_id'     => $client_id,
				'client_secret' => $client_secret,
			);

			if ( ! empty( $refresh_token ) ) {
				$body['grant_type']    = 'refresh_token';
				$body['refresh_token'] = $refresh_token;
				unset( $body['code'] );
				unset( $body['redirect_uri'] );
			}

			$args = [
				'method'  => 'POST',
				'headers' => [
					'Content-Type' => 'application/x-www-form-urlencoded',
				],
				'body'    => $body,
			];

			$request_url      = 'https://oauth2.googleapis.com/token';
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
			$user_data_connect = get_user_meta( $user_id, '_lp_google_connect', true );

			$client_id     = isset( $user_data_connect['client_id'] ) ? $user_data_connect['client_id'] : '';
			$client_secret = isset( $user_data_connect['client_secret'] ) ? $user_data_connect['client_secret'] : '';
			$code          = isset( $user_data_connect['client_code'] ) ? $user_data_connect['client_code'] : '';
			$refresh_token = '';

			if ( $refesh ) {
				$token_data    = get_user_meta( $user_id, '_lp_google_token', true );
				$refresh_token = isset( $token_data->refresh_token ) ? $token_data->refresh_token : '';
				if ( empty( $refresh_token ) ) {
					$refresh_token = get_user_meta( $user_id, '_lp_refresh_token', true );
				}
			}

			$result = $this->generateAccessToken( $client_id, $client_secret, $code, $refresh_token );

			//update user meta
			if ( ! is_wp_error( $result ) ) {
				update_user_meta( $user_id, '_lp_google_token', $result );
			}
			return $result;
		}

		/**
		 * It takes in an array of data, a method, an authorization string, and a request URL, and returns
		 * the response body as an object
		 *
		 * @param array body The body of the request.
		 * @param string method The HTTP method to use for the request.
		 * @param string auth The authorization token you got from the previous step.
		 * @param string request_url The URL to which you are sending the request.
		 *
		 * @return The response body is being returned.
		 */
		public function learnpress_google_requests( $body, string $method = 'GET', string $auth, string $request_url ) {

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
			$result          = array(
				'code'    => $responseCode,
				'message' => $responseMessage,
				'body'    => json_decode( $responseBody ),
				'error'   => $responseBody,
			);

			if ( $responseCode == 401 ) {
				$token = $this->generateAndSaveAccessToken( true );
				if ( ! is_wp_error( $token ) ) {
					$auth = 'Bearer ' . $token->access_token;
					//resend the request after regenerating access token
					return $this->learnpress_google_requests( $body, $method, $auth, $request_url );
				}
			} else {
				return $result;
			}
		}
	}
}
