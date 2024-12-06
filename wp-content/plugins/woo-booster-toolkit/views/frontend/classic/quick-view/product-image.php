<?php
global $product;

$columns           = apply_filters('woocommerce_product_thumbnails_columns', 4);
$post_thumbnail_id = $product->get_image_id();

$wrapper_classes   = apply_filters(
    'wcbt/filter/quick-view/product-imgae/wrapper-class',
    array(
        'woocommerce-product-gallery',
        'woocommerce-product-gallery--' . ( $post_thumbnail_id ? 'with-images' : 'without-images' ),
        'woocommerce-product-gallery--columns-' . absint($columns),
        'images',
    )
);
?> 
<div class="<?php echo esc_attr(implode(' ', array_map('sanitize_html_class', $wrapper_classes))); ?>"
     data-columns="<?php echo esc_attr($columns); ?>" style="opacity: 0; transition: opacity .25s ease-in-out;">
    <?php

    if ($post_thumbnail_id) {
        $attachment_ids = $product->get_gallery_image_ids();
        $wcbt_view_img_size    = apply_filters( 'wcbt_product_thumbnails_size', 'thumbnail' );
        if (empty($attachment_ids)) {
            echo wc_get_gallery_image_html($post_thumbnail_id, true);
        } else {
            if ($product->get_image_id()) {
                $attachment_ids = array_merge(array($product->get_image_id()), $attachment_ids);
            }
            ?>
            <div class="woocommerce wcbt-quick-view-img">
                <?php
                if ($attachment_ids) {
                    foreach ($attachment_ids as $attachment_id) {
                        $html = wc_get_gallery_image_html($attachment_id, true);
                        ?>
                        <div class="wcbt-quick-view-single">
                            <?php
                            echo apply_filters(
                                'woocommerce_single_product_image_thumbnail_html',
                                $html,
                                $attachment_id
                            );
                            ?>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
            <div class="wcbt-quick-view-thumbnails">
                <?php
                if ($attachment_ids) {
                    $wcbt_thumbnail_size    = apply_filters( 'wcbt_product_thumbnails_size', 'thumbnail' );
                    foreach ($attachment_ids as $attachment_id) {
                        ?>
                        <div class="wcbt-quick-thumb-single" data-image-id="<?php echo esc_attr($attachment_id);?>">
                            <?php
                            echo wp_get_attachment_image($attachment_id,$wcbt_thumbnail_size,false, "", array( "alt" => get_the_title()) );
                            ?>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
            <?php
        }
    } else {
        ?>
        <div class="woocommerce-product-gallery__image--placeholder">
            <img src="<?php echo esc_url(wc_placeholder_img_src('woocommerce_single')); ?>"
                 alt="<?php echo esc_html__('Awaiting product image', 'wcbt'); ?>" class="wp-post-image"/>
        </div>
        <?php
    }
    ?>
</div>
