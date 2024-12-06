<?php
/**
 * myCRED learnpress instructor hook class.
 *
 * @author   ThimPress
 * @package  LearnPress/myCRED/Classes
 * @version  3.0.1
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'myCred_LearnPress_Instructor' ) ) {
	/**
	 * Class myCred_LearnPress_Instructor.
	 */
	class myCred_LearnPress_Instructor extends myCRED_Hook {

		/**
		 * myCred_LearnPress_Instructor constructor.
		 *
		 * @param $hook_prefs
		 * @param string $type
		 */
		public function __construct( $hook_prefs, $type = 'mycred_default' ) {
			$defaults = array(
				'course_50'  => array(
					'creds' => 5,
					'log'   => '%plural%' . ' ' . __( 'for having a course with more than 50 learners', 'learnpress-mycred' ),
				),
				'course_100' => array(
					'creds' => 10,
					'log'   => '%plural%' . ' ' . __( 'for having a course with more than 100 learners', 'learnpress-mycred' ),
				),
				'course_200' => array(
					'creds' => 20,
					'log'   => '%plural%' . ' ' . __( 'for having a course with more than 200 learners', 'learnpress-mycred' ),
				),
				'course_500' => array(
					'creds' => 100,
					'log'   => '%plural%' . ' ' . __( 'for having a course with more than 500 learners', 'learnpress-mycred' ),
				),
			);

			parent::__construct( array(
				'id'       => 'learnpress_instructor',
				'defaults' => $defaults
			), $hook_prefs, $type );
		}

		/**
		 * Hook into WordPress
		 */
		public function run() {
			// Action take a course
			add_action( 'learn_press_update_order_status', array( $this, 'check_course' ), 10, 2 );
		}

		/**
		 * Check number of students in a course.
		 *
		 * @param $status
		 * @param $order_id
		 */
		public function check_course( $status, $order_id ) {
			// Check if order is invalid
			if ( ! $order_id || $status != 'completed' ) {
				return;
			}

			$order = new LP_Order( $order_id );
			if ( ! $order ) {
				return;
			}
			if ( $items = $order->get_items() ) {
				foreach ( $items as $item ) {

					$course_id = ! empty( $item['course_id'] ) ? absint( $item['course_id'] ) : 0;
					if ( ! $course_id ) {
						continue;
					}
					$course = learn_press_get_course( $course_id );

					if ( ! $course ) {
						continue;
					}

					// Get author data
                    $user = learn_press_get_user( get_post_field( 'post_author', $course_id ) );
                    $is_instructor= $user->is_instructor();
					// Check if course is invalid
					if ( !$is_instructor ) {
						continue;
					}
                    $instructor_id = $user->get_id();
					// Check if user is excluded
					if ( $this->core->exclude_user( $instructor_id ) ) {
						continue;
					}

					$learners = $course->get_users_enrolled( true );
					// Check if course has no learners
					switch ( $learners ) {
						case 50:
							$course_type = 'course_50';
							break;
						case 100:
							$course_type = 'course_100';
							break;
						case 200:
							$course_type = 'course_200';
							break;
						case 500:
							$course_type = 'course_500';
							break;
						default:
							$course_type = 0;
							break;
						//return;
					}
					if ( ! $course_type ) {
						return;
					}

					// Make sure we award points other then zero
					if ( ! isset( $this->prefs[ $course_type ]['creds'] ) ) {
						continue;
					}
					if ( empty( $this->prefs[ $course_type ]['creds'] ) || $this->prefs[ $course_type ]['creds'] == 0 ) {
						continue;
					}

					// Execute
					$this->core->add_creds( 'learnpress_instructor', $instructor_id, $this->prefs[ $course_type ]['creds'], $this->prefs[ $course_type ]['log'], $course_id, array( 'ref_type' => 'post' ), $this->mycred_type
					);
				}
			}
		}

		/**
		 * Add Settings.
		 */
		public function preferences() {
			// Our settings are available under $this->prefs
			$prefs = $this->prefs;
			?>

            <label for="<?php echo $this->field_id( array( 'course_50' => 'creds' ) ); ?>"
                   class="subheader"><?php echo $this->core->template_tags_general( __( '%plural% for having course with more than 50 learners', 'learnpress-mycred' ) ); ?></label>
            <ol>
                <li>
                    <div class="h2">
                        <input type="text" name="<?php echo $this->field_name( array( 'course_50' => 'creds' ) ); ?>"
                               id="<?php echo $this->field_id( array( 'course_50' => 'creds' ) ); ?>"
                               value="<?php echo $this->core->number( $prefs['course_50']['creds'] ); ?>" size="8"/>
                    </div>
                </li>
            </ol>
            <label for="<?php echo $this->field_id( array( 'course_50' => 'log' ) ); ?>"
                   class="subheader"><?php _e( 'Log Template', 'learnpress-mycred' ); ?></label>
            <ol>
                <li>
                    <div class="h2">
                        <input type="text" name="<?php echo $this->field_name( array( 'course_50' => 'log' ) ); ?>"
                               id="<?php echo $this->field_id( array( 'course_50' => 'log' ) ); ?>"
                               value="<?php echo esc_attr( $prefs['course_50']['log'] ); ?>" class="long"/>
                    </div>
                    <span class="description"><?php echo $this->available_template_tags( array(
							'general',
							'post'
						) ); ?></span>
                </li>
            </ol>

            <label for="<?php echo $this->field_id( array( 'course_100' => 'creds' ) ); ?>"
                   class="subheader"><?php echo $this->core->template_tags_general( __( '%plural% for having course with more than 100 learners', 'learnpress-mycred' ) ); ?></label>
            <ol>
                <li>
                    <div class="h2">
                        <input type="text" name="<?php echo $this->field_name( array( 'course_100' => 'creds' ) ); ?>"
                               id="<?php echo $this->field_id( array( 'course_100' => 'creds' ) ); ?>"
                               value="<?php echo $this->core->number( $prefs['course_100']['creds'] ); ?>" size="8"/>
                    </div>
                </li>
            </ol>
            <label for="<?php echo $this->field_id( array( 'course_100' => 'log' ) ); ?>"
                   class="subheader"><?php _e( 'Log Template', 'learnpress-mycred' ); ?></label>
            <ol>
                <li>
                    <div class="h2">
                        <input type="text" name="<?php echo $this->field_name( array( 'course_100' => 'log' ) ); ?>"
                               id="<?php echo $this->field_id( array( 'course_100' => 'log' ) ); ?>"
                               value="<?php echo esc_attr( $prefs['course_100']['log'] ); ?>" class="long"/>
                    </div>
                    <span class="description"><?php echo $this->available_template_tags( array(
							'general',
							'post'
						) ); ?></span>
                </li>
            </ol>

            <label for="<?php echo $this->field_id( array( 'course_200' => 'creds' ) ); ?>"
                   class="subheader"><?php echo $this->core->template_tags_general( __( '%plural% for having course with more than 200 learners', 'learnpress-mycred' ) ); ?></label>
            <ol>
                <li>
                    <div class="h2">
                        <input type="text" name="<?php echo $this->field_name( array( 'course_200' => 'creds' ) ); ?>"
                               id="<?php echo $this->field_id( array( 'course_200' => 'creds' ) ); ?>"
                               value="<?php echo $this->core->number( $prefs['course_200']['creds'] ); ?>" size="8"/>
                    </div>
                </li>
            </ol>
            <label for="<?php echo $this->field_id( array( 'course_200' => 'log' ) ); ?>"
                   class="subheader"><?php _e( 'Log Template', 'learnpress-mycred' ); ?></label>
            <ol>
                <li>
                    <div class="h2">
                        <input type="text" name="<?php echo $this->field_name( array( 'course_200' => 'log' ) ); ?>"
                               id="<?php echo $this->field_id( array( 'course_200' => 'log' ) ); ?>"
                               value="<?php echo esc_attr( $prefs['course_200']['log'] ); ?>" class="long"/>
                    </div>
                    <span class="description"><?php echo $this->available_template_tags( array(
							'general',
							'post'
						) ); ?></span>
                </li>
            </ol>

            <label for="<?php echo $this->field_id( array( 'course_500' => 'creds' ) ); ?>"
                   class="subheader"><?php echo $this->core->template_tags_general( __( '%plural% for having course with more than 500 learners', 'learnpress-mycred' ) ); ?></label>
            <ol>
                <li>
                    <div class="h2">
                        <input type="text" name="<?php echo $this->field_name( array( 'course_500' => 'creds' ) ); ?>"
                               id="<?php echo $this->field_id( array( 'course_500' => 'creds' ) ); ?>"
                               value="<?php echo $this->core->number( $prefs['course_500']['creds'] ); ?>" size="8"/>
                    </div>
                </li>
            </ol>
            <label for="<?php echo $this->field_id( array( 'course_500' => 'log' ) ); ?>"
                   class="subheader"><?php _e( 'Log Template', 'learnpress-mycred' ); ?></label>
            <ol>
                <li>
                    <div class="h2">
                        <input type="text" name="<?php echo $this->field_name( array( 'course_500' => 'log' ) ); ?>"
                               id="<?php echo $this->field_id( array( 'course_500' => 'log' ) ); ?>"
                               value="<?php echo esc_attr( $prefs['course_500']['log'] ); ?>" class="long"/>
                    </div>
                    <span class="description"><?php echo $this->available_template_tags( array(
							'general',
							'post'
						) ); ?></span>
                </li>
            </ol>

			<?php
		}

		/**
		 * Sanitize Preferences
		 */
		public function sanitise_preferences( $data ) {
			return $data;
		}
	}
}
