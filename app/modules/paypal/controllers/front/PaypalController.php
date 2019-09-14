<?php
namespace modules\paypal\controllers\front;


use front\ModuleFrontController;
use model\Bill;
use model\Client;
use model\Currency;
use modules\paypal\classes\payment\PayPalAPI;
use System\Config;
use System\Notifier;
use System\Router;
use System\Tools;

class PaypalController extends ModuleFrontController
{
    public $auth = 0;
    private $payPal;
    public function __construct()
    {
        parent::__construct();

        $pconfig= Config::factory('payments');
        $this->payPal = new PayPalAPI(array(
            'client_id' => $pconfig->paypal->client_id,
            'secret'    => $pconfig->paypal->secret,
            'test_mode' => $pconfig->paypal->test_mode
        ));
    }

    public function actionPay(){
        $id_bill = Router::getParam('id_bill');


        $Bill = new Bill($id_bill);

        $dcurrency = new Currency($this->config->currency_default);


        $pconfig= Config::factory('payments');

        $this->payPal->createPayment(array(
            'id'          => $Bill->id,
            'amount' => $Bill->total + $Bill->total * ($pconfig->paypal->percent / 100),
            'description' => 'Оплата услуг компании',
            'status_url'    => Tools::link('modules/paypal/return'),
            'fail_url'  => Tools::link('modules/paypal/cancel')
        ));


    }

    public function actionReturn(){
        $paymentId = Tools::rGET('paymentId');
        $PayerID = Tools::rGET('PayerID');
        $token = Tools::rGET('token');
        $pconfig= Config::factory('payments');

        $payment = $this->payPal->approvePayment(array(
            'paymentId' => $paymentId,
            'PayerID'   => $PayerID,
            'token'     => $token)
        );
        $bill = null;
        $success= false;

        if($payment->verified){
            $bill = new Bill($payment->getId());

            if ($bill->isLoadedObject() && $payment::convertAmountAsString($bill->total + $bill->total * ($pconfig->paypal->percent / 100)) == $payment->getAmount()) {
                $bill->pay();
                $Client = new Client($bill->client_id);
                Notifier::PaidBill($Client, $bill);

                $success = true;
            }
        }

        $view = $this->getView('bill/payment_status.php');

        $view->psystem  = 'paypal';


        $view->success = $success;

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

    public function actionCancel(){
        $view = $this->getView('bill/payment_status.php');

        $view->psystem  = 'paypal';


        $view->success = 0;

        $this->layout->import('content', $view);
    }
}