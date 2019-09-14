<?php
namespace modules\easypay\controllers\front;

use front\ModuleFrontController;
use model\Bill;
use System\Tools;

class EasyPayController extends ModuleFrontController{
    public $auth = 0;
    // display default system page
    public function actionDisplayStatus(){
        $id_bill = Tools::rRequest('MERCHANT_ORDER_ID');
        $success = 0;


        if(Tools::rGET('success') == 1){
            $success = 1;
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