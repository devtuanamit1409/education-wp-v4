<div class="wcbt-quick-view-overlay"></div>
<div class="wcbt-quick-view-container">
    <div class="close">
        <svg width="30px" height="30px" viewBox="0 0 36 36" preserveAspectRatio="xMidYMid meet"
             xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
            <title>remove-solid</title>
            <path class="clr-i-solid clr-i-solid-path-1"
                  d="M18,2A16,16,0,1,0,34,18,16,16,0,0,0,18,2Zm8,22.1a1.4,1.4,0,0,1-2,2l-6-6L12,
                    26.12a1.4,1.4,0,1,1-2-2L16,18.08,9.83,11.86a1.4,1.4,0,1,1,2-2L18,16.1l6.17-6.17a1.4,
                    1.4,0,1,1,2,2L20,18.08Z"></path>
            <rect x="0" y="0" width="30" height="30" fill-opacity="0"/>
        </svg>
    </div>
    <div class="<?php echo esc_attr( apply_filters( 'wcbt/filter/quick-view/inner/wrapper', 'wcbt-quick-view-content-wrapper' ) ); ?>">
        <div id="product-<?php the_ID(); ?>" <?php post_class( 'product' ); ?>>
            <div class="<?php echo esc_attr( apply_filters( 'wcbt/filter/quick-view/inner/class', 'wcbt-quick-view-inner' ) ); ?>">
                <div class="wcbt-quickview-content woocommerce">
                    <div class="product">
						<?php
						do_action( 'wcbt_quickview_product_image' );
						?>
                        <div class="summary entry-summary">
                            <div class="summary-content">
								<?php
								//                            Remove this hook
								//							do_action( 'woocommerce_single_product_summary' );
								woocommerce_template_single_title();
								woocommerce_template_single_rating();
								do_action( 'wcbt/layout/quickview/single-price/before' );
								woocommerce_template_single_price();
								do_action( 'wcbt/layout/quickview/single-price/after' );
								woocommerce_template_single_excerpt();
								//                                woocommerce_template_single_add_to_cart();

								global $product;
								if ( $product->get_type() ) {
									if ( $product->get_type() === 'simple' ) {
										woocommerce_simple_add_to_cart();
									}
									if ( $product->get_type() === 'grouped' ) {
										woocommerce_grouped_add_to_cart();
									}
									if ( $product->get_type() === 'variable' ) {
										woocommerce_variable_add_to_cart();
									}
									if ( $product->get_type() === 'external' ) {
										woocommerce_external_add_to_cart();
									}
								}

								do_action( 'wcbt/layout/quickview/add-to-cart/after' );
								woocommerce_template_single_meta();
								do_action( 'wcbt/layout/quickview/single-meta/after' );
								?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
