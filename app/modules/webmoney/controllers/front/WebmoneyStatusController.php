<?php
namespace modules\webmoney\controllers\front;

use front\ModuleFrontController;
use model\Bill;
use model\Client;
use modules\webmoney\classes\payment\WebMoneyAPI;
use System\Config;
use System\Notifier;


class WebmoneyStatusController extends ModuleFrontController{
    public $auth = 0;

    public function actionStatus(){

        $pconfig = Config::factory('payments');
        $webmoney =  new WebMoneyAPI(array(
            'purse'            => $pconfig->webmoney->purse,
            'secret_key'       => $pconfig->webmoney->secret_key,
            'secret_keyx20'    => $pconfig->webmoney->secret_keyx20
        ));

        $payment   = $webmoney->getPayment();
        if ($payment->verified) {

            $bill = new Bill($payment->getId());
            if ($bill->isLoadedObject() && $bill->total == $payment->getAmount()) {
                $bill->pay();
                $Client = new Client($bill->client_id);
                Notifier::PaidBill($Client, $bill);

                exit('OK' . $payment->getId());
            }
        }

        exit('ERROR');
    }


}