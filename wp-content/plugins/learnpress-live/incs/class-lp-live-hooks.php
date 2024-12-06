<?php

defined( 'ABSPATH' ) || exit();

class LP_Live_Hooks {
	private static $instance;

	protected function __construct() {
		$this->hooks();
	}

	protected function hooks() {
		add_action( 'learn-press/rewrite/tags', array( $this, 'add_rewrite_tags' ) );
		add_action( 'learn-press/rewrite/rules', array( $this, 'add_rewrite_rules' ) );
		add_action( 'admin_bar_menu', array( $this, 'add_admin_menu' ), 80 );
		add_action( 'template_include', array( $this, 'template_includes' ), 1000 );
		add_action( 'init', array( $this, 'add_shortcode_meetings' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_template_frontend' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_template_admin' ) );
		//show meeting in single item course
		add_filter( 'lp/metabox/lesson/lists', array( $this, 'admin_meta_box_v4' ), 10, 1 );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

	}

	/**
	 * Add admin menu.
	 */
	public function admin_menu() {
		global $submenu;
		$permalink                = LP_Addon_Live_Preload::$addon->url_page_setting();
		$submenu['learn_press'][] = array( __( 'LearnPress Live', 'learnpress-live' ), 'manage_options', $permalink );
	}

	public function admin_meta_box_v4( $meta_boxes ) {
		if ( empty( $meta_boxes ) ) {
			return;
		}

		$meeting_ids = array(
			'_lp_meeting_zoom_id'   => new LP_Meta_Box_Zooms(
				esc_html__( 'Zoom Meeting', 'learnpress-live' ),
				esc_html__( 'Select to show Zoom Meeting Information for Lesson. Choose and Update to take effect.', 'learnpress-live' ),
				''
			),
			'_lp_meeting_google_id' => new LP_Meta_Box_Google(
				esc_html__( 'Google Meet', 'learnpress-live' ),
				esc_html__( 'Select to show Google Meet Information for Lesson. Update to take effect.', 'learnpress-live' ),
				''
			),
		);

		$meta_boxes = array_merge( $meeting_ids, $meta_boxes );

		return $meta_boxes;
	}

	public function enqueue_scripts_template_frontend() {
		if ( is_singular( 'lp_course' ) ) {
			wp_enqueue_style( 'zoom-setting-template', LP_ADDON_LIVE_PLUGIN_URL . '/assets/live.css', array(), LP_ADDON_LIVE_VER );
		}
	}

	public function enqueue_scripts_template_admin() {
		wp_enqueue_script( 'zoom-setting-template-js', LP_ADDON_LIVE_PLUGIN_URL . '/assets/live.js', array( 'jquery' ), LP_ADDON_LIVE_VER );
	}

	public function add_shortcode_meetings() {
		add_shortcode( 'learn_press_zoom_meeting', array( $this, 'shortcode_zoom_callback' ), 10, 2 );
		add_shortcode( 'learn_press_google_meeting', array( $this, 'shortcode_google_callback' ), 10, 2 );
	}

	public function shortcode_zoom_callback( $atts, $content ) {
		$zoom_meeting = new LP_Shortcode_Zoom_Meeting( $atts );
		ob_start();
		learn_press_print_messages();
		$html = ob_get_clean();

		try {
			$html .= $zoom_meeting->output();
		} catch ( Exception $ex ) {
			$html .= $ex->getMessage();
		}

		return '<div class="learnpress_detail_meeting">' . $html . '</div>';
	}

	public function shortcode_google_callback( $atts, $content ) {
		$google_meeting = new LP_Shortcode_Google_Meeting( $atts );
		ob_start();
		learn_press_print_messages();
		$html = ob_get_clean();

		try {
			$html .= $google_meeting->output();
		} catch ( Exception $ex ) {
			$html .= $ex->getMessage();
		}

		return '<div class="learnpress_detail_meeting">' . $html . '</div>';
	}

	public function enqueue_scripts() {
		$v_rand = uniqid();
		if ( ! $this->can_view_meeting_setting() ) {
			return;
		}

		$user = learn_press_get_current_user();

		//check is config setting connect zoom
		$is_auth_zoom    = false;
		$data_token_zoom = get_user_meta( $user->get_id(), '_lp_zoom_token', true );
		if ( ! empty( $data_token_zoom->access_token ) ) {
			$is_auth_zoom = true;
		}

		//check is config setting connect google meet
		$is_auth_google    = false;
		$data_token_google = get_user_meta( $user->get_id(), '_lp_google_token', true );
		if ( ! empty( $data_token_google->access_token ) ) {
			$is_auth_google = true;
		}

		$info = include LP_ADDON_LIVE_PLUGIN_PATH . '/build/learnpress-live.asset.php';
		wp_enqueue_style( 'learnpress-live-setting', LP_ADDON_LIVE_PLUGIN_URL . '/build/learnpress-live.css', array(), $info['version'], false );
		wp_enqueue_script( 'learnpress-live-setting', LP_ADDON_LIVE_PLUGIN_URL . '/build/learnpress-live.js', $info['dependencies'], $info['version'], true );

		wp_localize_script(
			'learnpress-live-setting',
			'learnpress_live_setting',
			apply_filters(
				'learnpress_zoom_setting_localize_script',
				array(
					'page_slug'      => LP_Addon_Live_Preload::$addon->get_slug_page(),
					'site_url'       => home_url( '/' ),
					'admin_url'      => admin_url(),
					'logout_url'     => wp_logout_url( home_url() ),
					'is_admin'       => current_user_can( 'manage_options' ),
					'nonce'          => wp_create_nonce( 'wp_rest' ),
					'use_pmi'        => get_user_meta( $user->get_id(), '_lp_zoom_meeting_pmi', true ),
					'page_settings'  => LP_Addon_Live_Preload::$addon->url_page_setting(),
					'is_auth_zoom'   => $is_auth_zoom,
					'is_auth_google' => $is_auth_google,
				)
			)
		);
		wp_set_script_translations( 'learnpress-live-setting', 'learnpress-live', LP_ADDON_LIVE_PLUGIN_PATH . '/languages' );

		do_action( 'learnpress/addons/live/enqueue_scripts' );
	}

	/**
	 * @param $tags
	 *
	 * @return mixed
	 */
	public function add_rewrite_tags( $tags ) {
		$tags['%live-setting%'] = '(.*)';

		return $tags;
	}

	/**
	 * Add rewrite rules for Live
	 *
	 * @param array $rules
	 *
	 * @return array
	 */
	public function add_rewrite_rules( array $rules ) {
		$root_slug = LP_Addon_Live_Preload::$addon->get_slug_page();
		if ( ! $root_slug ) {
			return $rules;
		}

		// Dashboard
		$rules['lp-addon-live']['live'] = [
			"^$root_slug/?$" => 'index.php?live-setting=1',
		];
		// Settings
		$rules['lp-addon-live']['live-settings'] = [
			"^$root_slug/(settings)/?$" => 'index.php?live-setting=1',
		];
		//zooms
		$rules['lp-addon-live']['live-zoom'] = [
			"^$root_slug/(zooms)/?$" => 'index.php?live-setting=1',
		];
		//google
		$rules['lp-addon-live']['live-google'] = [
			"^$root_slug/(google)/?$" => 'index.php?live-setting=1',
		];

		return $rules;
	}

	public function add_admin_menu( $wp_admin_bar ) {
		if ( ! $this->can_view_meeting_setting() ) {
			return;
		}

		$title = esc_html__( 'LearnPress Live Settings', 'learnpress-live' );
		$href  = LP_Addon_Live_Preload::$addon->url_page_setting();

		$wp_admin_bar->add_node(
			array(
				'id'    => 'lp-zoom-setting',
				'title' => '
					<img style="width: 20px; height: 20px; padding: 0; line-height: 1.84615384; vertical-align: middle; margin: -6px 0 0 0;" src="data:image/png;base64,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">
					<span class="ab-label">' . $title . '</span>',
				'href'  => $href,
			)
		);
	}

	public function template_includes( $template ) {
		global $wp_query;
		if ( LP_Addon_Live_Preload::$addon->is_page_live_setting() ) {
			if ( $this->can_view_meeting_setting() ) {

				$this->setup_the_scripts();

				wp_head();
				?>

				<div id="learnpress-live-setting-root"></div>

				<?php
				wp_footer();
				return;
			} else {
				wp_redirect( home_url() );
				exit();
			}
		}

		return $template;
	}

	/**
	 * It removes all actions from `wp_head` and `wp_footer` and then adds back only the ones we want
	 */
	public function setup_the_scripts() {
		add_filter( 'show_admin_bar', '__return_false' );

		remove_all_actions( 'wp_head' );
		remove_all_actions( 'wp_print_styles' );
		remove_all_actions( 'wp_print_head_scripts' );
		remove_all_actions( 'wp_footer' );

		// Handle `wp_head`
		add_action( 'wp_head', 'wp_enqueue_scripts', 1 );
		add_action( 'wp_head', 'wp_print_styles', 8 );
		add_action( 'wp_head', 'wp_print_head_scripts', 9 );
		add_action( 'wp_head', 'wp_site_icon' );

		// Handle `wp_footer`
		add_action( 'wp_footer', 'wp_print_footer_scripts', 20 );

		// Handle `wp_enqueue_scripts`
		remove_all_actions( 'wp_enqueue_scripts' );

		// Also remove all scripts hooked into after_wp_tiny_mce.
		remove_all_actions( 'after_wp_tiny_mce' );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 999999 );

		do_action( 'learnpress/live-addon/init' );
	}

	public function can_view_meeting_setting() {
		return is_user_logged_in() && current_user_can( 'edit_lp_courses' );
	}

	public static function instance(): LP_Live_Hooks {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
