<?php
namespace modules\interkassa\controllers\front;

use email\Email;
use front\ModuleFrontController;
use model\Bill;
use model\Client;
use modules\FreeKassa\classes\payment\FreeKassaAPI;
use System\Config;
use System\Notifier;
use System\Tools;

class InterkassaController extends ModuleFrontController{
    public $auth = 0;
    // display default system page
    public function actionReturn(){

        $success = 0;
        $id_bill = null;

        $id_bill = Tools::rPOST('ik_pm_no');
        if (Tools::rPOST('ik_inv_st') == 'success') {
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