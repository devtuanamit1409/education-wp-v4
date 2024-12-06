<?php

if ( ! isset( $field ) ) {
	return;
}

use WCBT\Helpers\General;
use WCBT\Helpers\Media;

$max_width = $max_height = '';
if ( ! empty( $field->max_size ) ) {
	$max_width  = $field->max_size['width'];
	$max_height = $field->max_size['height'];
}

?>
	<div class="<?php echo esc_attr( ltrim( $field->class . ' ' . 'wcbt-field-wrapper wcbt-file-upload-wrapper' ) ); ?>">
		<?php
		if ( ! empty( $field->title ) ) {
			?>
			<div class="wcbt-title-wrapper">
				<label for="<?php echo esc_attr( $field->id ); ?>"><?php echo esc_html( $field->title ); ?></label>
			</div>
			<?php
		}
		if ( $field->multiple ) {
			if ( empty( $field->value ) ) {
				$field->value = array();
			} else {
				$field->value = explode( ',', $field->value );
				if ( count( $field->value ) > $field->max_number ) {
					$field->value = array_slice( $field->value, 0, $field->max_number );
				}
			}

			$value_data = implode( ',', $field->value );
			?>
			<div class="wcbt-image-info multiple"
				 data-max-file-size="<?php echo esc_attr( $field->max_file_size ); ?>"
				 data-max-width="<?php echo esc_attr( $max_width ); ?>"
				 data-max-height="<?php echo esc_attr( $max_height ); ?>">
				<div class="wcbt-gallery-inner">
					<input type="hidden" name="<?php echo esc_attr( $field->name ); ?>"
						   data-number="<?php echo esc_attr( $field->max_number ); ?>"
						   value="<?php echo esc_attr( $value_data ); ?>" readonly/>
					<?php
					$count = count( $field->value );
					for ( $i = 0; $i < $count; $i ++ ) {
						$data_id = empty( $field->value[ $i ] ) ? '' : $field->value[ $i ];
						$img_src = '';
						if ( ! empty( wp_get_attachment_image_url( $data_id, 'thumbnail' ) ) ) {
							$img_src = wp_get_attachment_image_url( $data_id, 'thumbnail' );
						}
						$alt_text = Media::get_image_alt( $data_id );
						?>
						<div class="wcbt-gallery-preview" data-id="<?php echo esc_attr( $data_id ); ?>">
							<div class="wcbt-gallery-centered">
								<img src="<?php echo esc_url_raw( $img_src ); ?>"
									 alt="<?php echo esc_attr( $alt_text ); ?>">
							</div>
							<span class="wcbt-gallery-remove dashicons dashicons dashicons-no-alt"></span>
						</div>
						<?php
					}
					?>
					<button type="button"
							class="button wcbt-gallery-add"><?php echo esc_html( $field->button_title ); ?></button>
				</div>
			</div>
			<?php
			if ( ! empty( $field->description ) ) {
				?>
				<p class="wcbt-description"><?php echo General::ksesHTML( $field->description ); ?></p>
				<?php
			}
		} else {
			$image_id            = $field->value;
			$alt_text            = Media::get_image_alt( $image_id );
			$image_full_url      = wp_get_attachment_image_url( $image_id, 'full' );
			$image_thumbnail_url = wp_get_attachment_image_url( $image_id, 'thumbnail' );
			?>
			<div class="wcbt-image-info" data-max-file-size="<?php echo esc_attr( $field->max_file_size ); ?>"
				 data-max-width="<?php echo esc_attr( $max_width ); ?>"
				 data-max-height="<?php echo esc_attr( $max_height ); ?>">
				<div class="wcbt-image-inner">
					<div class="wcbt-image-preview">
						<img src="<?php echo esc_url_raw( $image_thumbnail_url ); ?>"
							 alt="<?php echo esc_attr( $alt_text ); ?>">
					</div>
					<div class="wcbt-image-control">
						<input type="hidden" name="<?php echo esc_attr( $field->name ); ?>"
							   value="<?php echo esc_attr( $image_id ); ?>" readonly/>
						<input type="text" id="<?php echo esc_attr( $field->id ); ?>"
							   value="<?php echo esc_attr( $image_full_url ); ?>" readonly/>
						<button type="button" href="#"
								class="button button-secondary wcbt-image-add"><?php esc_html_e( 'Select Image', 'wcbt' ); ?></button>
						<button type="button" href="#"
								class="button button-secondary wcbt-image-remove"><?php esc_html_e( 'Remove', 'wcbt' ); ?></button>
					</div>
				</div>
				<?php
				if ( ! empty( $field->description ) ) {
					?>
					<p class="wcbt-description"><?php echo General::ksesHTML( $field->description ); ?></p>
					<?php
				}
				?>
			</div>
			<?php
		}
		?>
	</div>
<?php
if ( ! did_action( 'wp_enqueue_media' ) ) {
	wp_enqueue_media();
}

