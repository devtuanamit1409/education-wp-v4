<?php
/**
 * Class LP_User_Item_H5p
 *
 * @author  ThimPress
 * @package LearnPress/H5P/Classes
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'LP_User_Item_H5p' ) ) {

	/**
	 * Class LP_User_Item_Assignment
	 */
	class LP_User_Item_H5p extends LP_User_Item {
		/**
		 * @var array
		 */
		protected $_answers = array();

		/**
		 * LP_User_Item_H5p constructor.
		 *
		 * @param array $data
		 */
		public function __construct( $data ) {
			$this->_curd = new LP_H5p_CURD();

			parent::__construct( $data );
		}

		/**
		 * Get ID of the course that this item assigned to.
		 *
		 * @return array|mixed
		 */
		public function get_course_id() {
			return $this->get_data( 'ref_id' );
		}

		/**
		 * @param string $context
		 *
		 * @return bool|float|int|LP_Duration|string
		 */
		public function get_time_interval( $context = '' ) {
			$interval = parent::get_time_interval();

			if ( $context == 'display' ) {
				$h5p = $this->get_h5p();

				if ( $interval && $h5p->get_duration() ) {
					$interval = new LP_Duration( $interval );
					$interval = $interval->to_timer();
				} else {
					$interval = '--:--';
				}
			}

			return $interval;
		}

		/**
		 * @return bool|LP_Assignment
		 */
		public function get_h5p() {
			return LP_H5p::get_h5p( $this->get_item_id() );
		}

		/**
		 * Return true of item is completed/evaluated.
		 *
		 * @param array $status
		 *
		 * @return bool
		 */
		public function is_completed( $status = array( 'completed' ) ) {
			return in_array( $this->get_status(), $status );
		}
	}
}
