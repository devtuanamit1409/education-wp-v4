<?php
/**
 * Settings for Stripe payment gateway.
 */

return apply_filters(
	'learn-press/gateway-payment/stripe/settings',
	array(
		[ 'type' => 'title' ],
		array(
			'title'   => esc_html__( 'Enable', 'learnpress-stripe' ),
			'id'      => '[enable]',
			'default' => 'no',
			'type'    => 'yes-no',
		),
		array(
			'type'       => 'text',
			'title'      => esc_html__( 'Title', 'learnpress-stripe' ),
			'default'    => esc_html__( 'Stripe', 'learnpress-stripe' ),
			'id'         => '[title]',
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
			'title'      => esc_html__( 'Redirect Payment to Stripe Page', 'learnpress-stripe' ),
			'id'         => '[direct_payment_on_stripe_page]',
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
			'type'       => 'textarea',
			'title'      => esc_html__( 'Description', 'learnpress-stripe' ),
			'default'    => esc_html__( 'Pay with Stripe', 'learnpress-stripe' ),
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
			'title'      => esc_html__( 'Live secret key', 'learnpress-stripe' ),
			'id'         => '[live_secret_key]',
			'type'       => 'text',
			'visibility' => array(
				'state'       => 'show',
				'conditional' => array(
					array(
						'field'   => '[enable]',
						'compare' => '=',
						'value'   => 'yes',
					),
					array(
						'field'   => '[test_mode]',
						'compare' => '!=',
						'value'   => 'yes',
					),
				),
			),
		),
		array(
			'type'       => 'text',
			'title'      => esc_html__( 'Live publish key', 'learnpress-stripe' ),
			'default'    => '',
			'id'         => '[live_publish_key]',
			'visibility' => array(
				'state'       => 'show',
				'conditional' => array(
					array(
						'field'   => '[enable]',
						'compare' => '=',
						'value'   => 'yes',
					),
					array(
						'field'   => '[test_mode]',
						'compare' => '!=',
						'value'   => 'yes',
					),
				),
			),
		),
		array(
			'title'      => esc_html__( 'Enable test mode', 'learnpress-stripe' ),
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
			'type'       => 'text',
			'title'      => esc_html__( 'Test secret key', 'learnpress-stripe' ),
			'default'    => '',
			'id'         => '[test_secret_key]',
			'visibility' => array(
				'state'       => 'show',
				'conditional' => array(
					array(
						'field'   => '[enable]',
						'compare' => '=',
						'value'   => 'yes',
					),
					array(
						'field'   => '[test_mode]',
						'compare' => '=',
						'value'   => 'yes',
					),
				),
			),
		),
		array(
			'type'       => 'text',
			'title'      => esc_html__( 'Test publish key', 'learnpress-stripe' ),
			'default'    => '',
			'id'         => '[test_publish_key]',
			'visibility' => array(
				'state'       => 'show',
				'conditional' => array(
					array(
						'field'   => '[enable]',
						'compare' => '=',
						'value'   => 'yes',
					),
					array(
						'field'   => '[test_mode]',
						'compare' => '=',
						'value'   => 'yes',
					),
				),
			),
		),
		[ 'type' => 'sectionend' ],
	)
);
