<?php

@ini_set('display_errors', 1);

define('HB_DEMO_MODE', 0);


if( ! ini_get('date.timezone') )
{
    date_default_timezone_set('GMT');
}

spl_autoload_register('hopebilling_autoload');

function hopebilling_autoload($namespace_class){
    $class_data = explode('\\', $namespace_class);
    $class_name = $class_data[count($class_data) - 1] . '.php';
    $path       = implode('/', $class_data);
    $path .= '.php';

    //echo '<br>'; print_r($class_data);
    if($class_data[0]=='modules'){
      //  echo _BASE_DIR_APP_ . $path;
        require_once(_BASE_DIR_APP_ . $path);
        return;
    }


    if (substr($class_name, 0, 1) == 'IFace') {
        // echo $path;
    } else {
        if (strpos($class_name, 'Controller')) {
            $dir = _BASE_DIR_APP_ . 'controllers/';


            require_once($dir . $path);
        } else {
            if (file_exists(_BASE_DIR_APP_ . 'classes/' . $path)) {
                require_once(_BASE_DIR_APP_ . 'classes/' . $path);
            } else if (file_exists(_BASE_DIR_CORE_ . 'classes/' . $path)) {
                require_once(_BASE_DIR_CORE_ . 'classes/' . $path);
            }
        }
    }
}