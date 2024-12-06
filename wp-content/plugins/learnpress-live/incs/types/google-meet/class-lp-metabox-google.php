<?php
/**
 * LP_Meta_Box_Google
 *
 * @author minhpd
 * @version 1.0.0
 * @since 4.0.0
 */
/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

class LP_Meta_Box_Google extends LP_Meta_Box_Field {

	public function output( $thepostid ) {

		$value = $this->meta_value( $thepostid );
		$data  = [
			'meta_box_google' => $this,
			'value'           => $value,
		];

		LP_Addon_Live_Preload::$addon->get_template(
			'/metabox/metabox-google.php',
			compact( 'data' )
		);
	}

	public function save( $post_id ) {

		$value     = isset( $_POST[ $this->id ] ) ? LP_Helper::sanitize_params_submitted( $_POST[ $this->id ], 'string' ) : '';
		$value_old = $this->meta_value( $post_id );

		//update post meta value
		update_post_meta( $post_id, $this->id, $value );

		//insert shortcode
		$content = get_post_field( 'post_content', $post_id );
		preg_match_all(
			'/' . get_shortcode_regex() . '/',
			$content,
			$matches,
			PREG_SET_ORDER
		);

		//using save data to call api zoom
		$list_meetings = array( $value );

		if ( ! has_shortcode( $content, 'learn_press_google_meeting' ) ) {
			if ( $value ) {
				$content = '[learn_press_google_meeting meeting_id="' . $value . '"]' . $content;
			}
		} else {
			if ( $matches ) {
				foreach ( $matches as $match ) {
					if ( $match[3] ) {
						$attr = shortcode_parse_atts( $match[3] );
						if ( isset( $attr['meeting_id'] ) ) {
							$id = $attr['meeting_id'];
							if ( $id == $value_old && $value != $value_old ) {
								if ( $value ) {
									$content = str_replace( $id, $value, $content );
								}
							}
							//use to save data zoom to db
							if ( ! in_array( $id, $list_meetings ) ) {
								$list_meetings[] = $id;
							}
							//remove shortcode if value empty
							if ( empty( $value ) ) {
								if ( $value_old == $id ) {
									$content = str_replace( $match[0], '', $content );
								} else {
									update_post_meta( $post_id, $this->id, $id );
								}
							} else {
								if ( $id != $value ) {
									update_post_meta( $post_id, $this->id, $id );
								}
							}
							break;
						}
					}
				}
			}
		}

		wp_update_post(
			array(
				'ID'           => $post_id,
				'post_content' => $content,
			)
		);

		//save db using render FE
		global $wpdb;
		$db_live = LP_Live_Database::instance();

		foreach ( $list_meetings as $meeting_id ) {
			$id_exists = $wpdb->get_row( "SELECT * FROM $db_live->tb_lp_live WHERE live_id = '" . $meeting_id . "'", ARRAY_A );
			if ( $id_exists ) {
				continue;
			};
			$controller = new LearnPress_Google_Setting_Api();
			$request    = new WP_REST_Request();
			$request->set_param( 'ID', $meeting_id );
			$response    = $controller->get_meeting( $request );
			$object_data = $response->get_data();

			if ( ! empty( $object_data->data ) ) {
				$data = array(
					'live_type'  => 'google_meet',
					'live_value' => json_encode( $object_data->data ),
				);
				$db_live->update( $meeting_id, $data );
			}
		}
	}

}
