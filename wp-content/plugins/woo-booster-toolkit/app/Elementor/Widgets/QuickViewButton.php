<?php

namespace WCBT\Elementor\Widgets;

use WCBT\Elementor\Widgets\WishListButton;
use Elementor\Controls_Manager;
use WCBT\Models\ProductModel;
use WCBT\Helpers\Template;
use WCBT\Elementor\ElementorWCBT;

class QuickViewButton extends WishListButton {
    /**
     * @return string
     */
    public function get_name() {
        return WCBT_PREFIX . '-quickview';
    }

    /**
     * @return string
     */
    public function get_title() {
        return WCBT_EL_PREFIX . ' Quick View Button';
    }

    /**
     * @return string
     */
    public function get_icon() {
        return ' eicon-preview-medium';
    }

    /**
     * @return string[]
     */
    public function get_categories() {
        return ['wcbt-category'];
    }

    protected function _register_controls() {
        $this->_register_settings_general();
        $this->_register_style_general('quickview', '.wcbt-product-quick-view');
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
        ?>
        <div class="wcbt-product-quick-view" data-product-id="<?php echo esc_attr($data['product_id']); ?>" data-type="<?php echo esc_attr($data['quick_view_type']); ?>">
            <?php if (isset($settings['display']) && $settings['display'] == 'icon_tooltip') {
                if (!empty($settings['icon_wishlist']['value'])) {
                    \Elementor\Icons_Manager::render_icon($settings['icon_wishlist'], ['aria-hidden' => 'true']);
                    if (!empty($settings['text_tooltip'])) {
                    ?>
                        <span class="tooltip"><?php echo esc_html($settings['text_tooltip']); ?></span>
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
