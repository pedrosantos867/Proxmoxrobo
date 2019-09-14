<?php
namespace front;

use model\Bill;
use System\Module;
use System\Router;
use System\Tools;

class BillController extends FrontController
{
    public $auth = false;

    public function actionPay()
    {


        $bill_id = Router::getParam(0);
        $bill = new Bill($bill_id);

        if (!$bill->isLoadedObject()) {
            Tools::redirect('/');
        }

        $view = $this->getView('bill/pay_no_auth.php');
        $view->error = '';


        $total = 0;
        if ($bill->type == Bill::TYPE_INC) {
            $bills = explode('|', $bill->inc);
            $error = 0;
            foreach ($bills as $id_bill) {
                $hb = new Bill($id_bill);
                if ($hb->is_paid != 0) {
                    $error = 1;
                    break;
                }
                $total += $hb->total;
            }

            if ($error) {
                $bill->is_paid = -1;
                $bill->save();
            }

            $view->bills = $bills;
            $bill->total = $total;
        }


        if ($bill->total == 0) {
            $bill->pay();
            Tools::redirect('/');
        }

        if ($bill->is_paid != 0) {
            $view->error = 'bill_is_paid';
        }


        $view->bill = $bill;


        $mview = &$view;
        Module::execHook('displayPaymentMethods', $mview);

        $this->carcase->import('content', $view);


    }
}