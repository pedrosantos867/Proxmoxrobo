<?php

namespace modules\stripe;

use model\Bill;
use model\Currency;
use modules\stripe\classes\stripe\StripeAPI;
use stdClass;
use System\Config;
use System\Module;
use System\Router;
use System\Tools;

class stripe extends Module
{
    public $name = 'Платежная система stripe.com';
    public $author = '<a target="_blank" href="http://hopebilling.com">hopebilling.com</a>';
    public $category = 3; // for payments systems


    public function install()
    {

        $pconfig = new Config('payments');
        $pconfig->stripe = new stdClass();
        $pconfig->stripe->currency = 0;
        $pconfig->stripe->secret_key = "";
        $pconfig->stripe->publishable_key = "";
        $pconfig->save();

        $this->registerHook('displayPaymentMethods');

    }

    public function uninstall()
    {

        $pconfig = new Config('payments');
        $pconfig->delete('stripe');
        $pconfig->save();


        return parent::uninstall();
    }

    public function displayPaymentMethods(&$view)
    {
        $pconfig = new Config('payments');
        $id_bill = Router::getParam(0);

        $Bill = new Bill($id_bill);
        $view = $this->getModuleView('bill/pay.php');


        $view->id_bill = $id_bill;
        $dcurrency = new Currency($this->config->currency_default);
        $currency = new Currency($pconfig->stripe->currency);

        if (!$currency->isLoadedObject()) {
            $currency = $dcurrency;
        }


        $payment_amount = Currency::convert($Bill->total, $currency->id);
        $view->total = intval($payment_amount * 100);

        $view->stripe_currency = $currency->iso;

        $stripe = array(
            'secret_key' => $pconfig->stripe->secret_key,
            'publishable_key' => $pconfig->stripe->publishable_key
        );

        $view->stripe = $stripe;


    }

    public function actionSetting()
    {
        $pconfig = new Config('payments');
        $view = $this->getModuleView('setting.php', 'admin');
        $view->error = 0;

        if (Tools::rPOST()) {

            $pconfig->stripe->secret_key = Tools::rPOST('secret_key');
            $pconfig->stripe->publishable_key = Tools::rPOST('publishable_key');
            $pconfig->stripe->currency = Tools::rPOST('currency');

            $pconfig->save();
        }

        $view->pconfig = $pconfig;
        $view->currencies = Currency::factory()->getRows();
        return $view;
    }

}


