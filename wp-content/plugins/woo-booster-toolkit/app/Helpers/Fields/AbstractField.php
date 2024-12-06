<?php

namespace WCBT\Helpers\Fields;

use WCBT\Helpers\Template;

/**
 * class AbstractFields
 */
abstract class AbstractField
{
    /**
     * @var string id of field, require unique
     */
    public $id = '';
    /**
     * @var string name of field
     */
    public $name = '';
    /**
     * @var string class of field
     */
    public $class = '';
    /**
     * @var string $description of field
     */
    public $description = '';
    /**
     * @var string title of field
     */
    public $title = '';
    /**
     * @var mixed value of field
     */
    public $value = '';
    /**
     * @var string type sanitize of field
     */
    public $sanitize_type = 'text';
    /**
     * @var path file template of field
     */
    public $path_view = '';
    /**
     * @var default value of field
     */
    public $default = '';

    /**
     * @var bool
     */
    public $is_single_key = false;

    /**
     * View template of field
     *
     * @return void
     */
    public function render()
    {
        Template::instance()->get_admin_template($this->path_view, array( 'field' => $this ));
    }

    public function set_args($args): self
    {
        $properties = array_keys(get_object_vars($this));

        foreach ($properties as $property) {
            $this->{$property} = $args[ $property ] ?? $this->{$property} ?? '';
            if (! empty($this->{$property}) && in_array($property, array( 'id', 'class' ))) {
                $this->{$property} = WCBT_PREFIX . '_' . $this->{$property};
            }
        }

        return $this;
    }

    public function save()
    {
    }
}
