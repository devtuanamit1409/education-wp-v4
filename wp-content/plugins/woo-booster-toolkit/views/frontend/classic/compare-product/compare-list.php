<?php
use WCBT\Helpers\General;

if(!isset($data['product_ids'])){
    return;
}

$product_ids = $data['product_ids'];

?>
<div class="wcbt-compare-box">
    <?php

    if (empty($product_ids)) {
        ?>
        <span class="notice"><?php esc_html_e('No product found.', 'wcbt'); ?></span>
        <?php
        return;
    }

    if (is_string($product_ids)) {
        $product_ids = explode(',', $product_ids);
    }
    ?>
    <table>
        <thead>
        <tr>
            <th><?php esc_html_e('Products', 'wcbt');?></th>
            <?php
            foreach ($product_ids as $product_id) {
                $image_url = get_the_post_thumbnail_url($product_id);

                if (empty($image_url)) {
                    $image_url = General::get_default_image();
                }
                ?>
                <th>
                    <div class="remove" title="<?php esc_attr_e('Remove', 'wcbt'); ?>"
                         data-product-id="<?php echo esc_attr($product_id); ?>">
                        <svg class="icon-close" width="15" height="15" viewBox="0 0 16 17" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M8.91788 8.47292L15.8102 1.51213C15.9318 1.38884 16 1.22196 15.9999
                            1.04803C15.9997 0.874104 15.9313 0.707312 15.8096 0.584187C15.5659 0.339537
                            15.1366 0.338301 14.8905 0.585423L8.00001 7.54622L1.10709 0.583569C0.862164
                            0.339537 0.432926 0.340772 0.189222 0.584805C0.128732 0.645593 0.0808472 0.717927
                            0.0483617 0.797588C0.0158762 0.877248 -0.000559128 0.962638 1.45145e-05
                            1.04878C1.45145e-05 1.22423 0.06737 1.38857 0.189222 1.51027L7.08152 8.4723L0.189834
                            15.435C0.0682183 15.5584 0.0001119 15.7255 0.000456277 15.8996C0.000800655
                            16.0737 0.0695678 16.2405 0.191672 16.3635C0.30985 16.4815 0.477014 16.5495
                            0.649688 16.5495H0.653362C0.82665 16.5489 0.993814 16.4803 1.10954 16.361L8.00001
                            9.40025L14.8929 16.3629C15.0148 16.4852 15.1777 16.5532 15.3503 16.5532C15.4357 16.5534
                            15.5203 16.5366 15.5992 16.5038C15.6782 16.4709 15.7499 16.4226 15.8103
                            16.3617C15.8706 16.3008 15.9185 16.2285 15.951 16.1488C15.9836 16.0692
                            16.0002 15.9838 16 15.8977C16 15.7228 15.9326 15.5579 15.8102 15.4362L8.91788
                            8.47292Z" fill="black"></path>
                        </svg></div>
                    <a href="<?php echo esc_url_raw(get_permalink($product_id)); ?>">
                        <img src="<?php echo esc_url_raw($image_url); ?>"
                             alt="<?php echo esc_attr(get_the_title($product_id)); ?>">
                    </a>
                </th>
                <?php
            }
            ?>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>
                <strong><?php esc_html_e('Title', 'wcbt'); ?></strong>
            </td>
            <?php
            foreach ($product_ids as $product_id) {
                ?>
                <td>
                    <a href="<?php echo esc_url_raw(get_permalink($product_id)); ?>">
	                    <?php echo esc_html(get_the_title($product_id)); ?>
                    </a>
                <?php
            }
            ?>
        </tr>
        <tr>
            <td>
                <strong><?php esc_html_e('Price', 'wcbt'); ?></strong>
            </td>
            <?php

            foreach ($product_ids as $product_id) {
                $product = wc_get_product($product_id);
                ?>
                <td><?php echo $product->get_price_html(); ?></td>
                <?php
            }
            ?>
        </tr>
        <tr>
            <td>
                <strong><?php esc_html_e('Description', 'wcbt'); ?></strong>
            </td>
            <?php

            foreach ($product_ids as $product_id) {
                $product = wc_get_product($product_id);
                ?>
                <td>
                    <?php
                    echo General::ksesHTML(
                        apply_filters('woocommerce_short_description', $product->get_short_description())
                    );
                    ?>
                </td>
                <?php
            }
            ?>
        </tr>
        <tr>
            <td>
                <strong><?php esc_html_e('SKU', 'wcbt'); ?></strong>
            </td>
            <?php

            foreach ($product_ids as $product_id) {
                $product = wc_get_product($product_id);
                $sku     = $product->get_sku();
                if (! $sku) {
                    $sku = '';
                }
                ?>
                <td><?php echo esc_html($sku); ?></td>
                <?php
            }
            ?>
        </tr>
        <tr>
            <td>
                <strong><?php esc_html_e('Stock', 'wcbt'); ?></strong>
            </td>
            <?php

            foreach ($product_ids as $product_id) {
                $product      = wc_get_product($product_id);
                $availability = $product->get_availability();

                if (empty($availability['availability'])) {
                    $stock = __('In stock', 'wcbt');
                } else {
                    $stock = $availability['availability'];
                }
                ?>
                <td><?php echo esc_html($stock); ?></td>
                <?php
            }
            ?>
        </tr>
        <tr>
            <td>
                <strong><?php esc_html_e('Weight', 'wcbt'); ?></strong>
            </td>
            <?php

            foreach ($product_ids as $product_id) {
                $product = wc_get_product($product_id);
                $weight  = $product->get_weight();
                $weight  = $weight ? ( wc_format_localized_decimal($weight) . ' ' .
                                       esc_attr(get_option('woocommerce_weight_unit')) ) : '-';
                ?>
                <td><?php echo esc_html($weight); ?></td>
                <?php
            }
            ?>
        </tr>
        <tr>
            <td>
                <strong><?php esc_html_e('Dimension', 'wcbt'); ?></strong>
            </td>
            <?php

            foreach ($product_ids as $product_id) {
                $product    = wc_get_product($product_id);
                $dimensions = wc_format_dimensions($product->get_dimensions(false));
                if (! $dimensions) {
                    $dimensions = '-';
                }

                ?>
                <td><?php echo esc_html($dimensions); ?></td>
                <?php
            }
            ?>
        </tr>
        </tbody>
        <?php
        do_action('wcbt/layout/compare-product/fields/after', $product_ids);
        ?>
    </table>
</div>
