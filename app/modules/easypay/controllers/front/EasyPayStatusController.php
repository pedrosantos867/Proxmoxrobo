<?php
namespace modules\easypay\controllers\front;


use front\ModuleFrontController;
use model\Bill;
use model\Client;
use modules\easypay\classes\payment\EasyPayAPI;
use System\Config;
use System\Notifier;

class EasyPayStatusController extends ModuleFrontController{
    protected $auth = false;
    public function actionPay(){
        $pconfig = new Config('payments');
        $easyPay =  new EasyPayAPI(array(
            'shop_id'       => $pconfig->easypay->merchant_id,
            'secret_key'    => $pconfig->easypay->secret_key,
        ));

        $payment   = $easyPay->getPayment();
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