<?php

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use LearnPress\ExternalPlugin\Elementor\LPElementorControls;

return LPElementorControls::add_fields_in_section(
	'style_countdown',
	esc_html__( 'Style', 'learnpress-coming-soon-courses' ),
	Controls_Manager::TAB_STYLE,
	[
		'counter_width'    => LPElementorControls::add_responsive_control_type(
			'counter_width',
			esc_html__( 'Width', 'learnpress-coming-soon-courses' ),
			[],
			Controls_Manager::SLIDER,
			[
				'size_units' => [ 'px', 'em', 'custom' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors'  => array(
					'{{WRAPPER}} .counter-block' => 'width: {{SIZE}}{{UNIT}};',
				),
			]
		),
		'counter_height'   => LPElementorControls::add_responsive_control_type(
			'counter_height',
			esc_html__( 'Height', 'learnpress-coming-soon-courses' ),
			[],
			Controls_Manager::SLIDER,
			[
				'size_units' => [ 'px', 'em', 'custom' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors'  => array(
					'{{WRAPPER}} .counter-block' => 'height: {{SIZE}}{{UNIT}};',
				),
			]
		),
		'background_color' => LPElementorControls::add_control_type_color(
			'background_color',
			esc_html__( 'Background Color', 'learnpress-coming-soon-courses' ),
			[
				'{{WRAPPER}} .counter-block' => 'background-color: {{VALUE}};',
			]
		),
		'counter_border'   => LPElementorControls::add_group_control_type(
			'counter_border',
			Group_Control_Border::get_type(),
			'{{WRAPPER}} .counter-block'
		),
		'border_radius'    => LPElementorControls::add_control_type(
			'border_radius',
			esc_html__( 'Border Radius', 'learnpress-coming-soon-courses' ),
			[],
			Controls_Manager::DIMENSIONS,
			[
				'size_units' => [ 'px', '%', 'custom' ],
				'selectors'  => [
					'{{WRAPPER}} .counter-block' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		),
		'number_color'     => LPElementorControls::add_control_type_color(
			'number_color',
			esc_html__( 'Number Color', 'learnpress-coming-soon-courses' ),
			[
				'{{WRAPPER}} .counter .number' => 'color: {{VALUE}};',
			],
			[
				'separator' => 'before',
			]
		),
		'number_typo'      => LPElementorControls::add_group_control_type(
			'number_typo',
			Group_Control_Typography::get_type(),
			'{{WRAPPER}} .counter',
			[
				'label' => esc_html__( 'Number Typography', 'learnpress-coming-soon-courses' ),
			]
		),
		'number_margin'    => LPElementorControls::add_responsive_control_type(
			'number_margin',
			esc_html__( 'Margin', 'learnpress' ),
			[],
			Controls_Manager::DIMENSIONS,
			[
				'size_units' => [ 'px', '%', 'custom' ],
				'selectors'  => array(
					'{{WRAPPER}} .counter-block .counter' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			]
		),
		'text_color'       => LPElementorControls::add_control_type_color(
			'text_color',
			esc_html__( 'Text Color', 'learnpress-coming-soon-courses' ),
			[
				'{{WRAPPER}} .counter-caption' => 'color: {{VALUE}};',
			],
			[
				'separator' => 'before',
			]
		),
		'text_typo'        => LPElementorControls::add_group_control_type(
			'text_typo',
			Group_Control_Typography::get_type(),
			'{{WRAPPER}} .counter-caption',
			[
				'label' => esc_html__( 'Text Typography', 'learnpress-coming-soon-courses' ),
			]
		),
	]
);
