<?php
/**
 * Assingment Import Export Addon class.
 *
 * @author   ThimPress
 * @package  LearnPress/Import-Export/Classes
 * @version  4.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LP_Assignment_Import_Export' ) ) {
	/**
	 * Class LP_Assignment_Import_Export.
	 */
	class LP_Assignment_Import_Export {

		private static $instance = null;

		public function __construct() {

			// export
			add_action( 'lpie_do_export_item_meta', array( $this, 'lpie_assignment_export_tag_attachtment' ), 10, 1 );

			// import
			add_filter( 'upload_mimes', array( $this, 'lpie_mime_types' ), 10, 1 );
			// add tag to export item  assignment
			add_filter( 'learn-press/import/postdata', array( $this, 'lpie_add_export_item_assingment' ), 10, 2 );
			// update attachment assignment
			add_action( 'learn-press/import/process-type', array( $this, 'lpie_update_attachment_assingment' ), 15, 2 );

		}

		/**
		 * add mime upload
		 *
		 * @param $mimes
		 */
		public function lpie_mime_types( $mimes ) {

			$mimes['pdf']  = 'application/pdf';
			$mimes['doc']  = 'application/msword';
			$mimes['docx'] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';

			return $mimes;
		}

		/**
		 * Export tag item assingment
		 *
		 * @param $post
		 */
		public function lpie_assignment_export_tag_attachtment( $post ) {
			if ( ! $post->ID ) {
				return;
			}
			if ( get_post_type( $post->ID ) != LP_COURSE_CPT ) {
				return;
			}
			$course   = learn_press_get_course( $post->ID );
			$item_ids = $course->get_item_ids();

			foreach ( $item_ids as $item_id ) {
				if ( get_post_type( $item_id ) == LP_ASSIGNMENT_CPT ) {
					$attachment_ids = get_post_meta( $item_id, '_lp_attachments', true );
					?>
				<wp:attachment_assingment id = "<?php echo $item_id; ?>">

					<?php
					if ( ! empty( $attachment_ids ) ) {
						foreach ( $attachment_ids as $attachment_id ) {
							$url = wp_get_attachment_url( $attachment_id );
							if ( ! $url ) {
								continue;
							}
							?>
						<wp:attachment_value>
							<?php echo $url; ?>
						</wp:attachment_value>
							<?php
						}
					}
					?>
				</wp:attachment_assingment>
					<?php
				}
			}
		}

		/**
		 * add tag to export item  assignment
		 *
		 * @param array  $postdata
		 * @param object $wp
		 */
		public function lpie_add_export_item_assingment( $post, $wp ) {
			if ( ! $wp ) {
				return;
			}

			$data_item = array();

			if ( isset( $wp->attachment_assingment ) ) {

				foreach ( $wp->attachment_assingment as $attachment_value ) {
					if ( ! empty( $attachment_value ) ) {
						$attr = $attachment_value->attributes();
						$id   = (int) $attr['id'];
						$value_item = array();
						foreach ( $attachment_value as $att_url ) {
							$value_item [] = ( (array) $att_url )[0];
						}
					}
					$data_item[ $id ] = $value_item;

				}
			}
			$post['custom'][ LP_ASSIGNMENT_CPT ] = $data_item;
			return $post;
		}

		/**
		 * update attachment assignment
		 *
		 * @param array $post_old
		 * @param array $processed_posts
		 */
		public function lpie_update_attachment_assingment( $post_old, $processed_posts ) {
			if ( ! $post_old || ! $processed_posts ) {
				return;
			}

			if ( ! empty( $post_old['custom'][ LP_ASSIGNMENT_CPT ] ) ) {
				$data_attachment_old  = $post_old['custom'][ LP_ASSIGNMENT_CPT ];
				$data_attachment_news = array();
				foreach ( $processed_posts as $id_item_old => $id_item_new ) {
					if ( array_key_exists( $id_item_old, $data_attachment_old ) ) {
						$data_attachment_news[ $id_item_new ] = $data_attachment_old[ $id_item_old ];
					}
				}
			}
			if ( ! empty( $data_attachment_news ) ) {
				foreach ( $data_attachment_news as $id_item_new => $data_attachment_new ) {
					$attach_ids = array();
					if ( ! empty( $data_attachment_new ) ) {
						foreach ( $data_attachment_new as $url ) {
							$url = trim( $url );
							if ( ! class_exists( 'WP_Http' ) ) {
								include_once ABSPATH . WPINC . '/class-http.php';
							}

							$http     = new WP_Http();
							$response = $http->request( $url );
							if ( $response['response']['code'] != 200 ) {
								return false;
							}

							$upload = wp_upload_bits( basename( $url ), null, $response['body'] );
							if ( ! empty( $upload['error'] ) ) {
								return false;
							}

							$file_path        = $upload['file'];
							$file_name        = basename( $file_path );
							$file_type        = wp_check_filetype( $file_name, null );
							$attachment_title = sanitize_file_name( pathinfo( $file_name, PATHINFO_FILENAME ) );
							$wp_upload_dir    = wp_upload_dir();

							$post_info = array(
								'guid'           => $wp_upload_dir['url'] . '/' . $file_name,
								'post_mime_type' => $file_type['type'],
								'post_title'     => $attachment_title,
								'post_content'   => '',
								'post_status'    => 'inherit',
							);

							$attach_id = wp_insert_attachment( $post_info, $file_path );

							// Include image.php
							require_once ABSPATH . 'wp-admin/includes/image.php';

							// Define attachment metadata
							$attach_data = wp_generate_attachment_metadata( $attach_id, $file_path );

							// Assign metadata to attachment
							wp_update_attachment_metadata( $attach_id, $attach_data );

							$attach_ids[] = $attach_id;

						}
					}

					if ( ! empty( $attach_ids ) ) {
						update_post_meta( $id_item_new, '_lp_attachments', $attach_ids );
					}
				}
			}
		}

		/**
		 * instance
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

	}
}

LP_Assignment_Import_Export::instance();
