<?php
namespace System;
class Config
{

    protected $filepath;
    public $options = array();
    private static $_cache = array();

    public function __construct($filename = 'global')
    {
        if (file_exists(Path::getRoot('app/config/') . $filename . '.config')) {
            $this->filepath = Path::getRoot('app/config/') . $filename . '.config';
        } else {
            touch(Path::getRoot('app/config/') . $filename . '.config');
            $this->filepath = Path::getRoot('app/config/') . $filename . '.config';
        }
        $this->load();
    }

    public static function factory($filename = 'global'){
        if(isset(self::$_cache[$filename])){
            return self::$_cache[$filename];
        } else {
            self::$_cache[$filename] = new self($filename);
            return self::$_cache[$filename];
        }
    }

    public function load()
    {
        $this->options = unserialize(file_get_contents($this->filepath));
    }

    private function __clone()
    {
    }



    public function __get($key)
    {
        if (isset($this->options[$key])) {
            return $this->options[$key];
        } else {
            return false;
        }
    }


    public function __set($key, $value)
    {
        if (!isset($this->$key))
            $this->options[$key] = $value;
        else
            $this->key = $value;
    }

    public function delete($key){
        if (isset($this->options[$key]))
            unset($this->options[$key]) ;
    }

    public function save()
    {
        // echo $this->filepath;
        return file_put_contents($this->filepath, serialize($this->options));
    }

    public function remove(){
        return @unlink($this->filepath);
    }

    public function clean()
    {
        $this->options = array();
        $this->save();
    }

} 