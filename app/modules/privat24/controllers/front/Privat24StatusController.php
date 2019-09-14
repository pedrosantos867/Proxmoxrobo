<?php

namespace modules\privat24\controllers\front;


use front\ModuleFrontController;
use model\Bill;
use model\Client;
use model\Currency;
use modules\privat24\classes\payment\Privat24API;
use System\Config;
use System\Logger;
use System\Notifier;

class Privat24StatusController extends ModuleFrontController{
    protected $auth = 0
    ;
    public function actionStatus(){
        $pconfig = new Config('payments');

        $p24     = new Privat24API(array(
            'merchant'   => $pconfig->privat24->id,
            'secret_key' => $pconfig->privat24->secret_key
        ));

        $payment = $p24->getPayment();
        if ($payment->verified) {
            $bill = new Bill($payment->getId());

            $currency = new Currency($pconfig->privat24->currency);
            if(!$currency->isLoadedObject()){
                $currency = Currency::getDefault();
            }
            if ($bill->isLoadedObject() && $currency->getPrice($bill->total) == $payment->getAmount()) {

                $bill->pay();
                $Client = new Client($bill->client_id);
                Notifier::PaidBill($Client, $bill);

                exit('OK');
            }
        }

        exit('ERROR');

    }
}