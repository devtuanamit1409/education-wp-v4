<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } 
global $foxtool_options;
# thay doi nut mua hang ở trang xem sản phẩm
if(!empty($foxtool_options['woo-text1'])){
function foxtool_sing_to_cart_text(){
	global $foxtool_options;
	$name = !empty($foxtool_options['woo-text1']) ? $foxtool_options['woo-text1'] : null;
    return $name;	
}
add_filter( 'woocommerce_product_single_add_to_cart_text', 'foxtool_sing_to_cart_text' ); 
}
# thay doi nut mua hang ở trang xem sản phẩm
if(!empty($foxtool_options['woo-text2'])){
function foxtool_pro_to_cart_text(){
	global $foxtool_options;
	$name = !empty($foxtool_options['woo-text2']) ? $foxtool_options['woo-text2'] : null;
    return $name;
}
add_filter( 'woocommerce_product_add_to_cart_text', 'foxtool_pro_to_cart_text' ); 
}
# liên hệ thay cho 0đ
if(!empty($foxtool_options['woo-text3'])){
function foxtool_product_zero( $price, $product ) {
	global $foxtool_options;
    if ( $product->get_price() == 0 ) {
        if ( $product->is_on_sale() && $product->get_regular_price() ) {
            $regular_price = wc_get_price_to_display( $product, array( 'qty' => 1, 'price' => $product->get_regular_price() ) );
            $price = wc_format_price_range( $regular_price, __( 'Free!', 'woocommerce' ) );
        } else {
			$name = !empty($foxtool_options['woo-text3']) ? $foxtool_options['woo-text3'] : null;
            $price = '<span class="woozero">' . $name . '</span>';
        }
    }
    return $price;
}
add_filter( 'woocommerce_get_price_html', 'foxtool_product_zero', 10, 2 );
}
# liên hệ thay cho hết hàng
if(!empty($foxtool_options['woo-text4'])){
function foxtool_product_end( $price, $product ) {
	global $foxtool_options;
    if ( !is_admin() && !$product->is_in_stock()) {
	   $name = !empty($foxtool_options['woo-text4']) ? $foxtool_options['woo-text4'] : null;
       $price = '<span class="woozero">' . $name . '</span>';
    }
    return $price;
}
add_filter( 'woocommerce_get_price_html', 'foxtool_product_end', 99, 2 );
}
# chuyen don vi tien te
function foxtool_currency_symbol( $currency_symbol, $currency ) {
    global $foxtool_options;
    if ( isset( $foxtool_options['woo-cy1'] ) && !empty( $foxtool_options['woo-cy1'] ) ) {
        $current_currency = get_option('woocommerce_currency');
        if ( $currency === $current_currency ) {
            $currency_symbol = $foxtool_options['woo-cy1'];
        }
    }
    return $currency_symbol;
}
add_filter('woocommerce_currency_symbol', 'foxtool_currency_symbol', 10, 2);
# thông báo telegram khi có đơn hàng
if (isset($foxtool_options['woo-tele1'])){
function foxtool_woo_telegram($order_id) {
    global $foxtool_options;
    if (!$order_id) return;
    $site = get_site_url();
    $order = wc_get_order($order_id);
    $order_data = $order->get_data();
    $first_name = $order_data['billing']['first_name'];
    $last_name = $order_data['billing']['last_name'];
    $phone = $order_data['billing']['phone'];
    $current_time = date('d/m/Y H:i:s');
    $msg = __('From', 'foxtool') . ": $site\n" .
           __('Code orders', 'foxtool') . ": $order_id\n" .
           __('Buyer', 'foxtool') . ": $last_name $first_name\n" .
           __('Contact', 'foxtool') . ": $phone\n" .
           __('Time', 'foxtool') . ": $current_time";

    $token = !empty($foxtool_options['woo-tele11']) ? $foxtool_options['woo-tele11'] : null;
    $chatID = !empty($foxtool_options['woo-tele12']) ? $foxtool_options['woo-tele12'] : null;
    $url = "https://api.telegram.org/bot" . $token . "/sendMessage?parse_mode=html&chat_id=" . $chatID;
    $url = $url . "&text=" . urlencode($msg);
    file_get_contents($url);
}
add_action('woocommerce_checkout_order_processed', 'foxtool_woo_telegram');
}











