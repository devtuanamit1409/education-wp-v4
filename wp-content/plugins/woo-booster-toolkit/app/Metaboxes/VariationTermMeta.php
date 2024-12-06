<?php

namespace WCBT\Metaboxes;

use WCBT\Helpers\Config;
use WCBT\Helpers\Validation;
use WCBT\Helpers\Variation;

class VariationTermMeta
{
    private $config;

    public function __construct()
    {
        $this->config = Config::instance()->get('variation');
        add_action('admin_init', array( $this, 'add_attribute_meta' ));
    }

    public function add_attribute_meta()
    {
        $attribute_taxonomies = wc_get_attribute_taxonomies();
        if ($attribute_taxonomies) {
            foreach ($attribute_taxonomies as $taxonomy) {
                $attribute_name = wc_attribute_taxonomy_name($taxonomy->attribute_name);
                add_action($attribute_name . '_add_form_fields', array( $this, 'add_term_metabox' ), 10, 1);
                add_action($attribute_name . '_edit_form', array( $this, 'edit_term_metabox' ), 10, 2);
                add_action('saved_' . $attribute_name, array( $this, 'save_term_metabox' ), 10, 1);
            }
        }
    }

    /**
     * @param $attribute_name
     *
     * @return void
     */
    public function add_term_metabox($attribute_name)
    {
        $attribute_type = Variation::get_attibute_type($attribute_name);

        if (! isset($this->config[ $attribute_type ])) {
            return;
        }

        $fields    = $this->config[ $attribute_type ]['fields'];
        $term_id   = Validation::sanitize_params_submitted($_GET['tag_ID'] ?? '');
        $term_meta = get_term_meta($term_id, WCBT_TERM_META_KEY, true);
        foreach ($fields as $field) {
            $field['value'] = $term_meta[ $field['name'] ] ?? $field['default'];
            $field['name']  = WCBT_TERM_META_KEY . '[' . $field['name'] . ']';
            $field['type']->set_args($field)->render();
        }
    }

    /**
     * @param $tag
     * @param $attribute_name
     *
     * @return void
     */
    public function edit_term_metabox($tag, $attribute_name)
    {
        $attribute_type = Variation::get_attibute_type($attribute_name);
        if (! isset($this->config[ $attribute_type ])) {
            return;
        }

        $fields    = $this->config[ $attribute_type ]['fields'];
        $term_id   = Validation::sanitize_params_submitted($_GET['tag_ID'] ?? '');
        $term_meta = get_term_meta($term_id, WCBT_TERM_META_KEY, true);
        foreach ($fields as $field) {
            $field['value'] = $term_meta[ $field['name'] ] ?? $field['default'];
            $field['name']  = WCBT_TERM_META_KEY . '[' . $field['name'] . ']';
            $field['type']->set_args($field)->render();
        }
    }

    /**
     * @param $term_id
     *
     * @return false|void
     */
    public function save_term_metabox($term_id)
    {
        $action = Validation::sanitize_params_submitted($_POST['action']);
        if (empty($action) || ! in_array($action, array( 'editedtag', 'add-tag' ))) {
            return false;
        }

        $term = get_term($term_id);

        $attribute_type = Variation::get_attibute_type($term->taxonomy);
        $fields         = $this->config[ $attribute_type ]['fields'];
        $data           = array();
        foreach ($fields as $field) {
            $key = Validation::sanitize_params_submitted(isset($_POST[ WCBT_TERM_META_KEY ][ $field['name'] ]));
            if ($key) {
                $sanitize               = $field['sanitize'] ?? 'text';
                $data[ $field['name'] ] = Validation::sanitize_params_submitted(
                    $_POST[ WCBT_TERM_META_KEY ][ $field['name'] ],
                    $sanitize
                );
            }
        }

        update_term_meta($term_id, WCBT_TERM_META_KEY, $data);
    }
}
