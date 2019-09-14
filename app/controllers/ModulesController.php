<?php

use System\ControllerFactory;
use System\Path;

class ModulesController {
    public function run($action){
        $this->$action();
    }

    public function actionRoute(){
        $module = \System\Router::getParam('module');


        $m = \System\Module::getModuleClass($module);

        if(!$m || !$m->getDbObject()->status){
            \System\Tools::display404Error();
        }

        if(!file_exists( \System\Path::getRoot('app/modules/'.$module.'/routes.php') )){
            \System\Tools::display404Error();
        }

        $routes = include( \System\Path::getRoot('app/modules/'.$module.'/routes.php') );
        $new_routes = array();


        foreach ($routes as $path => $route) {
            $pach_array = explode('|', $path);
            if(isset($pach_array[0]) && isset($pach_array[1])){
                $prefix = $pach_array[0].'/';
                $path = $pach_array[1];
            } else{
                $path = $pach_array[0];
                $prefix = '';
            }

            $path = $prefix.'modules/'.$module.'/'.$path;
            $new_routes[$path] = '[modules/'.$module.'/controllers/]'.$route;
        }



        $Router = new \System\ModuleRouter($new_routes);
        $Router->run();

    }



}