<?php
if (! isset($data)) {
    return;
}

use WCBT\Helpers\Template;

$template = Template::instance();
?>
<div class="wcbt-wishlist-content">
    <?php
    $sections = array(
        'shared/product-list/product-container.php',
        'shared/pagination/index.php',
    );

    $template->get_frontend_templates_type_classic($sections, compact('data'));
    ?>
</div>

