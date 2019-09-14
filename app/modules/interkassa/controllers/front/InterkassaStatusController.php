<?php

namespace modules\interkassa\controllers\front;


use front\ModuleFrontController;
use model\Bill;
use model\Client;
use model\Currency;
use modules\interkassa\classes\payment\InterkassaAPI;
use System\Config;
use System\Notifier;

class InterkassaStatusController extends ModuleFrontController{
    protected $auth = false;
    public function actionStatus(){
        $pconfig = new Config('payments');

        $ik = new InterkassaAPI(array(
            'id'         => $pconfig->interkassa->id,
            'secret_key' => $pconfig->interkassa->test_mode ? $pconfig->interkassa->test_secret_key : $pconfig->interkassa->secret_key
        ));



        if (count($_POST)) {

            $payment = $ik->getPayment();
            if ($payment->verified) {

                $currency = new Currency($pconfig->interkassa->currency);
                if(!$currency->isLoadedObject()){
                    $currency = Currency::getDefault();
                }

                $bill = new Bill($payment->getId());
                if ($bill->isLoadedObject() && $currency->getPrice($bill->total) == $payment->getAmount()) {
                    if ($bill->pay()) {
                        $Client = new Client($bill->client_id);

                        Notifier::PaidBill($Client, $bill);

                        exit('OK');
                    }
                }
            }
            exit('ERROR 1');
        }
        exit('ERROR 2');

    }
}