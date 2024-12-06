<?php

namespace WCBT\Helpers\Fields;

class FileUpload extends AbstractField
{
    public $multiple;
    public $button_title;
    public $max_number;
    public $max_size;
    public $max_file_size;
    public $path_view = 'fields/file-upload.php';

    public function __construct()
    {
    }
}
