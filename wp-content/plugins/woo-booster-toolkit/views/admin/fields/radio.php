<?php
if (! isset($field)) {
    return;
}

use WCBT\Helpers\General;

$field->value = ( empty($field->value) ) ? $field->default : $field->value;
?>
    <div class="<?php echo esc_attr(ltrim($field->class . ' ' . 'wcbt-field-wrapper wcbt-radio-button-wrapper')); ?>">
        <?php
        if (! empty($field->title)) {
            ?>
            <div class="wcbt-title-wrapper">
                <?php echo esc_html($field->title); ?>
            </div>
            <?php
        }
        ?>
        <div class="wcbt-radio-button-content-wrapper">
            <?php
            foreach ($field->options as $option_value => $option_args) {
                ?>

                <div class="wcbt-radio-button-content">
                    <input type="radio" name="<?php echo esc_attr($field->name); ?>"
                           id="<?php echo esc_attr($option_args['id']); ?>"
                           value="<?php echo esc_attr($option_value); ?>"
                        <?php checked($field->value, $option_value, true); ?>>
                    <label for="<?php echo esc_attr($option_args['id']); ?>"><?php echo esc_html($option_args['label']); ?></label>
                    <?php
                    if (! empty($option_args['description'])) {
                        ?>
                        <p class="wcbt-description"><?php echo esc_html($option_args['description']); ?></p>
                        <?php
                    }
                    ?>
                </div>
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
    </div>
<?php
