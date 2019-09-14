<?php
/**
 * Created by PhpStorm.
 * User: Viktor
 * Date: 21.06.15
 * Time: 13:09
 */

namespace System;


class Cookie
{
    const ONE_DAY   = 86400;
    const ONE_WEEK  = 604800;
    const ONE_MONAT = 2629743;
    const ONE_YEAR  = 31556926;

    public static function set($name, $value, $time = Cookie::ONE_MONAT)
    {
        return setcookie($name, $value, intval(time() + $time), '/');
    }

    public static function remove($name)
    {
        return setcookie($name, '', 0, '/');
    }

    public static function get($name, $default = null)
    {
        return (isset($_COOKIE[$name]) ? $_COOKIE[$name] : $default);
    }

} 