<?php

namespace WCBT\Controllers;

use WCBT\Helpers\Settings;
use WCBT\Helpers\Config;
use WCBT\Helpers\Debug;
use WCBT\Helpers\Page;
use WCBT\Helpers\RestApi;

/**
 * Class EnqueueScriptsController
 * @package WCBT\Controllers
 */
class EnqueueScriptsController {

	/**
	 * @var mixed|string
	 */
	private $version_assets = WCBT_VERSION;


	/**
	 * EnqueueScripts constructor.
	 */
	public function __construct() {
		if ( Debug::is_debug() ) {
			$this->version_assets = uniqid();
		}
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
	}

	/**
	 * @param $args
	 *
	 * @return mixed|void
	 */
	public function can_load_asset( $args ) {
		$current_page = Page::get_current_page();

		$can_load = false;

		if ( count( $args['screens'] ) > 0 ) {
			if ( in_array( $current_page, $args['screens'] ) ) {
				$can_load = true;
			}
		} elseif ( count( $args['exclude_screens'] ) > 0 ) {
			if ( ! in_array( $current_page, $args['exclude_screens'] ) ) {
				$can_load = true;
			}
		} else {
			$can_load = true;
		}

		$is_on = 'admin';
		if ( ! is_admin() ) {
			$is_on = 'frontend';
		}

		return apply_filters(
			'wcbt/filter/' . $is_on . '/can-load-assets/' . $args['type'] . '/' . $args['handle'],
			$can_load,
			$current_page,
			$args['screens']
		);
	}

	/**
	 * @return void
	 */
	public function admin_enqueue_scripts() {
		$styles = Config::instance()->get( 'styles:admin' );

		$this->enqueue_css( $styles );

		$scripts          = Config::instance()->get( 'scripts:admin' );
		$register_scripts = $scripts['register'];
		$this->enqueue_js( $register_scripts );

		$this->localize_admin_script();

		$js_translation = $scripts['js-translation'];
		if ( ! empty( $js_translation ) ) {
			foreach ( $js_translation as $handle => $args ) {
				wp_set_script_translations( $handle, 'wcbt', $args['src'] );
			}
		}
	}

	/**
	 * @return void
	 */
	public function wp_enqueue_scripts() {
		$styles = Config::instance()->get( 'styles:frontend' );
		$this->enqueue_css( $styles );

		$scripts          = Config::instance()->get( 'scripts:frontend' );
		$register_scripts = $scripts['register'];
		$this->enqueue_js( $register_scripts );

		$this->localize_frontend_script();
	}

	/**
	 * @return void
	 */
	public function localize_frontend_script() {
		$compare_product_page_id = Settings::get_setting_detail( 'compare:fields:page' );

		if ( is_multisite() ) {
			$blog_detail = get_blog_details( null, true );
			$blog_id     = $blog_detail->blog_id;
		} else {
			$blog_id = '';
		}

		$price_format = get_woocommerce_price_format();

		if ( $price_format === '%1$s%2$s' ) {
			$price_format = 'left';
		} elseif ( $price_format === '%2$s%1$s' ) {
			$price_format = 'right';
		} elseif ( $price_format === '%1$s&nbsp;%2$s' ) {
			$price_format = 'left-space';
		} else {
			$price_format = 'right-space';
		}

		wp_localize_script(
			'wcbt-global',
			'WCBT_GLOBAL_OBJECT',
			array(
				'rest_url'                 => get_rest_url(),
				'rest_namespace'           => RestApi::generate_namespace(),
				'compare_product_page_url' => empty( $compare_product_page_id ) ? '' :
					get_permalink( $compare_product_page_id ),
				'user_id'                  => is_user_logged_in() ? get_current_user_id() : '',
				'is_multisite'             => is_multisite(),
				'blog_id'                  => $blog_id,
				'currency_symbol'          => get_woocommerce_currency_symbol(),
				'decimal_separator'        => wc_get_price_decimal_separator(),
				'thousand_separator'       => wc_get_price_thousand_separator(),
				'decimals'                 => wc_get_price_decimals(),
				'price_format'             => $price_format,
				'is_shop_page'             => is_shop(),
				'is_product_taxonomy_page' => is_product_taxonomy()
			)
		);
	}

	/**
	 * @return void
	 */
	public function localize_admin_script() {
		wp_localize_script(
			'wcbt-global',
			'WCBT_GLOBAL_OBJECT',
			array(
				'rest_namespace' => RestApi::generate_namespace(),
			)
		);
	}

	/**
	 * @param $styles
	 *
	 * @return void
	 */
	public function enqueue_css( $styles ) {
		foreach ( $styles as $handle => $args ) {
			wp_register_style(
				$handle,
				$args['src'] ?? '',
				$args['deps'] ?? array(),
				$this->version_assets,
				'all'
			);

			$can_load_asset = $this->can_load_asset(
				array(
					'handle'          => $handle,
					'screens'         => $args['screens'] ?? array(),
					'exclude_screens' => $args['exclude_screens'] ?? array(),
					'type'            => 'css',
				)
			);

			if ( $can_load_asset ) {
				wp_enqueue_style( $handle );
			}
		}
	}

	/**
	 * @param $register_scripts
	 *
	 * @return void
	 */
	public function enqueue_js( $register_scripts ) {
		foreach ( $register_scripts as $handle => $args ) {
			if ( isset( $args['condition'] ) && $args['condition'] === false ) {
				continue;
			}

			wp_register_script(
				$handle,
				$args['src'],
				$args['deps'] ?? array(),
				$this->version_assets,
				$args['in_footer'] ?? true
			);

			$can_load_asset = $this->can_load_asset(
				array(
					'handle'          => $handle,
					'screens'         => $args['screens'] ?? [],
					'exclude_screens' => $args['exclude_screens'] ?? [],
					'type'            => 'js',
				)
			);

			if ( $can_load_asset ) {
				wp_enqueue_script( $handle );
			}
		}
	}
}
