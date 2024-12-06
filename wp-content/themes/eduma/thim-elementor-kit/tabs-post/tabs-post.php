<?php

namespace Elementor;

use Thim_EL_Kit\GroupControlTrait;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Thim_Ekit_Widget_Tabs_Post extends Widget_Base {

	use GroupControlTrait;

	protected $current_permalink;

	public function get_name() {
		return 'thim-tabs-post';
	}

	public function get_title() {
		return esc_html__( 'Tabs Post', 'eduma' );
	}

	public function get_icon() {
		return 'eduma-eicon eicon-tabs';
	}

	public function get_categories() {
		return [ 'eduma_ekit' ];
	}

	public function get_keywords() {
		return [
			'thim',
			'blog',
			'list blog',
			'blogs',
			'tab',
			'new'
		];
	}

	public function get_style_depends() {
		return [ 'custom-widget-style' ];
	}

	protected function get_cat_taxonomy( $taxomony = 'category', $cats = false, $id = false ) {
		if ( ! $cats ) {
			$cats = array();
		}
		$terms = new \WP_Term_Query(
			array(
				'taxonomy'     => $taxomony,
				'pad_counts'   => 1,
				'hierarchical' => 1,
				'hide_empty'   => 1,
				'orderby'      => 'name',
				'menu_order'   => true,
			)
		);

		if ( is_wp_error( $terms ) ) {
		} else {
			if ( empty( $terms->terms ) ) {
			} else {
				foreach ( $terms->terms as $term ) {
					$prefix = '';
					if ( $term->parent > 0 ) {
						$prefix = '--';
					}
					if ( $id ) {
						$cats[$term->term_id] = $prefix . $term->name;
					} else {
						$cats[$term->slug] = $prefix . $term->name;
					}
				}
			}
		}

		return $cats;
	}

	protected function register_controls() {

		$this->start_controls_section(
			'tabs_post',
			[
				'label' => esc_html__( 'Tabs Post', 'eduma' )
			]
		);

		$this->add_control(
			'skin_layout_tabs',
			[
				'label'       => esc_html__( 'Choose Skin', 'eduma' ),
				'type'        => class_exists( '\Thim_EL_Kit\Elementor\Controls\Image_Select' ) ? 'thim-ekit-image-select' : 'select',
				'label_block' => true,
				'options'     => [
					'tabs-1' => [
						'title' => 'Skin 1',
						'url'   => THIM_URI . 'images/layout/elementor/tabs-1.jpg'
					],
					'tabs-2' => [
						'title' => 'Skin 2',
						'url'   => THIM_URI . 'images/layout/elementor/tabs-2.jpg',
					],
				],
				'default'     => 'tabs-1',
				'toggle'      => false,
			]
		);

		$this->add_control(
			'cat_slug',
			array(
				'label'    => esc_html__( 'Select Category', 'eduma' ),
				'type'     => Controls_Manager::SELECT2,
				'multiple' => true,
				'options'  => $this->get_cat_taxonomy( 'category' ),
			)
		);

		$this->add_control(
			'number_posts',
			array(
				'label'   => esc_html__( 'Number Post', 'eduma' ),
				'default' => '5',
				'type'    => Controls_Manager::NUMBER,
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'   => esc_html__( 'Sort in', 'eduma' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'popular' => esc_html__( 'Popular', 'eduma' ),
					'recent'  => esc_html__( 'Date', 'eduma' ),
					'title'   => esc_html__( 'Title', 'eduma' ),
					'random'  => esc_html__( 'Random', 'eduma' ),
				),
				'default' => 'recent',
			)
		);

		$this->add_control(
			'order',
			array(
				'label'   => esc_html__( 'Order by', 'eduma' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'asc'  => esc_html__( 'ASC', 'eduma' ),
					'desc' => esc_html__( 'DESC', 'eduma' ),
				),
				'default' => 'asc',
			)
		);

		$this->end_controls_section();

		$this->_register_content();
		$this->_register_style_tab();
		$this->_register_style_category();
		$this->_register_style_title();
		$this->_register_style_meta();
		$this->_register_style_read_more();
	}

	protected function _register_content() {
		$this->start_controls_section(
			'content',
			[
				'label' => esc_html__( 'Content', 'eduma' )
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'    => 'thumbnail_size',
				'default' => 'full',
			)
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'key',
			array(
				'label'   => esc_html__( 'Type', 'eduma' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'title',
				'options' => array(
					'title'     => 'Title',
					'meta_data' => 'Meta Data',
					'content'   => 'Content',
					'read_more' => 'Read more',
				),
			)
		);

		$repeater->add_control(
			'title_tag',
			array(
				'label'     => __( 'Title HTML Tag', 'eduma' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				),
				'default'   => 'h4',
				'condition' => array(
					'key' => 'title',
				),
			)
		);

		$repeater->add_control(
			'excerpt_lenght',
			array(
				'label'     => esc_html__( 'Excerpt lenght', 'eduma' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 20,
				'condition' => array(
					'key' => 'content',
				),
			)
		);

		$repeater->add_control(
			'meta_data',
			array(
				'label'       => esc_html__( 'Meta Data', 'eduma' ),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT2,
				'default'     => array( 'date', 'comments' ),
				'multiple'    => true,
				'sortable'    => true,
				'options'     => array(
					'author'   => esc_html__( 'Author', 'eduma' ),
					'date'     => esc_html__( 'Date', 'eduma' ),
					'category' => esc_html__( 'Category', 'eduma' ),
				),
				'condition'   => array(
					'key' => 'meta_data',
				),
			)
		);

		$repeater->add_control(
			'show_one',
			[
				'label'     => esc_html__( 'Show one category', 'eduma' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => array(
					'key'       => 'meta_data',
					'meta_data' => 'category'
				),
			]
		);

		$repeater->add_control(
			'read_more_text',
			array(
				'label'     => esc_html__( 'Read More Text', 'eduma' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Read More', 'eduma' ),
				'condition' => array(
					'key' => 'read_more',
				),
			)
		);

		$this->add_control(
			'repeater',
			array(
				'label'       => esc_html__( 'Post Data', 'eduma' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'key' => 'title',
					),
					array(
						'key' => 'content',
					),
				),
				'title_field' => '<span style="text-transform: capitalize;">{{{ key.replace("_", " ") }}}</span>',
			)
		);

		$this->add_control(
			'icon_custom',
			array(
				'label'       => esc_html__( 'Choose icon type', 'eduma' ),
				'type'        => Controls_Manager::ICONS,
				'skin'        => 'inline',
				'label_block' => false
			)
		);

		$this->end_controls_section();
	}

	protected function _register_style_tab() {
		$this->start_controls_section(
			'section_style_tab',
			array(
				'label' => esc_html__( 'Course Tab', 'eduma' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'tab_align',
			array(
				'label'     => esc_html__( 'Alignment', 'eduma' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'eduma' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'eduma' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'eduma' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'right',
				'toggle'    => true,
				'selectors' => array(
					'{{WRAPPER}} .thim-tabs-post .nav-tabs' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'tab_item_spacing',
			array(
				'label'     => esc_html__( 'Spacing', 'eduma' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 120,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .thim-tabs-post .nav-tabs' => 'margin: 0 0 {{SIZE}}{{UNIT}} 0',
				),
				'default'   => array(
					'size' => 30,
				),
			)
		);

		$this->add_responsive_control(
			'tab_item_margin',
			array(
				'label'      => esc_html__( 'Margin', 'eduma' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'    => 0,
					'right'  => 0,
					'bottom' => 0,
					'left'   => 50,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .thim-tabs-post .nav-tabs li a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'tab_item_padding',
			array(
				'label'      => esc_html__( 'Padding', 'eduma' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'    => 0,
					'right'  => 0,
					'bottom' => 10,
					'left'   => 0,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .thim-tabs-post .nav-tabs li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		// start tab for content
		$this->start_controls_tabs(
			'course_style_tabs_item'
		);

		// start normal tab
		$this->start_controls_tab(
			'tab_item_style_normal',
			array(
				'label' => esc_html__( 'Normal', 'eduma' ),
			)
		);
		$this->add_control(
			'tab_item_text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'eduma' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .thim-tabs-post .nav-tabs li a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tab_item_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'eduma' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .thim-tabs-post .nav-tabs li a' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'tab_item_border',
				'label'    => esc_html__( 'Border', 'eduma' ),
				'selector' => '{{WRAPPER}} .thim-tabs-post .nav-tabs li a',
			)
		);

		$this->end_controls_tab();
		// end normal tab

		// start active tab
		$this->start_controls_tab(
			'tab_item_style_active',
			array(
				'label' => esc_html__( 'Active', 'eduma' ),
			)
		);
		$this->add_control(
			'tab_item_text_color_active',
			array(
				'label'     => esc_html__( 'Text Color', 'eduma' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .thim-tabs-post .nav-tabs li a:hover,{{WRAPPER}} .thim-tabs-post .nav-tabs li.active a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tab_item_bg_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'eduma' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .thim-tabs-post .nav-tabs li a:hover,{{WRAPPER}} .thim-tabs-post .nav-tabs li.active a' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'tab_item_border_active',
				'label'    => esc_html__( 'Border', 'eduma' ),
				'selector' => '{{WRAPPER}} .thim-tabs-post .nav-tabs li a:hover,{{WRAPPER}} .thim-tabs-post .nav-tabs li.active a',
			)
		);

		$this->end_controls_tab();
		// end hover tab

		$this->end_controls_tabs();

		$this->add_control(
			'tab_item_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'eduma' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .thim-tabs-post .nav-tabs li a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'tab_item_typography',
				'label'    => esc_html__( 'Typography', 'eduma' ),
				'selector' => '{{WRAPPER}} .thim-tabs-post .nav-tabs li a',
			)
		);

		$this->end_controls_section();
	}

	protected function _register_style_category() {
		$this->start_controls_section(
			'category_options',
			[
				'label' => esc_html__( 'Category', 'eduma' ),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);

		$this->register_button_style( 'category_new', '.thim-tabs-post__category' );

		$this->end_controls_section();
	}

	protected function _register_style_title() {
		$this->start_controls_section(
			'title_options',
			[
				'label' => esc_html__( 'Title', 'eduma' ),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .thim-tabs-post__title',
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
					'{{WRAPPER}} .thim-tabs-post__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function _register_style_meta() {
		$this->start_controls_section(
			'meta_data_options',
			[
				'label' => esc_html__( 'Meta data', 'eduma' ),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'display_meta',
			array(
				'label'     => esc_html__( 'Display', 'eduma' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'block'        => array(
						'title' => esc_html__( 'Block', 'eduma' ),
						'icon'  => 'eicon-editor-list-ul',
					),
					'inline-block' => array(
						'title' => esc_html__( 'Inline', 'eduma' ),
						'icon'  => 'eicon-ellipsis-h',
					),
				),
				'default'   => 'inline-block',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .thim-tabs-post__meta > *' => 'display: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'spacing_data',
			array(
				'label'     => esc_html__( 'Spacing', 'eduma' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 10,
					'unit' => 'px',
				),
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'body:not(.rtl) {{WRAPPER}} .thim-tabs-post__meta > * ' => 'margin-right: {{SIZE}}{{UNIT}};',
					'body.rtl {{WRAPPER}} .thim-tabs-post__meta > *'        => 'margin-left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'item_padding',
			array(
				'label'      => esc_html__( 'Padding', 'eduma' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .thim-tabs-post__meta > *' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function _register_style_read_more() {
		$this->start_controls_section(
			'read_more_options',
			[
				'label' => esc_html__( 'Read More', 'eduma' ),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'display_readmore',
			array(
				'label'     => esc_html__( 'Display', 'eduma' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'block'        => array(
						'title' => esc_html__( 'Block', 'eduma' ),
						'icon'  => 'eicon-editor-list-ul',
					),
					'inline-block' => array(
						'title' => esc_html__( 'Inline', 'eduma' ),
						'icon'  => 'eicon-ellipsis-h',
					),
				),
				'default'   => 'inline-block',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .thim-tabs-post__read-more' => 'display: {{VALUE}};',
				),
			)
		);

		$this->register_button_style( 'read_more_new', '.thim-tabs-post__read-more' );

		$this->add_responsive_control(
			'read_more_margin',
			array(
				'label'      => esc_html__( 'Margin', 'eduma' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .thim-tabs-post__read-more' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$layout_skin = isset( $settings['skin_layout_tabs'] ) ? $settings['skin_layout_tabs'] : 'tabs-1';
		$params      = array(
			'page_id'   => esc_attr( get_the_id() ),
			'widget_id' => esc_attr( $this->get_id() ),
		);
		$list_tab    = '';
		$categories  = [];
		if ( $settings['cat_slug'] ) {
			$categories = get_categories( array(
				'slug'       => $settings['cat_slug'],
				'hide_empty' => false,
			) );
		} else {
			$categories = get_categories( array(
				'number' => 5, // limit cat
			) );
		}
		echo '<div class="thim-tabs-post" data-params="' . htmlentities( json_encode( $params ) ) . '">';

		if ( $categories ) {

			foreach ( array_values( $categories ) as $k => $tab ) {
				$tab_class = ' class="cat-item"';
				if ( $k == 0 ) {
					$tab_class          = ' class="cat-item active"';
					$cat_default_active = $tab->term_id;
				}
				$list_tab .= '<li' . $tab_class . '><a data-cat="' . $tab->term_id . '" href="#">' . esc_html( $tab->name ) . '</a></li>';
			}

			// show html tab
			if ( $list_tab ) {
				echo '<ul class="nav nav-tabs">' . wp_kses_post( $list_tab ) . '</ul>';
			}

			echo '<div class="loop-wrapper thim-tabs-post__inner ' . esc_attr( $layout_skin ) . '">';
			$this->render_data_content_tab( $settings, $cat_default_active );
			echo '</div>';
		} else {
			echo '<div class="message-info">' . esc_html__(
					'No data were found matching your selection, you need to create Post or select Category of Widget.',
					'eduma'
				) . '</div>';
		}
		echo '</div>';
	}

	public function render_data_content_tab( $settings, $cat_id ) {
		$query_args = array(
			'post_type'           => 'post',
			'posts_per_page'      => absint( $settings['number_posts'] ),
			'order'               => ( 'asc' == $settings['order'] ) ? 'asc' : 'desc',
			'ignore_sticky_posts' => true,
		);

		switch ( $settings['orderby'] ) {
			case 'recent':
				$query_args['orderby'] = 'post_date';
				break;
			case 'title':
				$query_args['orderby'] = 'post_title';
				break;
			case 'popular':
				$query_args['orderby'] = 'comment_count';
				break;
			default:
				$query_args['orderby'] = 'rand';
		}

		$query_args['cat'] = $cat_id;
		$query_vars        = new \WP_Query( $query_args );

		if ( $query_vars->have_posts() ) {
			while ( $query_vars->have_posts() ) {
				$query_vars->the_post();
				$this->current_permalink = get_permalink();
				$this->render_post_tab( $settings, 'thim-tabs-post__item' );
			}
		} else {
			echo '<div class="message-info">' . __(
					'No data were found matching your selection, you need to create Post or select Category of Widget.',
					'eduma'
				) . '</div>';
		}

		wp_reset_postdata();
	}

	protected function render_post_tab( $settings, $class_item ) {
		?>
		<div class="<?php echo esc_attr( $class_item ); ?>">
			<?php
			$this->render_thumbnail( $settings );
			echo '<div class="thim-tabs-post__content">';
			if ( $settings['repeater'] ) {
				foreach ( $settings['repeater'] as $item ) {
					switch ( $item['key'] ) {
						case 'title':
							$this->render_title( $item );
							break;
						case 'content':
							$this->render_excerpt( $item );
							break;
						case 'meta_data':
							$this->render_meta_data( $item );
							break;
						case 'read_more':
							$this->render_read_more( $item['read_more_text'] );
							break;
					}
				}
			}
			echo '</div>';
			?>
		</div>
		<?php
	}

	protected function render_thumbnail( $settings ) {
		$settings['thumbnail_size'] = array(
			'id' => get_post_thumbnail_id(),
		);

		$thumbnail_html = Group_Control_Image_Size::get_attachment_image_html( $settings, 'thumbnail_size' );

		if ( empty( $thumbnail_html ) ) {
			return;
		}

		?>
		<div class="thim-tabs-post__thumbnail">
			<a class="post-thumbnail"
			   href="<?php
			   echo esc_url( $this->current_permalink ); ?>">
				<?php
				$this->render_icon( $settings );
				echo wp_kses_post( $thumbnail_html );
				?>
			</a>
		</div>
		<?php
	}

	protected function render_title( $item ) {
		?>
		<<?php
		Utils::print_validated_html_tag( $item['title_tag'] ); ?> class="thim-tabs-post__title">
		<a href="<?php
		echo esc_url( $this->current_permalink ); ?>">
			<?php
			the_title(); ?>
		</a>
		</<?php
		Utils::print_validated_html_tag( $item['title_tag'] ); ?>>
		<?php
	}

	protected function render_meta_data( $item ) {
		$meta_data = $item['meta_data'];
		?>
		<div class="thim-tabs-post__meta">
			<?php
			if ( in_array( 'author', $meta_data ) ) {
				$this->render_author();
			}
			if ( in_array( 'date', $meta_data ) ) {
				$this->render_date_by_type();
			}
			if ( in_array( 'category', $meta_data ) ) {
				$this->render_categories( $item );
			}
			?>
		</div>
		<?php
	}

	protected function render_categories( $item ) {
		$categories = get_the_category();
		if ( empty( $categories ) ) {
			return;
		}

		$category_list = [];
		foreach ( $categories as $category ) {
			$category_list[] = '<a class="thim-tabs-post__category ' . esc_attr( $category->slug ) . '" 
				href="' . esc_url( get_category_link( $category->term_id ) ) . '"
				title="' . esc_attr( $category->cat_name ) . '">' . esc_html( $category->cat_name ) . ' </a>';
		}

		if ( 'yes' == $item['show_one'] ) {
			$value = $category_list[0];
		} else {
			$value = implode( ' ', $category_list );
		}

		echo '<div class="thim-tabs-post__categories">';
		echo wp_kses_post( $value );
		echo '</div>';
	}

	protected function render_author() {
		?>
		<span class="thim-tabs-post__author">
			<?php
			the_author(); ?>
		</span>
		<?php
	}

	protected function render_excerpt( $item ) {
		?>
		<div class="thim-tabs-post__excerpt">
			<?php
			echo wp_kses_post( wp_trim_words( get_the_excerpt(), absint( $item['excerpt_lenght'] ) ) ); ?>
		</div>
		<?php
	}

	protected function render_date_by_type() {
		$date = get_the_date();
		?>

		<span class="thim-tabs-post__date">
			<?php
			echo esc_html( apply_filters( 'the_date', $date, get_option( 'date_format' ), '', '' ) ); ?>
		</span>

		<?php
	}

	protected function render_read_more( $text_read_more ) {
		?>
		<a class="thim-tabs-post__read-more"
		   href="<?php
		   echo esc_url( $this->current_permalink ); ?>">
			<?php
			echo esc_html( $text_read_more ); ?>
		</a>
		<?php
	}

	protected function render_icon( $settings ) {
		if ( ! empty( $settings['icon_custom']['value'] ) ) : ?>
			<span class="thim-tabs-post__icon">
				<?php
				Icons_Manager::render_icon( $settings['icon_custom'], [ 'aria-hidden' => 'true' ] ); ?>
			</span>
		<?php
		endif;
	}

}
