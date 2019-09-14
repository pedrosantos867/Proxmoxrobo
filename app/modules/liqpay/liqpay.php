<?php

namespace modules\liqpay;

use modules\liqpay\classes\payment\LiqPayAPI;
use model\Bill;
use model\Currency;
use stdClass;
use System\Config;
use System\Module;
use System\Router;
use System\Tools;

class liqpay extends Module{
    public $name = 'Платежная система liqpay.com';
    public $author = '<a target="_blank" href="http://hopebilling.com">hopebilling.com</a>';
    public $category = 3; // for payments systems


    public function install(){

        $pconfig                                 = new Config('payments');
        $pconfig->liqpay                        = new stdClass();
        $pconfig->liqpay->enable                = 0;
        $pconfig->liqpay->public_key            = "";
        $pconfig->liqpay->private_key           = "";
        $pconfig->save();

        $this->registerHook('displayPaymentMethods');

    }

    public function uninstall(){

        $pconfig                                 = new Config('payments');
        $pconfig->delete('liqpay');
        $pconfig->save();


        return parent::uninstall();
    }

    public function displayPaymentMethods(&$view){
        $pconfig = new Config('payments');
        $id_bill = Router::getParam(0);

        $Bill = new Bill($id_bill);
        $view = $this->getModuleView('bill/pay.php');
        $dcurrency = new Currency($this->config->currency_default);


        //liqpay

            $liqpay = new LiqPayAPI(array(
                'public_key'       => $pconfig->liqpay->public_key,
                'private_key'      => $pconfig->liqpay->private_key
            ));

            $payment       = $liqpay->createPayment(array(
                'id'          => $Bill->id,
                'amount'      => $Bill->total,
                'description' => 'Оплата услуг компании',
                'currency'    => $dcurrency->iso,
                'success_url' => Tools::link('modules/liqpay/result?bill='.$Bill->id),
                'status_url'  => Tools::link('modules/liqpay/status'),
            ));

            $view->liqpay = $payment;

        //liqpay end


        $view->id_bill = $id_bill;

    }

    public function actionSetting(){
        $pconfig = new Config('payments');

        if (Tools::rPOST()) {
            $pconfig->liqpay->public_key = Tools::rPOST('public_key');
            $pconfig->liqpay->private_key = Tools::rPOST('private_key');
            $pconfig->save();
        }

        $view  = $this->getModuleView('setting.php', 'admin');
        $view->pconfig= $pconfig;
        $view->currencies = Currency::factory()->getRows();
        return $view;
    }


}