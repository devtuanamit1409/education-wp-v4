<?php

use WCBT\Helpers\Settings;
use WCBT\Helpers\Template;
use WCBT\Models\ProductModel;

$product_id = get_the_ID();
$data       = ProductModel::get_product_data( $product_id );
$template   = Template::instance();
?>
<tr data-product-id="<?php echo esc_attr( $product_id ); ?>">
    <td>
        <a class="product-remove" href="#">&times;</a>
    </td>
    <td>
        <img src="<?php echo esc_attr( $data['thumbnail_url'] ); ?>"
             alt="<?php echo esc_attr( $data['title'] ); ?>">
        <a href="<?php echo esc_attr( $data['permalink'] ); ?>">
			<?php echo esc_html( $data['title'] ); ?>
        </a>
    </td>
    <td>
		<?php echo $data['price_html']; ?>
    </td>
    <td>
		<?php
		woocommerce_template_loop_add_to_cart(
			array(
				'quantity' => 1,
			)
		);
		?>

    </td>
</tr>
