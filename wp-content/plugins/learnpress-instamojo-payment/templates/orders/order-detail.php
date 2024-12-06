<?php
/**
 * Show payment id instamojo in detail Lp-order
 *
 */

if ( empty( $instamojo_payment_id ) ) {
	return;
}
?>


<div class="order-data-field order-data-instamojo-payment-id">
	<label><?php esc_html_e( 'Instamojo paymentID:', 'learnpress-instamojo-payment' ); ?></label>
	<?php echo $instamojo_payment_id; ?>
</div>
