<?php
if ( ! isset( $field ) ) {
	return;
}

use WCBT\Helpers\General;

$args = $field->args;
if ( ! empty( $field->name ) ) {
	$args['name'] = $field->name;
}

if ( ! empty( $field->value ) ) {
	$args['selected'] = $field->value;
}
?>
	<div class="<?php echo esc_attr( ltrim( $field->class . ' ' . 'wcbt-field-wrapper wcbt-wp-dropdown-user-wrapper' ) ); ?>">
		<?php
		if ( ! empty( $field->title ) ) {
			?>
			<div class="wcbt-title-wrapper">
				<label for="<?php echo esc_attr( $field->id ); ?>"><?php echo esc_html( $field->title ); ?></label>
			</div>
			<?php
		}
		?>
		<div class="wcbt-wp-dropdown-user-content">
			<?php
			wp_dropdown_users( $args );
			if ( ! empty( $field->description ) ) {
				?>
				<p class="wcbt-description"><?php echo General::ksesHTML( $field->description ); ?></p>
				<?php
			}
			?>
		</div>
	</div>
<?php
