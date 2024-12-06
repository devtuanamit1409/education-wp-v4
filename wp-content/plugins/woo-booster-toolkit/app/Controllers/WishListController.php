<?php

namespace WCBT\Controllers;

use WCBT\Helpers\RestApi;
use WCBT\Helpers\Template;
use WCBT\Models\ProductModel;
use WCBT\Helpers\Settings;
use WCBT\Helpers\WishList;
use WP_REST_Server;

/**
 * WishListController
 */
class WishListController {
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );

		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'add_wishlist_button' ), 30 );
		add_action( 'woocommerce_single_product_summary', array( $this, 'add_wishlist_button' ), 33 );

		add_shortcode( 'wcbt_wishlist_btn', array( $this, 'add_wishlist_button' ) );
		add_shortcode( 'wcbt_wishlist_link', array( $this, 'wishlist_link' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
	}

	/**
	 * @return void
	 */
	public function wp_enqueue_scripts() {
		wp_localize_script( 'wcbt-global', 'WCBT_WISHLIST_OBJECT', array(
			'enable'         => Settings::get_setting_detail( 'wishlist:fields:enable' ) === 'on',
			'tooltip_enable' => Settings::get_setting_detail( 'wishlist:fields:tooltip_enable' ) === 'on',
			'add_wishlist_tooltip_text' => WishList::get_wishlist_tooltip_text(),
			'remove_wishlist_tooltip_text' => WishList::get_wishlist_remove_tooltip_text(),
		) );
	}

	/**
	 * @return WishListController|null
	 */
	public static function instance() {
		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * @return void
	 */
	public function register_rest_routes() {
		register_rest_route(
			RestApi::generate_namespace(),
			'/wishlist',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'toggle_wishlist' ),
					'args'                => array(
						'product_id' => array(
							'required'    => false,
							'type'        => 'integer',
							'description' => 'The product id is required',
						),
					),
					'permission_callback' => function () {
						return is_user_logged_in();
					},
				),
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_user_wishlist' ),
					'permission_callback' => '__return_true',
				),
			)
		);
	}

	/**
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function toggle_wishlist( \WP_REST_Request $request ) {
		$user_id    = get_current_user_id();
		$product_id = $request->get_param( 'product_id' );

		$wishlist = get_user_meta( $user_id, WCBT_PREFIX . '_my_wishlist', true );

		$data = array();
		if ( empty( $wishlist ) ) {
			$meta_id = update_user_meta( $user_id, WCBT_PREFIX . '_my_wishlist', [ $product_id ] );
			if ( ! empty( $meta_id ) ) {
				$data['status'] = 'added';
			}
		} else {
			if ( in_array( $product_id, $wishlist ) ) {
				$key = array_search( $product_id, $wishlist );
				unset( $wishlist[ $key ] );
				if ( empty( $wishlist ) ) {
					$meta_id = delete_user_meta( $user_id, WCBT_PREFIX . '_my_wishlist' );
				} else {
					$meta_id = update_user_meta( $user_id, WCBT_PREFIX . '_my_wishlist', array_values( $wishlist ) );
				}

				if ( ! empty( $meta_id ) ) {
					$data['status'] = 'removed';
				}
			} else {
				$wishlist[] = $product_id;
				$meta_id    = update_user_meta( $user_id, WCBT_PREFIX . '_my_wishlist', $wishlist );
				if ( ! empty( $meta_id ) ) {
					$data['status'] = 'added';
				}
			}
		}

		$data['total'] = WishList::get_count();

		return RestApi::success( '', $data );
	}

	/**
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function get_user_wishlist( \WP_REST_Request $request ) {
		$params = $request->get_params();
		if ( is_user_logged_in() ) {
			$user_id  = get_current_user_id();
			$wishlist = get_user_meta( $user_id, WCBT_PREFIX . '_my_wishlist', true );
		} else {
			$wishlist = $params['post_in'] ?? array();
		}

		if ( empty( $wishlist ) ) {
			return RestApi::success( esc_html__( 'No product found.', 'product' ), array() );
		}

		$args = array(
			'posts_per_page' => $params['posts_per_page'] ?? 5,
			'paged'          => $params['page'] ?? 1,
			'orderby'        => $params['orderby'] ?? 'date',
			'order'          => $params['order'] ?? 'asc',
			'post_type'      => 'product',
			'post__in'       => $wishlist,
		);

		$data = array();

		$query    = new \WP_Query( $args );
		$template = Template::instance();

		ob_start();
		if ( $query->have_posts() ) {
			?>
            <table>
				<?php
				$template->get_frontend_template_type_classic(
					apply_filters( 'wcbt/filter/product-list/table-product-item/header', 'shared/product-list/table-product-item/header.php' )
				);
				while ( $query->have_posts() ) {
					$query->the_post();
					$template->get_frontend_template_type_classic(
						apply_filters( 'wcbt/filter/product-list/table-product-item/item', 'shared/product-list/table-product-item/item.php' )
					);
				}
				wp_reset_postdata();
				?>
            </table>
			<?php
		} else {
			return RestApi::success( esc_html__( 'No product found.', 'wcbt' ), array() );
		}

		$data['content'] = ob_get_clean();

		//Paginate
		$paginate_args = array(
			'paged'         => $args['paged'],
			'max_pages'     => $query->max_num_pages,
			'total'         => $query->found_posts,
			'item_per_page' => $args['posts_per_page'],
			'type'          => 'product',
		);

		RestApi::add_pagination_data( $data, $paginate_args );

		return RestApi::success( '', $data );
	}

	/**
	 * @param $attrs
	 *
	 * @return void
	 */
	public function add_wishlist_button( $attrs ) {
		if ( Settings::get_setting_detail( 'wishlist:fields:enable' ) !== 'on' ) {
			return;
		}

		global $product;
		$product_id = $attrs['product_id'] ?? '';

		if ( empty( $product_id ) ) {
			$product_id = ! is_null( $product ) ? $product->get_id() : 0;
		}

		if ( empty( $product_id ) ) {
			return;
		}

		$data = ProductModel::get_product_data( $product_id );

		Template::instance()->get_frontend_template_type_classic( 'wishlist/wishlist-btn.php', compact( 'data' ) );
	}


	public function add_wishlist_button_single_product() {
		if ( Settings::get_setting_detail( 'wishlist:fields:enable' ) !== 'on' ) {
			return;
		}

		global $product;
		$product_id = $attrs['product_id'] ?? '';

		if ( empty( $product_id ) ) {
			$product_id = ! is_null( $product ) ? $product->get_id() : 0;
		}

		if ( empty( $product_id ) ) {
			return;
		}

		$data                      = ProductModel::get_product_data( $product_id );
		$data['is_single_product'] = true;

		Template::instance()->get_frontend_template_type_classic( 'wishlist/wishlist-btn.php', compact( 'data' ) );
	}

	/**
	 * @param $attrs
	 *
	 * @return false|string|void
	 */
	public function wishlist_link( $attrs ) {
		if ( Settings::get_setting_detail( 'wishlist:fields:enable' ) !== 'on' ) {
			return;
		}

		$wishlist_page_id = Settings::get_setting_detail( 'wishlist:fields:page' );

		if ( empty( $wishlist_page_id ) ) {
			return;
		}

		$data = array(
			'wishlist_page_url' => get_permalink( $wishlist_page_id ),
		);

		ob_start();

		Template::instance()->get_frontend_template_type_classic( 'wishlist/wishlist-link.php', compact( 'data' ) );

		return ob_get_clean();
	}
}
