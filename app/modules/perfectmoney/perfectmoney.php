<?php

namespace modules\perfectmoney;

use modules\perfectmoney\classes\payment\PerfectMoneyAPI;
use model\Bill;
use model\Currency;
use stdClass;
use System\Config;
use System\Module;
use System\Router;
use System\Tools;

class perfectmoney extends Module{
    public $name = 'Платежная система PerfectMoney.is';
    public $author = '<a target="_blank" href="http://hopebilling.com">hopebilling.com</a>';
    public $category = 3; // for payments systems


    public function install(){

        $pconfig                                 = new Config('payments');
        $pconfig->perfectmoney                        = new stdClass();
        $pconfig->perfectmoney->enable                = 0;
        $pconfig->perfectmoney->payee_account         = '';
        $pconfig->perfectmoney->alternate_passphrase  = '';
		$pconfig->perfectmoney->payee_name            = "Биллинг панель";
        $pconfig->save();

        $this->registerHook('displayPaymentMethods');

    }

    public function uninstall(){

        $pconfig                                 = new Config('payments');
        $pconfig->delete('perfectmoney');
        $pconfig->save();


        return parent::uninstall();
    }

    public function displayPaymentMethods(&$view){
        $pconfig = new Config('payments');
        $id_bill = Router::getParam(0);

        $Bill = new Bill($id_bill);
        $view = $this->getModuleView('bill/pay.php');
        $dcurrency = new Currency($this->config->currency_default);


        //perfectmoney

            $perfectmoney = new PerfectMoneyAPI(array(
                'payee_account'        => $pconfig->perfectmoney->payee_account,
                'alternate_passphrase' => $pconfig->perfectmoney->alternate_passphrase,
				'payee_name'           => $pconfig->perfectmoney->payee_name
            ));

            $payment       = $perfectmoney->createPayment(array(
                'id'          => $Bill->id,
                'amount'      => $Bill->total,
                'description' => 'Оплата услуг компании',
                'currency'    => $dcurrency->iso,
                'success_url' => Tools::link('modules/perfectmoney/result?bill='.$Bill->id),
                'status_url'  => Tools::link('modules/perfectmoney/status'),
				'fail_url'    => Tools::link('modules/perfectmoney/cancel')
            ));

            $view->perfectmoney = $payment;

        //perfectmoney end


        $view->id_bill = $id_bill;

    }

    public function actionSetting(){
        $pconfig = new Config('payments');

        if (Tools::rPOST()) {
            $pconfig->perfectmoney->payee_account           = Tools::rPOST('payee_account');
            $pconfig->perfectmoney->alternate_passphrase    = Tools::rPOST('alternate_passphrase');
			$pconfig->perfectmoney->payee_name              = Tools::rPOST('payee_name');
            $pconfig->save();
        }

        $view = $this->getModuleView('setting.php', 'admin');
        $view->pconfig= $pconfig;
        $view->currencies = Currency::factory()->getRows();
        return $view;
    }


}