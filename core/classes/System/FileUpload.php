<?php
namespace System;

class FileUpload
{
    private $files = null;

    public function __construct($field)
    {
        $this->files = [];

        if (isset($_FILES[$field])) {
            for ($i = 0; $i < count($_FILES[$field]['tmp_name']); $i++) {
                $tmp_name = $_FILES[$field]['tmp_name'][$i];
                $name     = Tools::transliteration($_FILES[$field]['name'][$i]);
                $type     = $_FILES[$field]['type'][$i];
                $error    = $_FILES[$field]['error'][$i];
                $size     = $_FILES[$field]['size'][$i];

                $this->files[$name] = (object)array(
                    'name'     => $name,
                    'tmp_name' => $tmp_name,
                    'type'     => $type,
                    'size'     => $size,
                    'error'    => $error
                );
            }
        }

    }

    public function upload($dir)
    {
        foreach ($this->files as $file) {
            move_uploaded_file($file->tmp_name, $dir . $file->name);
        }
    }

}