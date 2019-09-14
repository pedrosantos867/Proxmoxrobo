<?php

namespace modules\robokassa\controllers\front;


use front\ModuleFrontController;
use model\Bill;
use model\Client;
use model\Currency;
use modules\robokassa\classes\payment\RobokassaAPI;
use System\Config;
use System\Notifier;

class RobokassaStatusController extends ModuleFrontController{
    protected $auth = false;
    public function actionStatus(){
        $pconfig = new Config('payments');

        $robokassa =  new RobokassaAPI(array(
            'merchant'  => $pconfig->robokassa->merchant,
            'password1' => $pconfig->robokassa->password1,
            'password2' => $pconfig->robokassa->password2
        ));

        $payment   = $robokassa->getPayment();
        if ($payment->verified) {

            $currency = new Currency($pconfig->robokassa->currency);
            if(!$currency->isLoadedObject()){
                $currency = Currency::getDefault();
            }

            $bill = new Bill($payment->getId());
            if ($bill->isLoadedObject() && $currency->getPrice($bill->total) == $payment->getAmount()) {
                $bill->pay();
                $Client = new Client($bill->client_id);
               Notifier::PaidBill($Client, $bill);

                exit('OK' . $payment->getId());
            }
        }

        exit('ERROR');

    }
}