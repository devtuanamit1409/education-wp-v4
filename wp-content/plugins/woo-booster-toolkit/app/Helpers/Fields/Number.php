<?php

namespace WCBT\Helpers\Fields;

/**
 *
 */
class Number extends AbstractField
{
    public $min, $max, $required;
    public $path_view = 'fields/number.php';

    public function __construct()
    {
    }
}
