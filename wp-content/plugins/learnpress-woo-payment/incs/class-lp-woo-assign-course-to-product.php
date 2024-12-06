<?php
/**
 * Class LP_Woo_Assign_Course_To_Product
 *
 * @version 1.0.0
 * @author  minhpd
 * @since 4.0.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LP_Woo_Assign_Course_To_Product {
	/**
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * @var string
	 */
	public static $meta_key_lp_woo_courses_assigned = '_lp_woo_courses_assigned';

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	protected function __construct( $product = 0 ) {
		// add tab
		add_filter( 'woocommerce_product_data_tabs', array( $this, 'courses_data_tabs' ), 11, 1 );
		add_action( 'woocommerce_product_data_panels', array( $this, 'courses_product_panels' ) );
		add_action( 'admin_head', array( $this, 'wcpp_custom_style' ) );

		// save_meta_box
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_courses_data' ), 10, 1 );
		add_filter( 'woocommerce_product_tabs', array( $this, 'view_courses_by_product' ), 100, 1 );

		// Show message on archive courses
		add_filter( 'lp/template/archive-course/description', array( $this, 'archive_courses' ), 99 );

		// Hook show button purchase
		add_filter( 'learnpress/course/template/button-purchase/can-show', array( $this, 'can_show_button_purchase' ), 10, 3 );
		// Hook show button enroll
		add_filter( 'learnpress/course/template/button-enroll/can-show', array( $this, 'can_show_button_add_to_cart' ), 10, 3 );
		// Hook show price course
		add_filter( 'learn_press_course_price_html_free', array( $this, 'hide_show_price_course' ), 10, 3 );
		add_filter( 'learn_press_course_price_html', array( $this, 'hide_show_price_course' ), 10 );
		// Set quantity
		add_filter( 'woocommerce_add_to_cart_quantity', array( $this, 'set_quantity' ), 10, 2 );
		// Create LP Order when WC Order created manual completed
		//add_action( 'woocommerce_process_shop_order_meta', array( $this, 'create_lp_order_by_woo_order_manual' ), 55, 2 );
		// add notice when use purchase course via product
		add_action( 'learn-press/course-summary-sidebar', array( $this, 'notice_purchase_course_via_product' ), 15 );
	}

	/**
	 * Create lp_order when create by woo order manual
	 *
	 * @author minhpd
	 * @version 1.0.0
	 * @since 4.0.3
	 */
	public function create_lp_order_by_woo_order_manual( $post_id, $post ) {
		if ( ! $post_id ) {
			return;
		}

		$lp_woo_order = new LP_Woo_Order( 0, $post_id );
		$lp_woo_order->create_lp_order();
	}

	/**
	 * Hook set not show button purchase when enable payment via product
	 *
	 * @param bool      $can_show
	 * @param LP_User   $user
	 * @param LP_Course $course
	 *
	 * @return bool
	 */
	public function can_show_button_purchase( bool $can_show, LP_User $user, LP_Course $course ): bool {
		return apply_filters( 'lp-woo/button-purchase/can-show', false );
	}

	/**
	 * Hook set not show button add to cart when enable payment via product
	 *
	 * @param bool      $can_show
	 * @param LP_User   $user
	 * @param LP_Course $course
	 * @return bool
	 * @throws Exception
	 * @author minpd
	 * @version 4.0.2
	 */
	public function can_show_button_add_to_cart( bool $can_show, LP_User $user, LP_Course $course ): bool {
		$can_show = $user->has_purchased_course( $course->get_id() );
		return apply_filters( 'lp-woo/button-add-to-cart/can-show', $can_show );
	}

	/**
	 * Hide price course
	 */
	public function hide_show_price_course( $price ) {
		if ( ! is_admin() ) {
			$price_new = '';
			return apply_filters( 'lp-woo/courses/price/can-show', $price_new, $price );
		}
		return $price;
	}

	/**
	 * Show message on archive courses
	 *
	 * @author minpd
	 * @version 4.0.2
	 */
	public function archive_courses() {
		$shop_page_url   = get_permalink( wc_get_page_id( 'shop' ) );
		$shop_page_title = get_the_title( wc_get_page_id( 'shop' ) );

		$html = sprintf(
			'<p class="course-archive-message-by-via-product">%s %s %s</p>',
			__( 'If you want to buy courses, please go to the', 'learnpress-woo-payment' ),
			'<a href="' . esc_attr( $shop_page_url ) . '"><i>' . esc_html( $shop_page_title ) . '</i></a>',
			__( 'page to buy products assigned courses!', 'learnpress-woo-payment' )
		);

		echo $html;
	}

	/**
	 * Add course data tabs product
	 *
	 * @param array $tabs
	 * @return array
	 */
	public function courses_data_tabs( array $tabs ): array {
		$tabs['course_data'] = array(
			'label'  => __( 'Courses', 'learnpress-woo-payment' ),
			'target' => 'course_product_data',
		);

		return $tabs;
	}

	public function metabox() {
		global $post;

		$filter         = new LP_Course_Filter();
		$filter->fields = array( 'ID', 'post_title' );
		$filter->limit  = -1;
		$courses_query  = LP_Course::get_courses( $filter );
		$courses        = array();

		foreach ( $courses_query as $course ) {
			$courses[ $course->ID ] = $course->post_title;
		}

		$values = get_post_meta( $post->ID, self::$meta_key_lp_woo_courses_assigned, true ) ?? array();

		return array(
			self::$meta_key_lp_woo_courses_assigned => new LP_Meta_Box_Select_Field(
				__( 'Assign courses to this product', 'learnpress-woo-payment' ),
				'',
				'',
				array(
					'options'  => $courses,
					'multiple' => true,
					'value'    => $values,
				)
			),
			// After LP v4.1.7.3 release, can use below code to instead of.
			/*self::$meta_key_lp_woo_courses_assigned => new LP_Meta_Box_Autocomplete_Field(
				__( 'Assign courses to this product', 'learnpress-woo-payment' ),
				'',
				$courses,
				array(
					'placeholder' => esc_html__( 'Search courses...', 'learnpress-prerequisites-courses' ),
					'data'        => 'lp_course',
				)
			),*/
		);
	}

	/**
	 * Add content tabs courses product
	 */
	public function courses_product_panels() {
		global $post;

		echo '<div id="course_product_data" class="panel woocommerce_options_panel hidden">';

		foreach ( $this->metabox() as $key => $object ) {
			$object->id = $key;
			echo $object->output( $post->ID );
		}

		echo '</div>';
	}

	/**
	 * Save courses data
	 */
	public function save_courses_data( $post_id ) {

		$multile = apply_filters( 'lp/woo/single-product/multiple-course', true );
		if ( ! isset( $_POST[ self::$meta_key_lp_woo_courses_assigned ] ) && ! $multile ) {
			return;
		}

		$courses_data = ! empty( $_POST[ self::$meta_key_lp_woo_courses_assigned ] ) ? (array) LP_Helper::sanitize_params_submitted( $_POST[ self::$meta_key_lp_woo_courses_assigned ] ) : array();

		update_post_meta( $post_id, self::$meta_key_lp_woo_courses_assigned, $courses_data, false );
	}

	/**
	 * CSS To Add Custom tab Icon
	 */
	public function wcpp_custom_style() {
		$screen = get_current_screen();
		if ( ! $screen || $screen->id != 'product' ) {
			return;
		}
		?>
		<style>
		#woocommerce-product-data ul.wc-tabs li.course_data_options a:before { font-family: WooCommerce; content: '\e006'; }
		</style>
		<script>
			jQuery(document).ready(function ($) {
				if ( $.fn.select2 ) {
					$( '.lp-select-2 select' ).select2({ width: '50%' });
				}
			});
		</script>
		<?php
	}

	/**
	 * Add tabs show list courses by product
	 */
	public function view_courses_by_product( $tabs ) {
		global $post;

		$courses = get_post_meta( $post->ID, self::$meta_key_lp_woo_courses_assigned, true );

		if ( ! empty( $courses ) ) {
			$tabs['_courses_data'] = array(
				'title'    => __( 'Courses', 'learnpress-woo-payment' ),
				'priority' => 100,
				'callback' => array( $this, 'content_tabs_courses' ),
			);
		}

		return $tabs;
	}

	public function content_tabs_courses() {
		global $post;
		wp_enqueue_style( 'lp-woo-css' );
		$courses = get_post_meta( $post->ID, self::$meta_key_lp_woo_courses_assigned, true );
		$user    = learn_press_get_current_user();

		echo '<ul class="list-courses-assign-product">';
		foreach ( $courses as $course_id ) {
			$can_purchase = $user->can_purchase_course( $course_id );
			echo '<li> <a href=' . get_permalink( $course_id ) . '>' . get_the_title( $course_id ) . ( is_wp_error( $can_purchase ) ? '(' . $can_purchase->get_error_message() . ')' : '' ) . '</a></li>';
		}
		echo '</ul>';
	}

	/**
	 * Product has courses only add one time to cart
	 *
	 * @param int $quantity
	 * @param int $product_id
	 * @return int
	 * @since 4.0.2
	 * @author tungnx
	 */
	public function set_quantity( int $quantity, int $product_id ): int {
		$product_has_courses = get_post_meta( $product_id, self::$meta_key_lp_woo_courses_assigned, true );

		if ( ! empty( $product_has_courses ) ) {
			$message  = __( 'Product which has courses is only added one time.', 'learnpress-woo-payment' );
			$cart     = WC()->cart;
			$cart_key = $cart->generate_cart_id( $product_id );

			if ( array_key_exists( $cart_key, $cart->cart_contents ) ) {
				$quantity = 0;

				wc_add_notice( $message );
			} elseif ( $quantity > 1 ) {
				wc_add_notice( $message );

				$quantity = 1;
			}
		}

		return $quantity;
	}

	/**
	 * Show notice on course.
	 * 1. If course is not assigned to any product.
	 * 2. Show list products assigned to course.
	 *
	 * @return void|string
	 */
	public function notice_purchase_course_via_product() {
		global $post;
		$user     = learn_press_get_user( get_current_user_id() );
		$enrolled = $user->has_enrolled_or_finished( $post->ID );

		if ( ! $enrolled ) {
			ob_start();
			LP_Addon_Woo_Payment_Preload::$addon->get_template( 'notice', array( 'course_id' => $post->ID ) );
			return ob_get_contents();
		}
	}
}

LP_Woo_Assign_Course_To_Product::instance();
