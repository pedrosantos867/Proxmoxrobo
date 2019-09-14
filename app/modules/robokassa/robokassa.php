<?php

namespace modules\robokassa;

use model\Bill;
use model\Currency;
use modules\interkassa\classes\payment\InterkassaAPI;
use modules\robokassa\classes\payment\RobokassaAPI;
use stdClass;
use System\Config;
use System\Module;
use System\Router;
use System\Tools;

class robokassa extends Module{
    public $name = 'Платежная система robokassa.ru';
    public $author = '<a target="_blank" href="http://hopebilling.com">hopebilling.com</a>';
    public $category = 3; // for payments systems


    public function install(){

        $pconfig                                    = new Config('payments');
        $pconfig->robokassa                        = new stdClass();
        $pconfig->robokassa->merchant                    = "";
        $pconfig->robokassa->password1            = "";
        $pconfig->robokassa->password2            = "";
        $pconfig->robokassa->test_mode             = 0;
        $pconfig->robokassa->currency              = 0;

        $pconfig->save();

        $this->registerHook('displayPaymentMethods');

    }

    public function uninstall(){

        $pconfig                                 = new Config('payments');
        $pconfig->delete('robokassa');
        $pconfig->save();


        return parent::uninstall();
    }

    public function displayPaymentMethods(&$view){
        $pconfig = new Config('payments');
        $id_bill = Router::getParam(0);

        $Bill = new Bill($id_bill);
        $view = $this->getModuleView('bill/pay.php');
        $dcurrency = new Currency($this->config->currency_default);

        $currency = new Currency($pconfig->robokassa->currency);

        if(!$currency->isLoadedObject())
        {
            $currency = $dcurrency;
        }

        $payment_amount = Currency::convert($Bill->total, $currency->id);

        $payment_id     = $Bill->id; // Your payment id

        $payment_desc   = 'Оплата услуг хостинга'; // Payment description


        $rbks = new RobokassaAPI(array(
            'merchant'  => $pconfig->robokassa->merchant,
            'password1' => $pconfig->robokassa->password1,
            'password2' => $pconfig->robokassa->password2
        ));

        $payment         = $rbks->createPayment(array(
            'id'          => $payment_id,
            'amount'      => $payment_amount,
            'description' => $payment_desc,
            'currency'    => $currency->iso,
            'test_mode'   => $pconfig->robokassa->test_mode
        ));

        $view->robokassa = $payment;

        $view->id_bill = $id_bill;

    }

    public function actionSetting(){
        $pconfig = new Config('payments');

        if (Tools::rPOST()) {

            $pconfig->robokassa->merchant       = Tools::rPOST('merchant');
            $pconfig->robokassa->password1      = Tools::rPOST('password1');
            $pconfig->robokassa->password2      = Tools::rPOST('password2');
            $pconfig->robokassa->currency       = Tools::rPOST('currency');
            $pconfig->robokassa->test_mode      = Tools::rPOST('test_mode');

            $pconfig->save();
        }

        $view  = $this->getModuleView('setting.php', 'admin');
        $view->pconfig= $pconfig;
        $view->currencies = Currency::factory()->getRows();
        return $view;
    }


}