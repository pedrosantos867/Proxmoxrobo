<?php
namespace modules\stripe\controllers\front;


use Exception;
use front\ModuleFrontController;
use model\Bill;
use model\Client;
use model\Currency;
use modules\stripe\classes\stripe\Charge;
use modules\stripe\classes\stripe\Error\Card;
use modules\stripe\classes\stripe\Stripe;
use System\Config;
use System\Notifier;
use System\Router;
use System\Tools;

class StripeController extends ModuleFrontController
{


    public function actionPay()
    {
        $view = $this->getView('bill/payment_status.php');
        $view->psystem = 'stripe';
        $id_bill = Router::getParam('id_bill');
        $bill = new Bill($id_bill);

        if (Tools::rPOST()) {
            $token = $_POST['stripeToken'];
            $amount = $_POST['total'];
            $stripe_currency = $_POST['stripe_currency'];
            $desc = $_POST['desc'];
            $pconfig = new Config('payments');

            Stripe::setApiKey($pconfig->stripe->secret_key);
            try {
                $charge = Charge::create(array(
                    'card' => $token,
                    'amount' => $amount,
                    'currency' => $stripe_currency,
                    'description' => $desc
                ));
                $dcurrency = new Currency($this->config->currency_default);
                $currency = new Currency($pconfig->stripe->currency);

                if (!$currency->isLoadedObject()) {
                    $currency = $dcurrency;
                }
                $payment_amount = Currency::convert($bill->total, $currency->id);
                if ($charge->status === "succeeded" && $charge->amount == intval($payment_amount * 100) && strtoupper($charge->currency) == $currency->iso) {
                    $bill->pay();
                    $view->success = 1;

                    if ($bill->type == Bill::TYPE_INC) {
                        $bills = array();
                        foreach (explode('|', $bill->inc) as $id_bill) {
                            $bills[] = $id_bill;
                        }
                        $view->bills = $bills;
                    }

                    Tools::reload();
                } else {
                    $view->success = 0;
                }

            } catch (Exception $e) {

                $view->success = 0;
            }

        } else {

            $view->success = $bill->is_paid == 1 ? 1 : 0;
        }


        $view->bill = $bill;

        $this->layout->import('content', $view);

    }


}