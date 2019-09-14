<?php
namespace modules\billmanagervps\controllers\admin;

use admin\ModuleFrontController;
use model\Client;
use model\Module;
use modules\billmanagervps\classes\BillManagerAPI;
use modules\billmanagervps\classes\model\Order;
use modules\billmanagervps\classes\model\Plan;
use System\Config;
use System\Tools;

class OrdersController extends ModuleFrontController  {
    public function actionListAjax(){
        $orderObject = new Order();
        $view = $this->getModuleView('order/list.php');

        $this->layout->import('content', $view);

        $orderObject
            ->select('*')
            ->select(Plan::factory(), 'name')
            ->select(Client::factory(), 'name', 'client')
            ->join(Client::factory(), 'client_id', 'id')
            ->join(Plan::factory(), 'plan_id', 'id')
            ->limit($this->from, $this->count);


        $filter  = Tools::rPOST('filter');
        $vfilter = array();



        if (isset($filter) && $filter != '') {

        unset($filter['name']);


            foreach ($filter as $field => $option) {

                $value = $option['value'];
                $type  = isset($option['type']) ? $option['type'] : 'like';
                $vfilter[$field] = $value;

                if($field == 'client'){
                    $orderObject->where(Client::factory(), 'name', 'LIKE', '%' . $value . '%');

                    continue;
                }

                if ($field && $value != '') {
                    if ($type == 'like') {
                        $orderObject->where($field, 'LIKE', '%' . $value . '%');
                    } else if ($type == 'equal') {
                        $orderObject->where($field, $value);
                    }
                }
            }
        }

        $order = Tools::rPOST('order');

        if ($order['field']) {
            $orderObject->order($order['field'], $order['type']);
        } else {
            $orderObject->order('id', 'desc');
        }

        $view->filter = $vfilter;


        $view->plans = Plan::factory()->getRows();

        $view->orders = $orderObject->getRows();
        $this->pagination($orderObject->lastQuery()->getRowsCount());
    }

    public function actionRemoveAjax(){
        $orderObject = new Order(Tools::rGET('id_order'));
        if($orderObject->isLoadedObject()){

            $config = new Config('billmanagervps.module');
            $billManager = new BillManagerAPI($config->url, $config->username, $config->password);

            $billManager->removeVds($orderObject->order_id);

            if($orderObject->remove()){
                $this->returnAjaxAnswer(1, 'Успешно удалено');
            }

        }
        $this->returnAjaxAnswer(0);
    }

    public function actionInfoAjax(){
        $view = $this->getModuleView('order/info.php');


        $orderObject = new Order(Tools::rGET('id_order'));



        if(!$orderObject->username) {
            $config = new Config('billmanagervps.module');
            $billManager = new BillManagerAPI($config->url, $config->username, $config->password);

            $orderInfo = $billManager->getVdsInfo($orderObject->order_id);
            if(!empty($orderInfo)) {
                $orderObject->ip = $orderInfo['ip'];
                $orderObject->username = $orderInfo['username'];
                $orderObject->userpassword = $orderInfo['userpassword'];
                $orderObject->password = $orderInfo['password'];

                $orderObject->save();
            }
        }

        $view->order = $orderObject;
        $view->plan = new Plan($orderObject->plan_id);

        $this->carcase->import('content', $view);
    }
}