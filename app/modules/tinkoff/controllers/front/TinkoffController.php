<?php

namespace modules\tinkoff\controllers\front;


use front\ModuleFrontController;
use model\Bill;
use model\Client;
use modules\tinkoff\classes\tinkoff\TinkoffAPI;
use System\Config;
use System\Notifier;
use System\Router;
use System\Tools;

class TinkoffController extends ModuleFrontController
{


    public function actionPay()
    {

        $id_bill = Router::getParam('id_bill');
        $Bill = new Bill($id_bill);

        $pconfig = new Config('payments');
        $tinkoff = new TinkoffAPI(array(
            'terminal_name' => $pconfig->tinkoff->terminal_name,
            'secret_key' => $pconfig->tinkoff->secret_key,
            'url' => $pconfig->tinkoff->url
        ));
        $Client = new Client($Bill->client_id);

        $result = $tinkoff->createPayment(array(
            'id' => $Bill->id,
            'amount' => $Bill->total,
            'description' => 'Оплата услуг компании',
            'email' => $Client->email
        ));
        $result = json_decode($result);
        if (isset($result->Status) && $result->Status == 'NEW') {
            Tools::redirect($result->PaymentURL);
        } else {
            $view = $this->getView('bill/payment_status.php');

            $view->psystem = 'tinkoff';

            $view->bill = null;
            $view->success = 0;

            $this->layout->import('content', $view);
        }
    }

    public function actionSuccess()
    {
        $pconfig = new Config('payments');

        $tinkoff = new TinkoffAPI(array(
            'terminal_name' => $pconfig->tinkoff->terminal_name,
            'secret_key' => $pconfig->tinkoff->secret_key,
            'url' => $pconfig->tinkoff->url
        ));
        $params = array(
            'PaymentId' => $_GET["PaymentId"],
        );
        $payment = json_decode($tinkoff->getState($params));
        if (isset($payment->Status) && $payment->Status == 'CONFIRMED' && isset($payment->OrderId)) {
            $bill = new Bill($payment->OrderId);
            if (isset($payment->Success) && $payment->Success && isset($payment->ErrorCode) && $payment->ErrorCode == 0) {
                if (isset($payment->TerminalKey) && $payment->TerminalKey == $pconfig->tinkoff->terminal_name) {
                    $payment->verified = true;
                    $bill->pay();
                    $Client = new Client($bill->client_id);
                    Notifier::PaidBill($Client, $bill);
                    $view = $this->getView('bill/payment_status.php');
                    $view->success = 1;
                    $view->bill = $bill;

                    $this->layout->import('content', $view);
                }
            }

        }
    }

    public function actionCancel()
    {
        $bill = new Bill($_GET["OrderId"]);
        $bill->is_paid = -1;
        $bill->save();
        $view = $this->getView('bill/payment_status.php');

        $view->psystem = 'tinkoff';

        $view->bill = null;
        $view->success = 0;

        $this->layout->import('content', $view);
    }

}