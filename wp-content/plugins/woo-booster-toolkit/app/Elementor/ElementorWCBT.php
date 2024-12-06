<?php

namespace WCBT\Elementor;

use WCBT\Elementor\Widgets\QuickViewButton;
use WCBT\Elementor\Widgets\CompareButton;
use WCBT\Elementor\Widgets\WishListButton;
use WCBT\Elementor\Widgets\FilterProduct;
use WCBT\Elementor\Widgets\ProductFilterSelected;
use WCBT\Helpers\Template;
class ElementorWCBT{
    public function __construct(){
        if (class_exists('Thim_EL_Kit')) {
            add_action('elementor/widgets/register', array( $this, 'register_new_widgets' ));
	        add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
        }
    }
    /**
     * Register new Elementor widgets.
     *
     * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
     * @return void
     */
    public function register_new_widgets($widgets_manager){
        $widgets_manager->register(new WishListButton());
        $widgets_manager->register(new CompareButton());
        $widgets_manager->register(new QuickViewButton());
        $widgets_manager->register(new FilterProduct());
        $widgets_manager->register(new ProductFilterSelected());
    }
    /**
	 * Get template elementor file
	 *
	 * @param string $file_name
	 * @param array $args
	 *
	 * @return void
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	public static function get_elementor_template( string $file_name = '', array $args = array() ) {
		$file_name = str_replace( '.php', '', $file_name );
		$path_file = WCBT_ELEMENT . "{$file_name}.php";
		Template::instance()->get_template( $path_file, $args );
	}

	public function register_category( \Elementor\Elements_Manager $elements_manager ) {
		$categories = apply_filters(
			'wcbt/filter/elementor/category',
			array(
				'wcbt-category'       => array(
					'title' => esc_html__( 'Woo Booster Toolkit', 'wcbt' ),
					'icon'  => 'fa fa-plug',
				),
			)
		);

		$old_categories = $elements_manager->get_categories();
		$categories     = array_merge( $categories, $old_categories );

		$set_categories = function ( $categories ) {
			$this->categories = $categories;
		};

		$set_categories->call( $elements_manager, $categories );
	}
}
