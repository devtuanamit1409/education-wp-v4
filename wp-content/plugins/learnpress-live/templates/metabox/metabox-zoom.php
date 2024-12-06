<?php
/**
 * Template meta-box Zoom List meetings
 *
 * @since 4.0.2
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $data ) || ! isset( $data['meta_box_zoom'] ) ||
	! $data['meta_box_zoom'] instanceof LP_Meta_Box_Zooms ) {
	return;
}

$meta_box_zoom = $data['meta_box_zoom'];
$value           = esc_attr( $data['value'] ?? '' );

?>
<div class="form-field lp_meeting_zoom_id">
	<label for="<?php echo esc_attr( $meta_box_zoom->id ); ?>">
		<?php echo wp_kses_post( $meta_box_zoom->label ); ?>
	</label>
	<div class="lp_live_addon_meta_box__content">
		<div class="content">
			<select id="_lp_meeting_zoom_id" class="_lp_live_select_id" name="_lp_meeting_zoom_id">
				<option value=""><?php __('Select Meeting ID', 'learnpress-live') ?></option>
				<?php
				$controller = new LearnPress_Zoom_Setting_Api();
				$request    = new WP_REST_Request();
				$request->set_param( 'type', 'scheduled' );
				$response    = $controller->get_all_events( $request );
				$object_data = $response->get_data();
				if ( empty( $object_data->data )) {
					echo '<option value="">'.__('No meeting found', 'learnpress-live').'</option>';
				} else {
					foreach ( $object_data->data->meetings as $meeting ) {
						$selected = '';
						if ( $value == $meeting->id ) {
							$selected = 'selected';
						}
						echo '<option value="'.$meeting->id.'" '.$selected.'>'.$meeting->topic.'</option>';
					}
				}
				?>
			</select>
		</div>
		<?php
			if ( ! empty( $meta_box_zoom->description ) ) {
				echo '<span class="description" style="margin-top:10px;margin-left: 0px;display:inline-block;">' . wp_kses_post( $meta_box_zoom->description ) . '</span>';
			}
		?>
	</div>
</div>
<style>
	.lp_live_addon_meta_box__content .select2-selection__clear{
		line-height: 28px;
		width: 25px;
		height: 26px;
		text-align: center;
		background: #f7f7f7;
		font-size: 15px;
		display: flex;
		align-items: center;
		justify-content: center;
	}
	.lp_live_addon_meta_box__content{
		flex: 1;
	}
</style>