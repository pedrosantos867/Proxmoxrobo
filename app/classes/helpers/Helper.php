<?php
namespace helpers;

use model\Languages;
use System\Cookie;
use System\LanguageDictionary;
use System\Path;
use System\Router;
use System\Tools;

class Helper
{

    private $path = '';
    private $_template = '';
    private $_folder = '';
    private $_root = '';
    private $_data = array();
    private $_langs = array();

    public function __construct()
    { }

    public function init($viewParams){
        $this->_template    = $viewParams['template'];
        $this->path         = $viewParams['path'];
        $this->_folder      = $viewParams['folder'];
        $this->_root      = $viewParams['root'];
        $this->_data       = $viewParams['data'];




        return $this;
    }

    public function isActive($request, $link){

        return preg_match('~'.$link.'~', $request);
    }

    public function tr($word, $folder, $file, $to){
        if(isset($this->_langs[$to])){
            $lang = $this->_langs[$to];
        } else {
            $lang = new Languages($to);
            $this->_langs[$to] = $lang;
        }

        if (isset($lang) && $lang->id >1) {
            $languageDB = new LanguageDictionary($lang->iso_code, $folder, $file);
            $word = ($languageDB->{$word} ? $languageDB->{$word} : $word);
        }

        return $word;
    }

    public function l($word, $params= null)
    {
        $id_lang = null;
        if(isset($this->_data['force_lang'])){
            $id_lang = $this->_data['force_lang'];
        }

        return Languages::translate($word, $this->_root, $this->_template, $params, $id_lang);
    }

    public function link($link, $get = '')
    {


        if (substr($link, 0, 1) != '/') {
            $link = '/' . $link;
        }

        if($get){
            if(strpos($link, '?') !== FALSE){
                if(strpos($get, '?')!==FALSE){
                    $link = substr($link, 0, strpos($link, '?')).$get;
                } else {
                    $link .= '&' . $get;
                }
            } else {
                $link .= '?'.$get;
            }
        }
        return _SITE_URL_ . $link;

    }
    public function phpJS($file){
        echo "<script>\n";
        //ini_set('display_errors', 'off');
            include($this->_root . 'js/' .$file);
        //ini_set('display_errors', 'on');
        echo '</script>';
    }

    public function JS($src)
    {
        echo '<script type="text/javascript" src="' . ($this->path . 'js/' . $src) . '"></script>';
    }

    public function CSS($href)
    {
        echo '<link rel="stylesheet" href="' . ($this->path . 'css/' . $href) . '">';
    }

    public function path($path)
    {
        return $this->path . $path;
    }

    public function p($k = null, $v = '')
    {
        return Tools::rPOST($k, $v);
    }

    public function rGET($k = null, $v = '')
    {
        return Tools::rGET($k, $v);
    }

    public function rPOST($k = null, $v = '')
    {
        return Tools::rPOST($k, $v);
    }

    public function cookie($key,$default=''){
        return Cookie::get($key, $default);
    }

}

?>