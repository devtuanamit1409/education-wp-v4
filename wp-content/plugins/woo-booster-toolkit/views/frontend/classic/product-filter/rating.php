<?php
if (!isset($data)) {
	return;
}
$show_title = isset($data['show_title']) ? $data['show_title'] : 'yes';
$show_count = isset($data['show_count']) ? $data['show_count'] : 'yes';  

use WCBT\Models\ProductModel;

$value = isset($_GET['rating']) ? json_decode(urldecode($_GET['rating'])) : array();
if (empty($value) || !is_array($value)) {
	$value = array();
}
?>
<div class="rating wrapper <?php if (!empty($data['extra_class'])) {
								echo $data['extra_class'];
							}; ?>">
	<?php if ($show_title == 'yes') { ?>
		<div class="item-filter-heading"><?php esc_html_e('Rating', 'wcbt'); ?></div>
	<?php } ?>
	<ul class="wrapper-content">
		<?php
		for ($j = 5; $j > 0; $j--) { 

		?>
			<li>
				<input type="checkbox" name="rating" value="<?php echo $j; ?>" <?php checked(in_array($j, $value), true, true); ?>>
				<?php
				$total = ProductModel::get_product_total_by_rating($j);
				?>
				<div class="star">
					<?php
					for ($i = 0; $i < 5; $i++) {
						if($i < $j){
							echo '<i class="star-solid"></i>';
						}else{
							echo '<i class="star-regular"></i>';
						}
					}
					?>
				</div>
				<?php if ($show_count == 'yes') {
				?>
					<span class="product-count">(<?php echo esc_html($total); ?>)</span>
				<?php } ?>
			</li>
		<?php
		}
		?>
	</ul>
</div>