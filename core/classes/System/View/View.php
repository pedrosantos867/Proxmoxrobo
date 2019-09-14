<?php namespace System\View;

use System\Dispatcher;
use System\Exception;
use System\Path;

class View
{

    const FRONT = 1;
    const ADMIN = 2;

    const TEMPLATE_SYS       = 1;
    const TEMPLATE_COMPONENT = 2;

    protected $cssStyle;

    private $_template = '';
    private $_folder = '';

    protected $_templateFullPath;
    protected $_data = array();
    protected $_templates = array();
    protected $_cache;
    protected $_globals = array();

    protected $_globTemplate = false;


    protected $_root;
    protected $_url;


    private $_helper_data = array();
    public function __construct()
    {

        if (func_num_args()) {
            $args = func_get_args();

            $this->load($args);
        }
    }

    public function isLoaded($template = '')
    {
        if ($template) {
            if (isset($this->_templates[$template]) && $this->_templates[$template]->_templateFullPath) {
                return true;
            } else {
                return false;
            }
        }

        if ($this->_templateFullPath) {
            return true;
        }

        return false;
    }

    private function load($args)
    {

        $folder   = null;
        $template = null;

        $this->_root = Path::getRoot('template/');
        $this->_url  = Path::getURL('template/');

        if (count($args) == 1) {
            $template = $args[0];
            // $this->load($template);
        } elseif (count($args) == 2) {
            $folder   = $args[0];
            $template = $args[1];
        } elseif (count($args) == 3) {
            $folder   = $args[0];
            $template = $args[1];
            $this->_root = Path::getRoot($args[2]);
            $this->_url  = Path::getURL($args[2]);
        }



        // print_r( $template);

        if ($folder) {
            $this->_root .= $folder . '/';
            $this->_url .= $folder . '/';
        }


        //  echo $this->_root.'---';
        $this->_template = $template;
        $this->_folder = $folder;
        $template_path = $this->_root . $template;




        if (file_exists($template_path)) {
            $this->_templateFullPath = $template_path;
        } else {
            throw new Exception('File ' . $template_path . ' not exists.');
        }



    }


    public function assign($key, $value)
    {
        $this->_data[$key] = $value;
    }

    public function assign_array($array)
    {
        foreach ($array as $key => $value) {
            $this->assign($key, $value);
        }

    }

    public function __set($key, $value)
    {
        $this->_data[$key] = $value;
    }

    public function __get($key)
    {
        return (isset($this->_data[$key]) ? $this->_data[$key] : '');
    }


    public function set($var, $data)
    {
        echo $var;
    }

    public function import($var, $template)
    {

            $this->_templates[$var] = $template;


    }

    public function glob($var, &$value)
    {
        $this->_globals[$var] = & $value;
    }

    public function g($var, $value)
    {
        $this->_globals[$var] = $value;
    }

    public function t()
    {

    }

    public function display()
    {
        echo $this->fetch();
    }

    public function fetch()
    {


        $dispatcher = new Dispatcher();
        $helpers =  $dispatcher->EventGetHelpersForView();

        foreach($helpers as $v => $class){
            $object = new $class();
            if(method_exists($object, 'init')) {
                $this->assign($v, $object->init(array('template' => $this->_template, 'path' => $this->_url, 'folder' => $this->_folder, 'root' => $this->_root, 'data' => $this->_helper_data)));
            } else {
                throw new Exception('Method init not exist on Helper class.');
            }
        }

        if ($this->_templateFullPath && !$this->_cache) {

            if (count($this->_templates)) {
                foreach ($this->_templates as $var => $view) {
                    if(!is_array($view)) {
                        // $view->_data = array_merge($view->_data, $this->_data);
                        $view->_globals = $this->_globals;
                        foreach ($this->_globals as $key => $v) {
                            $view->$key = $v;

                            $this->$key = $v;
                        }

                        $this->_data[$var] = $view->fetch();
                    } else {
                       $fetch_html = '';

                        foreach ($view as $item) {
                            if($item) {
                                // $view->_data = array_merge($view->_data, $this->_data);
                                $item->_globals = $this->_globals;
                                foreach ($this->_globals as $key => $v) {
                                    $item->$key = $v;
                                    $this->$key = $v;
                                }
                                $fetch_html .= $item->fetch();
                            }
                        }
                        $this->_data[$var] = $fetch_html;
                    }
                }
            }
            foreach ($this->_globals as $key => $v) {
                $this->$key = $v;
            }


            ob_start();

            $data = $this->_data;

            extract($data);


            require($this->_templateFullPath);


            $content = ob_get_contents();

            echo $content;

            ob_end_clean();
            $this->_cache = $content;

            //  echo $this->_templateFullPath;
            return $content;
        } else {
            return $this->_cache;
        }
    }

    public function setDataToHelper($key, $data){
        $this->_helper_data[$key] = $data;
    }


}

?>