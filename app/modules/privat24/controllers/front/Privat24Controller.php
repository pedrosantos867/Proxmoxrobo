<?php
namespace modules\privat24\controllers\front;

use email\Email;
use front\ModuleFrontController;
use model\Bill;
use model\Client;
use System\Config;
use System\Notifier;
use System\Tools;

class Privat24Controller extends ModuleFrontController{

    public $auth = 0;
    // display default system page
    public function actionReturn(){

        $success = 0;
        $id_bill = null;

        if (Tools::rPOST('payment')) {
            $arr_payment = explode('&', Tools::rPOST('payment'));
            $res         = array();


            foreach ($arr_payment as $p) {
                $r          = explode('=', $p);
                $res[$r[0]] = $r[1];
            }
            $id_bill = $res['order'];
            if ($res['state'] == 'ok' || $res['state'] == 'test') {
                $success = 1;
            }
        }

        $view = $this->getView('bill/payment_status.php');


        $bill = new Bill($id_bill);

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
}