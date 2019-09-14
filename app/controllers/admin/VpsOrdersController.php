<?php

namespace admin;

use model\Client;
use model\VpsOrder;
use model\VpsPlan;
use model\VpsServer;
use System\Tools;
use vps\VPSAPI;

class VpsOrdersController extends FrontController{

    public function actionListAjax()
    {
        $view = $this->getView('vps/order/list.php');
        $VpsOrder           = new VpsOrder();
        $VpsOrder->select('*')

            //add clients table
            ->select(Client::getInstance(), 'name', 'user_name')
            ->join(Client::getInstance(), 'client_id', 'id')

            //add plan table
            ->select(VpsPlan::getInstance(), 'name')
            ->select(VpsPlan::getInstance(), 'price')
            ->join(VpsPlan::getInstance(), 'plan_id', 'id')


            ->select(VpsServer::getInstance(), 'name', 'server_name')
            ->join(VpsServer::getInstance(), 'server_id', 'id')

            ->limit($this->from, $this->count);


        $filter  = Tools::rPOST('filter');
        $vfilter = array();
        if (isset($filter) && $filter != '') {
            foreach ($filter as $field => $option) {

                $value = Tools::clearXSS($option['value']);
                $type  = isset($option['type']) ? $option['type'] : 'like';

                $vfilter[$field] = $value;

                if ($field && $value != '') {

                    if ($type == 'like') {
                        if ($field == 'name') {
                            $VpsOrder->where(Client::getInstance(), $field, 'LIKE', '%' . $value . '%');
                        } else {
                            $VpsOrder->where($field, 'LIKE', '%' . $value . '%');
                        }
                    } else if ($type == 'equal') {

                        $VpsOrder->where($field, $value);

                    }


                }
            }
        }
        $view->filter = $vfilter;
        $order        = Tools::rPOST('order');

        if ($order['field']) {
            if ($order['field'] == 'name') {
                $VpsOrder->order(Client::getInstance(), $order['field'], $order['type']);
            } else {
                $VpsOrder->order($order['field'], $order['type']);
            }
        } else {
            $VpsOrder->order('id', 'desc');
        }

        $view->orders = $VpsOrder->getRows();


        $this->pagination($VpsOrder->lastQuery()->getRowsCount());

        $this->layout->import('content', $view);
    }

    public function actionRemoveAjax(){
        $id_order = Tools::rGET('id_order');

        $vpsOrderObject = new VpsOrder($id_order);

        if ($vpsOrderObject->isLoadedObject()) {


            $vpsOrderObject->remove();

            $this->returnAjaxAnswer(1, 'Заказ успешно удален');
        }
        $this->returnAjaxAnswer(0, 'Возникла ошибка удаления');
    }
}