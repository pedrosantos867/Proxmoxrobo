<?php
namespace modules\payture\controllers\front;


use front\ModuleFrontController;
use model\Bill;
use model\Client;
use model\Currency;
use modules\payture\classes\payture\PaytureAPI;
use System\Config;
use System\Notifier;
use System\Router;
use System\Tools;

class PaytureController extends ModuleFrontController
{
    public $auth = 0;

    public function actionReturn()
    {
        $view = $this->getView('bill/payment_status.php');

        $view->psystem = 'payture';

        $success = Tools::rGET('success');
        if ($success === 'True') {
            $view->success = 1;
        } else {
            $view->success = 0;
        }
        $bill = new Bill(Tools::rGET('id_bill'));
        if ($view->success == 1) {
            $pconfig = new Config('payments');
            $payture = new PaytureAPI(array(
                'key' => $pconfig->payture->key,
                'host_serv' => (stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://') . $_SERVER["HTTP_HOST"],
                'host' => ($pconfig->payture->test == 1) ? 'sandbox2' : 'secure'
            ));
            $payture->payStatus($bill->id);
            if (!$payture->verified) {
                $view->success = 0;
            } else {
                $bill->pay();
            }
        }


        if ($bill->type == Bill::TYPE_INC) {
            $bills = array();
            foreach (explode('|', $bill->inc) as $id_bill) {
                $bills[] = $id_bill;
            }
            $view->bills = $bills;
        }


        $view->bill = $bill;

        $this->layout->import('content', $view);

    }

    public function actionPay()
    {

        $id_bill = Router::getParam('id_bill');
        $Bill = new Bill($id_bill);

        $pconfig = new Config('payments');
        $payture = new PaytureAPI(array(
            'key' => $pconfig->payture->key,
            'password' => $pconfig->payture->password,
            'host_serv' => (stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://') . $_SERVER["HTTP_HOST"],
            'host' => ($pconfig->payture->test == 1) ? 'sandbox2' : 'secure'
        ));
        $currency = new Currency($pconfig->payture->currency);
        $result = $payture->createPayment(array(
            'id' => $Bill->id,
            'amount' => $currency->getPrice($Bill->total),
            'description' => '"Оплата услуг компании"'

        ));
        if (isset($result['code'])) {
            Tools::redirect('modules/payture/return?success=0&&id_bill=' . $id_bill);
        } else {
            Tools::redirect($result);
        }


    }


}