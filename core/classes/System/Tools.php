<?php namespace System;

use System\View\View;

class Tools
{


    /**
     * Redirect user to another page
     *
     * @param string $url Desired URL
     */
    public static function redirect($url = '')
    {



        if(strpos($url, 'http') === false){
            if (substr($url, 0, 1) != '/') {
                $url = '/' . $url;
            }

            $url = _SITE_URL_.$url;
        }

       header('Location: ' . $url);
        exit;
    }

    public static function redirectToSSL($url = '/')
    {
        if (substr($url, 0, 1) != '/') {
            $url = '/' . $url;
        }

        header('Location: ' . 'https://' . _SITE_DOMINE_ . $url);
        exit;
    }

    public static function reload()
    {

        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit();
    }

    public static function rGET($key=null, $def_value = false)
    {
        if(!$key){
            return ($_SERVER['REQUEST_METHOD'] == 'GET' && !empty($_GET));
        }

        if (isset($_GET[$key])) {
            return $_GET[$key];
        } else {
            return $def_value;
        }
    }

    public static function rPOST($key = null, $def_value = false, $xss_clear = true)
    {
        if (!$key) {
            return ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST));
        }
        if (!$key && isset($_POST)) {
            return $_POST;
        } else if (!isset($_POST)) {
            return $def_value;
        }

        if (isset($_POST[$key])) {
            return $xss_clear && is_string($_POST[$key]) ? self::clearXSS($_POST[$key]) : $_POST[$key];
        } else {
            return $def_value;
        }
    }


    public static function clearXSS($string){
        if(!is_string($string)) return '';
        $string = strip_tags($string);
        $string = htmlspecialchars($string);
        return $string;
    }

    public static function rRequest($key)
    {
        if (isset($_REQUEST[$key])) {
            return $_REQUEST[$key];
        } else {
            return false;
        }
    }

    public static function removeGetParam($param){
       if(Tools::rGET()){
            unset($_GET[$param]);
           if(count($_GET)) {

               return '?' . http_build_query($_GET);
           }
       }
        return '';

    }
    public static function display403Error()
    {
        header('HTTP/1.1 403 Forbidden');
        header('Status: 403 Forbidden');

        echo '<div style="text-align: center"><h2 >Error 403 <br> Forbidden </h2><a href="/">Open Home</a><br><br><span>Powered by HopeBilling</span></div>';
        die;
    }
    public static function display404Error()
    {
        header('HTTP/1.1 404 Not Found');
        header('Status: 404 Not Found');
        echo '<div style="text-align: center"><h2 >Error 404 <br> Page Not Found</h2><a href="/">Open Home</a><br><br><span>Powered by HopeBilling</span></div>';
        die;
    }

    public static function passCrypt($pass)
    {
        return sha1(md5($pass) . _SECRET_);
    }

    public static function getWord($number, $suffix = array())
    {

        if (count($suffix) == 2) {
            if ($number == 1) {
                $suffix_key = 0;
            } else {
                $suffix_key = 1;
            }
        } else {

            $keys = array(
                2,
                0,
                1,
                1,
                1,
                2);
            $mod  = $number % 100;

            $suffix_key = ($mod > 7 && $mod < 20) ? 2 : $keys[min($mod % 10, 5)];
        }
        return $suffix[$suffix_key];
    }

    public static function checkEmail($email)
    {
        if (preg_match("/^[a-zA-Z0-9_]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+$]/", $email)) {
            return false;
        } else {
            return true;
        }
    }

    public static function getBrowser()
    {
        if (isset($_SERVER["HTTP_USER_AGENT"]) or ($_SERVER["HTTP_USER_AGENT"] != "")) {
            $visitor_user_agent = $_SERVER["HTTP_USER_AGENT"];
        } else {
            $visitor_user_agent = "Unknown";
        }
        $bname   = 'Unknown';
        $version = "0.0.0";

        // Next get the name of the useragent yes seperately and for good reason
        if (preg_match('/MSIE/i', $visitor_user_agent) && !preg_match('/Opera/i', $visitor_user_agent)) {
            $bname = 'Internet Explorer';
            $ub    = "MSIE";
        } elseif (preg_match('/Firefox/i', $visitor_user_agent)) {
            $bname = 'Mozilla Firefox';
            $ub    = "Firefox";
        } elseif (preg_match('/YaBrowser/i', $visitor_user_agent)) {
            $bname = 'Yandex Browser';
            $ub    = "YaBrowser";
        } elseif (preg_match('/Chrome/i', $visitor_user_agent)) {
            $bname = 'Google Chrome';
            $ub    = "Chrome";
        } elseif (preg_match('/Safari/i', $visitor_user_agent)) {
            $bname = 'Apple Safari';
            $ub    = "Safari";
        } elseif (preg_match('/Opera/i', $visitor_user_agent)) {
            $bname = 'Opera';
            $ub    = "Opera";
        } elseif (preg_match('/Netscape/i', $visitor_user_agent)) {
            $bname = 'Netscape';
            $ub    = "Netscape";
        } elseif (preg_match('/Seamonkey/i', $visitor_user_agent)) {
            $bname = 'Seamonkey';
            $ub    = "Seamonkey";
        } elseif (preg_match('/Konqueror/i', $visitor_user_agent)) {
            $bname = 'Konqueror';
            $ub    = "Konqueror";
        } elseif (preg_match('/Navigator/i', $visitor_user_agent)) {
            $bname = 'Navigator';
            $ub    = "Navigator";
        } elseif (preg_match('/Mosaic/i', $visitor_user_agent)) {
            $bname = 'Mosaic';
            $ub    = "Mosaic";
        } elseif (preg_match('/Lynx/i', $visitor_user_agent)) {
            $bname = 'Lynx';
            $ub    = "Lynx";
        } elseif (preg_match('/Amaya/i', $visitor_user_agent)) {
            $bname = 'Amaya';
            $ub    = "Amaya";
        } elseif (preg_match('/Omniweb/i', $visitor_user_agent)) {
            $bname = 'Omniweb';
            $ub    = "Omniweb";
        } elseif (preg_match('/Avant/i', $visitor_user_agent)) {
            $bname = 'Avant';
            $ub    = "Avant";
        } elseif (preg_match('/Camino/i', $visitor_user_agent)) {
            $bname = 'Camino';
            $ub    = "Camino";
        } elseif (preg_match('/Flock/i', $visitor_user_agent)) {
            $bname = 'Flock';
            $ub    = "Flock";
        } elseif (preg_match('/AOL/i', $visitor_user_agent)) {
            $bname = 'AOL';
            $ub    = "AOL";
        } elseif (preg_match('/AIR/i', $visitor_user_agent)) {
            $bname = 'AIR';
            $ub    = "AIR";
        } elseif (preg_match('/Fluid/i', $visitor_user_agent)) {
            $bname = 'Fluid';
            $ub    = "Fluid";
        } else {
            $bname = 'Unknown';
            $ub    = "Unknown";
        }

        // finally get the correct version number
        $known   = array(
            'Version',
            $ub,
            'other');
        $pattern = '#(?<browser>' . join('|', $known) .
            ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $visitor_user_agent, $matches)) {
            // we have no matching number just continue
        }

        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($visitor_user_agent, "Version") < strripos($visitor_user_agent, $ub)) {
                $version = $matches['version'][0];
            } else {
                $version = isset($matches['version'][1]) ? $matches['version'][1] : '';
            }
        } else {
            $version = $matches['version'][0];
        }

        // check if we have a number
        if ($version == null || $version == "") {
            $version = "?";
        }

        return array(
            'userAgent' => $visitor_user_agent,
            'name'      => $bname,
            'version'   => $version,
            'pattern'   => $pattern);
    }

    public static function getOS()
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        // Create list of operating systems with operating system name as array key
        $oses = array(
            'iPhone'         => '(iPhone)',
            'Windows 3.11'   => 'Win16',
            'Windows 95'     => '(Windows 95)|(Win95)|(Windows_95)', // Use regular expressions as value to identify operating system
            'Windows 98'     => '(Windows 98)|(Win98)',
            'Windows 2000'   => '(Windows NT 5.0)|(Windows 2000)',
            'Windows XP'     => '(Windows NT 5.1)|(Windows XP)',
            'Windows 2003'   => '(Windows NT 5.2)',
            'Windows Vista'  => '(Windows NT 6.0)|(Windows Vista)',
            'Windows 7'      => '(Windows NT 6.1)|(Windows 7)',
            'Windows 8'      => '(Windows NT 6.2)',
            'Windows NT 4.0' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)',
            'Windows ME'     => 'Windows ME',
            'Open BSD'       => 'OpenBSD',
            'Sun OS'         => 'SunOS',
            'Android'        => '(Android [0-9.]{0,})',
            'Linux'          => '(Linux)|(X11)',
            'Safari'         => '(Safari)',

            'Macintosh'      => '(Mac_PowerPC)|(Macintosh)',
            'QNX'            => 'QNX',
            'BeOS'           => 'BeOS',
            'OS/2'           => 'OS/2',
            'Search Bot'     => '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp/cat)|(msnbot)|(ia_archiver)');

        foreach ($oses as $os => $pattern) { // Loop through $oses array
            // Use regular expressions to check operating system type
            if (@preg_match('/' . $pattern . '/i', $userAgent, $matches)) { // Check if a value in $oses array matches current user agent.
                if ($os == "Android") {
                    $os = $matches[0];
                }

                return $os; // Operating system was matched so return $oses key
            }
        }

        return 'Unknown'; // Cannot find operating system so return Unknown
    }



    public static function generateCode($length = 6, $chars="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789")
    {



        $code = "";

        $clen = strlen($chars) - 1;
        while (strlen($code) < $length) {

            $code .= $chars[mt_rand(0, $clen)];
        }

        return $code;
    }



    public static function transliteration($input = '', $space_en = 0)
    {
        $gost = array(
            "Є" => "YE", "І" => "I", "Ѓ" => "G", "і" => "i", "№" => "-", "є" => "ye", "ѓ" => "g",
            "А" => "A", "Б" => "B", "В" => "V", "Г" => "G", "Д" => "D",
            "Е" => "E", "Ё" => "YO", "Ж" => "ZH",
            "З" => "Z", "И" => "I", "Й" => "J", "К" => "K", "Л" => "L",
            "М" => "M", "Н" => "N", "О" => "O", "П" => "P", "Р" => "R",
            "С" => "S", "Т" => "T", "У" => "U", "Ф" => "F", "Х" => "X",
            "Ц" => "C", "Ч" => "CH", "Ш" => "SH", "Щ" => "SHH", "Ъ" => "'",
            "Ы" => "Y", "Ь" => "", "Э" => "E", "Ю" => "YU", "Я" => "YA",
            "а" => "a", "б" => "b", "в" => "v", "г" => "g", "д" => "d",
            "е" => "e", "ё" => "yo", "ж" => "zh",
            "з" => "z", "и" => "i", "й" => "j", "к" => "k", "л" => "l",
            "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r",
            "с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "x",
            "ц" => "c", "ч" => "ch", "ш" => "sh", "щ" => "shh", "ъ" => "",
            "ы" => "y", "ь" => "", "э" => "e", "ю" => "yu", "я" => "ya",
            " " => ($space_en ? " " : "_"), "—" => "_", "," => ($space_en ? "," : "_"), "!" => "_", "@" => "_",
            "#" => "-", "$" => "", "%" => "", "^" => "", "&" => "", "*" => "",
            "(" => "", ")" => "", "+" => "", "=" => "", ";" => "", ":" => "",
            "'" => "", "~" => "", "`" => "", "?" => "", "/" => "",
            "[" => "", "]" => "", "{" => "", "}" => "", "|" => ""

        );

        return strtr($input, $gost);
    }

    public static function time2ago($date)
    {
        $my_date = date('Y-m-d-H-i-s', strtotime($date));

        $sepparator = '-';
        $parts      = explode($sepparator, $my_date);

        $old_date =
            mktime($parts[3], $parts[4], $parts[5], $parts[1], $parts[2], $parts[0]);

        $current_date =
            mktime(date('H'), date('i'), date('s'), date('m'), date('d'), date('Y')); //дата сегодня

        $difference = abs($current_date - $old_date); //разница в секундах


        $etime = $difference;

        $a = array(
            12 * 30 * 24 * 60 * 60 => 'y',
            30 * 24 * 60 * 60      => 'm',
            24 * 60 * 60           => 'd',
            60 * 60                => 'h',
            60                     => 'min',
            1                      => 's'
        );

        foreach ($a as $secs => $str) {
            $d = $etime / $secs;

            if ($d >= 1) {
                $r    = round($d);
                $word =  $str;

                return (object)array('day' => $r, 'word' => $word);
            }


        }

        return (object)array('day' => 0, 'word' => 's');
    }



    public static function destroy_dir($dir)
    {
        if (!is_dir($dir) || is_link($dir)) return @unlink($dir);
        foreach (scandir($dir) as $file) {
            if ($file == '.' || $file == '..') continue;
            if (!Tools::destroy_dir($dir . DIRECTORY_SEPARATOR . $file)) {
                @chmod($dir . DIRECTORY_SEPARATOR . $file, 0777);
                if (!Tools::destroy_dir($dir . DIRECTORY_SEPARATOR . $file)) return false;
            };
        }

        return @rmdir($dir);
    }



    public static function link($link)
    {
        if (substr($link, 0, 1) != '/') {
            $link = '/' . $link;
        }

        return _SITE_URL_ . $link;
    }

    public static function p($printable, $exit = false, $tag = 'pre')
    {
        echo "<$tag>";
        print_r($printable);
        echo "</$tag>";
        if ($exit) exit();
    }
}
