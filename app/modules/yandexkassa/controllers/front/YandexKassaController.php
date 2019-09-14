<?php
namespace modules\yandexkassa\controllers\front;

use front\ModuleFrontController;
use model\Bill;

use System\Tools;

class YandexKassaController extends ModuleFrontController{
    public $auth = 0;

    public function actionStatusOrder(){
        $id_bill = Tools::rRequest('orderNumber');
        $success = 0;


        if(Tools::rRequest('action') == 'PaymentSuccess'){
            $success = 1;
        }

        $view = $this->getView('bill/payment_status.php');
        $view->psystem = 'yandexmoney';


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