<?php
namespace System;

class ModuleRouter extends Router{



    public function includeController($controller, $controller_folder){

        preg_match('/\[(.*)\]/', $controller_folder, $matches );

        if(!isset($matches[1])){
            Tools::display404Error();
        }

        $dir = $matches[1];

        $controller_folder = preg_replace('/\[.*\]/', '', $controller_folder);


        $class_file = $dir.$controller_folder.'/'.$controller.'.php';

        include(Path::getRoot('app/'.$class_file));

        $class = $dir.$controller_folder.'/'.$controller;

        $class = str_replace('/', '\\', $class);

        return new $class();

        //return ControllerFactory::getController($controller, $controller_folder);
    }


}