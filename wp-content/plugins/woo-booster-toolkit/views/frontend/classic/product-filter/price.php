<?php
if ( ! isset( $data ) ) {
	return;
}
$show_title = isset($data['show_title']) ? $data['show_title'] : 'yes'; 
$min_price_setting  = $data['min_price'] ?? 0;
$max_price_setting  = $data['max_price'] ?? 250;
$step_price_setting = $data['step_price'] ?? 1;

$min_price_value = $_GET['min_price'] ?? $min_price_setting;
$max_price_value = $_GET['max_price'] ?? $max_price_setting;
$custom_style = "";
if(!empty($data['width'])){
	$custom_style .= 'style="width:'.$data['width'].'%"';
}
?>
<div class="price wrapper <?php if(!empty($data['extra_class'])){ echo $data['extra_class'];};?>" data-min="<?php echo esc_attr( $min_price_setting ); ?>"
     data-max="<?php echo esc_attr( $max_price_setting ); ?>"
     data-step="<?php echo esc_attr( $step_price_setting ); ?>"  <?php echo $custom_style;?>>
    <?php  if ( $show_title == 'yes' ) { ?>
        <div class="item-filter-heading"><?php esc_html_e( 'Price', 'wcbt' ); ?></div>
    <?php } ?>
    <input type="hidden" id="min-price" name="min-price" value="<?php echo esc_attr( $min_price_value ); ?>">
    <input type="hidden" id="max-price" name="max-price" value="<?php echo esc_attr( $max_price_value ); ?>">
    <div class="search-price wrapper-content" >
        <div class="show-price">
            <span class="min"><?php echo wc_price( $min_price_value ); ?></span>
            -
            <span class="max"><?php echo wc_price( $max_price_value ); ?></span>
        </div>
        <div id="search-price"></div>
    </div>
</div>
