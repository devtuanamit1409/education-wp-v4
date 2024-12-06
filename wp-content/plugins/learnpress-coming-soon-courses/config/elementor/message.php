<?php

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use LearnPress\ExternalPlugin\Elementor\LPElementorControls;

return LPElementorControls::add_fields_in_section(
	'style_message',
	esc_html__( 'Style', 'learnpress-coming-soon-courses' ),
	Controls_Manager::TAB_STYLE,
	[
		'text_color' => LPElementorControls::add_control_type_color(
			'text_color',
			esc_html__( 'Color', 'learnpress-coming-soon-courses' ),
			[
				'{{WRAPPER}} .learn-press-coming-soon-course-message' => 'color: {{VALUE}};',
			]
		),
		'text_typo'  => LPElementorControls::add_group_control_type(
			'text_typo',
			Group_Control_Typography::get_type(),
			'{{WRAPPER}} .learn-press-coming-soon-course-message',
			[
				'label' => esc_html__( 'Typography', 'learnpress-coming-soon-courses' ),
			]
		),
	]
);
