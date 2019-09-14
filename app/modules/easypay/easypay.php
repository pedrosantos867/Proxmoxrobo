<?php

namespace modules\easypay;

use modules\easypay\classes\payment\EasyPayAPI;

use model\Bill;
use model\Currency;
use stdClass;
use System\Config;
use System\Module;
use System\Router;
use System\Tools;

class easypay extends Module{
    public $name = 'Платежная система easypay.ua';
    public $author = '<a target="_blank" href="http://hopebilling.com">hopebilling.com</a>';
    public $category = 3; // for payments systems


    public function install(){

        $pconfig                                 = new Config('payments');
        $pconfig->easypay                        = new stdClass();
        $pconfig->easypay->merchant_id           = "";
        $pconfig->easypay->secret_key            = "";
        $pconfig->save();

        $this->registerHook('displayPaymentMethods');

    }

    public function uninstall(){

        $pconfig                                 = new Config('payments');
        $pconfig->delete('easypay');
        $pconfig->save();


        return parent::uninstall();
    }

    public function displayPaymentMethods(&$view){
        $pconfig = new Config('payments');
        $id_bill = Router::getParam(0);

        $Bill = new Bill($id_bill);
        $view = $this->getModuleView('bill/pay.php');
        $dcurrency = new Currency($this->config->currency_default);


        //easypay

            $easypay = new EasyPayAPI(array(
                'merchant_id'   => $pconfig->easypay->merchant_id,
                'secret_key'    => $pconfig->easypay->secret_key
            ));

            $payment       = $easypay->createPayment(array(
                'id'          => $Bill->id,
                'amount'      => $Bill->total,
                'description' => 'Оплата услуг компании',
                'currency'    => $dcurrency->iso,
                'success_url' =>  Tools::link('modules/easypay/success')
            ));

            $view->easypay = $payment;

        //easypay end


        $view->id_bill = $id_bill;

    }

    public function actionSetting(){
        $pconfig = new Config('payments');

        if (Tools::rPOST()) {
            $pconfig->easypay->merchant_id = Tools::rPOST('merchant_id');
            $pconfig->easypay->secret_key = Tools::rPOST('secret_key');
            $pconfig->save();
        }

        $view  = $this->getModuleView('setting.php', 'admin');
        $view->pconfig= $pconfig;
        $view->currencies = Currency::factory()->getRows();
        return $view;
    }


}