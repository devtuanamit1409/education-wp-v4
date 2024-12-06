<?php

use WCBT\Helpers\Compare;

if (! isset($data['compare_page_url'])) {
    return;
}

do_action('wcbt/layout/compare-product/compare-link/before', $data);
?>

    <div class="<?php echo esc_attr(
        apply_filters('wcbt/filter/compare-product/compare-link/wrapper-class', 'wcbt-show-compare')
    ); ?>">
        <span class="count"><?php echo esc_html(Compare::get_count()); ?></span>
        <a href="<?php echo esc_url($data['compare_page_url']); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16">
                <path fill="#444" d="M16 5v2H3v2L0 6l3-3v2zM0 12v-2h13V8l3 3-3 3v-2z"></path>
            </svg>
        </a>
    </div>

<?php
do_action('wcbt/layout/compare-product/compare-link/after', $data);

