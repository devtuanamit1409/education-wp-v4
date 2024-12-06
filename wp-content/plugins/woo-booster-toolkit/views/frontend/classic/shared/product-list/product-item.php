<?php

use WCBT\Helpers\Settings;
use WCBT\Helpers\Template;
use WCBT\Models\ProductModel;

$product_id = get_the_ID();
$data       = ProductModel::get_product_data( $product_id );
$template   = Template::instance();
?>
<div class="wcbt-product-item" data-product-id="<?php echo esc_attr( $product_id ); ?>">
    <div class="wcbt-product-header">
        <a href="<?php echo esc_attr( $data['permalink'] ); ?>">
            <img src="<?php echo esc_attr( $data['thumbnail_url'] ); ?>"
                 alt="<?php echo esc_attr( $data['title'] ); ?>">
        </a>
    </div>
    <div class="wcbt-product-body">
        <div class="wwcbt-product-item-title">
            <a href="<?php echo esc_attr( $data['permalink'] ); ?>"><?php echo esc_html( $data['title'] ); ?></a>
        </div>
		<?php
		if ( Settings::get_setting_detail( 'wishlist:fields:enable' ) === 'on' ) {
			$sections[] = 'wishlist/wishlist-btn.php';
		}

		if ( Settings::get_setting_detail( 'compare:fields:enable' ) === 'on' ) {
			$sections[] = 'compare-product/compare-btn.php';
		}

		if ( Settings::get_setting_detail( 'quick-view:fields:enable' ) === 'on' ) {
			$sections[] = 'quick-view/quick-view-btn.php';
		}

		$template->get_frontend_templates_type_classic( $sections, compact( 'data' ) );
		?>
    </div>
</div>
