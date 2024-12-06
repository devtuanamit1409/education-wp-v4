<?php
if (! isset($field)) {
    return;
}

use WCBT\Helpers\General;

$sortable_class = empty($field->sortable) ? '' : 'wcbt-sortable';
?>
    <div class="<?php echo esc_attr(ltrim($field->class . ' ' . 'wcbt-field-wrapper wcbt-checkbox-wrapper')); ?>">
        <?php
        if (! empty($field->title)) {
            ?>
            <div class="wcbt-title-wrapper">
                <?php echo esc_html($field->title); ?>
            </div>
            <?php
        }

        if ($field->multiple) {
            ?>
            <div class="<?php echo esc_attr(ltrim($sortable_class . ' ' . 'wcbt-checkbox-content-wrapper')); ?>">
                <?php
                $options = $field->options;
                if ($field->sortable) {
                    $order = $field->value['order'] ?? '';
                    ?>
                    <input type="hidden" name="<?php echo esc_attr($field->name . '[order]'); ?>"
                           value="<?php echo esc_attr($order); ?>">
                    <?php

                    if (! empty($order)) {
                        $order       = explode(',', $order);
                        $new_options = array();
                        foreach ($order as $value) {
                            // If key exist in option value, not in config, remove option value
                            if (isset($options[ $value ])) {
                                $new_options[ $value ] = $options[ $value ];
                            }
                        }
                        // If key options exist in config, not in option value, add to option value
                        foreach ($options as $key => $value) {
                            if (! isset($new_options[ $key ])) {
                                $new_options[ $key ] = $value;
                            }
                        }
                        $options = $new_options;
                    }
                }
                foreach ($options as $option_name => $option_args) {
                    $value_checkbox = $field->value;
                    if (isset($value_checkbox['order'])) {
                        unset($value_checkbox['order']);
                    }
                    ?>
                    <div class="wcbt-checkbox-content">
                        <?php
                        if ($field->sortable) {
                            ?>
                            <span><i class="dashicons dashicons-move"></i></span>
                            <?php
                        }
                        ?>
                        <input type="checkbox" id="<?php echo esc_attr($option_args['id']); ?>"
                               name="<?php echo esc_attr($field->name . '[]'); ?>"
                               value="<?php echo esc_attr($option_name); ?>"
                            <?php checked(in_array($option_name, $value_checkbox), true, true); ?>/>
                        <?php
                        if (isset($option_args['label'])) {
                            ?>
                            <label for="<?php echo esc_attr($option_args['id']); ?>"
                                   class="wcbt-checkbox-label"><?php echo esc_html($option_args['label']); ?></label>
                            <?php
                        }
                        ?>
                    </div>
                    <?php
                    if (! empty($field->description)) {
                        ?>
                        <p class="wcbt-description"><?php echo General::ksesHTML($field->description); ?></p>
                        <?php
                    }
                    ?>
                    <?php
                }
                ?>
            </div>
            <?php
        } else {
            ?>
            <div class="wcbt-checkbox-content">
                <input type="hidden"
                       name="<?php echo esc_attr($field->name); ?>"
                       value="">
                <input type="checkbox" id="<?php echo esc_attr($field->id); ?>"
                       name="<?php echo esc_attr($field->name); ?>"
                       value="on"
                    <?php checked('on', $field->value, true); ?>/>
                <?php
                if (! empty($field->label)) {
                    ?>
                    <label for="<?php echo esc_attr($field->id); ?>"
                           class="wcbt-checkbox-label"><?php echo esc_html($field->label); ?></label>
                    <?php
                }
                ?>
                <?php
                if (! empty($field->description)) {
                    ?>
                    <p class="wcbt-description"><?php echo General::ksesHTML($field->description); ?></p>
                    <?php
                }
                ?>
            </div>
            <?php
        }
        ?>
    </div>
<?php
