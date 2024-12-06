<?php

namespace WCBT\Helpers\Fields;

class Checkbox extends AbstractField {
	public $sortable;
	public $multiple;
	public $options;
	public $label;
	public $path_view = 'fields/checkbox.php';

	public function __construct() {
	}
}
