<?php

namespace modules\erip;

use model\Bill;
use model\Currency;
use stdClass;
use System\Config;
use System\Module;
use System\Router;
use System\Tools;

class erip extends Module{
    public $name = 'Единое Расчётное Информационное Пространство';
    public $author = '<a target="_blank" href="http://hopebilling.com">hopebilling.com</a>';
    public $category = 3; // for payments systems


    public function install(){
        $this->registerHook('displayPaymentMethods');

        $pconfig                                 = new Config('payments');
        $pconfig->erip                      = new stdClass();
        $pconfig->erip->shop_id             = "";
        $pconfig->erip->key                 = "";// for defaults
        $pconfig->erip->service_code        = "";
        $pconfig->erip->currency            = 0;
        $pconfig->save();

        return parent::install();
    }

    public function uninstall(){
        $pconfig                                 = new Config('payments');
        $pconfig->delete('erip');
        $pconfig->save();

        return parent::uninstall();

    }

    public function displayPaymentMethods(&$view){
        $id_bill = Router::getParam(0);
        $view = $this->getModuleView('bill/pay.php');
        $view->id_bill = $id_bill;

    }

    public function actionSetting(){

        $pconfig = new Config('payments');
        if (Tools::rPOST()) {

            $pconfig->erip                      = new stdClass();
            $pconfig->erip->shop_id             = Tools::rPOST('shop_id');
            $pconfig->erip->key                 = Tools::rPOST('key');
            $pconfig->erip->service_code        = Tools::rPOST('service_code');
            $pconfig->erip->currency            = (int)Tools::rPOST('currency');
            $pconfig->save();
        }

        $view  = $this->getModuleView('setting.php', 'admin');
        $view->currencies = Currency::factory()->getRows();
        $view->pconfig= $pconfig;
        return $view;
    }


}