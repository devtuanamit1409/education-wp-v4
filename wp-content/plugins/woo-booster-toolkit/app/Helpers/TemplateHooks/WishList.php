<?php

namespace WCBT\Helpers\TemplateHooks;

use WCBT\Helpers\Template;

class WishList
{
    public $template;

    public static function instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }

    protected function __construct()
    {
        $this->template = Template::instance();
        add_action('wcbt/layout/wishlist/container/before', array( $this, 'section_before_container' ));
        add_action('wcbt/layout/wishlist', array( $this, 'section_layout' ));
        add_action('wcbt/layout/quickview/single-price/before',array( $this, 'wcbt_quick_view_open_div' ));
        add_action('wcbt/layout/quickview/single-price/after',array( $this, 'wcbt_quick_view_on_sale' ),10);
        add_action('wcbt/layout/quickview/single-price/after',array( $this, 'wcbt_quick_view_close_div'),20);
    }

    /**
     * @param array $data
     *
     * @return void
     */
    public function section_before_container(array $data)
    {
        $sections = apply_filters(
            'wcbt/filter/wishlist/container/before',
            array(
                'shared/breadcrumb.php',
//                'wishlist/section/title.php',
            )
        );

        $this->template->get_frontend_templates_type_classic($sections, compact('data'));
    }

    public function section_layout(array $data){
        $sections = apply_filters(
            'wcbt/filter/wishlist/sections',
            array(
                'wishlist/section/content.php',
            )
        );

        $this->template->get_frontend_templates_type_classic($sections, compact('data'));
    }
    public function wcbt_quick_view_open_div(){
        echo '<div class="product_price_on">';
    }
    public function wcbt_quick_view_close_div(){
        echo '</div>';
    }

    public function wcbt_quick_view_on_sale(){
		global $product;
		if(!$product){
			return;
		}
		if ( $product->is_on_sale() ) :
			$percentage = '';
			if ( $product->get_type() == 'variable'  ) {

				$available_variations = $product->get_variation_prices();
				$max_percentage       = 0;

				foreach ( $available_variations['regular_price'] as $key => $regular_price ) {
					$sale_price = $available_variations['sale_price'][$key];

					if ( $sale_price < $regular_price ) {
						$percentage = round( ( ( (float) $regular_price - (float) $sale_price ) / (float) $regular_price ) * 100 );

						if ( $percentage > $max_percentage ) {
							$max_percentage = $percentage;
						}
					}
				}

				$percentage = $max_percentage;
			} elseif ( ( $product->get_type() == 'simple' || $product->get_type() == 'external' || $product->get_type() == 'variation' )) {
				$percentage = round( ( ( (float) $product->get_regular_price() - (float) $product->get_sale_price() ) / (float) $product->get_regular_price() ) * 100 );
			}
			if ( $percentage ) {
				echo '<div class="wcbt-onsale-wp"><span class="wcbt-onsale">' . sprintf( _x( 'Sale  %d%%', 'sale percentage', 'wcbt' ), $percentage ) . '</span></div>';
			}
		endif;
	}

}
