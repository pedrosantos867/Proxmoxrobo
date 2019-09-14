<?php


namespace update;

use System\Config;

class Update
{
    private static $server = 'http://service.hopebilling.com';

    public static function checkUpdates($beta = 0)
    {
        $config = new Config();

        $info   = @file_get_contents(self::$server . '/updater.php?get_info=' . $config->app_version .'&beta='.$beta);

        if($info && $info = json_decode($info)) {


            return $info;
        }

        return $config->app_version;
    }
    public static function is_writable_r($dir) {
        if (is_dir($dir)) {
            if(is_writable($dir)){
                $objects = scandir($dir);
                foreach ($objects as $object) {
                    if ($object != "." && $object != "..") {
                        if(substr($object, 0, 1) == '.'){continue;}
                        if (!self::is_writable_r($dir."/".$object)){

                            return false;}
                        else continue;
                    }
                }
                return true;
            } else {
                    return false;
            }
        } else if(file_exists($dir)){
            return (is_writable($dir));
        }
    }
    public static function getUpdate()
    {


        $config = new Config();

        $file = fopen('update.php', 'w');
        $ch   = curl_init();
        $license_key = @file_get_contents('key.lic');
        curl_setopt($ch, CURLOPT_URL, self::$server . '/updater.php?get_update=1&version=' . $config->app_version .'&key='.$license_key);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
      //  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_FILE, $file);
        curl_exec($ch);
        fclose($file);

        if (file_exists('update.php') && @file_get_contents('update.php') != '') {
            return true;
        }

        return false;
    }

}