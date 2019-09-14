<?php

namespace modules\tinkoff;

use model\Currency;
use stdClass;
use System\Config;
use System\Module;
use System\Router;
use System\Tools;

class tinkoff extends Module
{
    public $name = 'Платежная система tinkoff.ru';
    public $author = '<a target="_blank" href="http://hopebilling.com">hopebilling.com</a>';
    public $category = 3; // for payments systems


    public function install()
    {

        $pconfig = new Config('payments');
        $pconfig->tinkoff = new stdClass();
        $pconfig->tinkoff->terminal_name = '';
        $pconfig->tinkoff->secret_key = '';
        $pconfig->tinkoff->url = '';
        $pconfig->save();

        $this->registerHook('displayPaymentMethods');

    }

    public function uninstall()
    {

        $pconfig = new Config('payments');
        $pconfig->delete('tinkoff');
        $pconfig->save();

        return parent::uninstall();
    }

    public function displayPaymentMethods(&$view)
    {
        $id_bill = Router::getParam(0);
        $view = $this->getModuleView('bill/pay.php');
        $view->id_bill = $id_bill;
    }

    public function actionSetting()
    {
        $pconfig = new Config('payments');
        $view = $this->getModuleView('setting.php', 'admin');
        $view->error = 0;

        if (Tools::rPOST()) {

            $pconfig->tinkoff->terminal_name = Tools::rPOST('terminal_name');
            $pconfig->tinkoff->secret_key = Tools::rPOST('secret_key');
            $pconfig->tinkoff->url = Tools::rPOST('url');

            $pconfig->save();
        }

        $view->pconfig = $pconfig;
        $view->currencies = Currency::factory()->getRows();
        return $view;
    }

}


