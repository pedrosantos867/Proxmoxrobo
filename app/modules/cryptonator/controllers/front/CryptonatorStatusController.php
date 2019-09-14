<?php
namespace modules\cryptonator\controllers\front;


use front\ModuleFrontController;
use model\Bill;
use model\Client;
use modules\cryptonator\classes\payment\CryptonatorAPI;
use System\Config;
use System\Logger;
use System\Notifier;

class CryptonatorStatusController extends ModuleFrontController{
    protected $auth = false;
    public function actionStatus(){
        $pconfig = new Config('payments');

        $cryptonator =  new CryptonatorAPI(array(
            'item_name'                 => $pconfig->cryptonator->item_name,
            'merchant_id'               => $pconfig->cryptonator->merchant_id,
			'secret'                    => $pconfig->cryptonator->secret
        ));

        $payment   = $cryptonator->getPayment();
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