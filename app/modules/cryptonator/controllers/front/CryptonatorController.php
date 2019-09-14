<?php
namespace modules\cryptonator\controllers\front;

use email\Email;
use front\ModuleFrontController;
use model\Bill;
use model\Client;
use System\Config;
use System\Notifier;
use System\Tools;

class CryptonatorController extends ModuleFrontController{
    public $auth = 0;
    // display default system page
    public function actionResult(){

        $success = 0;


        $bill= new Bill(Tools::rGET('bill'));
        if($bill->is_paid ==1){
            $success = 1;
        }

        if(Tools::rGET('show_result')) {
            $view = $this->getView('bill/payment_status.php');
        }else {
            $view = $this->getModuleView('bill/payment_status.php');
        }



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

        $view->psystem  = 'cryptonator';

        $view->bill = null;
        $view->success = 0;

        $this->layout->import('content', $view);
    }
}