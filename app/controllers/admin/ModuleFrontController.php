<?php
namespace admin;

use System\Path;
use System\View\View;

class ModuleFrontController extends FrontController{

    public $module;

    public function __construct()
    {
        parent::__construct();

        $called_class = get_called_class();
        $path_array = explode('\\', $called_class);
        $this->module = $path_array[1];
        
    }

    public function getModuleView($path)
    {
        //if selected front template not exist in module dir than use default template
        if(!file_exists( Path::getRoot('app/modules/'.$this->module.'/'.'template/admin/' . $this->viewTheme.'/'.$path))){
            $this->viewTheme = 'default';
        }

        $view = new View('template/admin/' . $this->viewTheme, $path, 'app/modules/'.$this->module.'/');
        return $view;
    }
}