<?php

namespace WCBT\Helpers\Fields;

/**
 * Class Textarea
 * @package WCBT\Helpers\AbstractForm
 */
class Textarea extends AbstractField
{
    public $path_view = 'fields/textarea.php';
    /**
     * @var int
     */
    public $rows = 3;
    public $placeholder;

    public function __construct()
    {
    }
}
