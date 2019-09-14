<?php

namespace modules\siteheart;

use System\Config;
use System\Module;
use System\Tools;


class siteheart extends Module{
    /*
     * Module name
     * */
    public $name = 'Онлайн чат Siteheart';

    /*
     * Module author
     * */
    public $author = '<a target="_blank" href="http://hopebilling.com">hopebilling.com</a>';

    /*
     * Module category
     * */
    public $category = 5;


    public function install(){
        $this->registerHook('displayAfterContent');
        return parent::install();
    }

    public function displayAfterContent(&$mview){

        $mview = $this->getModuleView('siteheart.php');
       $mview->code = Config::factory()->siteheart;
    }

    public function uninstall(){

        return parent::uninstall();
    }

    public function actionSetting()
    {
        $pconfig = Config::factory();
        $view = $this->getModuleView('setting.php', 'admin');
        $view->error = 0;
        if (Tools::rPOST()) {

            $pconfig->siteheart = Tools::rPOST('code');
            $pconfig->save();
        }

        $view->pconfig = $pconfig;
        return $view;
    }


}

