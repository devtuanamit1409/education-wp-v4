<?php

namespace WCBT\Widgets;

use WCBT\Helpers\Config;
use WCBT\Helpers\General;
use WCBT\Helpers\Template;
use WCBT\Helpers\ProductFilter as ProductFilterHelper;

class ProductFilter extends \WP_Widget {
	private $settings;

	public function __construct() {
		$this->settings = Config::instance()->get( 'product-filter', 'widgets' );
		parent::__construct(
			'wcbt-product-filter',
			esc_html__( '(WCBT) Product Filter', 'wcbt' ),
			array(
				'description' => esc_html__(
					'This widget only works on Archive Product page and Shop page.',
					'wcbt'
				),
			)
		);
	}

	/**
	 * @param $instance
	 *
	 * @return string|void
	 */
	public function form( $instance ) {
		?>
		<?php
	}

	/**
	 * @param $args
	 * @param $instance
	 *
	 * @return void
	 */
	public function widget( $args, $instance ) {
		ob_start();
		$data = ProductFilterHelper::get_data();
		if ( $data['enable'] && ! empty( $data['fields'] ) ) {
			echo General::ksesHTML( $args['before_widget'] );
			Template::instance()->get_frontend_template_type_classic( 'product-filter.php', compact( 'data' ) );
			echo General::ksesHTML( $args['after_widget'] );
		}

		$content = ob_get_clean();
		echo $content;
	}

	/**
	 * @param $new_instance
	 * @param $old_instance
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		return $old_instance;
	}
}
