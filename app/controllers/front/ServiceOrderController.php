<?php

namespace front;

use email\Email;
use model\Bill;
use model\Client;
use model\Promocode;
use model\Service;
use model\ServiceField;
use model\ServiceFieldValue;
use model\ServiceOrder;
use sms\SMS;
use System\Notifier;
use System\Router;
use System\Tools;
use System\View\View;

class ServiceOrderController extends FrontController {

    public function actionListAjax(){

        $this->layout->import('content', $v = $this->getView('service/order/list.php'));


        $ServiceOrder =  new ServiceOrder();
        $ServiceOrder->select('*')
            ->select(Service::factory(), 'name')
            ->join(Service::factory(), 'service_id', 'id')
            ->order('id', 'desc')
            ->where('client_id', $this->client->id)
            ->where(Service::factory(), 'category_id', Router::getParam('category_id'))
        ;


        $filter  = Tools::rPOST('filter');
        $vfilter = array();

        if (isset($filter) && $filter != '') {
            foreach ($filter as $field => $option) {

                $value = Tools::clearXSS($option['value']);
                $type  = isset($option['type']) ? $option['type'] : 'like';

                $vfilter[$field] = $value;

                if ($field == 'date' && $value != '') {
                    $res = explode(' - ', $value);
                    $time1 = strtotime($res[0]);
                    $time2 = strtotime($res[1]);
                    $d1 = date('Y-m-d', $time1);
                    $d2 = date('Y-m-d', $time2);

                    $ServiceOrder->where($field, '>', $d1);
                    $ServiceOrder->where($field, '<', $d2);
                    //  echo $d1;
                    //  echo $d2;
                } else if ($field == 'paid_to' && $value != '') {
                    $res = explode(' - ', $value);
                    $time1 = strtotime($res[0]);
                    $time2 = strtotime($res[1]);
                    $d1 = date('Y-m-d', $time1);
                    $d2 = date('Y-m-d', $time2);

                    $ServiceOrder->where($field, '>', $d1);
                    $ServiceOrder->where($field, '<', $d2);
                    //  echo $d1;
                    //  echo $d2;
                } else if ($field && $value != '') {

                    if ($type == 'like') {
                        $ServiceOrder->where($field, 'LIKE', '%' . $value . '%');
                    } else if ($type == 'equal') {
                        $ServiceOrder->where($field, $value);
                    }


                }
            }
        }

        $order = Tools::rPOST('order');

        if ($order['field']) {
            $ServiceOrder->order($order['field'], $order['type']);
        } else {
            $ServiceOrder->order('id', 'desc');
        }

        $v->filter = $vfilter;

        $v->orders = $ServiceOrder->limit($this->from, $this->count)->getRows();


        $this->pagination($ServiceOrder->lastQuery()->getRowsCount());
    }

    public function actionValidateAjax(){
        $field = Tools::rPOST('field');
        $val   = Tools::rPOST('value');
        if ($field == "promocode"){
            $service_id = Router::getParam('service_id');
            $serviceObject = new Service($service_id);
            $promocode = new Promocode();
            $promocode->where("code", $val);
            $promocode= new Promocode($promocode->getRow());
            if ($promocode->isAvailable($serviceObject->category_id)) {
                echo json_encode(['result' => 1]);
            } else {
                echo json_encode(['result' => 0]);
            }
        }
    }

    public function actionInfoAjax()
    {
        $view = $this->getView('service/order/info.php');

        $serviceOrderObject = new ServiceOrder(Tools::rGET('id_order'));

        if($serviceOrderObject->client_id != $this->client->id){
            $this->returnAjaxAnswer(0, 'Доступ запрещен');
        }

        $view->order = $serviceOrderObject;

        $this->carcase->import('content', $view);
    }

    public function actionRemoveAjax()
    {
        $ServiceOrder = new ServiceOrder(Tools::rGET('id_order'));

        if ($ServiceOrder->client_id != $this->client->id) {
            $this->returnAjaxAnswer(0, 'Доступ запрещен');
        }

        if($ServiceOrder->remove()) $this->returnAjaxAnswer(1, "Заказ успешно удален");
        else $this->returnAjaxAnswer(0, "Произошла ошибка");
    }

    public function actionShowAjax()
    {
        $view = $this->getView('service/order/show.php');

        $ServiceOrder = new ServiceOrder(Tools::rGET('id_order'));

        if($ServiceOrder->client_id != $this->client->id){
            Tools::display403Error();
        }

        $view->order = $ServiceOrder;

        $view->service = new Service($ServiceOrder->service_id);
        $ServiceFieldValue = new ServiceFieldValue();

        $view->fields = $ServiceFieldValue
            ->select('*')
            ->select(ServiceField::factory(), 'name')
            ->join(ServiceField::factory(), 'field_id', 'id')
            ->where('order_id', $ServiceOrder->id)->getRows();


        $this->carcase->import('content', $view);
    }

    public function actionSelect()
    {
        $this->layout->import('content', $view = $this->getView('service/select.php'));
        $service_category_id = Router::getParam(0);
        $Service = new Service();

        $view->services = ($Service->where('category_id', $service_category_id)->getRows());
    }

    public function actionProlong(){
        $this->layout->import('content', $view = $this->getView('service/order/prolong.php'));

        $serviceOrderObject = new ServiceOrder(Tools::rGET('id_order'));
        $serviceObject      = new Service($serviceOrderObject->service_id);

        if($serviceOrderObject->client_id != $this->client->id){
            Tools::display403Error();
        }

        if(!$serviceOrderObject->isLoadedObject() && !$serviceObject->isLoadedObject()){
            Tools::display404Error();
        }

        $billObject    = new Bill();
        $bills = $billObject
            ->where('service_order_id', $serviceOrderObject->id)
            ->where('type',             Bill::TYPE_SERVICE_ORDER)
            ->where('is_paid',          0)
            ->getRow();


        if(!$bills && Tools::rPOST()){
            $periods = array('1','2','6','12');
            if (!in_array(Tools::rPOST('pay_period'), $periods)) {
                Tools::display404Error();
            }

            $bill                     = new Bill();
            $bill->client_id          = $this->client->id;
            $bill->service_order_id   = $serviceOrderObject->id;
            $bill->price              = $serviceObject->price;
            $bill->pay_period         = Tools::rPOST('pay_period');
            $bill->total              = $serviceObject->price * Tools::rPOST('pay_period');
            $bill->date               = date('Y-m-d');
            $bill->type               = Bill::TYPE_SERVICE_ORDER;

            if ($bill->save()) {
                Notifier::NewBill($this->client, $bill);

                Tools::redirect('/bill/' . $bill->id);
            }
        }

        $view->order = $serviceOrderObject;
        $view->service = $serviceObject;
        $view->error = null;
        if ($bills) {
            $view->error = ('bill_exist');
        }




    }

    public function actionOrder(){
        if(Tools::rPOST('pay_period', 1) <= 0){
            Tools::display404Error();
        }

        $this->layout->import('content', $v = $this->getView('service/order.php'));

        $service_id = Router::getParam('service_id');
        $Service = new Service($service_id);

        //   print_r($Service);

        $ServiceField = new ServiceField();
        $fields = $ServiceField->where('service_id', $Service->id)->getRows();


        if($Service->isLoadedObject() && (Tools::rPOST() || $Service->type !=0 && empty($fields))){
            // print_r($_POST);

            $ServiceOrder = new ServiceOrder();
            $ServiceOrder->service_id = $Service->id;
            $ServiceOrder->client_id = $this->client->id;
            $ServiceOrder->status = 0;
            $ServiceOrder->price = $Service->price;
            $ServiceOrder->date = date('Y-m-d');
            $ServiceOrder->paid_to = date('Y-m-d');
            $ServiceOrder->type = $Service->type;
            $ServiceOrder->save();

            $additional_price = 0;
            foreach ($fields as $field) {
                $ServiceFieldValue = new ServiceFieldValue();
                $serviceFieldObject = new ServiceField($field->id);
                if($serviceFieldObject->type == 4) {
                    $value = $serviceFieldObject->parseSelectValues(Tools::rPOST($field->id));
                    if(is_object($value)){
                        $additional_price               += $value->price;
                        $ServiceFieldValue->value       =  $value->value;
                    } else {
                        $ServiceFieldValue->value       =  '';
                    }

                } else if($serviceFieldObject->type == 5) {
                    $serviceFieldObject = new ServiceField($field->id);
                    $value = $serviceFieldObject->parseRangeValues();

                    if(is_object($value)){
                        $additional_price   += $value->price*(int)Tools::rPOST($field->id);
                    }
                    $ServiceFieldValue->value       =  Tools::rPOST($field->id);
                }
                else{
                    $ServiceFieldValue->value       =  Tools::rPOST($field->id);
                }

                $ServiceFieldValue->service_id  = $Service->id; // not using ?
                $ServiceFieldValue->order_id    = $ServiceOrder->id;
                $ServiceFieldValue->field_id    = $field->id;

                $ServiceFieldValue->save();

            }
            $ServiceOrder->price = $Service->price+$additional_price;
            $ServiceOrder->save();

            $ServiceOrder->sendEvent('create');

            Notifier::NewServiceOrder($this->client, $ServiceOrder, $Service);

            $pay_period = Tools::rPOST('pay_period', 1);
            if($pay_period <= 0){
                $pay_period = 1;
            }

            $Bill = new Bill();
            $Bill->pay_period = $pay_period;

            $total = $ServiceOrder->price * $Bill->pay_period;

            $promocode = new Promocode();
            $promocode = new Promocode($promocode->where("code", Tools::rPOST("promocode"))->getRow());


            $total = $promocode->calcPrice($total, $Service->category_id/*$ServiceOrder->service_id*/);

            $Bill->type = Bill::TYPE_SERVICE_ORDER;
            $Bill->client_id = $this->client->id;
            $Bill->service_order_id = $ServiceOrder->id;

            $Bill->price = $ServiceOrder->price;
            $Bill->total = $total;
            $Bill->save();

            Notifier::NewBill($this->client, $Bill);

            Tools::redirect('bill/'.$Bill->id);

        }

        foreach ($fields as &$field){
            if($field->type == 4){


                $field = new ServiceField($field);
                $new_values =  $field->parseSelectValues();
                $field->values = $new_values;

            } else  if($field->type == 5){
                $field = new ServiceField($field);
                $slider =  $field->parseRangeValues();
                $field->slider = $slider;
            }
        }
        $v->service = $Service;
        $v->fields = ($fields);

    }


}