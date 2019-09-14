<?php
namespace modules\bepaid\controllers\front;

use front\ModuleFrontController;
use model\Bill;

use System\Tools;

class BepaidController extends ModuleFrontController{

    public $auth = 0;
    // display default system page
    public function actionResult(){

        $success = 0;

        if(Tools::rGET('status') == 'successful'){
            $success=1;
        }

        $view = $this->getView('bill/payment_status.php');




        $view->success = $success;


        $view->bill = null;

        $this->layout->import('content', $view);

    }
}