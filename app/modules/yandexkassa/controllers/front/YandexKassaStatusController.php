<?php
namespace modules\yandexkassa\controllers\front;

use front\ModuleFrontController;
use model\Bill;
use model\Client;
use modules\yandexkassa\classes\payment\YandexKassaAPI;
use System\Config;
use System\Logger;
use System\Notifier;
use System\Tools;

class YandexKassaStatusController extends ModuleFrontController{
    public $auth = 0;

    public function actionCheckOrder(){

        if(Tools::rRequest('action') == 'checkOrder') {

            Logger::log('YandexKassa checkOrder:'.json_encode($_REQUEST));
            $pconfig = Config::factory('payments');

            $YandexKassa = new YandexKassaAPI(array(
                'shopId'            => $pconfig->yandex->shopId,
                'scid'              => $pconfig->yandex->scid,
                'password'          => $pconfig->yandex->password
            ));
            $payment = $YandexKassa->getPayment();

            if ($payment->verified) {
                echo '<checkOrderResponse performedDatetime="' . $_REQUEST['requestDatetime'] . '" 
                    code="0" 
                    invoiceId="' . $_REQUEST['invoiceId'] . '" shopId="' . $_REQUEST['shopId'] . '"/>';
            } else {
                echo '<paymentAvisoResponse performedDatetime="' . $_REQUEST['requestDatetime'] . '" code="1" message="Значение параметра md5 не совпадает с результатом расчета хэш-функции"/>';

            }
            exit();

        }else if(Tools::rRequest('action') == 'paymentAviso') {
            Logger::log('YandexKassa actionStatus:'.json_encode($_REQUEST));

            $id_bill = Tools::rRequest('orderNumber');
            $pconfig = Config::factory('payments');

            $YandexKassa = new YandexKassaAPI(array(
                'shopId'            => $pconfig->yandex->shopId,
                'scid'              => $pconfig->yandex->scid,
                'password'          => $pconfig->yandex->password
            ));
            $payment = $YandexKassa->getPayment();

            if ($payment->verified) {

                $bill = new Bill($payment->getId());
                if ($bill->isLoadedObject() && $bill->total == $payment->getAmount()) {
                    $bill->pay();
                    $Client = new Client($bill->client_id);
                    Notifier::PaidBill($Client, $bill);

                    exit('<paymentAvisoResponse performedDatetime="'.Tools::rRequest('requestDatetime').'" code="0" invoiceId="'.Tools::rRequest('invoiceId').'" shopId="'.Tools::rRequest('shopId').'"/>');
                }
            }

            exit('<paymentAvisoResponse performedDatetime="'.Tools::rRequest('requestDatetime').'" code="1" message="Значение параметра md5 не совпадает с результатом расчета хэш-функции"/>');

        }

            exit();
    }


}