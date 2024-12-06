<?php
/**
 * Template for displaying notice when buy package via product
 *
 * @author  ThimPress
 * @package LearnPress/Templates
 * @version 1.0.1
 */

defined( 'ABSPATH' ) || exit();

if ( ! isset( $package_id ) ) {
	return;
}

$lp_db                    = LP_Database::getInstance();
$filter                   = new LP_Post_Type_Filter();
$filter->post_type        = 'product';
$filter->collection       = $lp_db->wpdb->posts;
$filter->collection_alias = 'p';
$filter->join[]           = 'INNER JOIN ' . $lp_db->wpdb->postmeta . ' AS pm ON pm.post_id = p.ID';
$filter->where[]          = $lp_db->wpdb->prepare( 'AND pm.meta_key = %s AND pm.meta_value=%s', LP_Wc_Upsell::$lp_wc_package_assigned, $package_id );
// $filter                   = apply_filters( 'lp/woo-payment/notice-sidebar/get-products', $filter );
try {
	$products = $lp_db->execute( $filter );
} catch ( Throwable $e ) {
}
wp_enqueue_style( 'lp-woo-css' );
?>

<div class="course-via-product">
	<div class="learn-press-message error">
		<?php if ( empty( $products ) ) : ?>
			<p>
				<?php
				if ( current_user_can( 'administrator' ) || current_user_can( LP_TEACHER_ROLE ) ) {
					_e( 'Purchase is only available if the package is already assigned to a product!', 'learnpress-woo-payment' );
				} else {
					_e( 'You couldn\'t purchase this package because it hasn\'t been assigned to any product yet!', 'learnpress-woo-payment' );
				}
				?>
			</p>
		<?php else : ?>
			<p>
				<?php
				_e( 'You need to purchase package from products list to begin.', 'learnpress-woo-payment' );
				?>
			</p>
			<h6><?php _e( '--List products: ', 'learnpress-woo-payment' ); ?></h6>
			<ul>
				<?php foreach ( $products as $product ) : ?>
					<li>
						<a href="<?php echo get_permalink( $product->ID ); ?>"><?php echo get_the_title( $product->ID ); ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</div>
