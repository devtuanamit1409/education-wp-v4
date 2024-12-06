<?php

/**
 * Class LP_Addon_Gradebook
 *
 * @since 4.0.0
 * @author Nhamdv <daonham95@gmail.com>
 */
class LP_Addon_Gradebook extends LP_Addon {
	/**
	 * Gradebook version
	 *
	 * @var string
	 */
	public $version = LP_ADDON_GRADEBOOK_VER;

	/**
	 * LP require version
	 *
	 * @var null|string
	 */
	public $require_version = LP_ADDON_GRADEBOOK_REQUIRE_VER;

	/**
	 * Path file addon
	 *
	 * @var null|string
	 */
	public $plugin_file = LP_ADDON_GRADEBOOK_PLUGIN_FILE;

	/**
	 * LP_Addon_Gradebook constructor.
	 */
	public function __construct() {
		parent::__construct();

		add_filter( 'manage_lp_course_posts_columns', array( $this, 'manage_course_posts_columns' ) );
		add_action( 'manage_lp_course_posts_custom_column', array( $this, 'manage_course_post_column' ), 10, 2 );

		add_action( 'admin_menu', array( $this, 'register_submenu_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_enqueue_scripts_on_admin' ) );

		// Add tab to profile.
		add_filter( 'learn-press/profile-tabs', array( $this, 'profile_tabs' ) );
	}

	protected function _includes() {
		require_once LP_ADDON_GRADEBOOK_PLUGIN_PATH . '/inc/functions.php';
		require_once LP_ADDON_GRADEBOOK_PLUGIN_PATH . '/inc/class-database.php';
		require_once LP_ADDON_GRADEBOOK_PLUGIN_PATH . '/inc/class-rest-controller.php';
	}

	public function load_enqueue_scripts_on_admin( $hook ) {
		// course-gradebook added in add_submenu_page.
		if ( ! strpos( $hook, 'course-gradebook' ) ) {
			return;
		}

		$url    = '/build/min/gradebook';
		$suffix = '.min';

		if ( LP_Debug::is_debug() && apply_filters( 'learnpress/gradebook/enqueue/debug/enable', true ) ) { // add hook for child theme.
			$url    = '/build/gradebook';
			$suffix = '';
		}

		$file_info = include LP_ADDON_GRADEBOOK_PLUGIN_PATH . $url . $suffix . '.asset.php';

		wp_enqueue_script(
			'learnpress-gradebook-admin',
			plugins_url( $url . $suffix . '.js', LP_ADDON_GRADEBOOK_PLUGIN_FILE ),
			$file_info['dependencies'],
			$file_info['version'],
			true
		);

		wp_enqueue_style( 'learnpress-gradebook-admin', plugins_url( $url . '.css', LP_ADDON_GRADEBOOK_PLUGIN_FILE ), array(), $file_info['version'] );

		// Translate file need file name: learnpress-gradebook-en_US-learnpress-gradebook-admin.json in folder languages
		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'learnpress-gradebook-admin', 'learnpress-gradebook', LP_ADDON_GRADEBOOK_PLUGIN_PATH . '/languages' );
		}
	}

	public function register_submenu_page() {
		if ( ! isset( $_GET['page'] ) || 'course-gradebook' !== $_GET['page'] ) {
			return;
		}

		// Set default title for page, if not error strip_tags(): Passing null on WP.
		global $title;
		$title = 'Grade-book';

		add_submenu_page(
			'',
			esc_html__( 'Course Gradebook', 'learnpress-gradebook' ),
			'course-gradebook',
			'edit_published_lp_courses',
			'course-gradebook',
			array( $this, 'add_submenu_page_callback' )
		);
	}

	/**
	 * Admin gradebook callback.
	 */
	public function add_submenu_page_callback() {
		?>
		<div id="learnpress-gradebook-react"></div>
		<?php
	}
	/**
	 * Add grade book column to course page in admin.
	 *
	 * @param  array $column
	 *
	 * @return array
	 */
	public function manage_course_posts_columns( $column ) {
		$date                = ! empty( $column['date'] ) ? $column['date'] : null;
		$column['gradebook'] = esc_html__( 'Gradebook', 'learnpress-gradebook' );

		if ( $date ) {
			unset( $column['date'] );
			$column['date'] = $date;
		}

		return $column;
	}

	/**
	 * Add the grade book column content.
	 *
	 * @param $column
	 * @param $post_id
	 */

	public function manage_course_post_column( $column, $post_id ) {
		switch ( $column ) {
			case 'gradebook':
				printf(
					'<a class="button" href="%s">%s</a>',
					learn_press_gradebook_nonce_url( array( 'course_id' => $post_id ) ),
					esc_html__( 'View', 'learnpress-gradebook' )
				);
				break;
		}
	}

	/**
	 * Add custom tabs into user's profile.
	 *
	 * @param array $tabs
	 *
	 * @return mixed
	 */
	public function profile_tabs( $tabs ) {
		// Only admin or instructor can view.
		if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'lp_teacher' ) ) {
			return $tabs;
		}

		$tabs['gradebook'] = array(
			'title'    => esc_html__( 'Gradebook', 'learnpress-gradebook' ),
			'slug'     => 'gradebook',
			'callback' => array( $this, 'profile_tab_content' ),
			'priority' => 12,
			'icon'     => '<i class="fa fa-database" aria-hidden="true"></i>',
		);

		return $tabs;
	}

	/**
	 * Content of profile courses page.
	 */
	public function profile_tab_content() {
		?>
		<div>
			<a href="<?php echo esc_url( admin_url( '/edit.php?post_type=lp_course' ) ); ?>" class="button">
				<?php esc_html_e( 'Go to Gradebook', 'learnpress-gradebook' ); ?>
			</a>
		</div>
		<?php
	}
}
