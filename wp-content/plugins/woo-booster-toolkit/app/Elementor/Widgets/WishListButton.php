<?php

namespace WCBT\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use WCBT\Helpers\Template;
use WCBT\Models\ProductModel;
use WCBT\Elementor\ElementorWCBT;

class WishListButton extends Widget_Base {
    /**
     * @return string
     */
    public function get_name() {
        return WCBT_PREFIX . '-wishlist';
    }
    /**
     * @return string
     */
    public function get_title() {
        return WCBT_EL_PREFIX . ' WishList Button';
    }

    /**
     * @return string
     */
    public function get_icon() {
        return 'eicon-instagram-likes';
    }

    /**
     * @return string[]
     */
    public function get_categories() {
        return ['wcbt-category'];
    }

    protected function _register_controls() {
        $this->_register_settings_general();
        $this->_register_style_general('wishlist', '.wcbt-product-wishlist');
        $this->_register_style_tooltip();
    }
    protected function _register_settings_general() {
        $this->start_controls_section(
            'section_content',
            array(
                'label' => esc_html__('Content', 'wcbt'),
            )
        );
        $this->add_control(
            'display',
            array(
                'label'        => esc_html__('Display', 'wcbt'),
                'type'         => Controls_Manager::SELECT,
                'default'      => 'icon_tooltip',
                'options'      => array(
                    'text'         => esc_html__('Text', 'wcbt'),
                    'icon_tooltip' => esc_html__('Icon & ToolTip', 'wcbt'),
                ),
            )
        );
        $this->add_control(
            'wishlist_text',
            [
                'label' => esc_html__('Text', 'wcbt'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__('Add WishList', 'wcbt'),
                'condition'   => array(
                    'display' => 'text',
                ),
            ]
        );
        $this->add_control(
            'icon_wishlist',
            array(
                'label'       => esc_html__('Choose Icon', 'wcbt'),
                'type'        => Controls_Manager::ICONS,
                'skin'        => 'inline',
                'label_block' => false,
                'default' => [
                    'value' => 'far fa-heart',
                    'library' => 'solid',
                ],
                'condition'   => array(
                    'display' => 'icon_tooltip',
                ),
                // 'exclude_inline_options' =>['svg']
            )
        );
        $this->add_control(
            'text_tooltip',
            [
                'label' => esc_html__('Text Tooltip', 'wcbt'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__('Tooltip', 'wcbt'),
                'condition'   => array(
                    'display' => 'icon_tooltip',
                ),
            ]
        );
        $this->add_control(
            'text_active_tooltip',
            [
                'label' => esc_html__('Text Active Tooltip', 'wcbt'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__('Remove Tooltip', 'wcbt'),
                'condition'   => array(
                    'display' => 'icon_tooltip',
                ),
            ]
        );
        $this->end_controls_section();
    }
    protected function _register_style_general($label, $class) {
        $this->start_controls_section(
            'section_general_style' . $label,
            array(
                'label' => esc_html__('Style', 'wcbt'),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => $label . '_typography',
                'label'    => esc_html__('Typography', 'wcbt'),
                'selector' => '{{WRAPPER}} ' . $class,
                'condition'   => array(
                    'display' => 'icon_tooltip',
                ),
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
            $label . '_width',
            array(
                'label'      => esc_html__('Width', 'wcbt'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array('px'),
                'range'      => array(
                    'px' => [
                        'min' => 0,
                        'max' => 250,
                        'step' => 5,
                    ],
                ),
                'default' => [
                    'unit' => 'px',
                    'size' => 44,
                ],
                'selectors'  => array(
                    'body {{WRAPPER}}  ' . $class => 'width: {{SIZE}}{{UNIT}};display: flex;justify-content: center;align-items: center;',
                ),
            )
        );
        $this->add_responsive_control(
            $label . '_height',
            array(
                'label'      => esc_html__('Height', 'wcbt'),
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
                    'body {{WRAPPER}} ' . $class => 'height: {{SIZE}}{{UNIT}}',
                ),
                'condition'   => array(
                    'display' => 'icon_tooltip',
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
        $this->add_responsive_control(
            $label . '_icon_width',
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
                    'body {{WRAPPER}}  ' . $class . ' i' => 'font-size: {{SIZE}}{{UNIT}};',
                    'body {{WRAPPER}}  ' . $class . ' svg' => 'width: {{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};',
                ),
                'condition'   => array(
                    'display' => 'icon_tooltip',
                ),
            )
        );
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => $label . '_box_shadow',
                'selector' => '{{WRAPPER}}  ' . $class,
            ]
        );
        $this->start_controls_tabs('tabs_wishlist_style');

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
                    '{{WRAPPER}}  ' . $class => 'color: {{VALUE}};',
                ),
                'condition'   => array(
                    'display' => 'text',
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
            $label . '_border_color',
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
        $this->add_control(
            $label . '_icon_color',
            array(
                'label'     => esc_html__('Icon Color', 'wcbt'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => array(
                    '{{WRAPPER}}  ' . $class . ' i' => 'color: {{VALUE}};',
                    '{{WRAPPER}}  ' . $class . ' svg path' => 'stroke: {{VALUE}};',
                ),
                'condition'   => array(
                    'display' => 'icon_tooltip',
                ),
            )
        );
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
                    '{{WRAPPER}}  ' . $class . ':hover' => 'color: {{VALUE}};',
                ),
                'condition'   => array(
                    'display' => 'text',
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
                    '{{WRAPPER}}  ' . $class . ':hover svg path' => 'fill: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            $label . '_hover_border_color',
            array(
                'label'     => esc_html__('Border Color', 'wcbt'),
                'type'      => Controls_Manager::COLOR,
                'condition' => array(
                    'wishlist_border_border!' => ['none', ''],
                ),
                'selectors' => array(
                    '{{WRAPPER}} ' . $class . ':hover' => 'border-color: {{VALUE}};',
                ),
            )
        );
        $this->add_control(
            $label . '_icon_color_hover',
            array(
                'label'     => esc_html__('Icon Color', 'wcbt'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => array(
                    '{{WRAPPER}} ' . $class . ':hover i' => 'color: {{VALUE}};',
                    '{{WRAPPER}}  ' . $class . ':hover svg path' => 'stroke: {{VALUE}};',
                ),
                'condition'   => array(
                    'display' => 'icon_tooltip',
                ),
            )
        );

        $this->end_controls_tab();
        $this->start_controls_tab(
            'tab_' . $label . '_active',
            array(
                'label' => esc_html__('Active', 'wcbt'),
            )
        );
        $this->add_control(
            $label . '_color_active',
            array(
                'label'     => esc_html__('Color', 'wcbt'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}}  .active' . $class => 'color: {{VALUE}};',
                ),
                'condition'   => array(
                    'display' => 'text',
                ),
            )
        );
        $this->add_control(
            $label . '_active_background_color',
            array(
                'label'     => esc_html__('Background Color', 'wcbt'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .active' . $class => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            $label . '_active_border_color',
            array(
                'label'     => esc_html__('Border Color', 'wcbt'),
                'type'      => Controls_Manager::COLOR,
                'condition' => array(
                    'wishlist_border_border!' => ['none', ''],
                ),
                'selectors' => array(
                    '{{WRAPPER}} .active' . $class => 'border-color: {{VALUE}};',
                ),
            )
        );
        $this->add_control(
            $label . '_icon_color_active',
            array(
                'label'     => esc_html__('Icon Color', 'wcbt'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => array(
                    '{{WRAPPER}} .active'. $class . ' i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .active'. $class . ' svg path' => 'stroke: {{VALUE}};',
                ),
                'condition'   => array(
                    'display' => 'icon_tooltip',
                ),
            )
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();
    }
    protected  function _register_style_tooltip() {
        $this->start_controls_section(
            'section_general_tooltip',
            array(
                'label' => esc_html__('Tooltip', 'wcbt'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition'   => array(
                    'display' => 'icon_tooltip',
                ),
            )
        );
        $this->add_control(
            'tooltip_offset_orientation_h',
            array(
                'label'        => esc_html__('Orientation', 'wcbt'),
                'type'         => Controls_Manager::CHOOSE,
                'toggle'       => false,
                'default'      => 'top',
                'options'      => array(
                    'left'  => array(
                        'title' => esc_html__('Left', 'wcbt'),
                        'icon'  => 'eicon-h-align-left',
                    ),
                    'top'  => array(
                        'title' => esc_html__('Top', 'wcbt'),
                        'icon'  => 'eicon-v-align-top',
                    ),
                    'right' => array(
                        'title' => esc_html__('Right', 'wcbt'),
                        'icon'  => 'eicon-h-align-right',
                    ),
                ),
                'render_type'  => 'ui',
                'prefix_class' => 'wcbt-tooltip-offset-',
            )
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'     => 'tooltip_typography',
                'selector' => '{{WRAPPER}} .tooltip',
            )
        );
        $this->add_responsive_control(
            'tooltip_padding',
            array(
                'label'      => esc_html__('Padding', 'wcbt'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array('px', 'em'),

                'selectors' => array(
                    '{{WRAPPER}} .tooltip' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );
        $this->add_control(
            'tooltip_color',
            array(
                'label'     => esc_html__('Color', 'wcbt'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => array(
                    '{{WRAPPER}} .tooltip' => 'color: {{VALUE}};',
                ),
            )
        );
        $this->add_control(
            'tooltip_background_color',
            array(
                'label'     => esc_html__('Background Color', 'wcbt'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .tooltip,{{WRAPPER}} .tooltip:after' => 'background-color: {{VALUE}};',
                ),
            )
        );
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            array(
                'name'     => 'tooltip_border',
                // 'exclude'  => array('color'),
                'selector' => '{{WRAPPER}} .tooltip',
            )
        );
        $this->add_control(
            'tooltip_border_radius',
            array(
                'label'      => esc_html__('Border Radius', 'wcbt'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array('px', '%'),
                'selectors'  => array(
                    '{{WRAPPER}} .tooltip' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );
        $this->end_controls_section();
    }
    protected function render() {
        $settings = $this->get_settings_for_display();
        $product = wc_get_product();
        if (empty($product)) {
            return;
        }
        $product_id = $product->get_id();
        $data = ProductModel::get_product_data($product_id);
        if ($data['is_my_wishlist']) {
            $class = 'wcbt-product-wishlist active';
        } else {
            $class = 'wcbt-product-wishlist';
        }
        ?>
        <div class="<?php echo esc_attr($class); ?>" data-product-id="<?php echo esc_attr($data['product_id']); ?>" data-type="<?php echo esc_attr($data['wishlist_type']); ?>">
            <?php if (isset($settings['display']) && $settings['display'] == 'icon_tooltip') {
                if (!empty($settings['icon_wishlist']['value'])) {
                    \Elementor\Icons_Manager::render_icon( $settings['icon_wishlist'], [ 'aria-hidden' => 'true' ] );
                    if (!empty($data['wishlist_tooltip_text']) &&  $data['is_my_wishlist']) {
                ?>
                        <span class="tooltip">
                            <?php echo esc_html($settings['text_active_tooltip']); ?>
                        </span>
                    <?php
                    }
                    if (!$data['is_my_wishlist'] && !empty($settings['text_tooltip'])) {
                    ?>
                        <span class="tooltip">
                            <?php echo esc_html($settings['text_tooltip']); ?>
                        </span>
                    <?php
                    }
                }
            } else {
                esc_html_e($settings['wishlist_text'], 'wcbt');
            }
            ?>
        </div>
        <?php
    }
}
