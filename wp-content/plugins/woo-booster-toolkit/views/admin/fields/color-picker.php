<?php
if ( ! isset( $field ) ) {
	return;
}

use WCBT\Helpers\General;

?>
<div class="<?php echo esc_attr( ltrim( $field->class . ' ' . 'wcbt-field-wrapper' ) ); ?>">
	<?php
	if ( ! empty( $field->title ) ) {
		?>
		<div class="wcbt-title-wrapper">
			<label for="<?php echo esc_attr( $field->id ); ?>"><?php echo esc_html( $field->title ); ?></label>
		</div>
		<?php
	}
	?>
	<div class="wcbt-color-picker">
		<input type="text" data-jscolor="" id="<?php echo esc_attr( $field->id ); ?>"
			   name="<?php echo esc_attr( $field->name ); ?>"
			   value="<?php echo esc_attr( $field->value ?? '' ); ?>"
			<?php echo empty( $field->pattern ) ? '' : 'pattern = "' . esc_attr( $field->pattern ) . '"'; ?>
		/>
		<?php
		if ( ! empty( $field->description ) ) {
			?>
			<p class="wcbt-description"><?php echo General::ksesHTML( $field->description ); ?></p>
			<?php
		}
		?>
	</div>
</div>
