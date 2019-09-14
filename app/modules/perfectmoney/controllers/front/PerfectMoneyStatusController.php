<?php
namespace modules\perfectmoney\controllers\front;


use front\ModuleFrontController;
use model\Bill;
use model\Client;
use modules\perfectmoney\classes\payment\PerfectMoneyAPI;
use System\Config;
use System\Logger;
use System\Notifier;

class PerfectMoneyStatusController extends ModuleFrontController{
    protected $auth = false;
    public function actionStatus(){
        $pconfig = new Config('payments');

        $perfectmoney =  new PerfectMoneyAPI(array(
            'payee_account'             => $pconfig->perfectmoney->payee_account,
            'payee_name'                => $pconfig->perfectmoney->payee_name,
			'alternate_passphrase'      => $pconfig->perfectmoney->alternate_passphrase
        ));

        $payment   = $perfectmoney->getPayment();
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