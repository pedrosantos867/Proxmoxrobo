<?php namespace System;

class Crypt
{

    private $iv = 'dsf876dfg564sadf';
    private $key = '345jkh45kjhb4k5j6b';

    function encrypt($str)
    {
        $iv = $this->iv;

        $td = mcrypt_module_open('rijndael-128', '', 'cbc', $iv);

        mcrypt_generic_init($td, $this->key, $iv);
        $str       = $this->padString($str);
        $encrypted = mcrypt_generic($td, $str);

        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);

        return base64_encode($encrypted);
    }

    function decrypt($code)
    {

        $code = base64_decode($code);
        $iv   = $this->iv;

        $td = mcrypt_module_open('rijndael-128', '', 'cbc', $iv);

        mcrypt_generic_init($td, $this->key, $iv);
        if ($td && $code) {
            $decrypted = mdecrypt_generic($td, $code);
        } else {
            return false;
        }
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);

        return trim($decrypted);

    }

    private function padString($source)
    {
        $paddingChar = ' ';
        $size        = 16;
        $x           = strlen($source) % $size;
        $padLength   = $size - $x;

        for ($i = 0; $i < $padLength; $i++) {
            $source .= $paddingChar;
        }

        return $source;
    }

}