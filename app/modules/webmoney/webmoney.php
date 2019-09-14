<?php

namespace modules\webmoney;

use model\Bill;
use model\Currency;
use modules\webmoney\classes\payment\WebMoneyAPI;
use stdClass;
use System\Config;
use System\Module;
use System\Router;
use System\Tools;

class webmoney extends Module{

    public $name = 'Платежная система webmoney.ru';
    public $author = '<a target="_blank" href="http://hopebilling.com">hopebilling.com</a>';
    public $category = 3; // for payments systems


    public function install(){

        $pconfig                                 = new Config('payments');
        $pconfig->webmoney                       = new stdClass();
        $pconfig->webmoney->purse                = "";
        $pconfig->webmoney->secret_key           = "";
        $pconfig->webmoney->secret_keyx20        = "";

        $pconfig->save();

        $this->registerHook('displayPaymentMethods');

    }

    public function uninstall(){

        $pconfig                                 = new Config('payments');
        $pconfig->delete('webmoney');
        $pconfig->save();

        return parent::uninstall();
    }

    public function displayPaymentMethods(&$view){
        $pconfig = new Config('payments');
        $id_bill = Router::getParam(0);

        $Bill = new Bill($id_bill);
        $view = $this->getModuleView('bill/pay.php');
        $dcurrency = new Currency($this->config->currency_default);

        $payment_amount     = $Bill->total;
        $payment_id         = $Bill->id;
        $payment_desc       = 'Оплата услуг компании';
        $currency           = $dcurrency->iso;

        $webmoney = new WebMoneyAPI(array(
            'purse'             => $pconfig->webmoney->purse,
            'secret_key'        => $pconfig->webmoney->secret_key,
            'secret_keyx20'     => $pconfig->webmoney->secret_keyx20
        ));

        $payment       = $webmoney->createPayment(array(
            'id'          => $payment_id,
            'amount'      => $payment_amount,
            'description' => $payment_desc,
            'currency'    => $currency,
            'success_url' => Tools::link('modules/webmoney/result?status=1'),
            'fail_url'    => Tools::link('modules/webmoney/result?status=0'),
            'status_url'  => Tools::link('modules/webmoney/status'),
        ));

        $view->webmoney = $payment;

        $view->id_bill = $id_bill;

    }

    public function actionSetting(){
        $pconfig = new Config('payments');

        if (Tools::rPOST()) {

            $pconfig->webmoney->purse                = Tools::rPOST('purse');
            $pconfig->webmoney->secret_key           = Tools::rPOST('secret_key');
            $pconfig->webmoney->secret_keyx20        = Tools::rPOST('secret_keyx20');

            $pconfig->save();
        }

        $view  = $this->getModuleView('setting.php', 'admin');
        $view->pconfig  = $pconfig;
        $view->currencies = Currency::factory()->getRows();
        return $view;
    }


}