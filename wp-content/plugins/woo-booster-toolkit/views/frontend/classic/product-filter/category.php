<?php
if ( ! isset( $data ) ) {
	return;
}

if ( empty( $data['category_number'] ) ) {
	return;
}

$show_title = $data['show_title'] ?? 'yes';
$show_count = $data['show_count'] ?? 'no';
$value      = isset( $_GET['category'] ) ? json_decode( urldecode( $_GET['category'] ) ) : array();

if ( empty( $value ) || ! is_array( $value ) ) {
	$value = array();
}

$args = array(
	'taxonomy'   => 'product_cat',
	'orderby'    => 'name',
	'number'     => $data['category_number'],
	'hide_empty' => false
);

$all_categories = get_terms( $args );
?>
<div class="category wrapper <?php if ( ! empty( $data['extra_class'] ) ) {
	echo $data['extra_class'];
}; ?>">
	<?php if ( $show_title == 'yes' ) { ?>
        <div class="item-filter-heading"><?php esc_html_e( 'Categories', 'wcbt' ); ?></div>
	<?php } ?>
    <div class="category-content wrapper-content">
		<?php
		foreach ( $all_categories as $category ) {
			$checked = in_array( $category->term_id, $value );
			?>
            <div class="item">
                <input id="cats-<?php echo esc_attr( $category->term_id ); ?>" type="checkbox"
                       value="<?php echo esc_attr( $category->term_id ); ?>" <?php checked( $checked, true, true ) ?>>
                <label for="cats-<?php echo esc_attr( $category->term_id ); ?>">
                    <span><?php echo esc_html( $category->name ); ?></span>
                </label>
				<?php if ( $show_count == 'yes' ) {
					?>
                    <span class="product-count">(<?php echo esc_html( $category->count ); ?>)</span>
				<?php } ?>
            </div>
			<?php
		}
		?>
    </div>
</div>
