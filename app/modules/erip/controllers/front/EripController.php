<?php
namespace modules\erip\controllers\front;

use front\ModuleFrontController;
use model\Bill;
use modules\erip\classes\payment\erip\EripAPI;
use System\Config;
use System\Router;
use System\Tools;

class EripController extends ModuleFrontController{

    public $auth = 0;

    public function actionPay(){

        $pconfig = new Config('payments');

        $id_bill = Router::getParam('id_bill');
        $Bill = new Bill($id_bill);

        if(!$Bill->id || $Bill->is_paid != 0){
            Tools::display404Error();
        }

        $view = $this->getModuleView('status.php');
        $view->bill = $Bill;
          $erip = new EripAPI(array(
                'shop_id'       => $pconfig->erip->shop_id,
                'key'           => $pconfig->erip->key,
                'service_code'  => $pconfig->erip->service_code
            ));

            $payment       = $erip->createPayment(array(
                'id'          => $Bill->id,
                'amount'      => $Bill->total,
                'description' => 'ОПЛАТА УСЛУГ ХОСТИНГ КОМПАНИИ',
                'currency'    => $this->dcurrency->iso,
                'status_url' => Tools::link('modules/erip/status')
            ));
            $result = $payment->sendPaymentRequest(array(
                'email' => $this->client->email
            ));

        $status = 'error';
        $view->instruction = '';
        if(isset($result['transaction'])) {
            $status = $result['transaction']['payment']['status'];
            $view->instruction = $result['transaction']['erip']['instruction'][0];
        }

        $view->status = $status;
        //erip end



        $this->layout->import('content', $view);

    }

}