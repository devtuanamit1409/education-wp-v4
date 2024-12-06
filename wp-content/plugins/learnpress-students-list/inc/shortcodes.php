<?php
/**
 * Students list shortcode class.
 *
 * @author   ThimPress
 * @package  LearnPress/Students-List/Classes
 * @version  3.0.1
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LP_Abstract_Shortcode' ) ) {
	return;
}

if ( ! class_exists( 'LP_Students_List_Shortcode' ) ) {
	/**
	 * Class LP_Students_List_Shortcode.
	 */
	class LP_Students_List_Shortcode extends LP_Abstract_Shortcode {

		/**
		 * LP_Students_List_Shortcode constructor.
		 *
		 * @param mixed $atts
		 */
		public function __construct( $atts = '' ) {
			parent::__construct( $atts );
			$this->_atts = shortcode_atts(
				array(
					'title'     => '',
					'course_id' => '',
					'limit'     => '',
					'filter'    => '',
				),
				$this->_atts
			);
		}

		/**
		 * Shortcode output.
		 *
		 * @return mixed|string
		 */
		public function output() {
			wp_enqueue_style( 'addon-lp-students-list' );
			wp_enqueue_script( 'addon-lp-students-list' );

			$atts = $this->_atts;

			if ( $atts['title'] ) { ?>
				<h3 class="students-list-title"><?php echo esc_html( $atts['title'] ); ?></h3>
				<?php
			}

			if ( empty( $atts['course_id'] ) ) {
				echo __( 'Please enter Course ID.', 'learnpress-students-list' );
			} else {
				$course_id  = sanitize_text_field( $atts['course_id'] );
				$course_ids = explode( ',', $course_id );
				ob_start();

				foreach ( $course_ids as $course_id ) {
					$course = learn_press_get_course( $course_id );
					if ( ! $course ) {
						echo __( 'Course ID invalid, please check it again.', 'learnpress-students-list' );
					} else {
						echo sprintf(
							'%s: <a href="%s">%s</a>',
							esc_html__( 'Course', 'learnpress-students-list' ),
							$course->get_permalink(),
							$course->get_title()
						);

						do_action( 'lp-addon-students-list/students-list/layout', $course );
					}
				}

				return ob_get_clean();
			}

			return false;
		}
	}
}

new LP_Students_List_Shortcode();
