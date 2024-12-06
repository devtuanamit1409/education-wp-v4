<?php
if ( ! isset( $data ) ) {
	return;
}

use WCBT\Helpers\Template;

$template = Template::instance();

do_action( 'wcbt/layout/product-filter/before', $data );
?>
    <div class="<?php echo esc_attr( apply_filters( 'wcbt/filter/product-archive/sidebar/class', 'wcbt-product-archive-sidebar' ) ); ?>">
        <div class="<?php echo esc_attr( apply_filters( 'wcbt/filter/product-filter/class', 'wcbt-product-filter' ) ); ?>">
            <div class="wrapper-selected-fields">
            </div>
            <div class="wrapper-search-fields">
				<?php
				$fields = $data['fields'] ?? array();
				$template->get_frontend_template_type_classic( 'product-filter/selection.php', compact( 'data' ) );

				foreach ( $fields as $key ) {
					if ( str_contains( $key, 'pa_' ) ) {
						$data['key'] = $key;
						$key         = 'attributes';
					} else {
						$key = str_replace( array( '_', 'wcbt-' ), array( '-', '' ), $key );
					}

					$template->get_frontend_template_type_classic( 'product-filter/' . $key . '.php', compact( 'data' ) );
				}
				?>
            </div>
        </div>
    </div>
<?php
do_action( 'wcbt/layout/product-filter/after', $data );

wp_enqueue_script( 'wcbt-product-filter' );
wp_localize_script( 'wcbt-product-filter', 'WCBT_PRODUCT_FILTER', $data );
