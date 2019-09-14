<?php

namespace modules\privat24;

use model\Bill;
use model\Currency;
use modules\privat24\classes\payment\Privat24API;
use stdClass;
use System\Config;
use System\Module;
use System\Router;
use System\Tools;

class privat24 extends Module{
    public $name = 'Платежная система privat24.ua';
    public $author = '<a target="_blank" href="http://hopebilling.com">hopebilling.com</a>';
    public $category = 3; // for payments systems


    public function install(){

        $pconfig                                    = new Config('payments');
        $pconfig->privat24                          = new stdClass();
        $pconfig->privat24->id                      = "";
        $pconfig->privat24->secret_key              = "";
        $pconfig->privat24->currency                = 0;
        $pconfig->save();

        $this->registerHook('displayPaymentMethods');

    }

    public function uninstall(){

        $pconfig                                 = new Config('payments');
        $pconfig->delete('privat24');
        $pconfig->save();


        return parent::uninstall();
    }

    public function displayPaymentMethods(&$view){
        $pconfig = new Config('payments');
        $id_bill = Router::getParam(0);

        $Bill = new Bill($id_bill);
        $view = $this->getModuleView('bill/pay.php');
        $dcurrency = new Currency($this->config->currency_default);



            $p24 = new Privat24API(array(
                'merchant'   => $pconfig->privat24->id,
                'secret_key' => $pconfig->privat24->secret_key
            ));

            $currency = new Currency($pconfig->privat24->currency);

            if(!$currency->isLoadedObject())
            {
                $currency = $dcurrency;
            }
                $payment_amount = Currency::convert($Bill->total, $currency->id);

            $payment        = $p24->createPayment(array(
                'id'          => $Bill->id,
                'amount'      => $payment_amount,
                'description' => "Оплата услуг хостинг компании",
                'currency' => $currency->iso,
                'success_url' => Tools::link('modules/privat24/return'),
                'status_url'  => Tools::link('modules/privat24/status')
            ));
            $view->privat24 = $payment;



        $view->id_bill = $id_bill;

    }

    public function actionSetting(){
        $pconfig = new Config('payments');

        if (Tools::rPOST()) {
            $pconfig->privat24->id          = Tools::rPOST('id');
            $pconfig->privat24->secret_key  = Tools::rPOST('secret_key');
            $pconfig->privat24->currency    = Tools::rPOST('currency');
            $pconfig->save();
        }

        $view  = $this->getModuleView('setting.php', 'admin');
        $view->pconfig= $pconfig;
        $view->currencies = Currency::factory()->getRows();
        return $view;
    }


}