<?php

namespace WCBT\Controllers;

use WCBT\Helpers\RestApi;
use WP_REST_Server;

class PageController
{
    public function __construct()
    {
        add_action('rest_api_init', array( $this, 'register_rest_routes' ));
    }

    /**
     * @return void
     */
    public function register_rest_routes()
    {
        register_rest_route(
            RestApi::generate_namespace(),
            '/page',
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'insert_page' ),
                'permission_callback' => '__return_true',
            ),
        );
    }

    /**
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response
     */
    public function insert_page(\WP_REST_Request $request)
    {
        $params = $request->get_params();

        $args = array(
            'post_title'   => $params['title'] ?? '',
            'post_content' => $params['content'] ?? '',
            'post_type'    => $params['post_type'] ?? 'post',
            'post_status'  => $params['post_status'] ?? 'publish',
        );

        $id = wp_insert_post($args);

        if (empty($id) || is_wp_error($id)) {
            return RestApi::error(esc_html__('Could not create a page.', 'wcbt'), 409);
        }

        return RestApi::success(
            esc_html__('Create a page successfully.', 'wcbt'),
            array(
                'id'    => $id,
                'title' => get_the_title($id),
            )
        );
    }
}
