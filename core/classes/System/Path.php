<?php namespace System;

class Path
{

    private static $patches = array(
        'root' => _DOCUMENT_ROOT_,
        'url'  => _SITE_URL_,
    );

    public static function patches()
    {


        return self::$patches;
    }

    public function __construct()
    {

    }

    public static function get($name)
    {
        return self::patches()[$name];
    }


    public static function getRoot($name)
    {
        $path = self::patches()['root'] . '/' . $name;
        return $path;
    }

    public static function  getURL($name)
    {


        $path = self::patches()['url'] . '/' . $name;

        return $path;
    }


    public static function set()
    {

    }


    public static function rGET($key, $def_value = false)
    {
        if (isset($_GET[$key])) {
            return $_GET[$key];
        } else {
            return $def_value;
        }
    }

    public static function rPOST($key = null, $def_value = false)
    {
        if (!$key && isset($_POST)) {
            return $_POST;
        } else if (!isset($_POST)) {
            return false;
        }

        if (isset($_POST[$key])) {
            return $_POST[$key];
        } else {
            return $def_value;
        }
    }
} 