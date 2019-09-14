<?php
namespace System\View;

use model\Languages;
use System\LanguageDictionary;
use System\Path;
use System\Router;
use System\Tools;

class Helper
{

    private $path = '';
    private $_template = '';
    private $_folder = '';

    private $_langs = array();

    public function __construct()
    { }

    public function init($viewParams){
        $this->_template    = $viewParams['template'];
        $this->path         = $viewParams['path'];
        $this->_folder      = $viewParams['folder'];

        return $this;
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
}

?>