<?php
namespace modules\unitpay\controllers\front;

use front\ModuleFrontController;
use model\Bill;
use model\Client;
use model\Currency;
use modules\unitpay\classes\payment\UnitpayAPI;
use MongoDB\Driver\Exception\Exception;
use System\Config;
use System\Logger;
use System\Notifier;
use System\Tools;

class UnitpayStatusController extends ModuleFrontController{
    public $auth = 0;

    public function actionStatusOrder(){
        $pconfig = Config::factory('payments');

        $unitpay =  new UnitpayAPI(array(
            'public_key' => $pconfig->unitpay->public_key,
            'secret_key' => $pconfig->unitpay->secret_key,
        ));

        if(isset($_GET['method']) && $_GET['method'] == 'check'){
            exit(json_encode(['result' => ['message' => 'OK']]));
        }

        try {
            $payment = $unitpay->getPayment();

            if ($payment->verified) {
                
                $currency = new Currency($pconfig->unitpay->currency);
                if(!$currency->isLoadedObject()){
                    $currency = Currency::getDefault();
                }


                $bill = new Bill($payment->getId());


                if ($bill->isLoadedObject() && $currency->getPrice($bill->total) == $payment->getAmount()) {
                    $bill->pay();
                    $Client = new Client($bill->client_id);
                    Notifier::PaidBill($Client, $bill);


                    exit(json_encode(['result' => ['message' => 'OK']]));
                }
            }
            exit(json_encode(['error' => ['message' => 'Payment Error']]));
        } catch(Exception $e){
            exit(json_encode(['error' => ['message' => $e->getMessage()]]));
        }


    }


}