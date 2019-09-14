<?php
namespace modules\bepaid\controllers\front;


use front\ModuleFrontController;
use model\Bill;
use model\Client;
use model\Currency;
use modules\bepaid\classes\payment\BePaidAPI;

use System\Config;
use System\Notifier;

class BepaidStatusController extends ModuleFrontController{
    protected $auth = false;

    public function actionStatus(){

        $pconfig = new Config('payments');

        $bepaid =  new BePaidAPI(array(
            'shop_id'   => $pconfig->bepaid->shop_id,
            'key'       => $pconfig->bepaid->key
        ));

        $payment   = $bepaid->getPayment();
        if ($payment->verified) {

            $currency = new Currency($pconfig->bepaid->currency);
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