<?php

namespace WCBT\Elementor\Widgets;

use Elementor\Conditions;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use WCBT\Helpers\Template;
use WCBT\Models\ProductModel;
use WCBT\Elementor\ElementorWCBT;
use WCBT\Helpers\ProductFilter;
use WCBT\Elementor\Widgets\ProductFilterSelected;

class  FilterProduct extends ProductFilterSelected {
	/**
	 * @return string
	 */
	public function get_name() {
		return WCBT_PREFIX . '-filterproduct';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return WCBT_EL_PREFIX . ' Product Filter';
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-filter';
	}

	/**
	 * @return string[]
	 */
	public function get_categories() {
		return [ 'wcbt-category' ];
	}

	protected function register_controls() {
		$this->_register_control_filter_area();
		$this->_register_control_extral_option();
		$this->_resgister_style_filter_field_item();
		$this->_resgister_style_filter_field_heading();
		$this->_resgister_style_filter_field_label();
		$this->_resgister_style_filter_field_checkbox();
		$this->_resgister_style_filter_field_color();
		$this->_resgister_style_filter_field_ranger();
		$this->_resgister_style_filter_field_ratting();
		$this->_register_option_counter_styles();
		$this->_resgister_style_filter_button_toggle();
		$this->_resgister_style_filter_form_toggle();
		$this->_resgister_style_filter_field_selected_list_chil();
		$this->_resgister_style_filter_field_button_reset_chil();
	}

	protected function _register_control_filter_area() {
		$this->start_controls_section(
			'section_filter_area',
			array(
				'label' => esc_html__( 'Filter Area', 'wcbt' ),
			)
		);
		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'filter_type',
			array(
				'label'   => esc_html__( 'Select Type', 'wcbt' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'rating',
				'options' => array(
					// 'title'				=> esc_html__('Title', 'wcbt'),
					'availability' => esc_html__( 'Availability', 'wcbt' ),
					'category'     => esc_html__( 'Categories', 'wcbt' ),
					'attributes'   => esc_html__( 'Attributes', 'wcbt' ),
					'price'        => esc_html__( 'Price', 'wcbt' ),
					// 'vendor' 			=> esc_html__('Vendor', 'wcbt'),
					'rating'       => esc_html__( 'Rating', 'wcbt' ),
				),
			)
		);

		$repeater->add_control(
			'filter_type_attribute',
			array(
				'label'     => esc_html__( 'Select Attribute', 'wcbt' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => $this->_get_all_attribute_product(),
				'condition' => array(
					'filter_type' => 'attributes',
				),
			)
		);
		$repeater->add_control(
			'number_item',
			[
				'label'   => esc_html__( 'Item Number', 'wcbt' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 0,
				'max'     => 50,
				'step'    => 1,
				'default' => 10,
				'condition' => array(
					'filter_type' => ['attributes','category'],
				),
			]
		);
		$repeater->add_control(
			'show_count',
			[
				'label'        => esc_html__( 'Show Count', 'wcbt' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'wcbt' ),
				'label_off'    => esc_html__( 'Hide', 'wcbt' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'condition' => array(
					'filter_type!' => ['availability','price'],
				),
			]
		);
		$repeater->add_control(
			'price_popover_toggle',
			[
				'type'         => \Elementor\Controls_Manager::POPOVER_TOGGLE,
				'label'        => esc_html__( 'Price setting', 'wcbt' ),
				'label_off'    => esc_html__( 'Default', 'wcbt' ),
				'label_on'     => esc_html__( 'Custom', 'wcbt' ),
				'return_value' => 'yes',
				'condition'    => [
					'filter_type' => 'price',
				],
			]
		);

		$repeater->start_popover();
		$repeater->add_control(
			'max_price',
			[
				'label'   => esc_html__( 'Max Price', 'wcbt' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 0,
				'max'     => 1000,
				'step'    => 5,
				'default' => 200,
			]
		);
		$repeater->add_control(
			'min_price',
			[
				'label'   => esc_html__( 'Min Price', 'wcbt' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 0,
				'max'     => 1000,
				'step'    => 5,
				'default' => 0,
			]
		);
		$repeater->add_control(
			'step_price',
			[
				'label'   => esc_html__( 'Step Price', 'wcbt' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 0,
				'max'     => 100,
				'step'    => 5,
				'default' => 10,
			]
		);
		$repeater->end_popover();
		$repeater->add_control(
			'heading_popover_toggle',
			[
				'type'         => \Elementor\Controls_Manager::POPOVER_TOGGLE,
				'label'        => esc_html__( 'Heading setting', 'wcbt' ),
				'label_off'    => esc_html__( 'Default', 'wcbt' ),
				'label_on'     => esc_html__( 'Custom', 'wcbt' ),
				'return_value' => 'yes',
			]
		);

		$repeater->start_popover();
		$repeater->add_control(
			'show_title',
			[
				'label'        => esc_html__( 'Show Title', 'wcbt' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'wcbt' ),
				'label_off'    => esc_html__( 'Hide', 'wcbt' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);
		$repeater->add_control(
			'show_toggle',
			array(
				'label'     => esc_html__( 'Show Toggle', 'wcbt' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'none',
				'options'   => array(
					'none' => esc_html__( 'None', 'wcbt' ),
					'show' => esc_html__( 'Show', 'wcbt' ),
					'always_show' => esc_html__( 'Always Show', 'wcbt' ),
					'dropdown' => esc_html__( 'Dropdown', 'wcbt' ),
				),
				'condition'    => array(
					'show_title' => 'yes',
				),
			)
		);
		$repeater->end_popover();
		$repeater->add_responsive_control(
			'filter_item_width',
			array(
				'label'      => esc_html__( 'Width Content(%)', 'wcbt' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 100,
				'step' => 1,
				'default' => 100,
				'selectors' => array(
					'{{WRAPPER}} .wrapper-search-fields {{CURRENT_ITEM}}' => 'flex-basis: {{VALUE}}%;',
				),
			)
		);
		$this->add_control(
			'filter_list',
			[
				'label'       => esc_html__( 'Repeater Field filter', 'wcbt' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'filter_type' => array( 'rating' ),
					],
				],
				'title_field' => '<span style="text-transform: capitalize;">{{{ filter_type}}}</span>',
			]
		);

		$this->end_controls_section();
	}

	protected function _register_control_extral_option() {
		$this->start_controls_section(
			'section__extral_option',
			array(
				'label' => esc_html__( 'Extral Option ', 'wcbt' ),
			)
		);
		$this->add_control(
			'filter_button_popover_toggle',
			[
				'type'         => \Elementor\Controls_Manager::POPOVER_TOGGLE,
				'label'        => esc_html__( 'Filter Toggle Button', 'wcbt' ),
				'label_off'    => esc_html__( 'Default', 'wcbt' ),
				'label_on'     => esc_html__( 'Custom', 'wcbt' ),
				'return_value' => 'yes',
			]
		);
		$this->start_popover();
		$this->add_control(
			'show_filter_button_toggle',
			array(
				'label'     => esc_html__( 'Show Toggle', 'wcbt' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'none',
				'options'   => array(
					'none' => esc_html__( 'None', 'wcbt' ),
					'show' => esc_html__( 'Show', 'wcbt' ),
					'show_mobile' => esc_html__( 'Show Only Mobile', 'wcbt' ),
				),
			)
		);
		$this->add_control(
			'filter_button_toggle_title',
			[
				'label'       => esc_html__( 'Title', 'wcbt' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Filter', 'wcbt' ),
				'placeholder' => esc_html__( 'Type your title here', 'wcbt' ),
				'condition'   => array(
					'show_filter_button_toggle!' => 'none',
				),
			]
		);
		$this->add_control(
			'show_icon_filter_button_toggle',
			[
				'label'        => esc_html__( 'Show icon', 'wcbt' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'wcbt' ),
				'label_off'    => esc_html__( 'Hide', 'wcbt' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'condition'    => array(
					'show_filter_button_toggle!' => 'none',
				),
			]
		);
		$this->add_control(
			'filter_button_toggle_icon',
			[
				'label'                  => esc_html__( 'Icon', 'wcbt' ),
				'type'                   => Controls_Manager::ICONS,
				'fa4compatibility'       => 'icon',
				'skin'                   => 'inline',
				'label_block'            => false,
				'skin_settings'          => [
					'inline' => [
						'icon' => [
							'icon' => 'eicon-filter',
						],
					],
				],
				'default'                => [
					'value'   => 'eicon-filter',
					'library' => 'eicons',
				],
				'separator'              => 'after',
				'exclude_inline_options' => [ 'none' ],
				'condition'              => [
					'show_icon_filter_button_toggle' => 'yes',
					'show_filter_button_toggle!' => 'none',
				],
			]
		);
		$this->add_control(
			'filter_button_toggle_count',
			[
				'label'        => esc_html__( 'Show Count', 'wcbt' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'wcbt' ),
				'label_off'    => esc_html__( 'Hide', 'wcbt' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'condition'    => array(
					'show_filter_button_toggle!' => 'none',
				),
			]
		);
		$this->end_popover();
		$this->add_control(
			'show_selected_list',
			[
				'label'        => esc_html__( 'Show Selected list', 'wcbt' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'wcbt' ),
				'label_off'    => esc_html__( 'Hide', 'wcbt' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);
		$this->end_controls_section();
	}
	protected function _resgister_style_filter_field_item() {
		$this->start_controls_section(
			'section_filter_field_item_style',
			array(
				'label' => esc_html__( 'Item', 'wcbt' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_responsive_control(
			'filter_item_space_bottom',
			array(
				'label'      => esc_html__( 'Space Bottom(px)', 'wcbt' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 100,
				'step' => 5,
				'selectors'  => array(
					'body {{WRAPPER}} .wrapper .wrapper-content'  => 'margin-bottom: {{VALUE}}px;',
					'body {{WRAPPER}} .wrapper:last-child .wrapper-content' => 'margin-bottom:0;',
				),
			)
		);
		$this->add_responsive_control(
			'filter_item_space_right',
			array(
				'label'      => esc_html__( 'Space Right(px)', 'wcbt' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 100,
				'step' => 5,
				'selectors'  => array(
					'body {{WRAPPER}} .wrapper' => 'margin-right: {{VALUE}}px;',
					'body {{WRAPPER}} .wrapper:last-child' => 'margin-right:0;',
				),
			)
		);
		$this->add_control(
			'filter_item_toggle_dropdown_heading',
			[
				'label'     => esc_html__( 'Style Toggle Dropdown', 'wcbt' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);
		$this->add_responsive_control(
			'filter_item__offset_h',
			array(
				'label'       => esc_html__( 'Offset', 'wcbt' ),
				'type'        => Controls_Manager::NUMBER,
				'label_block' => false,
				'min'         => - 200,
				'step'        => 1,
				'selectors'   => array(
					'{{WRAPPER}}' => '--wcbt-toggle-item-offset:{{VALUE}}px',
				),
			)
		);
		$this->add_responsive_control(
			'filter_item_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wcbt' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),

				'selectors' => array(
					'{{WRAPPER}} .filter-item-dropdown .wrapper-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'filter_item__border',
				// 'exclude'  => array('color'),
				'selector' => '{{WRAPPER}} .filter-item-dropdown .wrapper-content' ,
			)
		);
		$this->add_control(
			'filter_item__border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wcbt' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .filter-item-dropdown .wrapper-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'filter_item_box_shadow',
				'selector' => '{{WRAPPER}} .filter-item-dropdown .wrapper-content',
			]
		);
		$this->end_controls_section();
	}
	protected function _resgister_style_filter_field_heading() {
		$this->start_controls_section(
			'section_filter_field_heading_style',
			array(
				'label' => esc_html__( 'Title', 'wcbt' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'item_filter_heading_typography',
				'label'    => esc_html__( 'Typography', 'wcbt' ),
				'selector' => '{{WRAPPER}} .item-filter-heading',
			]
		);
		$this->_resgister_option_general_style_filter_fields( 'item_filter_heading', '.item-filter-heading' );
		$this->add_control(
			'filter_icon_heading',
			[
				'label'     => esc_html__( 'Icon', 'wcbt' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);
		$this->add_responsive_control(
			'filter_icon_title_width',
			array(
				'label'      => esc_html__( 'Icon size(px)', 'wcbt' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 100,
				'step' => 1,
				'selectors'  => array(
					'body {{WRAPPER}} .item-filter-heading:after' => 'font-size: {{VALUE}}px;',
				),
			)
		);
		$this->start_controls_tabs( 'tabs_filter_icon_title_style' );
		$this->start_controls_tab(
			'tab_filter_icon_title_normal',
			array(
				'label' => esc_html__( 'Normal', 'wcbt' ),
			)
		);
		$this->add_control(
			'filter_title_color',
			array(
				'label'     => esc_html__( 'Color', 'wcbt' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .item-filter-heading' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'filter_title_bgcolor',
			array(
				'label'     => esc_html__( 'Background', 'wcbt' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .item-filter-heading' => 'background: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'filter_icon_title_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'wcbt' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .wrapper .item-filter-heading:after' => 'color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_filter_icon_title_active',
			array(
				'label' => esc_html__( 'Active', 'wcbt' ),
			)
		);
		$this->add_control(
			'filter_title_color_active',
			array(
				'label'     => esc_html__( 'Color', 'wcbt' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .open .item-filter-heading,{{WRAPPER}} .item-filter-heading:hover' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'filter_title_bgcolor_active',
			array(
				'label'     => esc_html__( 'Background', 'wcbt' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .open .item-filter-heading,{{WRAPPER}} .item-filter-heading:hover' => 'background: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'filter_icon_title_color_active',
			array(
				'label'     => esc_html__( 'Icon Color', 'wcbt' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .open .item-filter-heading:after,{{WRAPPER}} .item-filter-heading:after' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
	}

	protected function _resgister_style_filter_field_label() {
		$this->start_controls_section(
			'section_filter_field_label_style',
			array(
				'label' => esc_html__( 'Label', 'wcbt' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'filter_field_label_typography',
				'label'    => esc_html__( 'Typography', 'wcbt' ),
				'selector' => '{{WRAPPER}} .item label',
			]
		);
		$this->add_responsive_control(
			'filter_field_label_space',
			array(
				'label'      => esc_html__( 'Space(px)', 'wcbt' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 1000,
				'step' => 1,
				'selectors'  => array(
					'body {{WRAPPER}} .item,{{WRAPPER}}  .wrapper-content li' => 'margin-bottom: {{VALUE}}px;',
					'body {{WRAPPER}} .item:last-child,{{WRAPPER}}  .wrapper-content li:last-child' => 'margin-bottom:0;',
				),
			)
		);
		$this->add_responsive_control(
			'filter_field_label_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wcbt' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors' => array(
					'body {{WRAPPER}} .item label,{{WRAPPER}}  .wrapper-content li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->start_controls_tabs( 'tabs_filter_field_label_style' );
		$this->start_controls_tab(
			'tab_filter_field_label_normal',
			array(
				'label' => esc_html__( 'Normal', 'wcbt' ),
			)
		);
		$this->add_control(
			'filter_field_label_color',
			array(
				'label'     => esc_html__( 'Color', 'wcbt' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .item label' => 'color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_filter_field_label_active',
			array(
				'label' => esc_html__( 'Active', 'wcbt' ),
			)
		);
		$this->add_control(
			'filter_field_label_active',
			array(
				'label'     => esc_html__( 'Color', 'wcbt' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .item input:checked+label,{{WRAPPER}} .item label:hover' => 'color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
	}

	protected function _resgister_style_filter_field_checkbox() {
		$this->start_controls_section(
			'section_filter_field_checkbox_style',
			array(
				'label' => esc_html__( 'Checkbox', 'wcbt' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_responsive_control(
			'filter_field_checkbox_size',
			array(
				'label'      => esc_html__( 'Box size', 'wcbt' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'unit' => 'px',
				),
				'range'      => array(
					'em' => array(
						'min'  => 0,
						'max'  => 4,
						'step' => 0.1,
					),
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .item input,{{WRAPPER}} .wrapper-content li input' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};min-width: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'filter_field_checkbox_space',
			array(
				'label'      => esc_html__( 'Offset Right', 'wcbt' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'unit' => 'px',
				),
				'range'      => array(
					'em' => array(
						'min'  => 0,
						'max'  => 4,
						'step' => 0.1,
					),
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .item input,{{WRAPPER}} .wrapper-content li input' => 'margin-right: {{SIZE}}{{UNIT}}',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'filter_field_checkbox_box_border',
				'exclude'  => array( 'color' ),
				'selector' => '{{WRAPPER}} .item input,{{WRAPPER}} .wrapper-content li input',
			)
		);
		$this->add_control(
			'filter_field_checkbox_box__border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wcbt' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .item input,{{WRAPPER}} .wrapper-content li input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'filter_field_checkbox_checked_size',
			array(
				'label'      => esc_html__( 'Checked Size', 'wcbt' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'unit' => 'px',
				),
				'range'      => array(
					'em' => array(
						'min'  => 0,
						'max'  => 4,
						'step' => 0.1,
					),
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .item input:after,{{WRAPPER}} .wrapper-content li input:after' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->start_controls_tabs( 'tabs_filter_field_checkbox_style' );
		$this->start_controls_tab(
			'tab_filter_field_checkbox_normal',
			array(
				'label' => esc_html__( 'Normal', 'wcbt' ),
			)
		);
		$this->add_control(
			'filter_field_checkbox_checked_color',
			array(
				'label'     => esc_html__( 'Color', 'wcbt' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .item input::after,{{WRAPPER}} .wrapper-content li input::after' => 'border-color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'filter_field_checkbox_background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wcbt' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}  .item input,{{WRAPPER}} .wrapper-content li input' => 'background-color: {{VALUE}};outline: 0;',
				),
			)
		);

		$this->add_control(
			'filter_field_checkbox_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'wcbt' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'filter_field_checkbox_box_border_border!' => [ 'none', '' ],
				),
				'selectors' => array(
					'{{WRAPPER}}  .item input,{{WRAPPER}} .wrapper-content li input' => 'border-color: {{VALUE}};outline: 0;',
				),
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_filter_field_checkbox_focus',
			array(
				'label' => esc_html__( 'Active', 'wcbt' ),
			)
		);
		$this->add_control(
			'filter_field_checkbox_fosus_background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wcbt' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .item input:focus,{{WRAPPER}} .item input:checked' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .wrapper-content li input:focus,{{WRAPPER}} .wrapper-content li input:checked' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .item:hover input,{{WRAPPER}} .wrapper-content li:hover input' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'filter_field_checkbox_fosus_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'wcbt' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'filter_field_checkbox_box_border_border!' => [ 'none', '' ],
				),
				'selectors' => array(
					'{{WRAPPER}} .item input:focus,{{WRAPPER}} .item input:checked' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .wrapper-content li input:focus,{{WRAPPER}} .wrapper-content li input:checked' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .item:hover input,{{WRAPPER}} .wrapper-content li:hover input' => 'border-color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
	}

	protected function _resgister_style_filter_field_color() {
		$this->start_controls_section(
			'section_filter_field_color_style',
			array(
				'label' => esc_html__( 'Color', 'wcbt' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_responsive_control(
			'filter_field_color_width',
			array(
				'label'      => esc_html__( 'Width', 'wcbt' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'unit' => 'px',
				),
				'range'      => array(
					'em' => array(
						'min'  => 0,
						'max'  => 4,
						'step' => 0.1,
					),
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .att-type-bgcolor' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'filter_field_color_height',
			array(
				'label'      => esc_html__( 'Height', 'wcbt' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'unit' => 'px',
				),
				'range'      => array(
					'em' => array(
						'min'  => 0,
						'max'  => 4,
						'step' => 0.1,
					),
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .att-type-bgcolor' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'filter_field_color_space',
			array(
				'label'      => esc_html__( 'Offset Right', 'wcbt' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'unit' => 'px',
				),
				'range'      => array(
					'em' => array(
						'min'  => 0,
						'max'  => 4,
						'step' => 0.1,
					),
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .att-type-bgcolor' => 'margin-right: {{SIZE}}{{UNIT}}',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     =>'filter_field_color__border',
				'exclude'  => array('color'),
				'selector' => '{{WRAPPER}} .att-type-bgcolor',
			)
		);
		$this->start_controls_tabs( 'tabs_filter_field_color_style' ,array(
			'condition' => array(
				'filter_field_color__border_border!' => [ 'none', '' ],
			),
		));
		$this->start_controls_tab(
			'tab_filter_field_color_normal',
			array(
				'label' => esc_html__( 'Normal', 'wcbt' ),
			)
		);
		$this->add_control(
			'filter_field_color_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'wcbt' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'filter_field_color__border_border!' => [ 'none', '' ],
				),
				'selectors' => array(
					'{{WRAPPER}}  .att-type-bgcolor' => 'border-color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_filter_field_color_hover',
			array(
				'label' => esc_html__( 'Hover', 'wcbt' ),
			)
		);
		$this->add_control(
			'filter_field_color_hover_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'wcbt' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'filter_field_color__border_border!' => [ 'none', '' ],
				),
				'selectors' => array(
					'{{WRAPPER}} .item:hover .att-type-bgcolor' => 'border-color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_filter_field_color_active',
			array(
				'label' => esc_html__( 'Active', 'wcbt' ),
			)
		);
		$this->add_control(
			'filter_field_color_active_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'wcbt' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'filter_field_color__border_border!' => [ 'none', '' ],
				),
				'selectors' => array(
					'{{WRAPPER}} .item input:checked + label .att-type-bgcolor' => 'border-color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
	}

	protected function _resgister_style_filter_field_ranger() {
		$this->start_controls_section(
			'section_filter_field_ranger_style',
			array(
				'label' => esc_html__( 'Ranger', 'wcbt' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'filter_field_ranger_line_heading',
			[
				'label'     => esc_html__( 'Line', 'wcbt' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);
		$this->add_responsive_control(
			'filter_field_ranger_line_height',
			array(
				'label'     => esc_html__( 'Height(px)', 'wcbt' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 100,
				'step' => 5,
				'selectors' => array(
					'{{WRAPPER}} .noUi-horizontal' => 'height: {{VALUE}}px',
				),
			)
		);
		$this->add_responsive_control(
			'filter_field_ranger_line_width',
			array(
				'label'     => esc_html__( 'Width(px)', 'wcbt' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 1000,
				'step' => 5,
				'selectors' => array(
					'{{WRAPPER}} .noUi-horizontal' => 'width: {{VALUE}}px',
				),
			)
		);


		$this->add_responsive_control(
			'filter_field_ranger_button_line_size',
			array(
				'label'      => esc_html__( 'Button Size', 'wcbt' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'selectors'  => array(
					'{{WRAPPER}} .noUi-horizontal .noUi-handle' => 'width: {{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_control(
			'filter_field_ranger_line_color',
			array(
				'label'     => esc_html__( 'Line Color', 'wcbt' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .noUi-target' => 'background: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'filter_field_ranger_active_line_color',
			array(
				'label'     => esc_html__( 'Active Line Color', 'wcbt' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .noUi-connect' => 'background: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'filter_field_ranger_button_line_color',
			array(
				'label'     => esc_html__( 'Button Color', 'wcbt' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .noUi-handle' => 'background: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'filter_field_ranger_button_line_border_radius',
			array(
				'label'      => esc_html__( 'Button Radius', 'wcbt' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .noUi-horizontal .noUi-handle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'filter_field_ranger_button__margin',
			array(
				'label'      => esc_html__( 'Margin', 'wcbt' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),

				'selectors' => array(
					'{{WRAPPER}} .noUi-horizontal'=> 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
	}

	protected function _resgister_style_filter_field_ratting() {
		$this->start_controls_section(
			'section_filter_field_ratting_style',
			array(
				'label' => esc_html__( 'Ratting', 'wcbt' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'filter_field_ratting_star_heading',
			[
				'label'     => esc_html__( 'Star', 'wcbt' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);
		$this->add_control(
			'filter_field_ratting_star_color',
			array(
				'label'     => esc_html__( 'Star Color', 'wcbt' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .star i' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_control(
			'filter_field_ratting_star_size',
			array(
				'label'      => esc_html__( 'Star Size', 'wcbt' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'selectors'  => array(
					'{{WRAPPER}} .star i' => 'font-size: {{SIZE}}{{UNIT}}',
				),
			)
		);
		$this->add_control(
			'filter_field_ratting_space_between',
			array(
				'label'      => esc_html__( 'Space Between', 'wcbt' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'unit' => 'px',
				),
				'range'      => array(
					'em' => array(
						'min'  => 0,
						'max'  => 4,
						'step' => 0.1,
					),
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .star i' => 'margin-right: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .star i:last-child' => 'margin-right:0;',
				),
			)
		);
		$this->end_controls_section();
	}

	protected function _resgister_style_filter_button_toggle() {
		$this->start_controls_section(
			'section_filter_button_toggle_style',
			array(
				'label'     => esc_html__( 'Button Toggle', 'wcbt' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_filter_button_toggle!' => 'none',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'filter_button_toggle_typography',
				'label'    => esc_html__( 'Typography', 'wcbt' ),
				'selector' => '{{WRAPPER}} .filter-button-toggle-wp',
			]
		);
		$this->_resgister_option_general_style_filter_fields( 'filter_button_toggle', '.filter-button-toggle-wp' );
		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'filter_button_toggle_box_shadow',
				'selector' => '{{WRAPPER}} .filter-button-toggle-wp',
			]
		);
		$this->add_control(
			'filter_button_toggle_icon_heading',
			[
				'label'     => esc_html__( 'Icon', 'wcbt' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);
		$this->add_responsive_control(
			'filter_button_toggle_icon_width',
			array(
				'label'      => esc_html__( 'Icon size(px)', 'wcbt' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 200,
				'step' => 1,
				'selectors'  => array(
					'body {{WRAPPER}} .filter-button-toggle-wp i' => 'font-size: {{VALUE}}px;',
				),
			)
		);
		$this->add_responsive_control(
			'filter_button_toggle_icon_space',
			array(
				'label'      => esc_html__( 'Icon Space(px)', 'wcbt' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 200,
				'step' => 1,
				'selectors'  => array(
					'body {{WRAPPER}} .filter-button-toggle-wp i' => 'margin-right: {{VALUE}}px;',
				),
			)
		);
		$this->start_controls_tabs( 'tabs_filter_button_toggle_style' );
		$this->start_controls_tab(
			'tab_filter_button_toggle_normal',
			array(
				'label' => esc_html__( 'Normal', 'wcbt' ),
			)
		);
		$this->add_control(
			'filter_button_toggle_color',
			array(
				'label'     => esc_html__( 'Color', 'wcbt' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .filter-button-toggle-wp' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'filter_button_toggle_bgcolor',
			array(
				'label'     => esc_html__( 'Background', 'wcbt' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .filter-button-toggle-wp' => 'background: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'filter_button_toggle_icon_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'wcbt' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .filter-button-toggle-wp i' => 'color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'filter_button_toggle_hover',
			array(
				'label' => esc_html__( 'Hover', 'wcbt' ),
			)
		);
		$this->add_control(
			'filter_button_toggle_color_hover',
			array(
				'label'     => esc_html__( 'Color', 'wcbt' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .filter-button-toggle-wp:hover' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'filter_button_toggle_bgcolor_hover',
			array(
				'label'     => esc_html__( 'Background', 'wcbt' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .filter-button-toggle-wp:hover' => 'background: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'filter_button_toggle_icon_color_hover',
			array(
				'label'     => esc_html__( 'Color', 'wcbt' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .filter-button-toggle-wp:hover i' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'filter_button_toggle_count_heading',
			[
				'label'     => esc_html__( 'Count', 'wcbt' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);
		$this->_register_option_field_counter_style('counter_buton_toggle_','.wctb-count-filter');
		$this->end_controls_section();
	}

	protected function _resgister_style_filter_form_toggle() {
		$this->start_controls_section(
			'section_filter_form_toggle_style',
			array(
				'label'     => esc_html__( 'Form Toggle', 'wcbt' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_filter_button_toggle!' => 'none',
				),
			)
		);
		$this->add_control(
			'filter_form_toggle_offset_orientation_h',
			array(
				'label'        => esc_html__( 'Horizontal Orientation', 'wcbt' ),
				'type'         => Controls_Manager::CHOOSE,
				'toggle'       => false,
				'default'      => 'left',
				'options'      => array(
					'left'  => array(
						'title' => esc_html__( 'Left', 'wcbt' ),
						'icon'  => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'wcbt' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'render_type'  => 'ui',
				'prefix_class' => 'wcbt-filter-form-toggle-offset-',
			)
		);
		$this->add_responsive_control(
			'filter_form_toggle_indicator_offset_h',
			array(
				'label'       => esc_html__( 'Offset(px)', 'wcbt' ),
				'type'        => Controls_Manager::NUMBER,
				'label_block' => false,
				'min'         => - 200,
				'step'        => 1,
				'selectors'   => array(
					'{{WRAPPER}}' => '--wcbt-toggle-offset:{{VALUE}}px',
				),

			)
		);
		$this->add_responsive_control(
			'filter_form_toggle_width',
			array(
				'label'      => esc_html__( 'Width Content', 'wcbt' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => [
						'min'  => 50,
						'max'  => 1000,
						'step' => 5,
					],
					'%'  => [
						'min'  => 0,
						'max'  => 100,
						'step' => 5,
					],
				),
				// 'default' => [
				// 	'unit' => '%',
				// 	'size' => 100,
				// ],
				'condition'  => [
					'show_filter_button_toggle!' => 'none',
				],
				'selectors'  => array(
					'body {{WRAPPER}} .show-filter-toggle .wrapper-search-fields' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'filter_form_toggle_max_height',
			array(
				'label'      => esc_html__( 'Max Height', 'wcbt' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px','vh' ),
				'range'      => array(
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					],
					'vh' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				),
				'default' => [
					'unit' => 'vh',
					'size' => 100,
				],
				'selectors'  => array(
					'body {{WRAPPER}} .show-filter-toggle .wrapper-search-fields' => 'max-height: {{SIZE}}{{UNIT}};overflow: auto;',
				),
				'condition'  => [
					'show_filter_button_toggle!' => 'none',
				],
			)
		);
		$this->add_control(
			'filter_form_toggle_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wcbt' ),
				'type'      => Controls_Manager::COLOR,
				'default'	=> '#fff',
				'selectors' => array(
					'{{WRAPPER}} .show-filter-toggle .wrapper-search-fields' => 'background: {{VALUE}}',
				),
			)
		);
		$this->_resgister_option_general_style_filter_fields( 'filter_form_toggle', '.show-filter-toggle .wrapper-search-fields' );
		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'filter_form_toggle_box_shadow',
				'selector' => '{{WRAPPER}} .show-filter-toggle .wrapper-search-fields',
			]
		);
		$this->end_controls_section();
	}
	protected function _register_option_counter_styles() {
		$this->start_controls_section(
			'section_filter_field_counter_items_style',
			array(
				'label'     => esc_html__( 'Counter', 'wcbt' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			)
		);
		$this->_register_option_field_counter_style('counter_items_','.product-count');
		$this->end_controls_section();
	}
	protected function _resgister_option_general_style_filter_fields( $label, $class ) {
		// $this->add_group_control(
		// 	\Elementor\Group_Control_Typography::get_type(),
		// 	[
		// 		'name'     => $label . '_typography',
		// 		'label'    => esc_html__('Typography', 'wcbt'),
		// 		'selector' => '{{WRAPPER}} ' . $class,
		// 	]
		// );
		$this->add_responsive_control(
			$label . '_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wcbt' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),

				'selectors' => array(
					'{{WRAPPER}} ' . $class => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			$label . '_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wcbt' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $class => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => $label . '_border',
				// 'exclude'  => array('color'),
				'selector' => '{{WRAPPER}} ' . $class,
			)
		);
		$this->add_control(
			$label . '_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wcbt' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $class => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
	}
	protected function _register_option_field_counter_style( $label, $class ) {
		$this->add_control(
			$label . 'filter_field_counter_items_style',
			array(
				'type'      => Controls_Manager::HEADING,
				'label'     => esc_html__( 'Counter', 'wcbt' ),
				'separator' => 'before',
			)
		);
		$this->add_control(
			$label . 'filter_field_counter_items_text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'wcbt' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $class => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_responsive_control(
			$label . 'filter_field_counter_items_font_size',
			array(
				'label'      => esc_html__( 'Font Size', 'wcbt' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'size_units' => array( '%', 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $class => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			$label . 'filter_field_counter_items_space',
			array(
				'label'      => esc_html__( 'Space', 'wcbt' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'size_units' => array( '%', 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $class => 'margin-left: {{SIZE}}{{UNIT}};',
				),
			)
		);
	}
	protected function _resgister_style_filter_field_button_reset_chil() {
		$this->start_controls_section(
			'section_filter_field_button_reset_style',
			array(
				'label' => esc_html__('Reset Button', 'wcbt'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_selected_list' => 'yes'
				),
			)
		);
		$this->resgister_option_general_style_filter_field_selected('filter_field_button_reset', '.clear', '.no-class');
		$this->resgister_option_tabs_color_general_filter_selected('filter_field_button_reset', '.clear', '.no-class');
		$this->end_controls_section();
	}
	protected function _resgister_style_filter_field_selected_list_chil() {
		$this->start_controls_section(
			'section_filter_field_selected_list_style',
			array(
				'label' => esc_html__('Selected List', 'wcbt'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_selected_list' => 'yes'
				),
			)
		);
		$this->resgister_option_general_style_filter_field_selected('filter_field_selected_list', '.list-item', '.woocommerce-Price-amount');
		$this->resgister_option_tabs_color_general_filter_selected('filter_field_selected_list', '.list-item', '.woocommerce-Price-amount');
		$this->add_control(
			'filter_field_selected_list_item_remove_heading',
			[
				'label' => esc_html__('Icon Remove', 'wcbt'),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);
		$this->add_responsive_control(
			'filter_field_selected_list_item_remove_width',
			array(
				'label'      => esc_html__('Icon size', 'wcbt'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array('px'),
				'range'      => array(
					'px' => [
						'min' => 0,
						'max' => 250,
						'step' => 5,
					],
				),
				'selectors'  => array(
					'body {{WRAPPER}} .remove svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'filter_field_selected_list_item_remove_space',
			array(
				'label'      => esc_html__('Icon Space', 'wcbt'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array('px'),
				'range'      => array(
					'px' => [
						'min' => 0,
						'max' => 250,
						'step' => 5,
					],
				),
				'selectors'  => array(
					'body {{WRAPPER}} .remove' => 'margin-left: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_control(
			'filter_field_selected_list_item_remove_color',
			array(
				'label'     => esc_html__('Color', 'wcbt'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}  .remove svg path' => 'stroke: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'filter_field_selected_list_item_remove_color_hover',
			array(
				'label'     => esc_html__('Color Hover', 'wcbt'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .list-item:hover .remove svg path' => 'stroke: {{VALUE}};',
				),
			)
		);
		$this->end_controls_section();
	}
	protected function _get_all_attribute_product() {
		$attributes           = array( '' => esc_html__( 'Select', 'wcbt' ) );
		$std_attribute        = '';
		$attribute_taxonomies = wc_get_attribute_taxonomies();

		if ( ! empty( $attribute_taxonomies ) ) {
			foreach ( $attribute_taxonomies as $tax ) {
				if ( taxonomy_exists( wc_attribute_taxonomy_name( $tax->attribute_name ) ) ) {
					$attributes['pa_' . $tax->attribute_name] = $tax->attribute_name;
				}
			}
			$std_attribute = current( $attributes );
		}

		return $attributes;
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		if ( is_array( $settings['filter_list'] ) && ! empty( $settings['filter_list'][0] ) ) {
			wp_enqueue_script( 'wcbt-product-filter' );
			wp_localize_script( 'wcbt-product-filter', 'WCBT_PRODUCT_FILTER', ProductFilter::get_data() );
			$class_toggle = "";
			if ( $settings['show_filter_button_toggle'] != 'none' ) {
				if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
					$class_button_show =  "";
				} else{
					$class_toggle      = 'show-filter-toggle';
					$class_button_show = $settings['show_filter_button_toggle'] == 'show_mobile' ? ' button-hidden-desktop ' : '';
				}
				?>
				<div class="filter-button-toggle-wp <?php echo $class_button_show; ?>">
					<?php
					if ( isset( $settings['show_icon_filter_button_toggle'] ) && $settings['show_icon_filter_button_toggle'] == 'yes' ) {
						if ( ! empty( $settings['filter_button_toggle_icon']['value'] ) ) {
							\Elementor\Icons_Manager::render_icon( $settings['filter_button_toggle_icon'], [ 'aria-hidden' => 'true' ] );
						}
					}
					if ( ! empty( $settings['filter_button_toggle_title'] ) ) {
						echo esc_html__($settings['filter_button_toggle_title'],'wcbt');
					}
					if($settings['filter_button_toggle_count'] == 'yes'){
						echo '<span class="wctb-count-filter">0</span>';
					}
					?>
				</div>
				<?php
			}
			echo '<div class="wcbt-product-filter ' . $class_toggle . '"><div class="wrapper-search-fields">';
			echo '<span class="close-filter-product"><i class="fa fa-times" aria-hidden="true"></i></span>';
			if(isset($settings['show_selected_list']) && $settings['show_selected_list'] == 'yes'){
				$this->_render_content_template();
			}
			foreach ( $settings['filter_list'] as $key => $field ) {
				$extra_class = $field['show_toggle'] != 'none' ? ' filter-title-toggle toggle-type-'.$field['show_toggle'] : '';
				$extra_class .= $field['show_toggle'] == 'always_show' ? " open" : '';
				$extra_class .= $field['show_toggle'] == 'dropdown' ? " filter-item-dropdown" : '';
				$extra_class .= ' has-btn-filter-'.$settings['show_filter_button_toggle'];
				$extra_class .= ' elementor-repeater-item-'.$field['_id'];
				switch ( $field['filter_type'] ) {
					case 'availability':
						$data = array(
							'extra_class' => $extra_class,
							'show_title'   => $field['show_title'],
						);
						Template::instance()->get_frontend_template_type_classic( 'product-filter/availability.php', compact( 'data' ) );

						break;
					case 'category':
						$data = array(
							'extra_class' => $extra_class,
							'show_title'   => $field['show_title'],
							'show_count'   => $field['show_count'],
							'category_number'	=> $field['number_item'],
						);
						Template::instance()->get_frontend_template_type_classic( 'product-filter/category.php', compact( 'data' ) );

						break;
					case 'attributes':
						$data = array(
							'extra_class' => $extra_class,
							'key'          => $field['filter_type_attribute'],
							'show_title'   => $field['show_title'],
							'show_count'   => $field['show_count'],
							'attribute_term_number' => $field['number_item'],
						);
						Template::instance()->get_frontend_template_type_classic( 'product-filter/attributes.php', compact( 'data' ) );

						break;
					case 'price':
						$data = array(
							'extra_class' => $extra_class,
							'min_price'    => $field['min_price'],
							'max_price'    => $field['max_price'],
							'step_price'   => $field['step_price'],
							'show_title'   => $field['show_title'],
						);
						Template::instance()->get_frontend_template_type_classic( 'product-filter/price.php', compact( 'data' ) );

						break;
					default:
						$data = array(
							'extra_class' => $extra_class,
							'show_title'   => $field['show_title'],
							'show_count'   => $field['show_count'],
						);
						Template::instance()->get_frontend_template_type_classic( 'product-filter/rating.php', compact( 'data' ) );
				}
			}
			echo '</div></div>';
		}
	}
}
