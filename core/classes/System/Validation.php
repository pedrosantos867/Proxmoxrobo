<?php namespace System;


class Validation
{


    public static function isNoEmpty($value)
    {
        return ($value != '');
    }

    public static function  isUserName($name)
    {
        return preg_match('/^[a-zA-Z][a-zA-Z0-9-_\.]{3,20}$/', $name);
    }

    public static function isFullName($value)
    {
        return preg_match('/(\S{0,}\s\S{0,})(\s\S{0,}|$)/', $value);
    }

    public static function isEmail($email)
    {
        return preg_match('/^[\w\.=-]+@[\w\.-]+\.[\w]{2,3}$/', $email);
    }

    public static function isPasswd($pass)
    {
        return preg_match('/^[a-z0-9-_A-Z]{4,}$/', $pass);
    }

    public static function isInt($val)
    {
        return is_int($val);
    }

    public static function isPhone($phone)
    {
        return preg_match('/^[+][0-9]{9,15}$/', $phone);
    }

    public static function onlyFromArray($value, $values, $return_default = '')
    {
        if (!is_array($values)) {
            return $return_default;
        }

        if (in_array($value, $values)) {
            return $value;
        }

        return $return_default;
    }

    public static function onlyString($value, $return_default = '')
    {
        if (is_string($value)) {
            return $value;
        }

        return $return_default;
    }

    public static function onlyBool($value, $return_default = false)
    {
        if (is_bool($value)) {
            return $value;
        }
        return $return_default;
    }

    public static function onlyBoolInt($value, $return_default = 0)
    {
        if ($value == 1) {
            return 1;
        }
        return $return_default;
    }

    public static function onlyNumber($value, $return_default = 0, $min = false, $max = false)
    {
        if (is_numeric($value)) {
            return $value;
        }
        if ($min !== false && $value < $min) {
            return $return_default;
        }

        if ($max !== false && $value > $max) {
            return $return_default;
        }

        return $return_default;
    }
}
