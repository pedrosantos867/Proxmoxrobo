<?php
namespace modules\robokassa\controllers\front;

use front\ModuleFrontController;
use model\Bill;
use System\Tools;

class RobokassaController extends ModuleFrontController{
    public $auth = 0;
    // display default system page
    public function actionReturn(){

        $success = 0;
        $id_bill = null;



        if (Tools::rPOST('SignatureValue')) {
            $success = 1;
            $id_bill = Tools::rPOST('InvId');
        } else {
            $success = 0;
            $id_bill = Tools::rPOST('InvId', null);
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