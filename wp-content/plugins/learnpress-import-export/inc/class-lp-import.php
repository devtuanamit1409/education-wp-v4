<?php
/**
 * Learnpress Import class.
 *
 * @author   ThimPress
 * @package  LearnPress/Import-Export/Classes
 * @version  3.0.0
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LP_Import' ) ) {
	/**
	 * Class LP_Import.
	 */
	class LP_Import {

		/**
		 * LP_Import constructor.
		 */
		public function __construct() {
			do_action( 'learn-press/import/init-hooks', $this );

			include_once LP_ADDON_IMPORT_EXPORT_INC . 'admin/providers/learnpress/class-lp-import-learnpress.php';
			include_once LP_ADDON_IMPORT_EXPORT_INC . 'admin/providers/learnpress/class-lp-import-user-data.php';

			//addon
			include_once LP_ADDON_IMPORT_EXPORT_INC . 'admin/providers/addons/class-lp-import-export-addons.php';
			// update _lp_info_extra_fast_query
			add_action( 'learn-press/import/process-type', array( $this, 'lpie_update_lp_info_extra_fast_query' ), 10, 2 );
		}

		/**
		 * Upadte meta key _lp_info_extra_fast_query when import course
		 *
		 * @param int   $post_old
		 * @param array $post_id_new
		 */
		public function lpie_update_lp_info_extra_fast_query( $post_old, $post_ids_new ) {
			if ( ! $post_old ) {
				return;
			}
			$post_old_id = $post_old['post_id'];
			$post_id     = ! empty( $post_ids_new[ $post_old_id ] ) ? $post_ids_new[ $post_old_id ] : 0;

			if ( ! $post_id ) {
				return;
			}

			if ( get_post_type( $post_id ) == LP_COURSE_CPT ) {
				$extra_info_str = get_post_meta( $post_id, '_lp_info_extra_fast_query', true );
				if ( $extra_info_str ) {
					$extra_info_stdclass                = json_decode( $extra_info_str );
					$extra_info_stdclass->first_item_id = LP_Course_DB::getInstance()->get_first_item_id( $post_id );
					$data                               = json_encode( $extra_info_stdclass );
					update_post_meta( $post_id, '_lp_info_extra_fast_query', $data );
				}
			}

		}
	}
}

return new LP_Import();
