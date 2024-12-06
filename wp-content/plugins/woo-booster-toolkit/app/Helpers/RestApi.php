<?php

namespace WCBT\Helpers;

use WP_REST_Response;

/**
 * Class RestApi
 */
class RestApi {
	/**
	 * @return string
	 */
	public static function generate_namespace(): string {
		return WCBT_PREFIX . '/' . WCBT_REST_VERSION;
	}

	public static function error( string $msg = '', $status_code = 404 ) {
		return new WP_REST_Response(
			array(
				'status'      => 'error',
				'msg'         => $msg,
				'status_code' => $status_code,
			),
			$status_code
		);
	}

	/**
	 * @param string $msg
	 * @param array $data
	 *
	 * @return WP_REST_Response
	 */
	public static function success( string $msg = '', array $data = array() ) {
		return new WP_REST_Response(
			array(
				'status' => 'success',
				'msg'    => $msg,
				'data'   => $data,
			),
			200
		);
	}

	/**
	 * @param $data
	 * @param $args
	 *
	 * @return void
	 */
	public static function add_pagination_data( &$data, $args = array() ) {
		ob_start();
		$current_page  = $args['paged'];
		$max_pages     = intval( $args['max_pages'] );
		$total         = $args['total'];
		$item_per_page = $args['item_per_page'];
		$from          = 1 + ( $current_page - 1 ) * $item_per_page;
		$to            = ( $current_page * $item_per_page > $total ) ? $total : $current_page * $item_per_page;
		$from_to       = '';
		if ( 1 === $total ) {
			$from_to = esc_html__( 'Showing only one result.', 'wcbt' );
		} else {
			if ( $total === $to ) {
				if ( $args['type'] === 'product' ) {
					$from_to = sprintf( esc_html__( 'Showing the last products of %s results.', 'wcbt' ), $total );
				}
			} else {
				$from_to = $from . '-' . $to;
				$from_to = sprintf( esc_html__( 'Showing %1$s of %2$s results.', 'wcbt' ), $from_to, $total );
			}
		}

		$data ['total']   = $total;
		$data ['from_to'] = $from_to;
		if ( $current_page > 1 ) {
			$data ['prev_page'] = $current_page - 1;
		}

		if ( $current_page < $max_pages ) {
			$data ['next_page'] = $current_page + 1;
		}

		Template::instance()->get_frontend_template_type_classic(
			'shared/pagination/standard/pagination.php',
			array(
				'max_page'     => $max_pages,
				'current_page' => $current_page,
			)
		);

		$data ['pagination'] = ob_get_clean();
	}
}
