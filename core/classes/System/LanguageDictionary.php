<?php

namespace System;


class LanguageDictionary
{
    private static $_instance = array();

    public $options;
    private $filepath = '';
    private $_template = '';

    public function __construct($lang, $dir, $template = '')
    {

        $this->options = new \stdClass();
        $this->_template = $template;

        //TODO need remove str replace and fix // problem
        $this->filepath = str_replace('//', '/',  $dir. '/i18n/' . $lang . '.lang' );


        if (!file_exists($this->filepath)) {
            $h = @fopen($this->filepath, "w");
            @fclose($h);
        }
        if (!file_exists($this->filepath)) {
            return;
        }

        $this->load();
    }

    public function load()
    {
        $this->options = json_decode(file_get_contents($this->filepath));

    }

    public static function get($lang, $dir, $template = '')
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self($lang, $dir, $template);
        }

        return self::$_instance;
    }

    public function __get($key)
    {

        if (isset($this->$key)) {
            return $this->$key;
        } else {

            if(isset($this->options->{$this->_template}->$key)) {
                return $this->options->{$this->_template}->$key;
            } else {
                return null;
            }
        }
    }


    public function __set($key, $value)
    {
        if (!isset($this->$key))
            if(isset($this->options->{$this->_template})) {
                $this->options->{$this->_template}->$key = $value;
            } else {
                if(!isset($this->options)){
                    $this->options = new \stdClass();
                }
                $this->options->{$this->_template} = (object)array();
                $this->options->{$this->_template}->$key = $value;
            }
        else
            $this->key = $value;
    }

    public function set($key, $value)
    {
        $this->$key = addslashes($value);
    }

    public function getAll()
    {
        return (array)$this->options;
    }

    public function setArray($array)
    {
        $this->options = (object)$array;
        $this->save();
    }

    public function save()
    {
        return @file_put_contents($this->filepath, json_encode($this->options, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
    }
} 