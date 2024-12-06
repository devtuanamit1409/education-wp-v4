<?php

use WCBT\Helpers\Fields\AbstractField;
use WCBT\Helpers\Validation;
use WCBT\Register\Setting;

/**
 * @var $config Setting
 */
if (! isset($config) || ! isset($data)) {
    return;
}

$group = $config;
unset($group['setting']);

$active_group = Validation::sanitize_params_submitted($_GET['tab'] ?? array_key_first($group));
$url          = add_query_arg(
    array( 'page' => $config['setting']['slug'] ),
    admin_url('admin.php')
);
?>
    <div class="wcbt-option-setting-wrapper">
        <div class="wcbt-option-setting-header">
            <div class="wcbt-option-setting-info">
                <?php printf(esc_html__('Version %s', 'wcbt'), WCBT_VERSION); ?>
            </div>
            <!-- Block-->
            <div>
            </div>
            <!--Display section-->
            <ul class="wcbt-option-setting-tab">
                <?php
                foreach ($group as $tab_name => $tab_args) {
                    $active_group_class = '';
                    if ($tab_name === $active_group) {
                        $active_group_class = 'active';
                    }
                    ?>
                    <li class="<?php echo esc_attr($active_group_class); ?>">
                        <a id="<?php echo esc_attr('wcbt_' . $tab_args['id']); ?>"
                           href="<?php echo esc_url_raw(add_query_arg('tab', $tab_name, $url)); ?>">
                            <span><?php echo esc_html($tab_args['title']); ?></span>
                        </a>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </div>

        <!--Display content-->
        <div class="wcbt-option-setting-content">
            <form method="POST" enctype="multipart/form-data">
                <?php
                wp_nonce_field('wcbt-option-setting-action', 'wcbt-option-setting-name');
                ?>
                <div class="wcbt-option-setting-field">
                    <?php
                    foreach ($group as $group_name => $group_args) {
                        if ($group_name === $active_group) {
                            $fields = $group_args['fields'];
                            foreach ($fields as $field_name => $field_args) {
                                if (isset($field_args['type'])) {
                                    if ($field_args['type'] === 'title') {
                                        ?>
                                        <div class="wcbt-title">
                                            <?php
                                            echo esc_html($field_args['title']);
                                            ?>
                                        </div>
                                        <?php
                                    } elseif ($field_args['type'] instanceof AbstractField) {
                                        $name                = $active_group . ':fields:' . $field_name;
                                        $field_args['value'] = $data[ $name ] ?? '';
                                        $field_args['name']  = WCBT_OPTION_KEY . '[' . $name . ']';
                                        $field_args['type']->set_args($field_args)->render();
                                    }
                                }
                            }
                        }
                    }
                    ?>
                </div>
                <button type="submit" class="button button-primary">
                    <?php esc_html_e('Save Changes', 'wcbt'); ?>
                </button>
                <?php wp_nonce_field('wcbt-option-setting-action', 'wcbt-option-setting-name'); ?>
            </form>
        </div>
    </div>
<?php

