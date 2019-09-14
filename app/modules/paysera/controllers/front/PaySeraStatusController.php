<?php

namespace modules\paysera\controllers\front;


use front\ModuleFrontController;
use model\Bill;
use model\Client;
use model\Currency;
use modules\paysera\classes\paysera\PaySeraAPI;
use System\Config;
use System\Notifier;

class PaySeraStatusController extends ModuleFrontController{
    protected $auth = false;
    public function actionStatus(){
        $pconfig = new Config('payments');

        $paysera =  new PaySeraAPI(array(
            'projectid'       => $pconfig->paysera->projectid,
            'sign_password'      => $pconfig->paysera->sign_password
        ));

        $payment   = $paysera->getPayment();
        if ($payment->verified) {

            $currency = new Currency($pconfig->paysera->currency);
            if(!$currency->isLoadedObject()){
                $currency = Currency::getDefault();
            }

            $bill = new Bill($payment->getId());
            if ($bill->isLoadedObject() && $currency->getPrice($bill->total) == $payment->getAmount() && strtoupper($payment->getCurrency()) == strtoupper($currency->iso)) {
                $bill->pay();
                $Client = new Client($bill->client_id);
                Notifier::PaidBill($Client, $bill);

                exit('OK' . $payment->getId());
            }
        }

        exit('ERROR');

    }
}