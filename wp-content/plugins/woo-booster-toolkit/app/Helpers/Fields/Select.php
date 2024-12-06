<?php

namespace WCBT\Helpers\Fields;

/**
 * Class Select
 * @package WCBT\Helpers\AbstractForm
 */
class Select extends AbstractField
{
    public $options;
    public $is_tom_select = false;
    public $is_multiple = false;
    /**
     * @var string path file template of field
     */
    public $path_view = 'fields/select.php';

    public function __construct()
    {
    }
}
