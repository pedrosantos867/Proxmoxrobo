<?php

namespace modules\paypal;

use model\Bill;
use model\Currency;
use modules\paypal\classes\payment\PayPalAPI;
use modules\yandexkassa\classes\payment\YandexKassaAPI;
use stdClass;
use System\Config;
use System\Module;
use System\Router;
use System\Tools;

class paypal extends Module{
    public $name = 'Платежная система paypal.com';
    public $author = '<a target="_blank" href="http://hopebilling.com">hopebilling.com</a>';
    public $category = 3; // for payments systems


    public function install(){

        $pconfig                                  = new Config('payments');
        $pconfig->paypal                          = new stdClass();
        $pconfig->paypal->client_id               = "";
        $pconfig->paypal->secret                  = "";
        $pconfig->paypal->token_data                  = array('expires_in' => 0, 'token' => '');
        $pconfig->paypal->test_mode               = 0;
        $pconfig->paypal->percent = 0;
        $pconfig->save();

        $this->registerHook('displayPaymentMethods');

    }

    public function uninstall(){

        $pconfig                                 = new Config('payments');
        $pconfig->delete('paypal');
        $pconfig->save();


        return parent::uninstall();
    }

    public function displayPaymentMethods(&$view){
        $pconfig = new Config('payments');
        $id_bill = Router::getParam(0);

        $Bill = new Bill($id_bill);
        $view = $this->getModuleView('bill/pay.php');


        $view->id_bill = $id_bill;

    }

    public function actionSetting(){
        $pconfig = new Config('payments');

        if (Tools::rPOST()) {

            $pconfig->paypal->client_id                = Tools::rPOST('client_id');
            $pconfig->paypal->secret                   = Tools::rPOST('secret');
            $pconfig->paypal->percent                  = Tools::rPOST('percent');
            $pconfig->paypal->test_mode                = Tools::rPOST('test_mode',0);

            $pconfig->save();
        }

        $view  = $this->getModuleView('setting.php', 'admin');
        $view->pconfig= $pconfig;
        $view->currencies = Currency::factory()->getRows();
        return $view;
    }

}