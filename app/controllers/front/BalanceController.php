<?php
namespace front;

use model\Bill;
use model\Ticket;
use System\Notifier;
use System\Router;
use System\Tools;
use System\View\View;

class BalanceController extends FrontController
{
    public function actionIndex()
    {

        $view = $this->getView('balance.php');

        $this->layout->import('content', $view);
    }

    public function actionCreateBill()
    {

        $hb       = new Bill();
        $hb->type = Bill::TYPE_BALANCE;
        $hb->client_id = $this->client->id;
        $hb->price = $this->currency->convertToDefault(Router::getParam('summ'));
        $hb->pay_period = 0;
        $hb->total = $this->currency->convertToDefault(Router::getParam('summ'));
        $hb->date       = date('Y-m-d');

        if ($hb->save()) {
            Notifier::NewBill($this->client, $hb);
            Tools::redirect('/bill/' . $hb->id);
        }
    }

}