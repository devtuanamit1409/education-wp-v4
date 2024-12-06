<?php

namespace Elementor;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Thim_Ekit_Widget_List_Instructors extends Widget_Base {

	public function get_name() {
		return 'thim-list-instructors';
	}

	public function get_title() {
		return esc_html__( 'List Instructors', 'eduma' );
	}

	public function get_icon() {
		return 'eduma-eicon thim-widget-icon thim-widget-icon-one-course-instructors';
	}

	public function get_categories() {
		return [ 'eduma_ekit' ];
	}


	public function get_base() {
		return basename( __FILE__, '.php' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content',
			[
				'label' => esc_html__( 'List Instructors', 'eduma' )
			]
		);

		$this->add_control(
			'layout',
			[
				'label'   => esc_html__( 'Layout', 'eduma' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'base' => esc_html__( 'Default', 'eduma' ),
					'new'  => esc_html__( 'New', 'eduma' ),
					'grid'  => esc_html__( 'Grid', 'eduma' ),
					'slider'  => esc_html__( 'Slider', 'eduma' )
				],
				'default' => 'base'
			]
		);
		$this->add_control(
			'limit_instructor',
			[
				'label'     => esc_html__( 'Limit', 'eduma' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 4,
				'min'       => 0,
				'step'      => 1,
				'condition' => [
					'layout' => [ 'base' ]
				]
			]
		);
		$this->add_control(
			'visible_item',
			[
				'label'   => esc_html__( 'Visible Instructors', 'eduma' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 3,
				'min'     => 0,
				'step'    => 1,
				'condition' => [
					'layout!' => [ 'slider' ]
				]
			]
		);

		$this->add_control(
			'columns',
			[
				'label'   => esc_html__( 'Columns', 'eduma' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 4,
				'min'     => 2,
				'max'     => 5,
				'step'    => 1,
				'condition' => [
					'layout' => [ 'grid' ]
				]
			]
		);

		$this->add_control(
			'show_pagination',
			[
				'label'   => esc_html__( 'Show Pagination?', 'eduma' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => [
					'layout!' => [ 'grid', 'slider' ]
				]
			]
		);
		$this->add_control(
			'show_navigation',
			[
				'label'   => esc_html__( 'Show navigation?', 'eduma' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => [
					'layout!' => [ 'grid', 'slider' ]
				]
			]
		);

		$this->add_control(
			'auto_play',
			[
				'label'       => esc_html__( 'Auto Play Speed (in ms)', 'eduma' ),
				'description' => esc_html__( 'Set 0 to disable auto play.', 'eduma' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => 0,
				'step'        => 100,
				'condition' => [
					'layout!' => [ 'grid', 'slider' ]
				]
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'panel_img',
			[
				'label'   => esc_html__( 'Avatar', 'eduma' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$repeater->add_control(
			'panel_id',
			[
				'label'   => esc_html__( 'Instructor', 'eduma' ),
				'type'    => Controls_Manager::SELECT,
				'options' => thim_get_instructors( array( esc_html__( 'Select', 'eduma' ) => '' ), true ),
				'default' => '',
			]
		);

		$this->add_control(
			'panel',
			[
				'label'     => esc_html__( 'Select Instructor', 'eduma' ),
				'type'      => Controls_Manager::REPEATER,
				'separator' => 'before',
				'fields'    => $repeater->get_controls(),
				'condition' => [
					'layout' => [ 'new', 'slider' ]
				]
			]
		);

		$this->end_controls_section();

		$this->_register_setting_slider();

		$this->_register_setting_slider_dot_style();

		$this->_register_setting_slider_nav_style();

		$this->_register_style_settings();
	}


	protected function _register_style_settings() {

		$this->start_controls_section(
			'section_style_setting',
			array(
				'label' => esc_html__( 'Setting', 'eduma' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'layout!' => [ 'slider' ]
				]
			)
		);
		$this->add_control(
			'heading_title_style',
			array(
				'label' => esc_html__( 'Title', 'eduma' ),
				'type'  => Controls_Manager::HEADING,

			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .thim-ekits-list-instructors .instructor-info .name',
				'exclude'  => [ 'letter_spacing', 'word_spacing' ],
			)
		);

		$this->add_control(
			'title_spacing',
			array(
				'label'     => esc_html__( 'Spacing', 'eduma' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .thim-ekits-list-instructors .instructor-info .name' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'title_text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'eduma' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .thim-ekits-list-instructors .instructor-info .name a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'text_title_hover',
			[
				'type'         => \Elementor\Controls_Manager::POPOVER_TOGGLE,
				'label'        => esc_html__( 'Title Hover', 'eduma' ),
				'label_off'    => esc_html__( 'Default', 'eduma' ),
				'label_on'     => esc_html__( 'Custom', 'eduma' ),
				'return_value' => 'yes',
			]
		);

		$this->start_popover();
		$this->add_control(
			'title_text_color_hover',
			array(
				'label'     => esc_html__( 'Text Color Hover', 'eduma' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .thim-ekits-list-instructors .instructor-info .name a:hover' => 'color: {{VALUE}};',
				),
				'condition' => [
					'text_title_hover' => 'yes'
				],
			)
		);
		$this->add_control(
			'text_title_decoration_hover',
			array(
				'label'     => esc_html__( 'Decoration', 'eduma' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => [
					''             => esc_html__( 'Default', 'eduma' ),
					'underline'    => esc_html__( 'Underline', 'eduma' ),
					'overline'     => esc_html__( 'Overline', 'eduma' ),
					'line-through' => esc_html__( 'Line Through', 'eduma' ),
					'none'         => esc_html__( 'None', 'eduma' ),
				],
				'selectors' => array(
					'{{WRAPPER}} .thim-ekits-list-instructors .instructor-info .name a:hover' => 'text-decoration: {{VALUE}};',
				),
			)
		);

		$this->end_popover();

		$this->add_control(
			'heading_desc_style',
			array(
				'label'     => esc_html__( 'Description', 'eduma' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'layout!' => [ 'grid' ]
				]
			)
		);
		$this->add_control(
			'desc_text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'eduma' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .thim-ekits-list-instructors .instructor-info .description' => 'color: {{VALUE}};',
				),
				'condition' => [
					'layout!' => [ 'grid' ]
				]
			)
		);

		$this->add_control(
			'instructor_position_style',
			array(
				'label'     => esc_html__( 'Position', 'eduma' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'layout' => [ 'grid' ]
				]
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'position_typography',
				'selector' => '{{WRAPPER}} .thim-ekits-list-instructors .instructor-info .job',
				'condition' => [
					'layout' => [ 'grid' ]
				]
			)
		);

		$this->add_control(
			'position_spacing',
			array(
				'label'     => esc_html__( 'Spacing', 'eduma' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .thim-ekits-list-instructors .instructor-info .job' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);


		$this->add_control(
			'position_text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'eduma' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .thim-ekits-list-instructors .instructor-info .job' => 'color: {{VALUE}};',
				),
				'condition' => [
					'layout' => [ 'grid' ]
				]
			)
		);

		$this->add_control(
			'heading_avatar_style',
			array(
				'label'     => esc_html__( 'Avatar', 'eduma' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_responsive_control(
			'content_avatar_padding',
			array(
				'label'      => esc_html__( 'Padding', 'eduma' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .instructor-item .avatar_item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'content_avatar_margin',
			array(
				'label'      => esc_html__( 'Margin', 'eduma' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .instructor-item .avatar_item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'avatar_border',
				'selector' => '{{WRAPPER}} .instructor-item .avatar_item',
			)
		);
		$this->add_control(
			'avatar_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'eduma' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .instructor-item .avatar_item, {{WRAPPER}} .instructor-item .avatar_item img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .instructor-item .avatar_item:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_control(
			'background_overlay_hover',
			array(
				'label'     => esc_html__( 'Overlay Hover', 'eduma' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .thim-list-instructors.instructor-grid .instructor-item:hover .avatar_item::before' => 'background: {{VALUE}}',
				),
				'condition' => [
					'layout' => [ 'grid' ]
				]
			)
		);
		$this->add_control(
			'heading_social_style',
			array(
				'label'     => esc_html__( 'Social', 'eduma' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_control(
			'social_text_color',
			array(
				'label'     => esc_html__( 'Social Color', 'eduma' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .thim-ekits-list-instructors .thim-author-social li a:not(:hover)' => 'color: {{VALUE}};border-color: {{VALUE}}',
				),
			)
		);
		$this->add_control(
			'social_hover_color',
			array(
				'label'     => esc_html__( 'Social Hover Color', 'eduma' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .thim-ekits-list-instructors .info_ins .thim-author-social li a:hover' => 'color: {{VALUE}};',
				),
				'condition' => [
					'layout' => [ 'grid' ]
				]
			)
		);
		$this->end_controls_section();
	}

	protected function _register_setting_slider() {
		// setting slider section

		$this->start_controls_section(
			'skin_slider_settings',
			[
				'label'     => esc_html__( 'Settings Slider', 'eduma' ),
				'condition' => array(
					'layout' => 'slider',
				),
			]
		);

		$this->add_responsive_control(
			'slidesPerView',
			[
				'label'              => esc_html__( 'Item Show', 'eduma' ),
				'type'               => Controls_Manager::NUMBER,
				'min'                => 1,
				'max'                => 20,
				'step'               => 1,
				'default'            => 3,
				'devices' => [ 'widescreen','desktop', 'tablet', 'mobile' ],
				'mobile_default' => '2',
				'frontend_available' => true,
			]
		);

		$this->add_responsive_control(
			'slidesPerGroup',
			[
				'label'              => esc_html__( 'Item Scroll', 'eduma' ),
				'type'               => Controls_Manager::NUMBER,
				'min'                => 1,
				'max'                => 20,
				'step'               => 1,
				'default'            => 3,
				'devices' => [ 'widescreen','desktop', 'tablet', 'mobile' ],
				'frontend_available' => true,
			]
		);
		$this->add_responsive_control(
			'spaceBetween',
			[
				'label'              => esc_html__( 'Item Space', 'eduma' ),
				'type'               => Controls_Manager::NUMBER,
				'min'                => 0,
				'max'                => 100,
				'step'               => 1,
				'default'            => 30,
				'devices' => [ 'widescreen','desktop', 'tablet', 'mobile' ],
				'mobile_default' => '15',
				'frontend_available' => true
			]
		);
		$this->add_control(
			'slider_speed',
			[
				'label'              => esc_html__( 'Speed', 'eduma' ),
				'type'               => Controls_Manager::NUMBER,
				'min'                => 1,
				'max'                => 10000,
				'step'               => 1,
				'default'            => 1000,
				'frontend_available' => true
			]
		);

		$this->add_control(
			'slider_autoplay',
			[
				'label'              => esc_html__( 'Autoplay', 'eduma' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'eduma' ),
				'label_off'          => esc_html__( 'No', 'eduma' ),
				'return_value'       => 'yes',
				'default'            => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'pause_on_interaction',
			[
				'label'              => esc_html__( 'Pause on Interaction', 'eduma' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'eduma' ),
				'label_off'          => esc_html__( 'No', 'eduma' ),
				'return_value'       => 'yes',
				'default'            => 'yes',
				'frontend_available' => true,
				'condition'          => [
					'slider_autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'pause_on_hover',
			[
				'label'              => esc_html__( 'Pause on Hover', 'elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => 'yes',
				'label_on'           => esc_html__( 'Yes', 'eduma' ),
				'label_off'          => esc_html__( 'No', 'eduma' ),
				'return_value'       => 'yes',
				'frontend_available' => true,
				'condition'          => [
					'slider_autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'slider_show_arrow',
			[
				'label'              => esc_html__( 'Show Arrow', 'eduma' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'eduma' ),
				'label_off'          => esc_html__( 'No', 'eduma' ),
				'return_value'       => 'yes',
				'default'            => '',
				'frontend_available' => true,
			]
		);
		$this->add_control(
			'slider_show_pagination',
			[
				'label'              => esc_html__( 'Pagination Options', 'eduma' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'none',
				'options'            => array(
					'none'        => esc_html__( 'Hide', 'eduma' ),
					'bullets'     => esc_html__( 'Bullets', 'eduma' ),
					'number'      => esc_html__( 'Number', 'eduma' ),
					'progressbar' => esc_html__( 'Progress', 'eduma' ),
					'scrollbar'   => esc_html__( 'Scrollbar', 'eduma' ),
					'fraction'    => esc_html__( 'Fraction', 'eduma' ),
				),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'slider_loop',
			[
				'label'              => esc_html__( 'Enable Loop?', 'eduma' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'eduma' ),
				'label_off'          => esc_html__( 'No', 'eduma' ),
				'return_value'       => 'yes',
				'default'            => '',
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();

	}

	protected function _register_setting_slider_dot_style() {
		// dot style
		$this->start_controls_section(
			'slider_dot_tab',
			[
				'label'     => esc_html__( 'Pagination', 'eduma' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'slider_show_pagination!' => 'none'
				]
			]
		);

		$this->add_control(
			'slider_pagination_offset_position_v',
			array(
				'label'       => esc_html__( 'Vertical Position', 'eduma' ),
				'type'        => Controls_Manager::CHOOSE,
				'toggle'      => false,
				'default'     => '100',
				'options'     => array(
					'0'   => array(
						'title' => esc_html__( 'Top', 'eduma' ),
						'icon'  => 'eicon-v-align-top',
					),
					'100' => array(
						'title' => esc_html__( 'Bottom', 'eduma' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'render_type' => 'ui',
				'selectors'   => [
					'{{WRAPPER}} .thim-slider-pagination' => 'top:{{VALUE}}%;',
				],
			)
		);
		$this->add_responsive_control(
			'slider_pagination_vertical_offset',
			array(
				'label'       => esc_html__( 'Vertical align', 'eduma' ),
				'type'        => Controls_Manager::NUMBER,
				'label_block' => false,
				'min'         => - 500,
				'max'         => 500,
				'step'        => 1,
				'selectors'   => array(
					'{{WRAPPER}} .thim-slider-pagination' => '-webkit-transform: translateY({{VALUE}}px); -ms-transform: translateY({{SIZE}}px); transform: translateY({{SIZE}}px);',
				),
			)
		);

		$this->add_responsive_control(
			'slider_dot_spacing',
			[
				'label'      => esc_html__( 'Spacing', 'eduma' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 12,
				],
				'condition'  => [
					'slider_show_pagination' => [ 'bullets', 'number' ]
				],
				'selectors'  => [
					'{{WRAPPER}} .thim-slider-pagination' => '--thim-pagination-space: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'pagination_number_typography',
				'condition' => [
					'slider_show_pagination' => 'number'
				],
				'selector'  => '{{WRAPPER}} .thim-number .swiper-pagination-bullet',
			)
		);

		$this->add_responsive_control(
			'pagination_number_padding',
			array(
				'label'      => esc_html__( 'Padding', 'eduma' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'condition'  => [
					'slider_show_pagination' => 'number'
				],
				'selectors'  => array(
					'{{WRAPPER}} .thim-number .swiper-pagination-bullet' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),

			)
		);

		$this->add_responsive_control(
			'slider_dot_border_radius',
			[
				'label'      => esc_html__( 'Border radius', 'eduma' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'condition'  => [
					'slider_show_pagination' => [ 'bullets', 'number' ]
				],
				'selectors'  => [
					'{{WRAPPER}} .thim-slider-pagination .swiper-pagination-bullet' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'slider_dot_active_border',
			array(
				'label'     => esc_html_x( 'Border Type', 'Border Control', 'eduma' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'none'   => esc_html__( 'None', 'eduma' ),
					'solid'  => esc_html_x( 'Solid', 'Border Control', 'eduma' ),
					'double' => esc_html_x( 'Double', 'Border Control', 'eduma' ),
					'dotted' => esc_html_x( 'Dotted', 'Border Control', 'eduma' ),
					'dashed' => esc_html_x( 'Dashed', 'Border Control', 'eduma' ),
					'groove' => esc_html_x( 'Groove', 'Border Control', 'eduma' ),
				),
				'condition' => [
					'slider_show_pagination' => [ 'bullets', 'number' ]
				],
				'default'   => 'none',
				'selectors' => array(
					'{{WRAPPER}} .thim-slider-pagination .swiper-pagination-bullet' => 'border-style: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'slider_dot_active_border_dimensions',
			array(
				'label'     => esc_html_x( 'Width', 'Border Control', 'eduma' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'condition' => array(
					'slider_dot_active_border!' => 'none',
				),
				'selectors' => array(
					'{{WRAPPER}} .thim-slider-pagination .swiper-pagination-bullet' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs(
			'dot_setting_tab',
			[
				'condition' => [
					'slider_show_pagination' => [ 'bullets', 'number', 'progressbar', 'scrollbar' ]
				]
			]
		);

		$this->start_controls_tab(
			'dot_slider_style',
			array(
				'label' => esc_html__( 'Default', 'eduma' ),
			)
		);

		$this->add_responsive_control(
			'slider_dot_width',
			[
				'label'      => esc_html__( 'Width', 'eduma' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 6,
				],
				'selectors'  => [
					'{{WRAPPER}} .thim-bullets .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'slider_show_pagination' => 'bullets'
				]
			]
		);

		$this->add_responsive_control(
			'slider_dot_height',
			[
				'label'      => esc_html__( 'Height', 'eduma' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 6,
				],
				'condition'  => [
					'slider_show_pagination' => [ 'bullets', 'progressbar', 'scrollbar' ]
				],
				'selectors'  => [
					'{{WRAPPER}} .thim-bullets .swiper-pagination-bullet'       => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .thim-progressbar,{{WRAPPER}} .thim-scrollbar' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'slider_dot_background',
			array(
				'label'     => esc_html__( 'Background Color', 'eduma' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .thim-slider-pagination .swiper-pagination-bullet'          => 'background-color: {{VALUE}}; opacity: 1;',
					'{{WRAPPER}} .swiper-pagination-progressbar,{{WRAPPER}} .thim-scrollbar' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'slider_pagination_number',
			array(
				'label'     => esc_html__( 'Color', 'eduma' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'slider_show_pagination' => 'number'
				],
				'selectors' => array(
					'{{WRAPPER}} .thim-number .swiper-pagination-bullet' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'slider_pagination_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'eduma' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'slider_dot_active_border!' => 'none',
				],
				'selectors' => array(
					'{{WRAPPER}} .thim-slider-pagination .swiper-pagination-bullet' => 'border-color: {{VALUE}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'slider_pagination_border_box_shadow_normal',
				'label'     => esc_html__( 'Box Shadow', 'eduma' ),
				'selector'  => '{{WRAPPER}} .thim-slider-pagination .swiper-pagination-bullet',
				'condition' => [
					'slider_show_pagination' => [ 'bullets', 'number' ]
				],
			]
		);
		//		$this->add_group_control(
		//			Group_Control_Border::get_type(),
		//			[
		//				'name'      => 'slider_dot_border',
		//				'label'     => esc_html__( 'Border', 'eduma' ),
		//				'condition'  => [
		//					'slider_show_pagination' => [ 'bullets', 'number' ]
		//				],
		//				'selector'  => '{{WRAPPER}} .thim-slider-pagination .swiper-pagination-bullet',
		//			]
		//		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'dot_slider_active_style',
			array(
				'label' => esc_html__( 'Active', 'eduma' ),
			)
		);

		$this->add_responsive_control(
			'slider_dot_active_width',
			[
				'label'      => esc_html__( 'Width', 'eduma' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 8,
				],
				'condition'  => [
					'slider_show_pagination' => 'bullets'
				],
				'selectors'  => [
					'{{WRAPPER}} .thim-bullets .swiper-pagination-bullet.swiper-pagination-bullet-active' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'slider_dot_active_height',
			[
				'label'      => esc_html__( 'Height', 'eduma' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 8,
				],
				'condition'  => [
					'slider_show_pagination' => 'bullets'
				],
				'selectors'  => [
					'{{WRAPPER}} .thim-bullets .swiper-pagination-bullet.swiper-pagination-bullet-active' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'slider_dot_active_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'eduma' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .thim-slider-pagination .swiper-pagination-bullet:hover,{{WRAPPER}} .thim-slider-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .swiper-pagination-progressbar .swiper-pagination-progressbar-fill,{{WRAPPER}} .thim-scrollbar .swiper-scrollbar-drag'                                 => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'slider_pagination_number_active',
			array(
				'label'     => esc_html__( 'Color', 'eduma' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'slider_show_pagination' => 'number'
				],
				'selectors' => array(
					'{{WRAPPER}} .thim-number .swiper-pagination-bullet:hover,{{WRAPPER}} .thim-number .swiper-pagination-bullet.swiper-pagination-bullet-active' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'slider_dot_active_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'eduma' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'slider_dot_active_border!' => 'none',
				],
				'selectors' => array(
					'{{WRAPPER}} .thim-slider-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active,{{WRAPPER}} .thim-slider-pagination .swiper-pagination-bullet:hover' => 'border-color: {{VALUE}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'slider_pagination_border_box_shadow_active',
				'label'     => esc_html__( 'Box Shadow', 'eduma' ),
				'selector'  => '{{WRAPPER}} .thim-slider-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active,{{WRAPPER}} .thim-slider-pagination .swiper-pagination-bullet:hover',
				'condition' => [
					'slider_show_pagination' => [ 'bullets', 'number' ]
				],
			]
		);
		//		$this->add_group_control(
		//			Group_Control_Border::get_type(),
		//			[
		//				'name'      => 'slider_dot_active_border',
		//				'label'     => esc_html__( 'Border', 'eduma' ),
		//				'condition' => [
		//					'slider_show_pagination' => [ 'bullets', 'number' ]
		//				],
		//				'selector'  => '{{WRAPPER}} .thim-slider-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active,{{WRAPPER}} .thim-slider-pagination .swiper-pagination-bullet:hover',
		//			]
		//		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

	}

	protected function _register_setting_slider_nav_style() {
		$this->start_controls_section(
			'slider_nav_style_tab',
			[
				'label'     => esc_html__( 'Nav', 'eduma' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'slider_show_arrow' => 'yes'
				]
			]
		);


		$this->start_controls_tabs(
			'slider_nav_group_tabs'
		);

		$this->start_controls_tab(
			'slider_nav_prev_tab',
			[
				'label' => esc_html__( 'Prev', 'eduma' ),
			]
		);
		$this->add_control(
			'slider_arrows_left',
			[
				'label'       => esc_html__( 'Prev Arrow Icon', 'eduma' ),
				'type'        => Controls_Manager::ICONS,
				'skin'        => 'inline',
				'label_block' => false,
				'default'     => [
					'value'   => 'fas fa-arrow-left',
					'library' => 'Font Awesome 5 Free',
				]
			]
		);

		$this->add_control(
			'prev_offset_orientation_h',
			array(
				'label'       => esc_html__( 'Horizontal Orientation', 'eduma' ),
				'type'        => Controls_Manager::CHOOSE,
				'toggle'      => false,
				'default'     => 'left',
				'options'     => array(
					'left'  => array(
						'title' => esc_html__( 'Left', 'eduma' ),
						'icon'  => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'eduma' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'render_type' => 'ui',
			)
		);
		$this->add_responsive_control(
			'prev_indicator_offset_h',
			array(
				'label'       => esc_html__( 'Offset', 'eduma' ),
				'type'        => Controls_Manager::NUMBER,
				'label_block' => false,
				'min'         => - 100,
				'step'        => 1,
				'default'     => 10,
				'selectors'   => array(
					'{{WRAPPER}} .thim-slider-nav-prev' => '{{prev_offset_orientation_h.VALUE}}:{{VALUE}}px',
				),
			)
		);

		$this->end_controls_tab();
		$this->start_controls_tab(
			'slider_nav_next_tab',
			[
				'label' => esc_html__( 'Next', 'eduma' ),
			]
		);
		$this->add_control(
			'slider_arrows_right',
			[
				'label'       => esc_html__( 'Next Arrow Icon', 'eduma' ),
				'type'        => Controls_Manager::ICONS,
				'skin'        => 'inline',
				'label_block' => false,
				'default'     => [
					'value'   => 'fas fa-arrow-right',
					'library' => 'Font Awesome 5 Free',
				],
			]
		);

		$this->add_control(
			'next_offset_orientation_h',
			array(
				'label'       => esc_html__( 'Horizontal Orientation', 'eduma' ),
				'type'        => Controls_Manager::CHOOSE,
				'toggle'      => false,
				'default'     => 'right',
				'options'     => array(
					'left'  => array(
						'title' => esc_html__( 'Left', 'eduma' ),
						'icon'  => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'eduma' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'render_type' => 'ui',
			)
		);

		$this->add_responsive_control(
			'next_indicator_offset_h',
			array(
				'label'       => esc_html__( 'Offset', 'eduma' ),
				'type'        => Controls_Manager::NUMBER,
				'label_block' => false,
				'min'         => - 100,
				'step'        => 1,
				'default'     => 10,
				'selectors'   => array(
					'{{WRAPPER}} .thim-slider-nav-next' => '{{next_offset_orientation_h.VALUE}}:{{VALUE}}px',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();
		$this->add_control(
			'slider_nav_offset_position_v',
			array(
				'label'       => esc_html__( 'Vertical Position', 'eduma' ),
				'type'        => Controls_Manager::CHOOSE,
				'toggle'      => false,
				'default'     => '50',
				'options'     => array(
					'0'   => array(
						'title' => esc_html__( 'Top', 'eduma' ),
						'icon'  => 'eicon-v-align-top',
					),
					'50'  => array(
						'title' => esc_html__( 'Middle', 'eduma' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'100' => array(
						'title' => esc_html__( 'Bottom', 'eduma' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'render_type' => 'ui',
				'selectors'   => [
					'{{WRAPPER}} .thim-slider-nav' => 'top:{{VALUE}}%;',
				],
			)
		);
		$this->add_responsive_control(
			'slider_nav_vertical_offset',
			array(
				'label'       => esc_html__( 'Vertical align', 'eduma' ),
				'type'        => Controls_Manager::NUMBER,
				'label_block' => false,
				'min'         => - 500,
				'max'         => 500,
				'step'        => 1,
				'selectors'   => array(
					'{{WRAPPER}} .thim-slider-nav' => '-webkit-transform: translateY({{VALUE}}px); -ms-transform: translateY({{SIZE}}px); transform: translateY({{SIZE}}px);',
				),
			)
		);

		$this->add_responsive_control(
			'slider_nav_font_size',
			[
				'label'      => esc_html__( 'Font Size', 'eduma' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 36,
				],
				'selectors'  => [
					'{{WRAPPER}} .thim-slider-nav' 		=> 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .thim-slider-nav svg' 	=> 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'slider_nav_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'eduma' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .thim-slider-nav' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);

		$this->add_responsive_control(
			'slider_nav_width',
			[
				'label'      => esc_html__( 'Width', 'eduma' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 50,
				],
				'selectors'  => [
					'{{WRAPPER}} .thim-slider-nav' => 'width: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->add_responsive_control(
			'slider_nav_height',
			[
				'label'      => esc_html__( 'Height', 'eduma' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 50,
				],
				'selectors'  => [
					'{{WRAPPER}} .thim-slider-nav' => 'height: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->start_controls_tabs(
			'slider_nav_hover_normal_tabs'
		);

		$this->start_controls_tab(
			'slider_nav_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'eduma' ),
			]
		);

		$this->add_responsive_control(
			'slider_nav_color_normal',
			array(
				'label'     => esc_html__( 'Color', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => array(
					'{{WRAPPER}} .thim-slider-nav'          => 'color: {{VALUE}};fill: {{VALUE}}',
					'{{WRAPPER}} .thim-slider-nav svg path' => 'stroke: {{VALUE}};',
				),
			)
		);
		$this->add_responsive_control(
			'slider_nav_bg_color_normal',
			[
				'label'     => esc_html__( 'Background Color', 'eduma' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => [
					'{{WRAPPER}} .thim-slider-nav' => 'background-color: {{VALUE}}'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'slider_nav_box_shadow_normal',
				'label'    => esc_html__( 'Box Shadow', 'eduma' ),
				'selector' => '{{WRAPPER}} .thim-slider-nav',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'slider_nav_border_normal',
				'label'    => esc_html__( 'Border', 'eduma' ),
				'selector' => '{{WRAPPER}} .thim-slider-nav',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'slider_nav_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'eduma' ),
			]
		);

		$this->add_responsive_control(
			'slider_nav_color_hover',
			array(
				'label'     => esc_html__( 'Color', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .thim-slider-nav:hover'          => 'color: {{VALUE}};fill: {{VALUE}}',
					'{{WRAPPER}} .thim-slider-nav:hover svg path' => 'stroke: {{VALUE}};',
				),
			)
		);
		$this->add_responsive_control(
			'slider_nav_bg_color_hover',
			[
				'label'     => esc_html__( 'Background Color', 'eduma' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thim-slider-nav:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'slider_nav_box_shadow_hover',
				'label'    => esc_html__( 'Box Shadow', 'eduma' ),
				'selector' => '{{WRAPPER}} .thim-slider-nav:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'slider_nav_border_hover',
				'label'    => esc_html__( 'Border', 'eduma' ),
				'selector' => '{{WRAPPER}} .thim-slider-nav:hover',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		// Map variables between Elementor and SiteOrigin
		$instance = array(
			'layout'           => $settings['layout'],
			'limit_instructor' => $settings['limit_instructor'],
			'visible_item'     => $settings['visible_item'],
			'show_pagination'  => $settings['show_pagination'],
			'show_navigation'  => $settings ['show_navigation'],
			'thim_kits_class'  => 'thim-ekits-list-instructors',
			'auto_play'        => $settings['auto_play'],
			'panel'            => $settings['panel'],
			'columns'          => $settings['columns'],
		);
		if ( $settings['layout'] == 'slider' ) {
			$this->render_instructors( $settings );
		} else {
			thim_ekit_get_widget_template( $this->get_base(), array(
				'instance' => $instance
			), $settings['layout'] );
		}
	}

	public function render_instructors( $settings ) {
		$panel_list = $settings['panel'] ? $settings['panel'] : '';
		$class       = 'thim-ekits-list-instructors';
		$class_inner = 'thim-ekits-list-instructors__inner';
		$class_item  = 'thim-ekits-list-instructors_article';

		if ( ! empty( $panel_list ) ) {
			$swiper_class = Plugin::$instance->experiments->is_feature_active( 'e_swiper_latest' ) ? 'swiper' : 'swiper-container';
			$class        .= ' thim-ekits-sliders ' . $swiper_class;
			$class_inner  = 'swiper-wrapper';
			$class_item   .= ' swiper-slide';

			if ( $settings['slider_show_pagination'] != 'none' ) : ?>
				<div class="thim-slider-pagination <?php echo 'thim-' . $settings['slider_show_pagination']; ?>"></div>
				<?php endif; ?>
				<?php if ( $settings['slider_show_arrow'] ) : ?>
				<div class="thim-slider-nav thim-slider-nav-prev">
					<?php
					Icons_Manager::render_icon( $settings['slider_arrows_left'], [ 'aria-hidden' => 'true' ] );
					?>
				</div>
				<div class="thim-slider-nav thim-slider-nav-next">
					<?php
					Icons_Manager::render_icon( $settings['slider_arrows_right'], [ 'aria-hidden' => 'true' ] );
					?>
				</div>
			<?php endif;
			?>
			
			<div class="<?php echo esc_attr( $class ); ?>">
				<div class="<?php echo esc_attr( $class_inner ); ?>">
					<?php foreach ( $panel_list as $key => $panel ) {
						$img_id = is_array( $panel['panel_img'] ) ? $panel['panel_img']['id'] : $panel['panel_img'];

						if ( isset($panel["panel_id"]) && empty(learn_press_get_user( $panel["panel_id"])) ) {
							$panel["panel_id"] = get_current_user_id();
						}

						$instructor = learn_press_get_user( $panel["panel_id"]);
						$instructor_statistic = $instructor->get_instructor_statistic();
						$link_author = $instructor->get_url_instructor();

						echo '<div class="' . esc_attr( $class_item ) . '">';
							echo '<div class="instructor-image">';
								echo thim_get_feature_image( $img_id );
								echo thim_lp_social_user( $panel["panel_id"] );
							echo '</div>';
							echo '<div class="instructor-content">';
								echo '<h4 class="instructor-name"><a href="' . $link_author . '">' . $instructor->get_display_name() . '</a></h4>';
								echo '<div class="instructor-item">';
									echo '<div class="instructor-count-students">';
									echo sprintf(
										'<span>%d</span> %s',
										$instructor_statistic['total_student'],
										_n( 'Student', 'Students', $instructor_statistic['total_student'], 'eduma' )
									);
									echo '</div>';
									echo '<div class="instructor-count-courses">';
									echo sprintf(
										'<span>%d</span> %s',
										$instructor_statistic['published_course'],
										_n( 'Course', 'Courses', $instructor_statistic['published_course'], 'eduma' )
									);
									echo '</div>';
								echo '</div>';
								echo '<div class="instructor-view-profile"><a href="' . $link_author . '">'. esc_html__( 'View portfolio', 'eduma' ) .'</a></div>';
							echo '</div>';
						echo '</div>';
					} ?>
				</div>
            </div>
			<?php
		}
	}

}
