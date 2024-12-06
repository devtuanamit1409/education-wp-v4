<?php
if ( ! isset( $data ) ) {
	return;
}

$is_enabled = apply_filters( 'wcbt/filter/breadcrumb/enable', true, $data );
if ( ! $is_enabled ) {
	return;
}

do_action( 'wcbt/layout/breadcrumb/container/before' );
?>
    <div class="wcbt-container">
		<?php
		do_action( 'wcbt/layout/breadcrumb/content/before' );
		woocommerce_breadcrumb();
		do_action( 'wcbt/layout/breadcrumb/content/after' );
		?>
    </div>
<?php
do_action( 'wcbt/layout/breadcrumb/container/after' );
