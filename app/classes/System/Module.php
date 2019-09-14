<?php

namespace System;



use front\ModuleFrontController;

use model\ModuleHook;
use System\View\View;

class Module {

    protected $viewTheme = 'default';
    protected $config;
    public $view=null;

    public $systemName = '';
    public $name = '';
    public $author = '';
    public $category = 1;
    public $disabled = false;
    public $paid = false;

    //protected $uniq_id = null;


    public function __construct()
    {
        $class = get_called_class();
        $namespace = explode("\\", $class);
        $class_name = end($namespace);
        $this->systemName = $class_name;
        if(!$this->name){


            $this->name = $class_name;
        }
        $this->config = new Config();

        $this->viewTheme = $this->config->front_template;


    }
    public function getModuleView($path, $type = 'front')
    {
        //if selected front template not exist in module dir than use default template
        if(!file_exists( Path::getRoot('app/modules/'.$this->systemName.'/'.'template/'.$type.'/' . $this->viewTheme.'/'.$path))){
            $this->viewTheme = 'default';
        }

        $view = new View('template/'.$type.'/' . $this->viewTheme, $path, 'app/modules/'.$this->systemName.'/');
        return $view;
    }

    public static function installModule($system_module_name){
        $ModuleClass = Module::getModuleClass($system_module_name);

        if($ModuleClass) {

            $Module = new \model\Module($ModuleClass->getId());
            if ($Module->isLoadedObject()) {

                $Module->status = 1;
                $Module->save();
                $ModuleClass->install();

            } else{
                $Module->system_name = $ModuleClass->systemName;
                $Module->name        = $ModuleClass->name;
                $Module->status      = 1;
                $Module->author      = $ModuleClass->author;
                $Module->save();
                $ModuleClass->install();

            }

            return true;
        }

        return false;
    }

    public function getDbObject(){
        $Module = new \model\Module();
        $row = $Module->where('system_name' , $this->systemName)->getRow();
        return new \model\Module($row);
    }

    public function getId(){
        $Module = new \model\Module();
        $row = $Module->where('system_name' , $this->systemName)->getRow();
        return isset($row->id) ? $row->id : 0;
    }

    public function getUniqID(){
        return crc32($this->systemName);
    }

    public static function getListAll()
    {
        $dirs = scandir(_BASE_DIR_APP_.'/modules/');

        unset($dirs[0]);
        unset($dirs[1]);
        $modules = array();

        foreach ($dirs as $dir) {
            if(file_exists(_BASE_DIR_APP_.'/modules/'.$dir.'/'.$dir.'.php')){
                $module = $dir;
                $module_name = 'modules\\'.$dir.'\\'.$module;

                include_once(Path::getRoot('app/modules/'.$module.'/'.$module.'.php'));

                $module_class = new $module_name();
                $modules[] = $module_class;
            }
        }

        return $modules;
    }

    public static function extendMethod($method, &$data = array())
    {
        if(isset(ModuleHook::$hooks[$method])) {


            $ModuleHook = new ModuleHook();

            $rows = $ModuleHook->where('hook_id', ModuleHook::$hooks[$method])->getRows();
            foreach ($rows as $row) {
                $Module = new \model\Module($row->module_id);
                $ModuleClass = self::getModuleClass($Module->system_name);
                if(method_exists($ModuleClass, $method)) {
                    $ModuleClass->$method($data);
                }
            }
        }
    }

    public static function getModuleClass($system_name)
    {
        if(file_exists(_BASE_DIR_APP_.'/modules/'.$system_name.'/'.$system_name.'.php')){
            $module = $system_name;
            $module_name = 'modules\\'.$system_name.'\\'.$module;

            include_once(Path::getRoot('app/modules/'.$module.'/'.$module.'.php'));

            $module_class = new $module_name();
           return $module_class;
        }
    }


    public function registerHook($hook)
    {
        if (isset(ModuleHook::$hooks[$hook])) {
            $Module = new \model\Module();
            $row = $Module->where('system_name', $this->systemName)->getRow();
            if ($row) {
                $ModuleHook = new ModuleHook();
                $ModuleHook->module_id = $row->id;
                $ModuleHook->hook_id = ModuleHook::$hooks[$hook];

                return $ModuleHook->save();
            }
            return false;
        }

        return false;
    }



    public static function execHook($hook_name, $view=null){


        $views = array();
        if(isset(ModuleHook::$hooks[$hook_name])) {



            $ModuleHook = new ModuleHook();

            $rows = $ModuleHook->where('hook_id', ModuleHook::$hooks[$hook_name])->getRows();
            foreach ($rows as $row) {
                $Module = new \model\Module($row->module_id);
                $ModuleClass = self::getModuleClass($Module->system_name);
                $mview = null;
                if(method_exists($ModuleClass, $hook_name)) {
                    $ModuleClass->$hook_name($mview);
                    if ($mview) {
                        $views[] = $mview;
                    }
                }
            }
        }

        if($view) {
            $view->import($hook_name, $views);
        }

    }

    public function uninstall()
    {
        $Module = new \model\Module();
        $row = $Module->where('system_name' , $this->systemName)->getRow();
        if($row) {


            $ModuleHook = new ModuleHook();
            return $ModuleHook->where('module_id', $row->id)->removeRows();

        }
        return false;
    }

    public function remove()
    {
        if ($this->uninstall()) {
            return Tools::destroy_dir(_BASE_DIR_APP_ . 'modules/' . $this->systemName);
        }
        return false;
    }

    public function install()
    {
        return true;
    }

}