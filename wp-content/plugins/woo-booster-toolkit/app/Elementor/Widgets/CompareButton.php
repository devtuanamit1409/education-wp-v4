<?php

namespace WCBT\Elementor\Widgets;

use WCBT\Models\ProductModel;
use WCBT\Elementor\ElementorWCBT;

class CompareButton extends WishListButton {
    /**
     * @return string
     */
    public function get_name() {
        return WCBT_PREFIX . '-compare';
    }

    /**
     * @return string
     */
    public function get_title() {
        return WCBT_EL_PREFIX . ' Compare Button';
    }

    /**
     * @return string
     */
    public function get_icon() {
        return 'eicon-exchange';
    }

    /**
     * @return string[]
     */
    public function get_categories() {
        return ['wcbt-category'];
    }

    protected function _register_controls() {
        $this->_register_settings_general();
        $this->_register_style_general('compare', '.wcbt-product-compare');
        $this->_register_style_tooltip();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $product = wc_get_product();
        if (empty($product)) {
            return;
        }
        $product_id = $product->get_id();

        $data = ProductModel::get_product_data($product_id);
        if ($data['is_my_compare']) {
            $class = 'wcbt-product-compare active';
        } else {
            $class = 'wcbt-product-compare';
        }
        ?>
        <div class="<?php echo esc_attr($class); ?>" data-product-id="<?php echo esc_attr($data['product_id']); ?>" data-type="<?php echo esc_attr($data['compare_type']); ?>">
            <?php if (isset($settings['display']) && $settings['display'] == 'icon_tooltip') {
                if (!empty($settings['icon_wishlist']['value'])) {
                    \Elementor\Icons_Manager::render_icon($settings['icon_wishlist'], ['aria-hidden' => 'true']);
                    if (!empty($settings['text_tooltip']) && $data['is_my_compare']) {
                    ?>
                        <span class="tooltip">
                            <?php echo esc_html($settings['text_tooltip']); ?>
                        </span>
                    <?php
                    }
                    if (!$data['is_my_compare'] && !empty($settings['text_active_tooltip'])) {
                    ?>
                        <span class="tooltip">
                            <?php echo esc_html($settings['text_active_tooltip']); ?>
                        </span>
            <?php
                    }
                }
            } else {
                esc_html_e($settings['wishlist_text'], 'wcbt');
            }
            ?>
        </div>
    <?php
    }
}
