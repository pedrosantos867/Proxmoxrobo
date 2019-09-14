<?php

namespace modules\cryptonator;

use modules\cryptonator\classes\payment\CryptonatorAPI;
use model\Bill;
use model\Currency;
use stdClass;
use System\Config;
use System\Module;
use System\Router;
use System\Tools;

class cryptonator extends Module{
    public $name = 'Платежная система Cryptonator';
    public $author = '<a target="_blank" href="http://hopebilling.com">hopebilling.com</a>';
    public $category = 3; // for payments systems


    public function install(){

        $pconfig                                     = new Config('payments');
        $pconfig->cryptonator                        = new stdClass();
        $pconfig->cryptonator->enable                = 0;
        $pconfig->cryptonator->item_name             = "Биллинг панель";
        $pconfig->cryptonator->merchant_id           = '';
		$pconfig->cryptonator->secret                = '';
        $pconfig->save();

        $this->registerHook('displayPaymentMethods');

    }

    public function uninstall(){

        $pconfig                                 = new Config('payments');
        $pconfig->delete('cryptonator');
        $pconfig->save();


        return parent::uninstall();
    }

    public function displayPaymentMethods(&$view){
        $pconfig = new Config('payments');
        $id_bill = Router::getParam(0);

        $Bill = new Bill($id_bill);
        $view = $this->getModuleView('bill/pay.php');
        $dcurrency = new Currency($this->config->currency_default);


        //cryptonator

            $cryptonator = new CryptonatorAPI(array(
                'item_name'            => $pconfig->cryptonator->item_name,
                'merchant_id'          => $pconfig->cryptonator->merchant_id,
				'secret'               => $pconfig->cryptonator->secret
            ));

            $payment       = $cryptonator->createPayment(array(
                'id'              => $Bill->id,
                'amount'          => $Bill->total,
                'description'     => 'Оплата услуг компании',
                'currency'        => $dcurrency->iso,
                'success_url'     => Tools::link('modules/cryptonator/result?bill='.$Bill->id),
                'status_url'      => Tools::link('modules/cryptonator/status'),
				'fail_url'        => Tools::link('modules/cryptonator/cancel')
            ));

            $view->cryptonator = $payment;

        //cryptonator end


        $view->id_bill = $id_bill;

    }

    public function actionSetting(){
        $pconfig = new Config('payments');

        if (Tools::rPOST()) {
            $pconfig->cryptonator->item_name               = Tools::rPOST('item_name');
            $pconfig->cryptonator->merchant_id             = Tools::rPOST('merchant_id');
			$pconfig->cryptonator->secret                  = Tools::rPOST('secret');
            $pconfig->save();
        }

        $view = $this->getModuleView('setting.php', 'admin');
        $view->pconfig= $pconfig;
        $view->currencies = Currency::factory()->getRows();
        return $view;
    }


}