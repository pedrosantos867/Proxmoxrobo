<?php namespace System;

class ControllerFactory
{

    public static function includeController($className, $folder = '')
    {



        if (!class_exists($className, false)) {
            if (file_exists(Path::getRoot('app/controllers/' . $folder . '/' . $className . '.php'))) {
                require_once(Path::getRoot('app/controllers/' . $folder . '/' . $className . '.php'));
            } else if (file_exists(Path::getRoot('core/controllers/' . $folder . '/' . $className . '.php'))) {
                require_once(Path::getRoot('core/controllers/' . $folder . '/' . $className . '.php'));
            } else {
                throw new Exception('Controller not exist. Please create Controller or configure route! ');


            }
        }
    }

    public static function getController($className, $folder = '')
    {


        ControllerFactory::includeController($className, $folder);

  //      echo $className;
        $class = ($folder . '\\' . $className);
//        echo $class;

        return new $class();
    }


}