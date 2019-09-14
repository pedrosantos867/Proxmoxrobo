<?php

namespace modules\payture;

use model\Bill;
use model\Currency;
use modules\payture\classes\payture\PaytureAPI;
use stdClass;
use System\Config;
use System\Module;
use System\Router;
use System\Tools;

class payture extends Module
{
    public $name = 'Платежная система payture.com';
    public $author = '<a target="_blank" href="http://hopebilling.com">hopebilling.com</a>';
    public $category = 3; // for payments systems


    public function install()
    {

        $pconfig = new Config('payments');
        $pconfig->payture = new stdClass();
        $pconfig->payture->orderid = 0;
        $pconfig->payture->currency = 0;
        $pconfig->payture->test = 0;
        $pconfig->payture->host = "";
        $pconfig->payture->key = "";
        $pconfig->payture->password = "";
        $pconfig->payture->host_serv = $_SERVER["HTTP_HOST"];
        $pconfig->save();

        $this->registerHook('displayPaymentMethods');

    }

    public function uninstall()
    {

        $pconfig = new Config('payments');
        $pconfig->delete('payture');
        $pconfig->save();


        return parent::uninstall();
    }

    public function displayPaymentMethods(&$view)
    {
        $pconfig = new Config('payments');
        $id_bill = Router::getParam(0);

        $Bill = new Bill($id_bill);
        $view = $this->getModuleView('bill/pay.php');


        $view->id_bill = $id_bill;

    }

    public function actionSetting()
    {
        $pconfig = new Config('payments');
        $currency = new Currency(Tools::rPOST('currency'));
        $view = $this->getModuleView('setting.php', 'admin');
        $view->error = 0;

        if (Tools::rPOST()) {

            $pconfig->payture->key = Tools::rPOST('key');
            $pconfig->payture->password = Tools::rPOST('password');
            $pconfig->payture->currency = Tools::rPOST('currency');
            $pconfig->payture->backend_secure_password = Tools::rPOST('backend_secure_password');
            $pconfig->payture->test = Tools::rPOST('test');

            $pconfig->save();
        }

        $view->pconfig = $pconfig;
        $view->currencies = Currency::factory()->getRows();
        return $view;
    }

}


