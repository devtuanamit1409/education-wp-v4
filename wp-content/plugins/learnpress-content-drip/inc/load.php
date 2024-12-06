<?php
/**
 * Plugin load class.
 *
 * @author   ThimPress
 * @package  LearnPress/Content-Drip/Classes
 * @version  3.0.0
 */

// Prevent loading this file directly
use LearnPress\Models\UserItems\UserCourseModel;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LP_Addon_Content_Drip' ) ) {
	/**
	 * Class LP_Addon_Content_Drip
	 */
	class LP_Addon_Content_Drip extends LP_Addon {
		/**
		 * Addon version
		 *
		 * @var string
		 */
		public $version = LP_ADDON_CONTENT_DRIP_VER;

		/**
		 * Require LP version
		 *
		 * @var string
		 */
		public $require_version = LP_ADDON_CONTENT_DRIP_REQUIRE_VER;

		/**
		 * Path file addon
		 *
		 * @var string
		 */
		public $plugin_file = LP_ADDON_CONTENT_DRIP_FILE;

		/**
		 * Metabox data
		 *
		 * @var null
		 */
		protected $_meta_box = null;

		/**
		 * @var array
		 */
		protected $_drip_items = array();

		/**
		 * LP_Addon_Content_Drip constructor.
		 */
		public function __construct() {
			parent::__construct();
		}

		/**
		 * Include files.
		 */
		protected function _includes() {
			require_once LP_ADDON_CONTENT_DRIP_INC_PATH . 'admin/class-drip-items-list-table.php';
			//api
			require_once LP_ADDON_CONTENT_DRIP_INC_PATH . 'admin/api/class-lp-content-drip-api.php';
			// Hooks
			require_once LP_ADDON_CONTENT_DRIP_INC_PATH . '/class-lp-content-drip-hooks.php';
			LP_Content_Drip_Hooks::instance();
		}

		/**
		 * It converts the time into seconds.
		 *
		 * @param int|float $number The number of units you want to convert.
		 * @param string $unit The unit of time you want to convert to seconds.
		 *
		 * @return float|int
		 */
		public function lpcd_data_to_seconds( $number, $unit ) {
			$weight_number = '';
			$seconds       = 60;
			if ( $unit == 'minute' ) {
				$weight_number = 60;
			} elseif ( $unit == 'hour' ) {
				$weight_number = $seconds * 60;
			} elseif ( $unit == 'day' ) {
				$weight_number = $seconds * 60 * 24;
			} elseif ( $unit == 'week' ) {
				$weight_number = $seconds * 60 * 24 * 7;
			}

			return $number * $weight_number;
		}

		/**
		 * This function is used to create an array of drip types
		 */
		public function lp_content_drip_types() {
			$types = array(
				'specific_date' => esc_html__( '1. Specific time after enrolled course', 'learnpress-content-drip' ),
				'sequentially'  => esc_html__( '2. Open the course items sequentially', 'learnpress-content-drip' ),
				'prerequisite'  => esc_html__( '3. Open item bases on prerequisite items', 'learnpress-content-drip' ),
			);

			return apply_filters( ' learn-press/content-drip/drip-types', $types );
		}

		/**
		 * Add email classes.
		 * Todo : not use
		 */
		public function add_content_drip_emails() {
			LP_Emails::instance()->emails['LP_Email_Drip_Item_Available'] = include 'class-lp-email-drip-item-available.php';
		}

		/**
		 * Check drip item
		 *
		 * @param LP_Course_Item $item
		 * @param LP_Course $course
		 * @param LP_Abstract_User $user
		 *
		 * @return array
		 */
		public function check_drip_item( LP_Course_Item $item, LP_Course $course, LP_Abstract_User $user ): array {
			$rs = [
				'locked'  => 0,
				'message' => '',
			];

			try {
				$item_author_id = get_post_field( 'post_author', $item->get_id() );
				// No apply for admin and author of course, author of item.
				if ( current_user_can( 'administrator' ) ||
				     $course->get_author( 'id' ) === $user->get_id() ||
				     $item_author_id === $user->get_id() ) {
					return $rs;
				}

				$course_id = $course->get_id();

				// Check content drip is enable.
				$enable = 'yes' === get_post_meta( $course_id, '_lp_content_drip_enable', true );
				if ( ! $enable ) {
					return $rs;
				}

				// Check has settings for each item of course.
				$drip_items = get_post_meta( $course_id, '_lp_drip_items', true );
				if ( ! $drip_items ) {
					return $rs;
				}

				// Get drip type
				$drip_type = get_post_meta( $course_id, '_lp_content_drip_drip_type', true );

				// Call new method get user course data.
				$user_course = $user->get_course_attend( $course_id );
				if ( ! $user_course instanceof UserCourseModel ) {
					return $rs;
				}

				// Still use old method get user course data, because check item below need it.
				$user_course = $user->get_course_data( $course_id );
				if ( ! $user_course ) {
					return $rs;
				}

				$drip_item = $drip_items[ $item->get_id() ] ?? false;
				if ( ! $drip_item ) {
					return $rs;
				}

				switch ( $drip_type ) {
					case 'sequentially':
						$args = compact( 'course', 'item', 'user_course', 'drip_item' );
						$this->drip_type_sequentially( $rs, $args );
						break;
					case 'specific_date':
						$args = compact( 'user_course', 'drip_item' );
						$this->drip_type_specific_date( $rs, $args );
						break;
					case 'prerequisite':
						$args = compact( 'course', 'item', 'user_course', 'drip_item' );
						$this->drip_type_prerequisite( $rs, $args );
						break;
					default:
						do_action( 'learn-press/content-drip/check-item', $course, $item, $user, $drip_type, $this );
						break;
				}
			} catch ( Throwable $e ) {
				error_log( 'check_drip_item_err: ' . $e->getMessage() );
			}

			return $rs;
		}

		/**
		 * Drip type Specific date
		 * Check delay time for each item of course.
		 *
		 * @param array $rs
		 * @param array $args
		 *
		 * @return void
		 */
		private function drip_type_specific_date( array &$rs, array $args ) {
			if ( ! isset( $args['user_course'] ) || ! $args['user_course'] instanceof LP_User_Item_Course ) {
				return;
			}
			$user_course = $args['user_course'];
			$drip_args   = $args['drip_item'] ?? [];

			$start_course_time            = $user_course->get_start_time()->getTimestamp();
			$drip_args['timestamp_point'] = $start_course_time;
			$this->delay_type( $rs, $drip_args );

			$rs = apply_filters( 'lp/content-drip/drip_type_specific_date', $rs, $args, $this );
		}

		/**
		 * Drip type Sequentially
		 * Check delay time for each item of course.
		 * Check item previous is completed.
		 *
		 * @param array $rs
		 * @param array $args
		 */
		private function drip_type_sequentially( array &$rs, array $args ) {
			if ( ! isset( $args['user_course'] ) || ! $args['user_course'] instanceof LP_User_Item_Course ||
			     ! isset( $args['course'] ) || ! $args['course'] instanceof LP_Course ||
			     ! isset( $args['item'] ) || ! $args['item'] instanceof LP_Course_Item ) {
				return;
			}

			$is_locked   = false;
			$user_course = $args['user_course'];
			$course      = $args['course'];
			$item        = $args['item'];
			$drip_args   = $args['drip_item'] ?? [];

			$start_course_time = $user_course->get_start_time()->getTimestamp();
			$item_ids          = $course->get_item_ids();
			$item_id           = $item->get_id();

			$first_item_id_course = $course->get_first_item_id();

			$drip_args['timestamp_point'] = $start_course_time;

			if ( $first_item_id_course !== $item_id ) {
				$item_id_prev = $item_ids[ array_search( $item_id, $item_ids ) - 1 ];
				$user_item    = $user_course->get_item( $item_id_prev );
				if ( ! $user_item || ! $user_item->is_completed() ) {
					$is_locked = true;
				}
				$is_locked = apply_filters( 'lp/content-drip/drip_type_sequentially/item-is-complete', $is_locked, $user_item, $args, $this );

				$rs['locked']  = $is_locked;
				$rs['message'] = sprintf(
					__( 'You must complete the item "<a href=%1$s>%2$s</a>" before this item is available.', 'learnpress-content-drip' ),
					$course->get_item_link( $item_id_prev ),
					get_the_title( $item_id_prev )
				);
			}

			// Check time delay
			if ( ! $rs['locked'] ) {
				$this->delay_type( $rs, $drip_args );
			}

			$rs = apply_filters( 'lp/content-drip/drip_type_sequentially', $rs, $args, $this );
		}

		/**
		 * Drip type: Prerequisite
		 * Check items required is completed.
		 * Check delay time for each item of course.
		 *
		 * @param array $rs
		 * @param array $args
		 *
		 * @return void
		 */
		private function drip_type_prerequisite( array &$rs, array $args ) {
			if ( ! isset( $args['user_course'] ) || ! $args['user_course'] instanceof LP_User_Item_Course ||
			     ! isset( $args['course'] ) || ! $args['course'] instanceof LP_Course ) {
				return;
			}

			$user_course                  = $args['user_course'];
			$course                       = $args['course'];
			$drip_args                    = $args['drip_item'] ?? [];
			$drip_args['timestamp_point'] = $user_course->get_start_time()->getTimestamp();
			$item_ids_must_completed      = $drip_args['prerequisite'] ?? [];
			$has_item_not_completed       = false;

			foreach ( $item_ids_must_completed as $item_must_complete ) {
				$user_item = $user_course->get_item( $item_must_complete );
				if ( ! $user_item || ! $user_item->is_completed() ) {
					$has_item_not_completed = true;
				}

				$has_item_not_completed = apply_filters(
					'lp/content-drip/drip_type_prerequisite/item-is-complete',
					$has_item_not_completed,
					$user_item,
					$args,
					$this
				);
			}

			if ( $has_item_not_completed ) {
				$item_must_completed = [];
				foreach ( $item_ids_must_completed as $item_must_complete_id ) {
					$item                  = get_post( $item_must_complete_id );
					$item_link             = $course->get_item_link( $item_must_complete_id );
					$item_must_completed[] = sprintf( '<a href="%s">%s</a>', $item_link, $item->post_title );
				}

				$item_must_completed_str = implode( ', ', $item_must_completed );

				$rs['locked']  = true;
				$rs['message'] = sprintf(
					__( 'You must complete the item: %s before this item is available.', 'learnpress-content-drip' ),
					$item_must_completed_str
				);
			}

			// Check time delay
			if ( ! $rs['locked'] ) {
				$this->delay_type( $rs, $drip_args );
			}

			$rs = apply_filters( 'lp/content-drip/drip_type_prerequisite', $rs, $args, $this );
		}

		/**
		 * Check delay time.
		 *
		 * @param array $rs
		 * @param array $drip_args
		 */
		private function delay_type( array &$rs, array $drip_args ) {
			try {
				$timestamp_point = $drip_args['timestamp_point'] ?? 0;
				$tz_offset       = get_option( 'gmt_offset' );
				if ( $tz_offset >= 0 ) {
					$tz_offset = '+' . $tz_offset;
				} else {
					$tz_offset = '-' . $tz_offset;
				}
				$wp_timezone = wp_timezone_string();
				$is_utc      = (int) $wp_timezone !== 0;
				$dateFormat  = get_option( 'date_format' );
				$timeFormat  = get_option( 'time_format' );

				if ( $is_utc ) {
					$wp_timezone = 'Timezone: UTC' . $wp_timezone;
				}

				//error_log( 'xxxx:' . print_r( $drip_args, true ) );
				switch ( $drip_args['type'] ?? '' ) {
					case 'specific':
						$date_str      = '';
						$timestamp_end = $drip_args['date'] ?? 0;
						$timestamp_now = time();
						$time_end      = new DateTime( '@' . $timestamp_end, new DateTimeZone( 'UTC' ) );
						$time_now      = new DateTime( 'now', new DateTimeZone( 'UTC' ) );
						$thirty_days   = 28 * 24 * 60 * 60;

						if ( $timestamp_now < $timestamp_end ) {
							$rs['locked'] = 1;

							if ( $timestamp_end - $timestamp_now > $thirty_days ) {
								$time_end_by_timezone_current = clone $time_end;
								$time_end_by_timezone_current->setTimezone( new DateTimeZone( $tz_offset ) );
								$date_str = $time_end_by_timezone_current->format( $dateFormat . ' ' . $timeFormat );
							} else {
								$date_str = 'around ' . self::format_human_time_diff( $time_now, $time_end );
							}

							$rs['message'] = sprintf(
								__( 'This item will be available in %1$s (%2$s)', 'learnpress-content-drip' ),
								$date_str,
								$wp_timezone
							);
						}
						break;
					case 'interval':
						$timestamp_duration = $drip_args['interval'][2] ?? 0;
						$timestamp_end      = $timestamp_point + $timestamp_duration;
						$time_end           = new DateTime( '@' . $timestamp_end, new DateTimeZone( 'UTC' ) );
						$timestamp_current  = time();
						$time_now           = new DateTime( 'now', new DateTimeZone( 'UTC' ) );

						if ( $timestamp_current < $timestamp_end ) {
							$rs['locked']  = 1;
							$rs['message'] = sprintf(
								__( 'This item will be available in around %1$s (%2$s)', 'learnpress-content-drip' ),
								$this->format_human_time_diff( $time_now, $time_end ),
								$wp_timezone
							);
						}
						break;
					default:
						break;
				}
			} catch ( Throwable $e ) {
				error_log( 'Delay calculate:' . $e->getMessage() );
			}
		}

		/**
		 * Display date human time diff.
		 * 1. Show number days, hours if >= 1 days
		 * 2. Show number hours, seconds if >= 1 hours
		 * 3. Show number seconds if < 1 hours
		 *
		 * @param DateTime $date_start
		 * @param DateTime $date_end
		 *
		 * @return string
		 * @since 4.0.3
		 * @version 1.0.1
		 */
		public static function format_human_time_diff( DateTime $date_start, DateTime $date_end ): string {
			$diff = $date_end->diff( $date_start );
			$week = floor( $diff->d / 7 );

			$format_date = '';
			$string      = array(
				'y' => '%y years',
				'm' => '%m months',
				'w' => '', // object don't have week, only add to custom week format
				'd' => '%d days, %h hours',
				'h' => '%h hours, %i minutes',
				'i' => '%i minutes, %s seconds',
				's' => '%s seconds',
			);

			foreach ( $string as $k => $v ) {
				if ( isset( $diff->{$k} ) && $diff->{$k} > 0 ) {
					if ( in_array( $k, array( 'y', 'm' ) ) ) {
						$date = new LP_Datetime( $date_end->getTimestamp() );
						$format_date = $date->format( LP_Datetime::I18N_FORMAT_HAS_TIME );
					} else {
						$format_date = $diff->format( $v );
					}
					break;
				} elseif ( 'w' === $k && $week > 0 ) {
					$day_remain  = $diff->d - $week * 7;
					$format_date = sprintf(
						'%d %s, %d %s', $week,
						_n( 'week', 'weeks', $week ),
						$day_remain,
						_n( 'day', 'days', $day_remain )
					);
					break;
				}
			}

			return $format_date;
		}
	}
}
