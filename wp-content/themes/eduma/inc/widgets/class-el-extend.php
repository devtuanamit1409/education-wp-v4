<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class Thim_Elementor_Extend {
	private static $instance = null;

	public function __construct() {
		add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'backend_style' ) );
		// add widget categories
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_categories' ) );

		add_action( 'elementor/widgets/register', array( $this, 'thim_register_widgets' ), 200 );

		// register font for vc
		add_action( 'vc_backend_editor_enqueue_js_css', array( $this, 'thim_vc_iconpicker_editor_jscss' ) );
		add_action( 'vc_frontend_editor_enqueue_js_css', array( $this, 'thim_vc_iconpicker_editor_jscss' ) );

		// add custom font for VC and SO and EL
		$list_icons = array( 'ionicons', 'flat_icon', 'stroke_icon' );
		foreach ( $list_icons as $list_icon ) {

			add_filter(
				'thim-builder-el-' . $list_icon . '-icon',
				function () use ( $list_icon ) {
					$arr              = $this->thim_load_font_icon();
					$list_new_icon_el = array();
					if ( ! empty( $arr ) ) {
						foreach ( $arr[$list_icon] as $icons ) {
							foreach ( $icons as $key => $label ) {
								$list_new_icon_el[$key] = $key;
							}
						}
					}

					return $list_new_icon_el;
				}
			);

			add_filter(
				'vc_iconpicker-type-' . $list_icon,
				function () use ( $list_icon ) {
					$custom_icon = $this->thim_load_font_icon();

					return $custom_icon[$list_icon];
				}
			);

			add_filter(
				'thim-builder-so-' . $list_icon . '-icon',
				function () use ( $list_icon ) {
					$arr           = $this->thim_load_font_icon();
					$list_new_icon = array();
					if ( ! empty( $arr ) ) {
						foreach ( $arr[$list_icon] as $icons ) {
							foreach ( $icons as $key => $label ) {
								$list_new_icon[] = str_replace( array( 'pe-7s-', 'flaticon-' ), '', $key );
							}
						}
					}

					return $list_new_icon;
				}
			);
		}

		// change HTML thim_ekit_footer_header
		$this->theme_ekit_footer_header();

		add_filter( 'learn-thim-kits-lp-meta-data', array( $this, 'thim_review_meta_data_widget_course' ), 100 );
		add_filter( 'thim-kits-extral-meta-data', array( $this, 'thim_kits_meta_data_course_ratting' ), 100, 3 );

		// remove font-awesome in elementor
		add_action( 'elementor/frontend/after_register_styles', function () {
			foreach ( [ 'solid', 'regular', 'brands' ] as $style ) {
				wp_deregister_style( 'elementor-icons-fa-' . $style );
				wp_deregister_style( 'font-awesome' );
			}
		}, 20 );

//		add_filter( 'elementor/element/after_section_end', array( $this, 'thim_kits_options_blog_list' ), 10, 3 );
		/*
		 * @Since 1.2.7 version thim-elementor-kit
		 */
 		add_filter('thim-ekits-custom-logo-url', function (){
			 return get_theme_mod( 'thim_logo', false );
		} );
	}

	public function backend_style() {
		wp_enqueue_style( 'flaticon', THIM_URI . 'assets/css/flaticon.css' );
		wp_enqueue_style( 'font-pe-icon-7', THIM_URI . 'assets/css/font-pe-icon-7.css' );
		wp_enqueue_style( 'ionicons', THIM_URI . 'assets/css/ionicons.min.css' );
		wp_enqueue_style( 'thim-font-icon', THIM_URI . 'assets/css/thim-icons.css', array(), THIM_THEME_VERSION );
	}

	function theme_ekit_footer_header() {
		// Thim Elementor Kit
		add_action( 'thim_ekit/header_footer/template/before_footer', 'thim_above_footer_area_fnc' );
		add_action( 'thim_ekit/header_footer/template/before_header', 'thim_print_preload', 5 );

		add_action(
			'thim_ekit/header_footer/template/after_footer',
			function () {
				echo '</div>';
			},
			1
		);
		add_action( 'thim_ekit/header_footer/template/after_footer', 'thim_footer_bottom', 5 );
		add_action(
			'thim_ekit/header_footer/template/after_footer',
			function () {
				echo '</div></div>';
			},
			10
		);

		add_action(
			'thim_ekit/header_footer/template/before_header',
			function () {
				echo '<div id="wrapper-container" class="wrapper-container"><div class="content-pusher">';
			},
			10
		);
		add_action(
			'thim_ekit/header_footer/template/after_header',
			function () {
				echo '<div id="main-content">';
			},
			5
		);
		// add class for menu of thim elementor kit
		add_filter( 'thim_ekit/mega_menu/menu_container/class', function () {
			return 'header .thim-nav-wrapper .tm-table';
		} );
	}

	public function register_categories( \Elementor\Elements_Manager $elements_manager ) {
		$categories = array(
			'eduma_ekit' => array(
				'title' => esc_html__( 'Eduma Theme', 'eduma' ),
				'icon'  => 'fa fa-plug',
			),
		);

		$old_categories = $elements_manager->get_categories();
		$categories     = array_merge( $categories, $old_categories );

		$set_categories = function ( $categories ) {
			$this->categories = $categories;
		};

		$set_categories->call( $elements_manager, $categories );
	}

	public function thim_register_widgets( $widgets_manager ) {

		$widgets_all = apply_filters( 'thim_register_shortcode', array() );
		if ( ! empty( $widgets_all ) ) {
			foreach ( $widgets_all as $base => $widgets ) {

				if ( $base == 'general' || ( $base != 'general' && class_exists( $base ) ) ) {
					foreach ( $widgets as $widget ) {
						// unregister thim wp-widget
						$widgets_manager->unregister( 'wp-widget-' . $widget );
						// register widget for EL
						$file = THIM_DIR . "thim-elementor-kit/$widget/$widget.php";

						if ( file_exists( $file ) ) {
							require_once $file;

							$class = ucwords( str_replace( '-', ' ', $widget ) );
							$class = str_replace( ' ', '_', $class );
							$class = sprintf( '\Elementor\Thim_Ekit_Widget_%s', $class );

							if ( class_exists( $class ) ) {
								$widgets_manager->register( new $class() );
							}
						}
					}
				}
			}
		}
		// register widget login popup
		require_once THIM_DIR . 'thim-elementor-kit/login-popup/login-popup.php';
		if ( class_exists( '\Elementor\Thim_Ekit_Widget_Login_Popup' ) ) {
			$widgets_manager->register( new \Elementor\Thim_Ekit_Widget_Login_Popup() );
		}
	}

	public static function get_instance() {
		if ( self::$instance == null ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	function thim_vc_iconpicker_editor_jscss() {
		wp_enqueue_style( 'thim-admin-ionicons' );
		wp_enqueue_style( 'thim-admin-font-flaticon' );
	}

	public function thim_load_font_icon() {
		$icon = include THIM_DIR . 'inc/widgets/icons.php';

		return $icon;
	}

	function thim_review_meta_data_widget_course( $opt ) {
		if ( class_exists( 'LP_Addon_Course_Review' ) ) {
			$opt['review_course'] = esc_html__( 'Review', 'eduma' );
		}

		return $opt;
	}

	function thim_kits_meta_data_course_ratting( $string, $meta_data, $settings ) {
		if ( class_exists( 'LP_Addon_Course_Review' ) && in_array( 'review_course', $meta_data ) ) {
			$course_rate = learn_press_get_course_rate( get_the_ID() );
			?>
			<span class="course-review">
			 <?php thim_print_rating( $course_rate ); ?>
		</span>
			<?php
		}

		return $string;
	}

	function thim_kits_options_blog_list( $element, $section_id, $args ) {
		if ( 'thim-ekits-list-blog' === $element->get_name() && 'section_content_list' === $section_id ) {
			$element->start_controls_section(
				'show_one_post_center',
				[
					'label' => __( 'One Featured Post', 'eduma' ),
				]
			);

			$element->add_control(
				'show_feature_image',
				array(
					'label'        => esc_html__( 'Show', 'thim-elementor-kit' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'default'      => 'no',
					'separator'    => 'before',
					'prefix_class' => 'feature-one-post-'
				)
			);
			$element->add_control(
			 	'skin_layout_base',
			 	[
			 		'label'   => esc_html__( 'Choose Skin Layout',  'eduma' ),
			 		'type'    => class_exists( '\Thim_EL_Kit\Elementor\Controls\Image_Select' ) ? 'thim-ekit-image-select' : 'select',
			 		'options' => [
						'base-1' => [
							'title' => 'Base 1',
							'url' => THIM_URI . 'images/layout/elementor/base-1.jpg',
 						],
						'base-2' => [
							'title' => 'Base 2',
							'url' => THIM_URI . 'images/layout/elementor/base-2.jpg',
						],
						'base-3' => [
							'title' => 'Base 3',
							'url' => THIM_URI . 'images/layout/elementor/base-3.jpg',
						],
 			 		],
					'default'      => 'base-1',
					'toggle'       => false,
					'condition'    => [
						'show_feature_image' => [ 'yes' ],
					],
					'prefix_class' => 'feature-style__',
			 		'styles'  => 'width: 30%;',
			 	]
			 );

			$element->add_responsive_control(
				'columns_space',
				array(
					'label'          => esc_html__( 'Columns Feature', 'thim-elementor-kit' ),
					'type'           => \Elementor\Controls_Manager::SELECT,
					'default'        => '3',
					'tablet_default' => '2',
					'mobile_default' => '1',
					'options'        => array(
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
					),
					'condition'      => [
						'show_feature_image' => [ 'yes' ],
					],
					'selectors'      => array(
						'{{WRAPPER}}' => '--thim-ekits-post-feature-columns: {{VALUE}}',
					),
				)
			);
			$element->add_responsive_control(
				'rows_space',
				array(
					'label'          => esc_html__( 'Rows Feature', 'thim-elementor-kit' ),
					'type'           => \Elementor\Controls_Manager::SELECT,
					'default'        => '2',
					'tablet_default' => '2',
					'mobile_default' => '1',
					'options'        => array(
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
					),
					'condition'      => [
						'show_feature_image' => [ 'yes' ],
					],
					'selectors'      => array(
						'{{WRAPPER}}' => '--thim-ekits-post-feature-rows: {{VALUE}}',
					),
				)
			);
			$element->end_controls_section();
		}

		return $element;
	}
}

Thim_Elementor_Extend::get_instance();
