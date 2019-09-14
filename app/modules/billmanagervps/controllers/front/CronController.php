<?php

namespace modules\billmanagervps\controllers\front;

use front\ModuleFrontController;
use email\Email;
use model\Client;
use modules\billmanagervps\classes\BillManagerAPI;
use modules\billmanagervps\classes\model\Order;
use modules\billmanagervps\classes\model\Plan;
use System\Config;

class CronController extends ModuleFrontController {

    public $auth = false;
    public function actionCron()
    {

        $config = new Config('billmanagervps.module');
        $billManager = new BillManagerAPI($config->url, $config->username, $config->password);

        $orderObject = new Order();
        $orders = $orderObject->where('notified', 0)->getRows();

        foreach ($orders as $order) {
            $orderObject = new Order($order);
            $planObject = new Plan($orderObject->plan_id);
            $orderInfo = $billManager->getVdsInfo($orderObject->order_id);

            if (!empty($orderInfo)) {
                $orderObject->ip = $orderInfo['ip'];
                $orderObject->username = $orderInfo['username'];
                $orderObject->userpassword = $orderInfo['userpassword'];
                $orderObject->password = $orderInfo['password'];
                $orderObject->save();

                $clientObject = new Client($orderObject->client_id);

                $email = new Email();
                $email->to = $clientObject->email;
                $eview = $this->getModuleView('order/new.php', 'email');
                $eview->order = $orderObject;
                $eview->plan = $planObject;
                $email->msg = $eview->fetch();
                $email->send();

                $orderObject->notified = 1;
                $orderObject->save();
            }


        }

        exit();
    }
}