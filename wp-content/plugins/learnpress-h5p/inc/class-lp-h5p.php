<?php
/**
 * Class LP_H5p.
 *
 * @author  ThimPress
 * @package LearnPress/H5P/Classes
 * @version 4.0.0
 */

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'LP_H5p' ) ) {

	/**
	 * Class LP_H5p
	 */
	class LP_H5p extends LP_Course_Item {
		/**
		 * @var array
		 *
		 * @deprecated
		 */
		protected static $_meta = array();

		/**
		 * @var string
		 */
		protected $_item_type = LP_H5P_CPT;

		/**
		 * @var int
		 */
		protected static $_loaded = 0;

		/**
		 * @var array
		 */
		protected $_data = array(
			'passing_grade' => 0,
			'h5p_interact'  => 0,
		);

		/**
		 * Constructor gets the post object and sets the ID for the loaded course.
		 *
		 * @param mixed $the_h5p
		 * @param mixed $args
		 */
		public function __construct( $the_h5p, $args = array() ) {

			// parent::__construct( $the_h5p, $args );

			$this->_curd = new LP_H5p_CURD();

			if ( is_numeric( $the_h5p ) && $the_h5p > 0 ) {
				$this->set_id( $the_h5p );
			} elseif ( $the_h5p instanceof self ) {
				$this->set_id( absint( $the_h5p->get_id() ) );
			} elseif ( ! empty( $the_h5p->ID ) ) {
				$this->set_id( absint( $the_h5p->ID ) );
			}
			if ( $this->get_id() > 0 ) {
				$this->load();
			}

			self::$_loaded ++;
			if ( self::$_loaded == 1 ) {
				add_filter( 'debug_data', array( __CLASS__, 'log' ) );
			}
		}

		/**
		 * Log debug data.
		 *
		 * @since 3.0.0
		 *
		 * @param $data
		 *
		 * @return array
		 */
		public static function log( $data ) {
			$data[] = __CLASS__ . '( ' . self::$_loaded . ' )';

			return $data;
		}

		/**
		 * @param string $context
		 *
		 * @return string
		 */
		public function get_heading_title( $context = '' ) {
			return $this->get_title( $context );
		}

		/**
		 * Load h5p data.
		 *
		 * @throws Exception
		 */
		public function load() {
			$this->_curd->load( $this );
		}

		/**
		 * Get default h5p meta.
		 *
		 * @since 3.0.0
		 *
		 * @return mixed
		 */
		public static function get_default_meta() {
			$meta = array(
				'passing_grade' => 50,
			);

			return apply_filters( 'learn-press/h5p/default-meta', $meta );
		}

		/**
		 * Save h5p data.
		 *
		 * @return mixed
		 *
		 * @throws Exception
		 */
		public function save() {
			if ( $this->get_id() ) {
				$return = $this->_curd->update( $this );
			} else {
				$return = $this->_curd->create( $this );
			}

			return $return;
		}

		/**
		 * @param $value
		 */
		public function set_passing_grade( $value ) {
			$this->_set_data( 'passing_grade', $value );
		}

		/**
		 * @return array|mixed
		 */
		public function get_passing_grade() {
			$value = $this->get_data( 'passing_grade' );

			return $value;
		}

		/**
		 * @param $value
		 */
		public function set_h5p_interact( $value ) {
			$this->_set_data( 'h5p_interact', $value );
		}

		/**
		 * @return array|mixed
		 */
		public function get_h5p_interact() {
			$value = $this->get_data( 'h5p_interact' );

			return $value;
		}

		/**
		 * Get duration of h5p
		 *
		 * @return LP_Duration
		 */
		public function get_duration() {
			$duration = parent::get_duration();

			return apply_filters( 'learn-press/h5p-duration', $duration, $this->get_id() );
		}

		/**
		 * Get h5p duration html.
		 *
		 * @return mixed
		 */
		public function get_duration_html() {
			$duration = get_post_meta( $this->get_id(), '_lp_duration', true );

			if ( absint( $duration ) > 1 ) {
				$duration .= 's';
			} else {
				$duration = esc_html__( 'Unlimited', 'learnpress-h5p' );
			}

			$duration = '<span>' . $duration . '</span>';

			return apply_filters( 'learn_press_h5p_duration_html', $duration, $this );
		}

		/**
		 * Get js localize script in frontend. [NOT USED]
		 *
		 * @return mixed
		 */
		public function get_localize() {
			$localize = array(
				'confirm_finish_h5p' => array(
					'title'   => esc_html__( 'Finish h5p', 'learnpress-h5p' ),
					'message' => esc_html__( 'Are you sure you want to finish this h5p?', 'learnpress-h5p' ),
				),
				'confirm_retake_h5p' => array(
					'title'   => esc_html__( 'Retake h5p', 'learnpress-h5p' ),
					'message' => esc_html__( 'Are you sure you want to retake this h5p?', 'learnpress-h5p' ),
				),
				'h5p_time_is_over'   => array(
					'title'   => esc_html__( 'Time\'s up!', 'learnpress-h5p' ),
					'message' => esc_html__( 'The time is up! Your h5p will automate come to finish', 'learnpress-h5p' ),
				),
				'finished_h5p'       => esc_html__( 'Congrats! You have finished this h5p', 'learnpress-h5p' ),
				'retaken_h5p'        => esc_html__( 'Congrats! You have re-taken this h5p. Please wait a moment and the page will reload', 'learnpress-h5p' ),
			);

			return apply_filters( 'learn_press_single_h5p_localize', $localize, $this );
		}

		/**
		 * __isset function.
		 *
		 * @param mixed $key
		 *
		 * @return bool
		 */
		public function __isset( $key ) {
			return metadata_exists( 'post', $this->get_id(), '_' . $key );
		}

		/**
		 * __get function.
		 *
		 * @param string $key
		 *
		 * @return mixed
		 * @deprecated 4.0.8
		 */
		public function __get( $key ) {
			_deprecated_function( __METHOD__, '4.0.8' );
			/*echo '@deprecated[' . $key . ']';
			learn_press_debug( debug_backtrace() );

			return false;*/
		}

		/**
		 * @param $feature
		 *
		 * @return mixed
		 * @throws Exception
		 */
		public function has( $feature ) {
			$args = func_get_args();
			unset( $args[0] );
			$method   = 'has_' . preg_replace( '!-!', '_', $feature );
			$callback = array( $this, $method );

			if ( is_callable( $callback ) ) {
				return call_user_func_array( $callback, $args );
			} else {
				throw new Exception( sprintf( __( 'The function %s doesn\'t exist', 'learnpress-h5p' ), $feature ) );
			}
		}

		/**
		 * @param mixed $the_h5p
		 * @param array $args
		 *
		 * @return LP_H5p|bool
		 */
		public static function get_h5p( $the_h5p = false, $args = array() ) {
			if ( is_numeric( $the_h5p ) && isset( LP_Global::$custom_posts['h5p'][ $the_h5p ] ) ) {
				return LP_Global::$custom_posts['h5p'][ $the_h5p ];
			}

			$the_h5p = self::get_h5p_object( $the_h5p );

			if ( ! $the_h5p ) {
				return false;
			}

			if ( isset( LP_Global::$custom_posts['h5p'][ $the_h5p->ID ] ) ) {
				return LP_Global::$custom_posts['h5p'][ $the_h5p->ID ];
			}

			if ( ! empty( $args['force'] ) ) {
				$force = ! ! $args['force'];
				unset( $args['force'] );
			} else {
				$force = false;
			}

			$key_args = wp_parse_args(
				$args,
				array(
					'id'   => $the_h5p->ID,
					'type' => $the_h5p->post_type,
				)
			);

			$key = LP_Helper::array_to_md5( $key_args );

			if ( $force ) {
				LP_Global::$custom_posts['h5p'][ $key ]         = false;
				LP_Global::$custom_posts['h5p'][ $the_h5p->ID ] = false;
			}

			if ( empty( LP_Global::$custom_posts['h5p'][ $key ] ) ) {
				$class_name = self::get_h5p_class( $the_h5p, $args );
				if ( is_string( $class_name ) && class_exists( $class_name ) ) {
					$h5p = new $class_name( $the_h5p->ID, $args );
				} elseif ( $class_name instanceof LP_Course_Item ) {
					$h5p = $class_name;
				} else {
					$h5p = new self( $the_h5p->ID, $args );
				}
				LP_Global::$custom_posts['h5p'][ $key ]         = $h5p;
				LP_Global::$custom_posts['h5p'][ $the_h5p->ID ] = $h5p;
			}

			return LP_Global::$custom_posts['h5p'][ $key ];
		}

		/**
		 * @param  string $h5p_type
		 *
		 * @return string|false
		 */
		private static function get_class_name_from_h5p_type( $h5pcontent_type ) {
			return LP_H5P_CPT === $h5pcontent_type ? __CLASS__ : 'LP_H5p_' . implode( '_', array_map( 'ucfirst', explode( '-', $h5pcontent_type ) ) );
		}

		/**
		 * Get the lesson class name
		 *
		 * @param  WP_Post $the_h5p
		 * @param  array   $args (default: array())
		 *
		 * @return string
		 */
		private static function get_h5p_class( $the_h5p, $args = array() ) {
			$h5pitem_id = absint( $the_h5p->ID );
			$type       = $the_h5p->post_type;

			$class_name = self::get_class_name_from_h5p_type( $type );

			// Filter class name so that the class can be overridden if extended.
			return apply_filters( 'learn-press/h5p/object-class', $class_name, $type, $h5pitem_id );
		}

		/**
		 * Get the h5p object
		 *
		 * @param  mixed $the_h5p
		 *
		 * @uses   WP_Post
		 * @return WP_Post|bool false on failure
		 */
		private static function get_h5p_object( $the_h5p ) {
			if ( false === $the_h5p ) {
				$the_h5p = get_post_type() === LP_H5P_CPT ? $GLOBALS['post'] : false;
			} elseif ( is_numeric( $the_h5p ) ) {
				$the_h5p = get_post( $the_h5p );
			} elseif ( $the_h5p instanceof LP_Course_Item ) {
				$the_h5p = get_post( $the_h5p->get_id() );
			} elseif ( ! ( $the_h5p instanceof WP_Post ) ) {
				$the_h5p = false;
			}

			return apply_filters( 'learn-press/h5p/post-object', $the_h5p );
		}

		/**
		 * Get template name of item.
		 *
		 * @return string
		 */
		/*
		public function get_template() {
			$item_type = $this->get_item_type();

			//return apply_filters( 'learn-press/section-item-h5p-template', LP_ADDON_ASSIGNMENT_PATH . '/templates/single-course/section/' . 'item-' . str_replace( 'lp_', '', $item_type ), $item_type );
		}*/

		/**
		 * @param mixed $offset
		 */
		public function offsetUnset( $offset ) {
			// Do not allow to unset value directly!
		}
	}
}
