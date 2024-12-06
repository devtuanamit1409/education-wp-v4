<?php
/**
 * Template Field type Select
 * @version 1.0.0
 */

use WCBT\Helpers\General;

/**
 * @var $field Select
 */
if (! isset($field)) {
    return;
}

$tom_select  = $field->is_tom_select ? 'wcbt-tom-select' : '';
$multiple = $field->is_multiple ? 'multiple' : '';
$name     = $field->is_multiple ? $field->name . '[]' : $field->name;
$class    = $field->class;
?>
<div class="<?php echo esc_attr(ltrim($class . ' ' . 'wcbt-field-wrapper wcbt-select-wrapper')); ?>">
    <?php
    if (! empty($field->title)) {
        ?>
        <div class="wcbt-title-wrapper">
            <label for="<?php echo esc_attr($field->id); ?>"><?php echo esc_html($field->title); ?></label>
        </div>
        <?php
    }
    ?>
    <div class="wcbt-select-content">
        <?php
        if ($multiple) {
            ?>
            <input type="hidden" name="<?php echo esc_attr($field->name); ?>">
            <?php
        }
        ?>
        <select name="<?php echo esc_attr($name); ?>" id="<?php echo esc_attr($field->id); ?>"
                class="<?php echo esc_attr($tom_select); ?>"
            <?php echo esc_attr($multiple); ?>>
            <?php

            foreach ($field->options as $option => $name) :
                $select_attr = $field->is_multiple ? selected(in_array($option, $field->value), true, false) :
                    selected($option, $field->value, false);
                ?>
                <option value="<?php echo esc_attr($option); ?>" <?php echo $select_attr; ?>>
                    <?php echo esc_html($name); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php
        if (! empty($field->description)) {
            ?>
            <p class="wcbt-description"><?php echo General::ksesHTML($field->description); ?></p>
            <?php
        }
        ?>
    </div>
</div>
