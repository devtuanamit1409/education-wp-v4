<?php

namespace WCBT\Controllers;

use WCBT\Helpers\Config;
use WCBT\Helpers\Settings;
use WCBT\Helpers\Template;
use WCBT\Helpers\Variation;

class VariationController {
	private $config = array();
	private $data;

	public function __construct() {
		$this->config = Config::instance()->get( 'variation' );
		add_action( 'init', array( $this, 'set_data' ) );
		add_filter( 'product_attributes_type_selector', array( $this, 'attribute_types' ), 99 );
		add_action( 'woocommerce_product_option_terms', array( $this, 'product_option_terms' ), 10, 3 );

		//Add custom field to Woocommerce add new attribute / edit page
		add_action( 'woocommerce_after_add_attribute_fields', array( $this, 'add_wc_attribute' ) );
		add_action( 'woocommerce_after_edit_attribute_fields', array( $this, 'edit_wc_attribute' ) );
		//Save
		add_action( 'woocommerce_attribute_added', array( $this, 'save_wc_attribute' ) );
		add_action( 'woocommerce_attribute_updated', array( $this, 'save_wc_attribute' ) );
		//Delete
		add_action( 'woocommerce_attribute_deleted', array( $this, 'delete_wc_attribute' ) );

		//Add variable in shop page
		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'add_variation_to_product_list' ), 10 );
		add_action(
			'woocommerce_after_shop_loop_item_title',
			array(
				$this,
				'add_image_default'
			),
			35
		);

		//Change variable.php template
		add_action( 'wc_get_template', array( $this, 'variable_template' ), 10, 2 );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
	}

	public function set_data() {
		$this->data = Variation::get_data();
	}

	/**
	 * @param $template
	 * @param $template_name
	 *
	 * @return mixed|string|null
	 */
	public function variable_template( $template, $template_name ) {

		if ( $template_name !== 'single-product/add-to-cart/variable.php' ) {
			return $template;
		}
		$data = $this->data;
		if ( $data['mode'] !== 'on' ) {
			return $template;
		}

		return Template::instance( false )->get_frontend_template_type_classic( 'variable/variable.php' );
	}

	public function add_image_default() {
		$data = $this->data;
		if ( $data['mode'] !== 'on' ) {
			return;
		}

		if ( $data['display_on_product_list'] !== 'on' ) {
			return;
		}


		global $product;
		$image_id  = $product->get_image_id();
		$image_url = wp_get_attachment_image_url( $image_id, 'woocommerce_thumbnail' );

		?>
        <img style="display:none;" class="image-default" src="<?php echo esc_url( $image_url ); ?>" alt="">
		<?php
	}

	public function add_variation_to_product_list() {
		$data = $this->data;
		if ( $data['mode'] !== 'on' ) {
			return;
		}

		if ( $data['display_on_product_list'] !== 'on' ) {
			return;
		}

		global $product;
		if ( $product->get_type() === 'variable' ) {
			do_action( 'woocommerce_variable_add_to_cart' );
		}
	}

	/**
	 * @param $id
	 *
	 * @return void
	 */
	public function delete_wc_attribute( $id ) {
		delete_option( "wcbt-variation-type-$id" );
	}

	/**
	 * @param $id
	 *
	 * @return void
	 */
	public function save_wc_attribute( $id ) {
		if ( is_admin() && isset( $_POST['wcbt-variation-type'] ) ) {
			update_option( 'wcbt-variation-type-' . $id, sanitize_text_field( $_POST['wcbt-variation-type'] ) );
		}
	}

	/**
	 * @return void
	 */
	public function add_wc_attribute() {
		$data = $this->data;
		if ( $data['mode'] !== 'on' ) {
			return;
		}

		?>
        <div class="form-field">
            <label for="wcbt-variation-type"><?php esc_html_e( 'Variation Type', 'wcbt' ); ?></label>
            <select name="wcbt-variation-type" id="wcbt-variation-type">
                <option value="square"><?php esc_html_e( 'Square', 'wcbt' ); ?></option>
                <option value="circle"><?php esc_html_e( 'Circle', 'wcbt' ); ?></option>
            </select>
        </div>
		<?php
	}

	/**
	 * @return void
	 */
	public function edit_wc_attribute() {
		$data = $this->data;
		if ( $data['mode'] !== 'on' ) {
			return;
		}

		$id    = isset( $_GET['edit'] ) ? absint( $_GET['edit'] ) : 0;
		$value = $id ? get_option( "wcbt-variation-type-$id" ) : '';
		?>
        <tr class="form-field form-required">
            <th scope="row">
                <label for="wcbt-variation-type"><?php esc_html_e( 'Variation Type', 'wcbt' ); ?></label>
            </th>
            <td>
                <select name="wcbt-variation-type" id="wcbt-variation-type">
                    <option value="square" <?php selected( 'square', $value, true ); ?>>
						<?php esc_html_e( 'Square', 'wcbt' ); ?>
                    </option>
                    <option value="circle" <?php selected( 'circle', $value, true ); ?>>
						<?php esc_html_e( 'Circle', 'wcbt' ); ?>
                    </option>
                </select>
            </td>
        </tr>
		<?php
	}

	/**
	 * @return array
	 */
	public function attribute_types() {
		$types = array(
			'select' => esc_html__( 'Select', 'wcbt' ),
		);

		$data = $this->data;
		if ( $data['mode'] !== 'on' ) {
			return $types;
		}

		foreach ( $this->config as $key => $taxonomy ) {
			$types[ $key ] = $taxonomy['title'];
		}

		return $types;
	}

	/**
	 * @param $attribute_taxonomy
	 * @param $i
	 * @param $attribute
	 *
	 * @return void
	 */
	public function product_option_terms( $attribute_taxonomy, $i, $attribute ) {
        $data = $this->data;
		if ( $data['mode'] !== 'on' ) {
			return;
		}

		if ( 'select' !== $attribute_taxonomy->attribute_type &&
		     in_array( $attribute_taxonomy->attribute_type, array_keys( $this->attribute_types() ) ) ) {
			$name = sprintf( 'attribute_values[%s][]', esc_attr( $i ) );
			?>
            <select multiple="multiple"
                    data-placeholder="<?php esc_attr_e( 'Select terms', 'wcbt' ); ?>"
                    class="multiselect attribute_values wc-enhanced-select" name="<?php echo esc_attr( $name ) ?>">
				<?php
				$args      = array(
					'orderby'    => empty( $attribute_taxonomy->attribute_orderby ) ? 'name' :
						$attribute_taxonomy->attribute_orderby,
					'hide_empty' => 0,
				);
				$all_terms = get_terms(
					$attribute->get_taxonomy(),
					apply_filters( 'woocommerce_product_attribute_terms', $args )
				);
				if ( $all_terms ) {
					foreach ( $all_terms as $term ) {
						$options  = $attribute->get_options();
						$options  = ! empty( $options ) ? $options : array();
						$selected = in_array( $term->term_id, $options );
						?>
                        <option value="<?php echo esc_attr( $term->term_id ); ?>"
							<?php selected( true, $selected ); ?>>
							<?php
							echo esc_html( apply_filters(
								'woocommerce_product_attribute_term_name',
								$term->name,
								$term
							) );
							?>
                        </option>
						<?php
					}
				}
				?>
            </select>
            <button class="button plus select_all_attributes"><?php esc_html_e( 'Select all', 'wcbt' ); ?></button>
            <button class="button minus select_no_attributes"><?php esc_html_e( 'Select none', 'wcbt' ); ?></button>
            <button class="button fr plus add_new_attribute"><?php esc_html_e( 'Add new', 'wcbt' ); ?></button>
			<?php
		}
	}

	/**
	 * @return void
	 */
	public function wp_enqueue_scripts() {
		$data = $this->data;
		if ($data['mode'] !== 'on') {
			return;
		}

		wp_localize_script('wcbt-global', 'WCBT_VARIATION_OBJECT', $data);
	}
}
