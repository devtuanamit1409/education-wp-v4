<?php
$data = array();

use WCBT\Helpers\Template;

$template = Template::instance();

do_action('wcbt/layout/wishlist/before', $data);
?>
    <div id="wcbt-wishlist">
        <?php
        do_action('wcbt/layout/wishlist/container/before', $data);
        ?>
        <div class="wcbt-container">
            <?php
            do_action('wcbt/layout/wishlist', $data);
            ?>
        </div>
        <?php
        do_action('wcbt/layout/wishlist/container/after', $data);
        ?>
    </div>
<?php
do_action('wcbt/layout/wishlist/after', $data);
