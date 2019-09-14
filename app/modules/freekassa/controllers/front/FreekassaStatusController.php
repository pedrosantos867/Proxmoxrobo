<?php
namespace modules\freekassa\controllers\front;


use front\ModuleFrontController;
use model\Bill;
use model\Client;
use model\Currency;
use modules\freekassa\classes\payment\FreeKassaAPI;
use System\Config;
use System\Notifier;

class FreekassaStatusController extends ModuleFrontController{
    protected $auth = false;
    public function actionStatus(){
        $pconfig = new Config('payments');
        $freekass =  new FreeKassaAPI(array(
            'shop_id'       => $pconfig->freekassa->shop_id,
            'secret_key'    => $pconfig->freekassa->secret_key,
            'secret_key2'    => $pconfig->freekassa->secret_key2
        ));

        $currency = new Currency($pconfig->freekassa->currency);
        if(!$currency->isLoadedObject()){
            $currency = Currency::getDefault();
        }

        $payment   = $freekass->getPayment();
        if ($payment->verified) {

            $bill = new Bill($payment->getId());
            if ($bill->isLoadedObject() &&  $currency->getPrice($bill->total) == $payment->getAmount()) {

                $bill->pay();

                $Client = new Client($bill->client_id);

                Notifier::PaidBill($Client, $bill);

                exit('YES');
            }
        }

        exit('ERROR');

    }
}