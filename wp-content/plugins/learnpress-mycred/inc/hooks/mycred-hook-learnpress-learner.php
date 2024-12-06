<?php
/**
 * myCRED learnpress learner hook class.
 *
 * @author   ThimPress
 * @package  LearnPress/myCRED/Classes
 * @version  3.0.1
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'myCred_LearnPress_Learner ' ) ) {
	/**
	 * Class myCred_LearnPress_Learner
	 */
	class myCred_LearnPress_Learner extends myCRED_Hook {

		/**
		 * myCred_LearnPress_Learner constructor.
		 *
		 * @param $hook_prefs
		 * @param string $type
		 */
		public function __construct( $hook_prefs, $type = 'mycred_default' ) {
			$defaults = array(
				'take_free_course' => array(
					'creds' => 1,
					'log'   => '%plural%' . ' ' . __( 'for taking a free course', 'learnpress-mycred' ),
					'limit' => '1/d',
				),
				'take_paid_course' => array(
					'creds' => 5,
					'log'   => '%plural%' . ' ' . __( 'for taking a paid course', 'learnpress-mycred' ),
					'limit' => '1/d',
				),
				'pass_course'      => array(
					'creds' => 5,
					'log'   => '%plural%' . ' ' . __( 'for passing a course', 'learnpress-mycred' ),
					'limit' => '1/d',
				),
			);

			parent::__construct(
				array(
					'id'       => 'learnpress_learner',
					'defaults' => $defaults,
				),
				$hook_prefs,
				$type
			);
		}

		/**
		 * Hook into WordPress
		 */
		public function run() {
			// Action take a course
			add_action( 'learn-press/order/status-changed', array( $this, 'enroll_course' ), 10, 3 );
			// Action pass a course
			add_action( 'learn-press/user-course-finished', array( $this, 'pass_course' ), 10, 3 );
		}

		/**
		 * Check if user enroll a course successfully
		 *
		 * @param $status
		 * @param $order_id
		 */
		public function enroll_course( $order_id, $old_status, $status ) {
			// Check if order is invalid
			if ( ! $order_id || $status != 'completed' ) {
				return;
			}

			$order = new LP_Order( $order_id );

			if ( ! $order ) {
				return;
			}
			$user_id = $order->get_user( 'ID' ); // get_post_meta( $order_id, '_learn_press_customer_id', true ) ? get_post_meta( $order_id, '_learn_press_customer_id', true ) : 0;
			// Check if user is invalid
			if ( ! $user_id ) {
				return;
			}

			// Check if user or order is invalid
			if ( $this->core->exclude_user( $user_id ) ) {
				return;
			}

			$items = $order->get_items();
			if ( $items ) {
				foreach ( $items as $item ) {
					//$take_course = get_post_meta( $order_id, '_learn_press_transaction_method', true ) == 'free' ? 'take_free_course' : 'take_paid_course';

					$course_id = ! empty( $item['course_id'] ) ? absint( $item['course_id'] ) : 0;
					if ( ! $course_id ) {
						continue;
					}
					$course = learn_press_get_course( $course_id );
					if ( ! $course ) {
						continue;
					}
					$take_course = $course->is_free() ? 'take_free_course' : 'take_paid_course';

					// Make sure we award points other then zero
					if ( ! isset( $this->prefs[ $take_course ]['creds'] ) ) {
						continue;
					}
					if ( empty( $this->prefs[ $take_course ]['creds'] ) || $this->prefs[ $take_course ]['creds'] == 0 ) {
						continue;
					}
					// Execute
					if ( ! $this->over_hook_limit( $take_course, 'learnpress_learner' . '_' . $take_course, $user_id ) ) {
						$this->core->add_creds(
							'learnpress_learner' . '_' . $take_course,
							$user_id,
							$this->prefs[ $take_course ]['creds'],
							$this->prefs[ $take_course ]['log'],
							$course_id,
							array( 'ref_type' => 'post' ),
							$this->mycred_type
						);
					}
				}
			}
		}

		/**
		 * Check if user passed a course.
		 *
		 * @param $course_id
		 * @param $user_id
		 * @param $result
		 *
		 * @throws Exception
		 */
		public function pass_course( $course_id, $user_id, $result ) {

			// Check if course or user is invalid
			if ( ! $course_id || ! $user_id ) {
				return;
			}

			// Check if user or order is invalid
			if ( $this->core->exclude_user( $user_id ) ) {
				return;
			}

			$user = learn_press_get_user( $user_id );
			// Check if user has not passed the course
			if ( ! $user->has_passed_course( $course_id ) ) {
				return;
			}

			// Make sure we award points other then zero
			if ( ! isset( $this->prefs['pass_course']['creds'] ) ) {
				return;
			}
			if ( empty( $this->prefs['pass_course']['creds'] ) || $this->prefs['pass_course']['creds'] == 0 ) {
				return;
			}
			// Execute
			if ( ! $this->over_hook_limit( 'pass_course', 'learnpress_learner_pass_course', $user_id ) ) {
				$this->core->add_creds(
					'learnpress_learner_pass_course',
					$user_id,
					$this->prefs['pass_course']['creds'],
					$this->prefs['pass_course']['log'],
					$course_id,
					array( 'ref_type' => 'post' ),
					$this->mycred_type
				);
			}
		}

		/**
		 * Add Settings.
		 */
		public function preferences() {
			// Our settings are available under $this->prefs
			$prefs = $this->prefs;
			?>

			<label for="<?php echo $this->field_id( array( 'take_free_course' => 'creds' ) ); ?>"
				   class="subheader"><?php echo $this->core->template_tags_general( __( '%plural% for taking a free course', 'learnpress-mycred' ) ); ?></label>
			<ol>
				<li>
					<div class="h2">
						<input type="text"
							   name="<?php echo $this->field_name( array( 'take_free_course' => 'creds' ) ); ?>"
							   id="<?php echo $this->field_id( array( 'take_free_course' => 'creds' ) ); ?>"
							   value="<?php echo $this->core->number( $prefs['take_free_course']['creds'] ); ?>"
							   size="8"/>
					</div>
				</li>
			</ol>
			<label for="<?php echo $this->field_id( array( 'take_free_course' => 'log' ) ); ?>"
				   class="subheader"><?php _e( 'Log Template', 'learnpress-mycred' ); ?></label>
			<ol>
				<li>
					<div class="h2">
						<input type="text"
							   name="<?php echo $this->field_name( array( 'take_free_course' => 'log' ) ); ?>"
							   id="<?php echo $this->field_id( array( 'take_free_course' => 'log' ) ); ?>"
							   value="<?php echo esc_attr( $prefs['take_free_course']['log'] ); ?>" class="long"/>
					</div>
					<span class="description">
					<?php
					echo $this->available_template_tags(
						array(
							'general',
							'post',
						)
					);
					?>
						</span>
				</li>
			</ol>
			<label class="subheader"><?php _e( 'Limit', 'learnpress-mycred' ); ?></label>
			<ol>
				<li>
					<?php echo $this->hook_limit_setting( $this->field_name( array( 'take_free_course' => 'limit' ) ), $this->field_id( array( 'take_free_course' => 'limit' ) ), $prefs['take_free_course']['limit'] ); ?>
				</li>
			</ol>

			<label for="<?php echo $this->field_id( array( 'take_paid_course' => 'creds' ) ); ?>"
				   class="subheader"><?php echo $this->core->template_tags_general( __( '%plural% for taking a paid course', 'learnpress-mycred' ) ); ?></label>
			<ol>
				<li>
					<div class="h2">
						<input type="text"
							   name="<?php echo $this->field_name( array( 'take_paid_course' => 'creds' ) ); ?>"
							   id="<?php echo $this->field_id( array( 'take_paid_course' => 'creds' ) ); ?>"
							   value="<?php echo $this->core->number( $prefs['take_paid_course']['creds'] ); ?>"
							   size="8"/>
					</div>
				</li>
			</ol>
			<label for="<?php echo $this->field_id( array( 'take_paid_course' => 'log' ) ); ?>"
				   class="subheader"><?php _e( 'Log Template', 'learnpress-mycred' ); ?></label>
			<ol>
				<li>
					<div class="h2">
						<input type="text"
							   name="<?php echo $this->field_name( array( 'take_paid_course' => 'log' ) ); ?>"
							   id="<?php echo $this->field_id( array( 'take_paid_course' => 'log' ) ); ?>"
							   value="<?php echo esc_attr( $prefs['take_paid_course']['log'] ); ?>" class="long"/>
					</div>
					<span class="description">
					<?php
					echo $this->available_template_tags(
						array(
							'general',
							'post',
						)
					);
					?>
						</span>
				</li>
			</ol>
			<label class="subheader"><?php _e( 'Limit', 'learnpress-mycred' ); ?></label>
			<ol>
				<li>
					<?php echo $this->hook_limit_setting( $this->field_name( array( 'take_paid_course' => 'limit' ) ), $this->field_id( array( 'take_paid_course' => 'limit' ) ), $prefs['take_paid_course']['limit'] ); ?>
				</li>
			</ol>

			<label for="<?php echo $this->field_id( array( 'pass_course' => 'creds' ) ); ?>"
				   class="subheader"><?php echo $this->core->template_tags_general( __( '%plural% for passing a course', 'learnpress-mycred' ) ); ?></label>
			<ol>
				<li>
					<div class="h2">
						<input type="text" name="<?php echo $this->field_name( array( 'pass_course' => 'creds' ) ); ?>"
							   id="<?php echo $this->field_id( array( 'pass_course' => 'creds' ) ); ?>"
							   value="<?php echo $this->core->number( $prefs['pass_course']['creds'] ); ?>" size="8"/>
					</div>
				</li>
			</ol>
			<label for="<?php echo $this->field_id( array( 'pass_course' => 'log' ) ); ?>"
				   class="subheader"><?php _e( 'Log Template', 'learnpress-mycred' ); ?></label>
			<ol>
				<li>
					<div class="h2">
						<input type="text" name="<?php echo $this->field_name( array( 'pass_course' => 'log' ) ); ?>"
							   id="<?php echo $this->field_id( array( 'pass_course' => 'log' ) ); ?>"
							   value="<?php echo esc_attr( $prefs['pass_course']['log'] ); ?>" class="long"/>
					</div>
					<span class="description">
					<?php
					echo $this->available_template_tags(
						array(
							'general',
							'post',
						)
					);
					?>
						</span>
				</li>
			</ol>
			<label class="subheader"><?php _e( 'Limit', 'learnpress-mycred' ); ?></label>
			<ol>
				<li>
					<?php echo $this->hook_limit_setting( $this->field_name( array( 'pass_course' => 'limit' ) ), $this->field_id( array( 'pass_course' => 'limit' ) ), $prefs['pass_course']['limit'] ); ?>
				</li>
			</ol>
			<?php
		}

		/**
		 * Sanitize Preferences
		 */
		public function sanitise_preferences( $data ) {

			$actions = array( 'take_free_course', 'take_paid_course', 'pass_course' );
			foreach ( $actions as $action ) {
				if ( isset( $data[ $action ]['limit'] ) && isset( $data[ $action ]['limit_by'] ) ) {
					$limit = sanitize_text_field( $data[ $action ]['limit'] );
					if ( $limit == '' ) {
						$limit = 0;
					}
					$data[ $action ]['limit'] = $limit . '/' . $data[ $action ]['limit_by'];
					unset( $data[ $action ]['limit_by'] );
				}
			}

			return $data;
		}
	}
}
