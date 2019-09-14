<?php
namespace modules\liqpay\controllers\front;


use front\ModuleFrontController;
use model\Bill;
use model\Client;
use modules\liqpay\classes\payment\LiqPayAPI;
use System\Config;
use System\Logger;
use System\Notifier;

class LiqPayStatusController extends ModuleFrontController{
    protected $auth = false;
    public function actionStatus(){
        $pconfig = new Config('payments');

        $liqpay =  new LiqPayAPI(array(
            'public_key'       => $pconfig->liqpay->public_key,
            'private_key'      => $pconfig->liqpay->private_key
        ));

        $payment   = $liqpay->getPayment();
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