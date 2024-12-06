<?php
if (!isset($data)) {
	return;
}

if (empty($data['key'])) {
	return;
}

if ( empty( $data['attribute_term_number'] ) ) {
	return;
}

$show_title = $data['show_title'] ?? 'yes';
$show_count = $data['show_count'] ?? 'no';
$args = array(
	'taxonomy' => $data['key'],
	'orderby'  => 'name',
	'number'    => $data['attribute_term_number'],
);

$all_terms = get_terms($args);

$taxonomy = get_taxonomy($data['key']);
$taxonomy_data = str_replace('pa_', '', $data['key']);

$value = isset($_GET[$taxonomy_data]) ? json_decode(urldecode($_GET[$taxonomy_data])) : array();

if (empty($value) || !is_array($value)) {
	$value = array();
}
$attribute_id   = wc_attribute_taxonomy_id_by_name($taxonomy_data);
$attribute      = wc_get_attribute($attribute_id);
$attribute_type = $attribute->type;
?>
<div class="attribute wrapper attribute-type-<?php echo $attribute_type; if(!empty($data['extra_class'])){ echo $data['extra_class'];};?>" data-taxonomy="<?php echo esc_attr($taxonomy_data); ?>">
	<?php
	if ( $show_title == 'yes' ) { ?>
		<div class="item-filter-heading"><?php echo esc_html( $taxonomy->labels->singular_name ); ?></div>
	<?php } ?>

	<div class="attribute-content wrapper-content">
		<?php
		foreach ($all_terms as $term) {
			$term_id = $term->term_id;
			if (!empty($term_id)) {
				$meta_data = get_term_meta($term_id, WCBT_TERM_META_KEY, true);
				if (!empty($meta_data) &&  $attribute_type === 'color') {
					$key         = 'product_attribute_color';
					$value_term_meta      = $meta_data[$key] ?? '';
					$class_type  = 'att-type-color ' . $attribute_type;
					$style_bg    = 'style="background:' . $value_term_meta . '"';
					$style_label = '<div  class="att-type-bgcolor"' . $style_bg . '></div><span>' . $term->name . '</span>';
				} else {
					$class_type  = 'att-type-text ' . $attribute_type;
					$style_label = '<span>' . $term->name . '</span>';
				}
			}
		?>
			<div class="item">
				<input id="attr-<?php echo esc_attr($term->term_id); ?>" type="checkbox" value="<?php echo esc_attr($term->term_id); ?>" <?php checked(in_array($term->term_id, $value), true, true); ?>>
				<label for="attr-<?php echo esc_attr($term->term_id); ?>">
					<?php echo $style_label; ?>
				</label>
				<?php if ($show_count == 'yes') {
				?>
					<span class="product-count">(<?php echo esc_html($term->count); ?>)</span>
				<?php } ?>
			</div>
		<?php
		}
		?>
	</div>
</div>
