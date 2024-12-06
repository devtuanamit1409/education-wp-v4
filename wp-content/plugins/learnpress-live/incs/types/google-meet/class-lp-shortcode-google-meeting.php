<?php
/**
 * Google Meeting Shortcode.
 *
 * @author   ThimPress
 * @category Shortcodes
 * @package  Learnpress/Shortcodes
 * @version  4.0.0
 * @extends  LP_Abstract_Shortcode
 */

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'LP_Shortcode_Google_Meeting' ) ) {

	/**
	 * Class LP_Shortcode_Google_Meeting
	 */
	class LP_Shortcode_Google_Meeting extends LP_Abstract_Shortcode {

		/**
		 * LP_Shortcode_Google_Meeting constructor.
		 *
		 * @param  mixed $atts
		 */
		public function __construct( $atts = '' ) {
			parent::__construct( $atts );
			$this->_atts = shortcode_atts(
				array(
					'meeting_id' => 0,
				),
				$this->_atts
			);
		}

		/**
		 * Output form.
		 *
		 * @return string
		 */
		public function output() {

			ob_start();
			$atts = $this->_atts;

			try {
				$meeting_id = $atts['meeting_id'];

				if ( empty( $meeting_id ) ) {
					throw new Exception( __( 'Meeting ID is required', 'learnpress-live' ) );
				}

				$db_live = LP_Live_Database::instance();
				$results = $db_live->get_data_by_id( $meeting_id );

				if ( empty( $results ) ) {
					throw new Exception( __( 'Meeting not found', 'learnpress-live' ) );
				}

				LP_Addon_Live_Preload::$addon->get_template(
					'shortcode/single-google.php',
					array(
						'results' => $results,
					)
				);

			} catch ( Exception $e ) {
				echo $e->getMessage();
			}

			return ob_get_clean();
		}
	}
}

