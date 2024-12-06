<?php

namespace WCBT\Helpers\Fields;

/**
 * Class Text
 * @package WCBT\Helpers\AbstractForm
 */
class Text extends AbstractField
{
    /**
     * @param $args
     *
     * @return string
     */
    public $pattern;
    /**
     * @var
     */
    public $placeholder;
    public $required;
    /**
     * @var string path file template of field
     */
    public $path_view = 'fields/text.php';

    public function __construct()
    {
    }
}
