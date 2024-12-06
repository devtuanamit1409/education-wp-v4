<?php
/**
 * Variable product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/variable.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 6.1.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $attributes ) || ! isset( $available_variations ) ) {
	return;
}

use WCBT\Helpers\Variation;

global $product;
global $woocommerce;

$attribute_keys  = array_keys( $attributes );
$variations_json = wp_json_encode( $available_variations );
$variations_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) :
	_wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );

do_action( 'woocommerce_before_add_to_cart_form' ); ?>
    <form class="variations_form cart" action="<?php echo esc_url( apply_filters(
		'woocommerce_add_to_cart_form_action',
		$product->get_permalink()
	) ); ?>" method="post" enctype='multipart/form-data' data-product_id="
        <?php echo absint( $product->get_id() ); ?>" data-product_variations="<?php echo $variations_attr; ?>">
		<?php do_action( 'woocommerce_before_variations_form' ); ?>

		<?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
            <p class="stock out-of-stock">
				<?php
				echo esc_html( apply_filters(
					'woocommerce_out_of_stock_message',
					__( 'This product is currently out of stock and unavailable.', 'wcbt' )
				) );
				?>
            </p>
		<?php else : ?>
            <table class="variations" cellspacing="0" role="presentation">
                <tbody>
				<?php
				foreach ( $attributes as $attribute_name => $options ) :
					//Custom variation
					$wc_type_attr = Variation::get_attibute_type( $attribute_name );
					$class_attr_type_select = ( ! empty( $wc_type_attr ) && $wc_type_attr == 'select' ) ?
						'td-attr-type-select' : '';

					$sanitized_name = sanitize_title( $attribute_name );
					?>
                    <tr class="attribute-<?php echo esc_attr( $sanitized_name ); ?>
                        wc-type-<?php echo esc_attr( $wc_type_attr ); ?>">
                        <td class="label"><label for="<?php echo esc_attr( $sanitized_name ); ?>">
								<?php echo wc_attribute_label( $attribute_name ); ?></label>
                        </td>
						<?php
						if ( isset( $_REQUEST[ 'attribute_' . $sanitized_name ] ) ) {
							$checked_value = $_REQUEST[ 'attribute_' . $sanitized_name ];
						} elseif ( isset( $selected_attributes[ $sanitized_name ] ) ) {
							$checked_value = $selected_attributes[ $sanitized_name ];
						} else {
							$checked_value = '';
						}
						?>
                        <td class="value <?php echo $class_attr_type_select; ?>">
							<?php
							if ( isset( $wc_type_attr ) && $wc_type_attr == 'select' ) : ?>
                            <div class="attr-type-select">
                                <div class="att-type-text type-select-default actived">
                                    <label><?php esc_attr_e( 'Choose an option', 'wcbt' ) ?></label>
                                </div>
								<?php
								endif;
								if ( ! empty( $options ) ) {
									if ( taxonomy_exists( $attribute_name ) ) {
										if ( in_array( $wc_type_attr, array( 'text', 'color', 'image', 'select' ) ) ) {
											// Get terms if this is a taxonomy - ordered. We need the names too.
											$terms = wc_get_product_terms(
												$product->get_id(),
												$attribute_name,
												array( 'fields' => 'all' )
											);
											foreach ( $terms as $term ) {
												if ( ! in_array( $term->slug, $options ) ) {
													continue;
												}
												Variation::print_attribute_radio(
													$checked_value,
													$term->slug,
													$term->name,
													$sanitized_name,
													$term->term_id,
													$attribute_name
												);
											}
										} else {
											wc_dropdown_variation_attribute_options(
												array(
													'options'   => $options,
													'attribute' => $attribute_name,
													'product'   => $product,
												)
											);
										}
									} else {
										foreach ( $options as $option ) {
											Variation::print_attribute_radio(
												$checked_value,
												$option,
												$option,
												$sanitized_name,
												'',
												$attribute_name
											);
										}
									}
								}
								if ( isset( $wc_type_attr ) && $wc_type_attr == 'select' ) :
									echo '</div>';
								endif;
								echo end( $attribute_keys ) === $attribute_name ? wp_kses_post(
									apply_filters(
										'woocommerce_reset_variations_link',
										'<a class="reset_variations" href="#">' . esc_html__( 'Clear', 'wcbt' ) . '</a>'
									)
								) : '';
								?>
                        </td>
                    </tr>
				<?php endforeach; ?>
                </tbody>
            </table>
			<?php do_action( 'woocommerce_after_variations_table' ); ?>

            <div class="single_variation_wrap">
				<?php
				/**
				 * Hook: woocommerce_before_single_variation.
				 */
				do_action( 'woocommerce_before_single_variation' );

				/**
				 * Hook: woocommerce_single_variation. Used to output the cart button and placeholder for variation
				 * data.
				 *
				 * @since 2.4.0
				 * @hooked woocommerce_single_variation - 10 Empty div for variation data.
				 * @hooked woocommerce_single_variation_add_to_cart_button - 20 Qty and cart button.
				 */
//				do_action( 'woocommerce_single_variation' );

				woocommerce_single_variation();
				woocommerce_single_variation_add_to_cart_button();

				/**
				 * Hook: woocommerce_after_single_variation.
				 */
				do_action( 'woocommerce_after_single_variation' );
				?>
            </div>
		<?php endif; ?>

		<?php do_action( 'woocommerce_after_variations_form' ); ?>
    </form>

<?php
do_action( 'woocommerce_after_add_to_cart_form' );
