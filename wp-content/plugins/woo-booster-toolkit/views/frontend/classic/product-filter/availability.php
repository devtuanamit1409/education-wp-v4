<?php
if ( ! isset( $data ) ) { 
	return;
}
$show_title = isset($data['show_title']) ? $data['show_title'] : 'yes';
$value = isset( $_GET['availability'] ) ? json_decode( urldecode( $_GET['availability'] ) ) : array();
if ( empty( $value ) || ! is_array($value) ) { 
	$value = array();
}
?> 
<div class="availability wrapper <?php if(!empty($data['extra_class'])){ echo $data['extra_class'];};?>">
    <?php  if ( $show_title == 'yes' ) { ?>
        <div class="item-filter-heading"><?php esc_html_e( 'Availability', 'wcbt' ); ?></div> 
    <?php } ?>
    <div class="availability-content wrapper-content">
        <?php
        $checked = in_array( 'in-stock', $value ); 
        ?>
        <div class="item">
            <input id="availability-in-stock" type="checkbox" value="in-stock" <?php checked($checked, true, true) ?>>
            <label for="availability-in-stock">
                <span><?php esc_html_e('In stock', 'wcbt'); ?></span>
            </label>
        </div>

	    <?php
	    $checked = in_array( 'out-stock', $value );
	    ?>
        <div class="item">
            <input id="availability-out-stock" type="checkbox" value="out-stock" <?php checked($checked, true, true) ?>>
            <label for="availability-out-stock">
                <span><?php esc_html_e('Out of stock', 'wcbt'); ?></span>
            </label>
        </div>
    </div>
</div>
