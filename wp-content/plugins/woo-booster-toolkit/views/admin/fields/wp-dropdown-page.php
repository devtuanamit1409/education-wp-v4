<?php

if ( ! isset( $field ) ) {
	return;
}

use WCBT\Helpers\General;

$args = $field->args;
if ( ! empty( $field->name ) ) {
	$args['name'] = $field->name;
}

if ( ! empty( $field->value ) && get_post_status( $field->value ) === 'publish' ) {
	$args['selected'] = $field->value;
}
$allow_create_page = $field->allow_create_page;
?>
	<div class="<?php echo esc_attr( ltrim( $field->class . ' ' . 'wcbt-field-wrapper wcbt-wp-dropdown-page-wrapper' ) ); ?>">
		<?php
		if ( ! empty( $field->title ) ) {
			?>
			<div class="wcbt-title-wrapper">
				<label for="<?php echo esc_attr( $field->id ); ?>"><?php echo esc_html( $field->title ); ?></label>
			</div>
			<?php
		}
		?>
		<div class="wcbt-wp-dropdown-page-content">
			<div class="wcbt-dropdown">
				<?php
				wp_dropdown_pages( $args );
				?>
			</div>
			<?php
			if ( ! empty( $allow_create_page ) ) {
				?>
				<button type="button" class="button wcbt-quick-add-page">
					<?php esc_html_e( 'Create a new page', 'wcbt' ); ?>
				</button>

				<p class="wcbt-quick-add-page-inline">
					<input type="text" placeholder="<?php esc_attr_e( 'New page title', 'wcbt' ); ?>"/>
					<button type="button" class="button">
						<?php esc_html_e( 'Confirm', 'wcbt' ); ?>
					</button>
					<a href=""><?php esc_html_e( 'Cancel [ESC]', 'wcbt' ); ?></a>
				</p>

				<p class="wcbt-quick-add-page-actions">
					<?php
					if ( ! empty( $args['selected'] ) ) {
						?>
						<a class="edit-page" href="<?php echo get_edit_post_link( $args['selected'] ); ?>"
						   target="_blank" rel="noopener"><?php esc_html_e( 'Edit page', 'wcbt' ); ?></a>
						&#124;
						<a class="view-page" href="<?php echo get_permalink( $args['selected'] ); ?>"
						   target="_blank" rel="noopener"><?php esc_html_e( 'View page', 'wcbt' ); ?></a>
						<?php
					}
					?>
				</p>
				<?php
			}
			if ( ! empty( $field->description ) ) {
				?>
				<p class="wcbt-description"><?php echo General::ksesHTML( $field->description ); ?></p>
				<?php
			}
			?>
		</div>
	</div>
<?php
