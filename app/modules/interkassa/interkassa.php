<?php

namespace modules\interkassa;

use model\Bill;
use model\Currency;
use modules\interkassa\classes\payment\InterkassaAPI;
use stdClass;
use System\Config;
use System\Module;
use System\Router;
use System\Tools;

class interkassa extends Module{
    public $name = 'Платежная система interkassa.com';
    public $author = '<a target="_blank" href="http://hopebilling.com">hopebilling.com</a>';
    public $category = 3; // for payments systems


    public function install(){

        $pconfig                                    = new Config('payments');
        $pconfig->interkassa                        = new stdClass();
        $pconfig->interkassa->id                    = "";
        $pconfig->interkassa->secret_key            = "";
        $pconfig->interkassa->test_secret_key       = "";
        $pconfig->interkassa->test_mode             = 0;
        $pconfig->interkassa->currency              = 0;

        $pconfig->save();

        $this->registerHook('displayPaymentMethods');

    }

    public function uninstall(){

        $pconfig                                 = new Config('payments');
        $pconfig->delete('interkassa');
        $pconfig->save();


        return parent::uninstall();
    }

    public function displayPaymentMethods(&$view){
        $pconfig = new Config('payments');
        $id_bill = Router::getParam(0);

        $Bill = new Bill($id_bill);
        $view = $this->getModuleView('bill/pay.php');
        $dcurrency = new Currency($this->config->currency_default);

        $currency = new Currency($pconfig->interkassa->currency);

        if(!$currency->isLoadedObject())
        {
            $currency = $dcurrency;
        }
        $payment_amount = Currency::convert($Bill->total, $currency->id);

        $payment_id     = $Bill->id; // Your payment id

        $payment_desc   = 'Оплата услуг хостинга'; // Payment description


            $interkassa = new InterkassaAPI(array(
                'id'         => $pconfig->interkassa->id,
                'secret_key' => $pconfig->interkassa->secret_key
            ));


            $payment = $interkassa->createPayment(array(
                'id'            => $payment_id,
                'amount'        => $payment_amount,
                'description'   => $payment_desc,
                'currency'      => $currency->iso,
                'success_url'   => Tools::link('modules/interkassa/result'),
                'fail_url'      => Tools::link('modules/interkassa/result'),
                'status_url'    => Tools::link('modules/interkassa/status'),
                'test_mode'     => $pconfig->interkassa->test_mode
            ));

            $view->interkassa = $payment;

        $view->id_bill = $id_bill;

    }

    public function actionSetting(){
        $pconfig = new Config('payments');

        if (Tools::rPOST()) {

            $pconfig->interkassa->id                = Tools::rPOST('id');
            $pconfig->interkassa->secret_key        = Tools::rPOST('secret_key');
            $pconfig->interkassa->test_secret_key   = Tools::rPOST('test_secret_key');
            $pconfig->interkassa->test_mode         = Tools::rPOST('test_mode') ? 1 : 0;
            $pconfig->interkassa->currency          = Tools::rPOST('currency');

            $pconfig->save();
        }

        $view  = $this->getModuleView('setting.php', 'admin');
        $view->pconfig= $pconfig;
        $view->currencies = Currency::factory()->getRows();
        return $view;
    }


}