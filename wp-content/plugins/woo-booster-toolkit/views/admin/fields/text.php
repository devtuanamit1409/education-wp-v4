<?php
/**
 * Template Field type Text
 * @version 1.0.0
 */

use WCBT\Helpers\Fields\Text;
use WCBT\Helpers\General;
/**
 * @var $field Text
 */
if ( ! isset( $field ) ) {
	return;
}

$required = $field->required ? ' required' : '';
?>
<div class="<?php echo esc_attr( ltrim( $field->class . ' ' . 'wcbt-field-wrapper wcbt-input-wrapper' ) ); ?>">
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
		<input type="text" id="<?php echo esc_attr( $field->id ); ?>"
			   name="<?php echo esc_attr( $field->name ); ?>"
			   value="<?php echo esc_attr( $field->value ?? '' ); ?>"
			<?php
			echo empty( $field->pattern ) ? '' : 'pattern = "' . esc_attr( $field->pattern ) . '"';
			echo empty( $field->placeholder ) ? '' : 'placeholder = "' . esc_attr( $field->placeholder ) . '"';
			echo esc_attr( $required );
			?>
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
