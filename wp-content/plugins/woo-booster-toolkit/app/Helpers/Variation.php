<?php

namespace WCBT\Helpers;

class Variation { 
	public static function get_attibute_type( $attribute_name ) {
		$attribute_id = wc_attribute_taxonomy_id_by_name( $attribute_name );
		$attribute    = wc_get_attribute( $attribute_id );

		return $attribute->type;
	}

	public static function print_attribute_radio( $checked_value, $value, $label, $name, $term_id, $attribute_name ) {
		global $product;
		$input_name = wc_variation_attribute_name( $name );

		$input_value = $value;
		//added product ID at the end of the name to target single products
		$id             = $name . '_v_' . $value . $product->get_id();
		$checked        = checked( $checked_value, $value, false );
		$filtered_label = apply_filters( 'woocommerce_variation_option_name', $label, esc_attr( $name ) );
		$class_type     = '';
		$style_label    = '';

		$attribute_id   = wc_attribute_taxonomy_id_by_name( $attribute_name );
		$attribute      = wc_get_attribute( $attribute_id );
		$attribute_type = $attribute->type;

		$variation_type = get_option( 'wcbt-variation-type-' . $attribute_id, 'circle' );

		if ( ! empty( $term_id ) ) {
			$meta_data = get_term_meta( $term_id, WCBT_TERM_META_KEY, true );
			if ( ! empty( $meta_data ) && in_array( $attribute_type, array( 'color', 'image' ) ) ) {
				if ( $attribute_type === 'color' ) {
					$key         = 'product_attribute_color';
					$value       = $meta_data[ $key ] ?? '';
					$class_type  = 'att-type-color ' . $variation_type;
					$style_bg    = 'style="background:' . $value . '"';
					$style_label = '<label for="' . $id . '" ><span ' . $style_bg . '></span>' . $filtered_label . '</label>';
				} elseif ( $attribute_type === 'image' ) {
					$key        = 'product_attribute_image';
					$value      = $meta_data[ $key ] ?? '';
					$class_type = 'att-type-img ' . $variation_type;
					$img_url    = wp_get_attachment_image_url( $value, 'woocommerce_thumbnail' );
					if ( ! empty( $img_url ) ) {
						$style_label = '<label for="' . $id . '" ><img src="' . esc_url( $img_url ) . '" alt="">
                        <span class="att-type-tooltip">' . $filtered_label . '</span></label>';
					} else {
						$style_label = '<label for="' . $id . '" ><span>' . $filtered_label . '</span></label>';
					}
				}
			} else {
				$class_type  = 'att-type-text ' . $variation_type;
				$style_label = '<label for="' . $id . '" >' . $filtered_label . '</label>';
			}
		}

		?>
        <div class="<?php echo esc_attr( $class_type ); ?>">
            <input type="radio" name="<?php echo esc_attr( $input_name ); ?>"
                   value="<?php echo esc_attr( $input_value ); ?>"
                   id="<?php echo esc_attr( $id ); ?>" <?php echo $checked; ?>>
			<?php
			echo $style_label;
			?>
        </div>
		<?php
	}

	public static function get_data() {
		$data          = array(
			'mode'                    => Settings::get_setting_detail( 'variation:fields:enable' ),
			'display_on_product_list' => Settings::get_setting_detail( 'variation:fields:display_on_product_list' ),
			'show_selected_name'      => Settings::get_setting_detail( 'variation:fields:show-selected-name' ),
		);

		return apply_filters( 'wcbt/filter/variation/data', $data );
	}
}
