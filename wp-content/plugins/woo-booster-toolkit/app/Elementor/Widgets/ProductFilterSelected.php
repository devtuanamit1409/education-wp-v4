<?php

namespace WCBT\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use WCBT\Helpers\Template;
use WCBT\Models\ProductModel;
use WCBT\Elementor\ElementorWCBT;
use WCBT\Helpers\ProductFilter;
use WCBT\Models\ProductAttributeModel;

class ProductFilterSelected extends Widget_Base {
	/**
	 * @return string
	 */
	public function get_name() {
		return WCBT_PREFIX . '-productfilterselected';
	}
	/**
	 * @return string
	 */
	public function get_title() {
		return WCBT_EL_PREFIX . ' Selected Product Filter';
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
		return ['wcbt-category'];
	}

	protected function register_controls() {
		$this->_resgister_style_filter_field_selected_list();
		$this->_resgister_style_filter_field_button_reset();
	}
	protected function _resgister_style_filter_field_button_reset() {
		// if(!isset($conditon) || $conditon == null){
		// 	return;
		// }
		$this->start_controls_section(
			'section_filter_field_button_reset_style',
			array(
				'label' => esc_html__('Reset Button', 'wcbt'),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->resgister_option_general_style_filter_field_selected('filter_field_button_reset', '.clear', '.no-class');
		$this->resgister_option_tabs_color_general_filter_selected('filter_field_button_reset', '.clear', '.no-class');
		$this->end_controls_section();
	}
	protected function _resgister_style_filter_field_selected_list() {
		// if(!isset($conditon) || $conditon == null){
		// 	return;
		// }
		$this->start_controls_section(
			'section_filter_field_selected_list_style',
			array(
				'label' => esc_html__('Selected List', 'wcbt'),
				'tab'   => Controls_Manager::TAB_STYLE,
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
	protected function resgister_option_general_style_filter_field_selected($label, $class, $class2) {
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => $label . '_typography',
				'label'    => esc_html__('Typography', 'wcbt'),
				'selector' => '{{WRAPPER}} ' . $class . ',{{WRAPPER}} ' . $class2,
			]
		);
		$this->add_responsive_control(
			$label . '_padding',
			array(
				'label'      => esc_html__('Padding', 'wcbt'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array('px', 'em'),

				'selectors' => array(
					'{{WRAPPER}} ' . $class => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			$label . '_margin',
			array(
				'label'      => esc_html__('Margin', 'wcbt'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array('px', 'em'),
				'selectors' => array(
					'{{WRAPPER}} ' . $class => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => $label . '_border',
				'exclude'  => array('color'),
				'selector' => '{{WRAPPER}} ' . $class,
			)
		);
		$this->add_control(
			$label . '_border_radius',
			array(
				'label'      => esc_html__('Border Radius', 'wcbt'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array('px', '%'),
				'selectors'  => array(
					'{{WRAPPER}} ' . $class => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
	}
	protected function resgister_option_tabs_color_general_filter_selected($label, $class, $class2) {
		$this->start_controls_tabs('tabs_' . $label . '_style');
		$this->start_controls_tab(
			'tab_' . $label . '_normal',
			array(
				'label' => esc_html__('Normal', 'wcbt'),
			)
		);
		$this->add_control(
			$label . '_color',
			array(
				'label'     => esc_html__('Color', 'wcbt'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}  ' . $class . ',{{WRAPPER}} ' . $class2 => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			$label . '_background_color',
			array(
				'label'     => esc_html__('Background Color', 'wcbt'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}  ' . $class => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			$label . '_border_color' . $label,
			array(
				'label'     => esc_html__('Border Color', 'wcbt'),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					$label . '_border_border!' => ['none', ''],
				),
				'selectors' => array(
					'{{WRAPPER}}  ' . $class => 'border-color: {{VALUE}};',
				),
			)
		);
		// $this->add_control(
		//     $label.'_icon_color',
		//     array(
		//         'label'     => esc_html__('Icon Color', 'wcbt'),
		//         'type'      => Controls_Manager::COLOR,
		//         'default'   => '',
		//         'selectors' => array(
		//             '{{WRAPPER}}  '.$class.' i' => 'color: {{VALUE}};',
		//         ),
		//     )
		// );
		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_' . $label . '_hover',
			array(
				'label' => esc_html__('Hover', 'wcbt'),
			)
		);
		$this->add_control(
			$label . '_color_hover',
			array(
				'label'     => esc_html__('Color', 'wcbt'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}  ' . $class . ':hover,{{WRAPPER}} ' . $class2 . ':hover' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			$label . '_hover_background_color',
			array(
				'label'     => esc_html__('Background Color', 'wcbt'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $class . ':hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			$label . '_hover_border_color_',
			array(
				'label'     => esc_html__('Border Color', 'wcbt'),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					$label . '_border_border!' => ['none', ''],
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $class . ':hover' => 'border-color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
	}
	protected function render() {
		$settings = $this->get_settings_for_display();
		$has_filter_field = false;
		if(isset( $_GET['category'] )){
			$has_filter_field = true;
		}
		if(isset( $_GET['availability'] )){
			$has_filter_field = true;
		}
		if(isset( $_GET['rating'] )){
			$has_filter_field = true;
		}
		if(isset( $_GET['min_price'] )){
			$has_filter_field = true;
		}
		$attributes = ProductAttributeModel::get_attribute_taxonomies();
		foreach ( $attributes as $attribute ) {
			$name          = $attribute->attribute_name;
			if (  isset( $_GET[ $name ] ) ) {
				$has_filter_field = true;
			}
		}
		if(!$has_filter_field){
			return;
		}
		$this->_render_content_template();
	}
	protected function _render_content_template() {
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			?>
			<div class="wcbt-product-filter-selection wrapper">
				<ul class="list">
					<li class="list-item" data-field="availability" data-value="in-stock">
						<span class="title"><?php esc_html_e('In stock','wcbt');?></span>
						<span class="remove">x</span>
					</li>
					<li class="list-item" data-field="rating" data-value="5">
						<span class="title"><?php esc_html_e('Five stars','wcbt');?></span>
						<span class="remove">x</span>
					</li>
				</ul>
				<button type="button" class="clear"><?php esc_html_e('Clear All','wcbt');?></button>
			</div>
		<?php
		}
		$data = array('true');
		Template::instance()->get_frontend_template_type_classic('product-filter/selection.php', compact('data'));
	}
}
