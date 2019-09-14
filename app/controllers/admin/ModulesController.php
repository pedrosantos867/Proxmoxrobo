<?php
namespace admin;

use System\Module;
use System\Router;
use System\Tools;

class ModulesController extends FrontController {
    public function actionList(){
        $gview = $this->getView('settings.php');
        $this->layout->import('content', $gview);

        $view = $this->getView('module/list.php');
        $gview->import('content', $view);

        $modules = Module::getListAll();
        $modules_view = array();
        foreach ($modules as $module) {
            $Module = new \model\Module();
            $row    = new \model\Module($Module->where('system_name', $module->systemName)->getRow());
            $object = null;

            if($row->isLoadedObject()){


                $row->system_name = $module->systemName;
                $row->name = $module->name;
                $row->author = $module->author;
                $row->save();

                $object = $row;

            } else {
                $Module->system_name = $module->systemName;
                $Module->name        = $module->name;
                $Module->status      = 0;
                $Module->author      = $module->author;
                $Module->save();

                $object        = $Module;
            }

            $object->has_setting_page = method_exists($module,'actionSetting');
            $object->module = $module;

            $modules_view[] = $object;
        }
        $view->modules = $modules_view;

    }

    public function actionInstall(){
        if(!HB_DEMO_MODE){
            $module_id = Tools::rGET('module_id');
            $Module = new \model\Module($module_id);
            if($Module->isLoadedObject()){
                $ModuleClass = Module::getModuleClass($Module->system_name);
                $ModuleClass->install();

                $Module->status =1;
                $Module->save();
            }

            Tools::redirect('admin/modules');
        }
        else $this->layout->demo_mode = true;
    }

    public function actionUninstall(){
        if(!HB_DEMO_MODE){
            $module_id = Tools::rGET('module_id');
            $Module = new \model\Module($module_id);
            if($Module->isLoadedObject()){
                $ModuleClass = Module::getModuleClass($Module->system_name);
                $ModuleClass->uninstall();

                $Module->status =0;
                $Module->save();
            }

            Tools::redirect('admin/modules');
        }
        else $this->layout->demo_mode = true;
    }

    public function actionSetting(){
        $module = Router::getParam('module');
        $Module = Module::getModuleClass($module);

        $moduleObject = \model\Module::factory()->where('system_name', $module)->getRow();

        if($moduleObject->status !=1){
            Tools::display404Error();
        }

        $view = $Module->actionSetting();

        $gview = $this->getView('settings.php');
        $this->layout->import('content', $gview);


        $gview->import('content', $view);
    }
}