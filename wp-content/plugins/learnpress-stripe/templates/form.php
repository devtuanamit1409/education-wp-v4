<?php
/**
 * Template for displaying Stripe payment form.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/addons/stripe-payment/form.php.
 *
 * @author   ThimPress
 * @package  LearnPress/Stripe/Templates
 * @version  4.0.1
 */

defined( 'ABSPATH' ) || exit();

$is_direct_pay_on_stripe_page = $is_direct_pay_on_stripe_page ?? false;
$is_test_mode                 = $is_test_mode ?? false;

echo sprintf( '<p>%s</p>', $description ?? '' );
if ( $is_direct_pay_on_stripe_page ) {
	echo sprintf( '<p>%s</p>', __( 'You will be redirected to Stripe to complete your payment.', 'learnpress-stripe' ) );
}
?>

<div id="lp-stripe-payment-form">
	<!-- Elements will mount form elements here -->
</div>

<?php if ( $is_test_mode && ! $is_direct_pay_on_stripe_page ) { ?>
	<?php
	learn_press_display_message(
		esc_html__( 'Test mode is enabled. You can use the card number 4242424242424242 with any CVC and a valid expiration date for testing purpose.', 'learnpress-stripe' ),
		'warning'
	);
	?>
<?php } ?>
