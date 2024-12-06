<?php
/**
 * Import Export Addon H5P class.
 *
 * @author   ThimPress
 * @package  LearnPress/Import-Export/Classes
 * @version  4.0.0
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LP_H5P_Import_Export' ) ) {
	/**
	 * Class LP_H5P_Import_Export.
	 */
	class LP_H5P_Import_Export {

		public function __construct() {
			// export
			add_action( 'lpie_do_export_item_meta', array( $this, 'lpie_h5p_export_content' ), 10, 1 );

			// add tag to export item  assignment
			add_filter( 'learn-press/import/postdata', array( $this, 'lpie_add_export_item_h5p' ), 10, 2 );

			// update attachment assignment
			add_action( 'learn-press/import/process-type', array( $this, 'lpie_update_attachment_h5p' ), 15, 2 );
		}

		public function lpie_h5p_export_content( $post ) {
			if ( ! $post->ID ) {
				return;
			}
			if ( get_post_type( $post->ID ) != LP_COURSE_CPT ) {
				return;
			}

			$course   = learn_press_get_course( $post->ID );
			$item_ids = $course->get_item_ids();

			foreach ( $item_ids as $item_id ) {
				if ( get_post_type( $item_id ) == 'lp_h5p' ) {
					$h5p_id        = get_post_meta( $item_id, '_lp_h5p_interact', true );
					if ( ! $h5p_id ) {
						continue;
					}
					$passing_grade = get_post_meta( $item_id, '_lp_passing_grade', true );
					//new h5p
					$plugin      = H5P_Plugin::get_instance();
					$content_h5p = $plugin->get_content($h5p_id);
					if ( ! $content_h5p ) {
						continue;
					}
					$export      = get_option('h5p_export', TRUE) ? $plugin->get_h5p_url() . '/exports/' . ($content_h5p['slug'] ? $content_h5p['slug'] . '-' : '') . $content_h5p['id'] . '.h5p' : '';
					$url         = home_url() . $export;

					?>
					<wp:h5p_content id = "<?php echo $item_id; ?>">
						<wp:h5p_url>
							<?php echo $url; ?>
						</wp:h5p_url>
						<wp:h5p_id>
							<?php echo $h5p_id; ?>
						</wp:h5p_id>
						<wp:h5p_passing_grade>
							<?php echo $passing_grade; ?>
						</wp:h5p_passing_grade>
					</wp:h5p_content>
					<?php
				}
			}
		}

		public function lpie_add_export_item_h5p( $post, $wp ) {
			if ( ! $wp ) {
				return;
			}
			$data_item = array();
			if ( isset( $wp->h5p_content ) ) {
				foreach ( $wp->h5p_content as $h5p_value ) {
					if ( ! empty( $h5p_value ) ) {
						$attr = $h5p_value->attributes();
						$id   = (int) $attr['id'];
						$data_item[ $id ] = array(
							'url'           => (string) $h5p_value->h5p_url,
							'passing_grade' => (int) $h5p_value->h5p_passing_grade,
							'h5p_id'        => (int) $h5p_value->h5p_id,
						);
					}
				}
			}
			$post['custom'][ 'lp_h5p' ] = $data_item;
			return $post;
		}

		public function lpie_update_attachment_h5p( $post_old, $processed_posts ) {
			if ( ! $post_old || ! $processed_posts ) {
				return;
			}

			if ( ! empty( $post_old['custom'][ 'lp_h5p' ] ) ) {
				$data_h5p_old  = $post_old['custom'][ 'lp_h5p' ];
				$data_h5p_new = array();
				foreach ( $processed_posts as $id_item_old => $id_item_new ) {
					if ( array_key_exists( $id_item_old, $data_h5p_old ) ) {
						$data_h5p_new[ $id_item_new ] = $data_h5p_old[ $id_item_old ];
					}
				}

				if ( ! empty( $data_h5p_new ) ) {
					foreach($data_h5p_new as $id_new => $value ){
						$passing_grade = $value['passing_grade'];
						$url           = $value['url'];
						$h5p_id        = $value['h5p_id'];
						$plugin        = H5P_Plugin::get_instance();
						//update passing grade
						update_post_meta( $id_new, '_lp_passing_grade', $passing_grade );
						//update item h5p
						$this->handle_content_creation( $plugin->get_content( $h5p_id ), $id_new );
					}
				}
			}
		}

		public function handle_content_creation( $content, $id_new ) {
			$plugin = H5P_Plugin::get_instance();
			$slug   = $plugin->get_plugin_slug();

			$core = $plugin->get_h5p_instance('core');
			// Keep track of the old library and params
			$oldLibrary = NULL;
			$oldParams = NULL;
			if ($content !== NULL) {
				$oldLibrary = $content['library'];
				$oldParams = json_decode($content['params']);
			}
			else {
				$content = array(
					'disable' => H5PCore::DISABLE_NONE
				);
			}
		
			// Get library
			$content['library'] = $core->libraryFromString( filter_input(INPUT_POST, 'library') );
			if (!$content['library']) {
				$core->h5pF->setErrorMessage(__('Invalid library.', $slug));
				return FALSE;
			}
			if ( $core->h5pF->libraryHasUpgrade($content['library'])) {
				$core->h5pF->setErrorMessage(__('Something unexpected happened. We were unable to save this content.', $slug));
				return FALSE;
			}
		
			// Check if library exists.
			$content['library']['libraryId'] = $core->h5pF->getLibraryId($content['library']['machineName'], $content['library']['majorVersion'], $content['library']['minorVersion']);
			if (! $content['library']['libraryId'] ) {
				$core->h5pF->setErrorMessage(__('No such library.', $slug));
				return FALSE;
			}
		
			// Check parameters
			$content['params'] = filter_input(INPUT_POST, 'parameters');
			if ( $content['params'] === NULL) {
				return FALSE;
			}
			$params = json_decode($content['params']);
			if ($params === NULL) {
				$core->h5pF->setErrorMessage(__('Invalid parameters.', $slug));
				return FALSE;
			}
		
			$content['params'] = json_encode($params->params);
			$content['metadata'] = $params->metadata;
		
			// Trim title and check length
			$trimmed_title = empty($content['metadata']->title) ? '' : trim($content['metadata']->title);
			if ($trimmed_title === '') {
				H5P_Plugin_Admin::set_error(sprintf(__('Missing %s.', $slug), 'title'));
				return FALSE;
			}
		
			if (strlen($trimmed_title) > 255) {
				H5P_Plugin_Admin::set_error(__('Title is too long. Must be 256 letters or shorter.', $slug));
				return FALSE;
			}
		
			// Set disabled features
			// $this->get_disabled_content_features($core, $content);
		
			try {
				// Save new content
				$content['id'] = $core->saveContent($content);
			}
			catch (Exception $e) {
				H5P_Plugin_Admin::set_error($e->getMessage());
				return;
			}
		
			// Move images and find all content dependencies
			$editor =  new H5peditor(
				$plugin->get_h5p_instance('core'),
				new H5PEditorWordPressStorage(),
				new H5PEditorWordPressAjax()
			);
			$editor->processParameters($content['id'], $content['library'], $params->params, $oldLibrary, $oldParams);

			update_post_meta( $id_new, '_lp_h5p_interact', $content['id'] );
		}
	}
}

return new LP_H5P_Import_Export();
