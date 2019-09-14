<?php
namespace modules\erip\controllers\front;

use email\Email;
use front\ModuleFrontController;
use model\Bill;
use model\Client;
use model\Currency;
use modules\erip\classes\payment\erip\EripAPI;
use System\Config;
use System\Notifier;
use System\Router;
use System\Tools;

class EripStatusController extends ModuleFrontController{

    protected $auth = false;

    public function actionStatus(){

        $pconfig = new Config('payments');
        $erip =  new EripAPI(array(
            'shop_id'       => $pconfig->erip->shop_id,
            'key'           => $pconfig->erip->key,
            'service_code'  => $pconfig->erip->service_code
        ));

        $payment   = $erip->getPayment();
        if ($payment->verified) {
            $currency = new Currency($pconfig->erip->currency);
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