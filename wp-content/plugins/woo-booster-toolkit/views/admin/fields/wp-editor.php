<?php

use WCBT\Helpers\General;

if (! isset($field)) {
    return;
}

$settings = array(
    'textarea_name' => $field->name,
);
?>
    <div class="<?php echo esc_attr(ltrim($field->class . ' ' . 'wcbt-field-wrapper wcbt-wp-editor-wrapper')); ?>">
        <?php
        if (! empty($field->title)) {
            ?>
            <div class="wcbt-title-wrapper">
                <?php echo esc_html($field->title); ?>
            </div>
            <?php
        }
        ?>
        <div class="wcbt-wp-editor-content">
            <?php
            wp_editor($field->value, $field->id, $settings);
            if (! empty($field->description)) {
                ?>
                <p class="wcbt-description"><?php echo General::ksesHTML($field->description); ?></p>
                <?php
            }
            ?>
        </div>
    </div>
<?php
