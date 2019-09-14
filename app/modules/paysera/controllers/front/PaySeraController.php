<?php
namespace modules\paysera\controllers\front;


use front\ModuleFrontController;
use model\Bill;
use model\Client;
use model\Currency;
use modules\paysera\classes\paysera\PaySeraAPI;
use System\Config;
use System\Notifier;
use System\Router;
use System\Tools;

class PaySeraController extends ModuleFrontController
{
    public $auth = 0;

    public function actionAccept(){


        $success = 0;
        $params = array();
        $id_bill = 0;
        if(Tools::rGET('data')) {
            parse_str(base64_decode(strtr($_GET['data'], array('-' => '+', '_' => '/'))), $params);
            $id_bill = $params['orderid'];

        }

        $bill= new Bill($id_bill);
        if($params['status'] == 1){
            $success = 1;
        }

        $view = $this->getView('bill/payment_status.php');




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

        $view->psystem  = 'paysera';

        $view->bill = null;
        $view->success = 0;

        $this->layout->import('content', $view);
    }
}