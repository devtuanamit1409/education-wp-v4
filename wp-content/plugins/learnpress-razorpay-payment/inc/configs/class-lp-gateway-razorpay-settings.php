<?php
return apply_filters(
	'learn-press/gateway-payment/razorpay/settings',
	array(
		array(
			'type' => 'title',
		),
		array(
			'title'   => esc_html__( 'Enable', 'learnpress-razorpay-payment' ),
			'id'      => '[enable]',
			'default' => 'no',
			'type'    => 'yes-no',
		),
		array(
			'type'       => 'text',
			'title'      => esc_html__( 'Title', 'learnpress-razorpay-payment' ),
			'default'    => esc_html__( 'Razorpay', 'learnpress-razorpay-payment' ),
			'id'         => '[title]',
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
			'title'      => esc_html__( 'Description', 'learnpress-razorpay-payment' ),
			'default'    => esc_html__( 'Make a payment with Razorpay', 'learnpress-razorpay-payment' ),
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
			'type'       => 'color',
			'title'      => esc_html__( 'Color Theme', 'learnpress-razorpay-payment' ),
			'id'         => '[color_theme]',
			'css'        => 'width:6em;',
			'default'    => '#0b72e7',
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
			'title'      => esc_html__( 'Key ID', 'learnpress-razorpay-payment' ),
			'id'         => '[key_id]',
			'type'       => 'text',
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
			'type'       => 'text',
			'title'      => esc_html__( 'Key Secret', 'learnpress-razorpay-payment' ),
			'default'    => '',
			'id'         => '[key_secret]',
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
			'type' => 'sectionend',
		),
	)
);
