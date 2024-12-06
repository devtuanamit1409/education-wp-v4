<?php
if (! isset($data)) {
    return;
}

if ($data['is_my_wishlist']) {
    $class = 'wcbt-product-wishlist active';
} else {
    $class = 'wcbt-product-wishlist';
}
?>

<div class="<?php echo esc_attr($class); ?>"
   data-product-id="<?php echo esc_attr($data['product_id']); ?>"
   data-type="<?php echo esc_attr($data['wishlist_type']); ?>">
   <svg aria-hidden="true" class="icon icon-accordion color-foreground-text" focusable="false" height="18"
                viewBox="0 0 20 20" width="20" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 5.2393L8.5149 3.77392C6.79996 2.08174 4.01945 2.08174 2.30451 3.77392C0.589562 5.4661
                0.589563 8.2097 2.30451 9.90188L10 17.4952L17.6955 9.90188C19.4104 8.2097 19.4104 5.4661 17.6955
                3.77392C15.9805 2.08174 13.2 2.08174 11.4851 3.77392L10 5.2393ZM10.765 3.06343C12.8777 0.978857
                16.3029 0.978856 18.4155 3.06343C20.5282 5.148 20.5282 8.52779 18.4155 10.6124L10.72 18.2057C10.3224
                18.5981 9.67763 18.5981 9.27996 18.2057L1.58446 10.6124C-0.528154 8.52779 -0.528154 5.14801 1.58446
                3.06343C3.69708 0.978859 7.12233 0.978858 9.23495 3.06343L10 3.81832L10.765 3.06343Z"
                    fill-rule="evenodd" fill="none"></path>
        </svg>
    <?php
    if (!empty($data['wishlist_tooltip_text']) && !$data['is_my_wishlist']) {
        ?>
        <span class="tooltip"><?php echo esc_html($data['wishlist_tooltip_text']);?></span>
        <?php
    }

    if (!empty($data['wishlist_remove_tooltip_text']) && $data['is_my_wishlist']) {
	    ?>
        <span class="tooltip"><?php echo esc_html($data['wishlist_remove_tooltip_text']);?></span>
	    <?php
    }
    ?>
</div>
