<?php

namespace modules\bepaid;

use model\Bill;
use model\Currency;
use modules\bepaid\classes\payment\BePaidAPI;
use stdClass;
use System\Config;
use System\Module;
use System\Router;
use System\Tools;

class bepaid extends Module{
    public $name = 'Платежная система bepaid.by';
    public $author = '<a target="_blank" href="http://hopebilling.com">hopebilling.com</a>';
    public $category = 3; // for payments systems


    public function install(){

        $pconfig                                 = new Config('payments');
        $pconfig->bepaid                        = new stdClass();
        $pconfig->bepaid->currency              = 0;
        $pconfig->bepaid->shop_id               = "";
        $pconfig->bepaid->key                   = "";
        $pconfig->save();

        $this->registerHook('displayPaymentMethods');

    }

    public function uninstall(){

        $pconfig                                 = new Config('payments');
        $pconfig->delete('bepaid');
        $pconfig->save();


        return parent::uninstall();
    }

    public function displayPaymentMethods(&$view){
        $pconfig = new Config('payments');
        $id_bill = Router::getParam(0);

        $Bill = new Bill($id_bill);
        $view = $this->getModuleView('bill/pay.php');
        $dcurrency = new Currency($this->config->currency_default);

        $currency = new Currency($pconfig->bepaid->currency);

        if(!$currency->isLoadedObject()){
            $currency = $dcurrency;
        }

        $bepaid = new BePaidAPI(array(
            'shop_id'   => $pconfig->bepaid->shop_id,
            'key'       => $pconfig->bepaid->key
        ));

        $payment       = $bepaid->createPayment(array(
            'id'          => $Bill->id,
            'amount'      => Currency::convert($Bill->total, $currency->id),
            'description' => 'Оплата услуг компании',
            'currency'    => $currency->iso,
            'success_url' => Tools::link('modules/bepaid/result'),
            'fail_url'    => Tools::link('modules/bepaid/result'),
            'status_url'  => Tools::link('modules/bepaid/status'),
        ));
        $view->bepaid = $payment;


        $view->id_bill = $id_bill;

    }

    public function actionSetting(){
        $pconfig = new Config('payments');

        if (Tools::rPOST()) {
            $pconfig->bepaid->shop_id   = Tools::rPOST('shop_id');
            $pconfig->bepaid->key       = Tools::rPOST('key');
            $pconfig->bepaid->currency  = Tools::rPOST('currency');
            $pconfig->save();
        }

        $view  = $this->getModuleView('setting.php', 'admin');
        $view->pconfig= $pconfig;
        $view->currencies = Currency::factory()->getRows();
        return $view;
    }


}