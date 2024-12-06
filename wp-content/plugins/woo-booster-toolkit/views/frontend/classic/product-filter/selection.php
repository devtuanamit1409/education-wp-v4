<?php

use WCBT\Models\ProductAttributeModel;

if ( ! isset( $data ) ) {
	return;
}
$has_filter_field = false;
?>
<div class="wcbt-product-filter-selection wrapper">
    <ul class="list">
		<?php
		//Category
		$categories = isset( $_GET['category'] ) ? json_decode( urldecode( $_GET['category'] ) ) : array();

		if ( empty( $categories ) || ! is_array( $categories ) ) {
			$categories = array();
		} else {
			$has_filter_field = true;
		}

		foreach ( $categories as $category ) {
			$term = get_term( $category );
			?>
            <li class="list-item" data-field="category" data-value="<?php echo esc_attr( $category ); ?>">
                <span class="title"><?php echo esc_html( $term->name ); ?></span>
                <span class="remove"><i class="xmark-solid"></i></span>
            </li>
			<?php
		}

		//Availability
		$availabilities = isset( $_GET['availability'] ) ? json_decode( urldecode( $_GET['availability'] ) ) : array();

		if ( empty( $availabilities ) || ! is_array( $availabilities ) ) {
			$availabilities = array();
		} else {
			$has_filter_field = true;
		}

		foreach ( $availabilities as $availability ) {
			$availability_name = '';
			if ( $availability === 'in-stock' ) {
				$availability_name = 'In stock';
			}

			if ( $availability === 'out-stock' ) {
				$availability_name = 'Out of stock';
			}
			?>
            <li class="list-item" data-field="availability" data-value="<?php echo esc_attr( $availability ); ?>">
                <span class="title"><?php echo esc_html( $availability_name ); ?></span>
                <span class="remove"><i class="xmark-solid"></i></span>
            </li>
			<?php
		}

		//Rating
		$ratings = isset( $_GET['rating'] ) ? json_decode( urldecode( $_GET['rating'] ) ) : array();
		if ( empty( $ratings ) || ! is_array( $ratings ) ) {
			$ratings = array();
		} else {
			$has_filter_field = true;
		}

		foreach ( $ratings as $rating ) {
			$rating_name = '';

			if ( $rating === '1' ) {
				$rating_name = '1 star';
			}

			if ( $rating === '2' ) {
				$rating_name = '2 stars';
			}

			if ( $rating === '3' ) {
				$rating_name = '3 stars';
			}

			if ( $rating === '4' ) {
				$rating_name = '4 stars';
			}

			if ( $rating === '5' ) {
				$rating_name = '5 stars';
			}

			?>
            <li class="list-item" data-field="rating" data-value="<?php echo esc_attr( $rating ); ?>">
                <span class="title"><?php echo esc_html( $rating_name ); ?></span>
                <span class="remove"><i class="xmark-solid"></i></span>
            </li>
			<?php
		}

		//Price
		$min_price = $_GET['min_price'] ?? '';
		$max_price = $_GET['max_price'] ?? '';

		if ( $min_price && $max_price ) {
			$price_value = $min_price . '-' . $max_price;
			$price_name  = wc_price( $min_price ) . ' - ' . wc_price( $max_price );
			?>
            <li class="list-item" data-field="price" data-value="<?php echo esc_attr( $price_value ); ?>">
                <span class="title"><?php echo $price_name; ?></span>
                <span class="remove"><i class="xmark-solid"></i></span>
            </li>
			<?php
			$has_filter_field = true;
		}

		//Attributes
		$attributes = ProductAttributeModel::get_attribute_taxonomies();
		foreach ( $attributes as $attribute ) {
			$name          = $attribute->attribute_name;
			$attr_term_ids = isset( $_GET[ $name ] ) ? json_decode( urldecode( $_GET[ $name ] ) ) : array();

			if ( empty( $attr_term_ids ) || ! is_array( $attr_term_ids ) ) {
				$attr_term_ids = array();
			} else {
				$has_filter_field = true;
			}

			foreach ( $attr_term_ids as $term_id ) {
				$term = get_term( $term_id );
				?>
                <li class="list-item" data-field="attribute" data-attribute-type="<?php echo esc_attr( $name ); ?>"
                    data-value="<?php echo esc_attr( $term_id ); ?>">
                    <span class="title"><?php echo esc_html( $term->name ); ?></span>
                    <span class="remove"><i class="xmark-solid"></i></span>
                </li>
				<?php
			}
		}
		?>
    </ul>
	<?php
	if ( $has_filter_field === true ) {
		?>
        <button type="button" class="clear"><?php esc_html_e( 'Clear All', 'wcbt' ); ?></button>
		<?php
	}
	?>
</div>
