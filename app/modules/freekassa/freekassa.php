<?php

namespace modules\freekassa;

use modules\freekassa\classes\payment\FreeKassaAPI;
use model\Bill;
use model\Currency;
use stdClass;
use System\Config;
use System\Module;
use System\Router;
use System\Tools;

class freekassa extends Module{
    public $name = 'Платежная система free-kassa.ru';
    public $author = '<a target="_blank" href="http://hopebilling.com">hopebilling.com</a>';
    public $category = 3; // for payments systems


    public function install(){

        $pconfig                                 = new Config('payments');
        $pconfig->freekassa                      = new stdClass();
        $pconfig->freekassa->enable              = 0;
        $pconfig->freekassa->shop_id             = "";
        $pconfig->freekassa->currency            = 0;// for defaults
        $pconfig->freekassa->secret_key          = "";
        $pconfig->freekassa->secret_key2         = "";
        $pconfig->save();

        $this->registerHook('displayPaymentMethods');

    }

    public function uninstall(){


        $pconfig                                 = new Config('payments');
        $pconfig->delete('freekassa');
        $pconfig->save();

        return parent::uninstall();
    }

    public function displayPaymentMethods(&$view){
        $pconfig = new Config('payments');
        $id_bill = Router::getParam(0);


        $Bill = new Bill($id_bill);
        $view = $this->getModuleView('bill/pay.php');
        $dcurrency = new Currency($this->config->currency_default);
        $currency = new Currency($pconfig->freekassa->currency);
        if(!$currency->isLoadedObject()){
            $currency = $dcurrency;
        }
        //freekassa

            $freekassa = new FreeKassaAPI(array(
                'shop_id'       => $pconfig->freekassa->shop_id,
                'secret_key'    => $pconfig->freekassa->secret_key
            ));

            $payment          = $freekassa->createPayment(array(
                'id'          => $Bill->id,
                'amount'      => Currency::convert($Bill->total, $currency->id),
                'description' => 'Оплата услуг компании',
                'currency'    => $currency->iso
            ));

            $view->freekassa = $payment;

        //freekassa end



        $view->id_bill = $id_bill;

    }

    public function actionSetting(){
        $pconfig = new Config('payments');
        if (Tools::rPOST()) {

            $pconfig->freekassa->shop_id       = Tools::rPOST('shop_id');
            $pconfig->freekassa->secret_key         = Tools::rPOST('secret_key');
            $pconfig->freekassa->secret_key2         = Tools::rPOST('secret_key2');
            $pconfig->freekassa->currency         = (int)Tools::rPOST('currency');
            $pconfig->save();
        }

        $view  = $this->getModuleView('setting.php', 'admin');
        $view->pconfig= $pconfig;
        $view->currencies = Currency::factory()->getRows();
        return $view;
    }


}