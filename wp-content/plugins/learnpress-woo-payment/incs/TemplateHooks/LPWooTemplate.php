<?php
/**
 * Class LPWooTemplate
 *
 * @since 4.1.4
 * @version 1.0.0
 */

use LearnPress\Helpers\Singleton;
use LearnPress\Helpers\Template;

class LPWooTemplate {
	use Singleton;

	public function init() {
		add_action( 'learnpress/woo-payment/btn-add-item-to-cart/layout', [ $this, 'btn_add_to_cart' ] );
	}

	/**
	 * Button add item to cart
	 * Require item id and item type
	 *
	 * @param array $item
	 *
	 * @return void
	 */
	public function btn_add_to_cart( array $item ) {
		wp_enqueue_script( 'lp-woo-payment-js' );
		if ( empty( $item['id'] ) || empty( $item['type'] ) ) {
			return;
		}

		$is_added_to_cart = LP_WC_Hooks::instance()->is_added_in_cart( $item['id'] );

		$html_wrap = [
			'<div class="wrap-btn-add-course-to-cart">' => '</div>',
		];

		ob_start();

		if ( ! $is_added_to_cart ) {
			$html_wrap['<form name="form-add-item-to-cart" method="post">'] = '</form>';
			$section                                                        = [
				'button_add_to_cart' => [
					'text_html' => '<button class="lp-button" type="submit">' . __( 'Add to cart', 'learnpress-woo-payment' ) . '</button>'
				],
				'input_id'           => [
					'text_html' => '<input type="hidden" name="item-id" value="' . esc_attr( $item['id'] ) . '"/>'
				],
				'input_type'         => [
					'text_html' => '<input type="hidden" name="item-type" value="' . esc_attr( $item['type'] ) . '"/>'
				],
			];

			Template::instance()->print_sections( $section );
		} else {
			LP_Addon_Woo_Payment_Preload::$addon->get_template( 'view-cart' );
		}

		echo Template::instance()->nest_elements( $html_wrap, ob_get_clean() );
	}
}
