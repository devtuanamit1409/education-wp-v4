<?php
/**
 * Template button view cart woo
 *
 * @author  ThimPress
 * @package LearnPress/Templates
 * @version 2.4
 */

defined( 'ABSPATH' ) || exit();
?>

<a class="btn-lp-course-view-cart" href="<?php echo esc_attr( wc_get_cart_url() ); ?>">
	<button class="lp-button"><?php _e( 'View cart', 'learnpress-woo-payment' ); ?></button>
</a>


