<?php

namespace modules\paysera;

use model\Bill;
use model\Currency;
use modules\paysera\classes\paysera\PaySeraAPI;
use stdClass;
use System\Config;
use System\Module;
use System\Router;
use System\Tools;

class paysera extends Module{
    public $name = 'Платежная система paysera.com';
    public $author = '<a target="_blank" href="http://hopebilling.com">hopebilling.com</a>';
    public $category = 3; // for payments systems


    public function install(){

        $pconfig                                  = new Config('payments');
        $pconfig->paysera                         = new stdClass();
        $pconfig->paysera->orderid                = 0;
        $pconfig->paysera->projectid              = 0;
        $pconfig->paysera->sign_password          = "";
        $pconfig->paysera->currency               = 0;
        $pconfig->paysera->test                   = 0;

        $pconfig->save();

        $this->registerHook('displayPaymentMethods');

    }

    public function uninstall(){

        $pconfig                                 = new Config('payments');
        $pconfig->delete('paysera');
        $pconfig->save();


        return parent::uninstall();
    }

    public function displayPaymentMethods(&$view){
        $pconfig = new Config('payments');
        $id_bill = Router::getParam(0);

        $Bill = new Bill($id_bill);
        $view = $this->getModuleView('bill/pay.php');

        $dcurrency = new Currency($this->config->currency_default);


        $currency = new Currency($pconfig->paysera->currency);

        if(!$currency->isLoadedObject())
        {
            $currency = $dcurrency;
        }

        $payment_amount = $currency->getPrice($Bill->total);


        $paysera = new PaySeraAPI(array(
            'projectid'     => $pconfig->paysera->projectid,
            'sign_password' => $pconfig->paysera->sign_password,
            'test'        => ($pconfig->paysera->test==1) ? 1 : 0
        ));

        $payment       = $paysera->createPayment(array(
            'id'          => $Bill->id,
            'amount'      => $payment_amount,
            'description' => 'Оплата услуг компании',
            'currency'    => $currency->iso,
            'success_url' => Tools::link('modules/paysera/accept'),
            'status_url'  => Tools::link('modules/paysera/callback'),
            'fail_url'    => Tools::link('modules/paysera/cancel')
        ));

        $view->paysera = $payment;
        $view->id_bill = $id_bill;

    }

    public function actionSetting()
    {
        $pconfig = new Config('payments');
        $currency = new Currency(Tools::rPOST('currency'));
        $view = $this->getModuleView('setting.php', 'admin');
        $view->error = 0;

        if (Tools::rPOST()) {

            $pconfig->paysera->projectid = Tools::rPOST('projectid');
            $pconfig->paysera->sign_password = Tools::rPOST('sign_password');
            $pconfig->paysera->currency = Tools::rPOST('currency');
            $pconfig->paysera->test = Tools::rPOST('test');

            $pconfig->save();
        }

        $view->pconfig= $pconfig;
        $view->currencies = Currency::factory()->getRows();
        return $view;
    }

}


