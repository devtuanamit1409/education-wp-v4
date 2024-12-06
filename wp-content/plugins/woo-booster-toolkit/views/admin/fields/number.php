<?php
if ( ! isset( $field ) ) {
	return;
}

use WCBT\Helpers\General;

$required = $field->required ? ' required' : '';
?>

<div class="<?php echo esc_attr( ltrim( $field->class . ' ' . 'wcbt-field-wrapper wcbt-input-number-wrapper' ) ); ?>">
	<?php
	if ( ! empty( $field->title ) ) {
		?>
		<div class="wcbt-title-wrapper">
			<label for="<?php echo esc_attr( $field->id ); ?>"><?php echo esc_html( $field->title ); ?></label>
		</div>
		<?php
	}
	?>
	<div class="wcbt-input-content">
		<input type="number" id="<?php echo esc_attr( $field->id ); ?>" step="any"
			   name="<?php echo esc_attr( $field->name ); ?>" value="<?php echo esc_attr( $field->value ); ?>"
			   min="<?php echo esc_attr( $field->min ); ?>" max="<?php echo esc_attr( $field->max ); ?>"
			<?php echo esc_attr( $required ); ?>
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
