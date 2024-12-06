<?php
/**
 * Template for displaying order id instamojo in page checkout
 *
 */

?>


<?php if ( isset( $_GET['payment_id'] ) ) : ?>
	<tr class="order-key order-instamojo">
		<th><?php esc_html_e( 'Instamojo Payment ID', 'learnpress-instamojo-payment' ); ?></th>
		<td>
			<?php echo esc_html( $_GET['payment_id'] ); ?>
		</td>
	</tr>
<?php endif; ?>
