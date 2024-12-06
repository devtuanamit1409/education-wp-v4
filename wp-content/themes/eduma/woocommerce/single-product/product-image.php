<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.0.0
 */

defined( 'ABSPATH' ) || exit;

global $post, $woocommerce, $product;

$columns           = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );
$post_thumbnail_id = $product->get_image_id();
$wrapper_classes   = apply_filters(
	'woocommerce_single_product_image_gallery_classes',
	array(
		'woocommerce-product-gallery',
		'woocommerce-product-gallery--' . ( $post_thumbnail_id ? 'with-images' : 'without-images' ),
		'woocommerce-product-gallery--columns-' . absint( $columns ),
		'images',
	)
);

$product_thumbnail = '';
if ( $product->get_image_id() ) {
	$image_title      = esc_attr( get_the_title( get_post_thumbnail_id() ) );
	$image_link       = wp_get_attachment_url( get_post_thumbnail_id() );
	$image            = get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), array(
		'title' => $image_title
	) );
	$product_thumbnail            = apply_filters( 'woocommerce_single_product_image_html', sprintf( '<a href="%s" class="thim-image-popup" title="%s" data-elementor-open-lightbox="no">%s</a>', esc_url( $image_link ), esc_attr( $image_title ), $image ), $post->ID );
}
wp_enqueue_script( 'flexslider' );
wp_enqueue_script( 'magnific-popup');

?>
<div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>" style="opacity: 0; transition: opacity .25s ease-in-out;">

<div id="slider" class="flexslider">
	<ul class="slides images">
		<?php
		if ( has_post_thumbnail() && $product_thumbnail ) {
			echo '<li class="main_product_thumbnai woocommerce-product-gallery__image">';
			echo ent2ncr($product_thumbnail);
			echo '</li>';
		}

		$attachment_ids = $product->get_gallery_image_ids();
		$loop           = 0;
		foreach ( $attachment_ids as $attachment_id ) {
			$image_link = wp_get_attachment_url( $attachment_id );
			if ( ! $image_link ) {
				continue;
			}
			$classes[]   = 'image-' . $attachment_id;
			$image       = wp_get_attachment_image( $attachment_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );
			$image_class = esc_attr( implode( ' ', $classes ) );
			$image_title = esc_attr( get_the_title( $attachment_id ) );
			echo '<li class="woocommerce-product-gallery__image">';
			echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<a href="%s" class="thim-image-popup" title="%s" data-elementor-open-lightbox="no">%s</a>', esc_url( $image_link ), esc_attr( $image_title ), $image ), $post->ID );
			echo '</li>';
			$loop ++;
		} ?>
	</ul><!-- .slides -->
</div><!-- #slider -->

<?php do_action( 'woocommerce_product_thumbnails' ); ?>
</div>
