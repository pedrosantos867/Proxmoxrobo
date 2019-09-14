<?php

namespace modules\unitpay;

use model\Bill;
use model\Currency;
use modules\unitpay\classes\payment\UnitpayAPI;
use stdClass;
use System\Config;
use System\Module;
use System\Router;
use System\Tools;

class unitpay extends Module{
    public $name = 'Платежная система unitpay.ru';
    public $author = '<a target="_blank" href="http://hopebilling.com">hopebilling.com</a>';
    public $category = 3; // for payments systems


    public function install(){

        $pconfig                                = new Config('payments');
        $pconfig->unitpay                        = new stdClass();
        $pconfig->unitpay->public_key            = "";
        $pconfig->unitpay->secret_key            = "";
        $pconfig->unitpay->currency                 = 0;

        $pconfig->save();

        $this->registerHook('displayPaymentMethods');

    }

    public function uninstall(){

        $pconfig                                 = new Config('payments');
        $pconfig->delete('unitpay');
        $pconfig->save();


        return parent::uninstall();
    }

    public function displayPaymentMethods(&$view){
        $pconfig = new Config('payments');
        $id_bill = Router::getParam(0);

        $Bill = new Bill($id_bill);
        $view = $this->getModuleView('bill/pay.php');
        $dcurrency  = new Currency($this->config->currency_default);

        $currency   = new Currency($pconfig->unitpay->currency);

        if(!$currency->isLoadedObject())
        {
            $currency = $dcurrency;
        }
        $payment_amount = Currency::convert($Bill->total, $currency->id);


        $upay = new UnitpayAPI(array(
            'public_key' => $pconfig->unitpay->public_key,
            'secret_key' => $pconfig->unitpay->secret_key
        ));

        $payment       = $upay->createPayment(array(
            'id'          => $Bill->id,
            'amount'      => $payment_amount,
            'description' => "Оплата услуг хостинг компании",
            'currency'    => $currency->iso
        ));
        $view->unitpay = $payment;




        $view->id_bill = $id_bill;

    }

    public function actionSetting(){
        $pconfig = new Config('payments');

        if (Tools::rPOST()) {

            $pconfig->unitpay->public_key                  = Tools::rPOST('public_key');
            $pconfig->unitpay->secret_key                  = Tools::rPOST('secret_key');
            $pconfig->unitpay->currency                  = Tools::rPOST('currency', 0);

            $pconfig->save();
        }

        $view  = $this->getModuleView('setting.php', 'admin');
        $view->pconfig= $pconfig;
        $view->currencies = Currency::factory()->getRows();
        return $view;
    }


}