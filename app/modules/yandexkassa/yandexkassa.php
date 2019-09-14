<?php

namespace modules\yandexkassa;

use model\Bill;
use model\Currency;
use modules\yandexkassa\classes\payment\YandexKassaAPI;
use stdClass;
use System\Config;
use System\Module;
use System\Router;
use System\Tools;

class yandexkassa extends Module{
    public $name = 'Платежная система kassa.yandex.ru';
    public $author = '<a target="_blank" href="http://hopebilling.com">hopebilling.com</a>';
    public $category = 3; // for payments systems


    public function install(){

        $pconfig                                = new Config('payments');
        $pconfig->yandex                        = new stdClass();
        $pconfig->yandex->shopId                = "";
        $pconfig->yandex->scid                  = "";
        $pconfig->yandex->test_mode             = 0;
        $pconfig->yandex->password              = "";
        $pconfig->save();

        $this->registerHook('displayPaymentMethods');

    }

    public function uninstall(){

        $pconfig                                 = new Config('payments');
        $pconfig->delete('yandex');
        $pconfig->save();


        return parent::uninstall();
    }

    public function displayPaymentMethods(&$view){
        $pconfig = new Config('payments');
        $id_bill = Router::getParam(0);

        $Bill = new Bill($id_bill);
        $view = $this->getModuleView('bill/pay.php');
        $dcurrency = new Currency($this->config->currency_default);


        //yandex

            $YandexKassa = new YandexKassaAPI(array(
                'shopId'            => $pconfig->yandex->shopId,
                'scid'              => $pconfig->yandex->scid,
                'password'          => $pconfig->yandex->password
            ));

            $payment       = $YandexKassa->createPayment(array(
                'id'          => $Bill->id,
                'amount'      => $Bill->total,
                'description' => 'Оплата услуг компании',
                'currency'    => $dcurrency->iso,
                'test_mode'   => $pconfig->yandex->test_mode,
                'success_url' => Tools::link('modules/yandexkassa/status'),
                'fail_url'    => Tools::link('modules/yandexkassa/status')
            ));

            $view->yandexkassa = $payment;

        //yandex end


        $view->id_bill = $id_bill;

    }

    public function actionSetting(){
        $pconfig = new Config('payments');

        if (Tools::rPOST()) {

            $pconfig->yandex->shopId                = Tools::rPOST('shopId');
            $pconfig->yandex->scid                  = Tools::rPOST('scid');
            $pconfig->yandex->test_mode             = Tools::rPOST('test_mode',0 );
            $pconfig->yandex->password              = Tools::rPOST('password');

            $pconfig->save();
        }

        $view  = $this->getModuleView('setting.php', 'admin');
        $view->pconfig= $pconfig;
        $view->currencies = Currency::factory()->getRows();
        return $view;
    }


}