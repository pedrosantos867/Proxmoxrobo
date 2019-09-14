<?php
namespace modules\unitpay\controllers\front;

use front\ModuleFrontController;
use model\Bill;
use model\Client;
use model\Currency;
use modules\unitpay\classes\payment\UnitpayAPI;
use MongoDB\Driver\Exception\Exception;
use System\Config;
use System\Logger;
use System\Notifier;
use System\Router;
use System\Tools;

class UnitpayController extends ModuleFrontController{

    public $auth = 0;
   public function actionResult(){
       $success = Router::getParam('status');
       $id_bill = null;




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