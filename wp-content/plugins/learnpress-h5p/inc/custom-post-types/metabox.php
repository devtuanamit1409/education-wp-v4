<?php
/**
 * Metabox in LP4
 */
class LP_H5P_Meta_Box {
	public static function output( $post ) {
		$post_id    = $post->ID;
		$h5p_chosen = array( '' => '' );
		$edit_link  = '';

		if ( ! lp_h5p_count_h5p_items() ) {
			$edit_link = __( 'There is no items to select. Create <a href="' . admin_url( 'admin.php?page=h5p_new' ) . '" target="_blank">here</a>.', 'learnpress-h5p' );
		}

		if ( $post_id ) {
			$appended_h5p = get_post_meta( $post_id, '_lp_h5p_interact', true );

			if ( ! empty( $appended_h5p ) ) {
				$h5p_chosen[ $appended_h5p ] = lp_h5p_get_content_title( $appended_h5p );
				$edit_link                   = '<a href="' . admin_url( 'admin.php?page=h5p&task=show&id=' ) . $appended_h5p . '" target="_blank">Edit the H5P item</a>.';
			}
		}

		wp_nonce_field( 'learnpress_save_meta_box', 'learnpress_meta_box_nonce' );
		?>

		<div class="lp-meta-box lp-meta-box--h5p">
			<div class="lp-meta-box__inner">
				<?php
					lp_meta_box_select_field(
						array(
							'id'            => '_lp_h5p_interact',
							'label'         => esc_html__( 'Interact H5P', 'learnpress-h5p' ),
							'default'       => '',
							'description'   => $edit_link,
							'options'       => $h5p_chosen,
							'multiple'      => false,
							'wrapper_class' => 'lp-select-2',
							'style'         => 'min-width: 200px',
						)
					);

					lp_meta_box_text_input_field(
						array(
							'id'          => '_lp_passing_grade',
							'label'       => esc_html__( 'Passing Grade (%)', 'learnpress-h5p' ),
							'description' => esc_html__( 'Requires user reached this point to pass this h5p content item.', 'learnpress-h5p' ),
							'type'        => 'number',
							'default'     => 50,
							'style'       => 'width: 80px',
						)
					);
				?>
			</div>
		</div>
		<?php
	}

	public static function save( $post_id ) {
		$h5p_interact  = isset( $_POST['_lp_h5p_interact'] ) ? wp_unslash( $_POST['_lp_h5p_interact'] ) : '';
		$passing_grade = isset( $_POST['_lp_passing_grade'] ) ? wp_unslash( absint( $_POST['_lp_passing_grade'] ) ) : 50;

		update_post_meta( $post_id, '_lp_h5p_interact', $h5p_interact );
		update_post_meta( $post_id, '_lp_passing_grade', $passing_grade );
	}
}

add_action( 'learnpress_save_lp_h5p_metabox', 'LP_H5P_Meta_Box::save' );
