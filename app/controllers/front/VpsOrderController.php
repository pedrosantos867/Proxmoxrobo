<?php

namespace front;

use Dompdf\Exception;
use model\Bill;
use model\Promocode;
use model\VpsOrder;
use model\VpsOrderIp;
use model\VpsPlan;
use model\VpsPlanDetail;
use model\VpsPlanParam;
use model\VpsServer;
use model\VpsServerIp;
use System\Cookie;
use System\Db\Schema\Schema;
use System\Db\Schema\Table;
use System\Notifier;
use System\Router;
use System\Tools;
use vps\VPSAPI;

class VpsOrderController extends FrontController {

    public function actionListAjax()
    {
        $view = $this->getView('vps/order/list.php');
        $VpsOrders   = new VpsOrder();
        $VpsOrders
            ->select('*')
            ->select(VpsPlan::getInstance(), 'name')
            ->select(VpsPlan::getInstance(), 'price')
            ->select(VpsServer::getInstance(), 'name', 'server_name')
            ->select(VpsServer::getInstance(), 'url', 'server_url')
            ->select(VpsServer::getInstance(), 'host', 'server_host')
            ->join(VpsPlan::getInstance(), 'plan_id', 'id')
            ->join(VpsServer::getInstance(), 'server_id', 'id')
            ->where('client_id', $this->client->id)
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
                        $VpsOrders->where($field, 'LIKE', '%' . $value . '%');
                    } else if ($type == 'equal') {
                        $VpsOrders->where($field, $value);
                    }
                }
            }
        }

        $order = Tools::rPOST('order');

        if ($order['field']) {
            $VpsOrders->order($order['field'], $order['type']);
        } else {
            $VpsOrders->order('id', 'desc');
        }

        $view->filter = $vfilter;

        $orders           = $VpsOrders->getRows();
        $view->orders     = $orders;

        $view->plans = VpsPlan::factory()->getRows();

        $all              = $VpsOrders->lastQuery()->getRowsCount();
        $view->pagination = $this->pagination($all);

        $view->currency = $this->currency;

        $this->layout->import('content', $view);

    }

    public function actionRemoveAjax()
    {
        $id_order = Tools::rGET('id_order');
        $vpsOrderObject = new VpsOrder($id_order);

        $this->checkAccess($vpsOrderObject);

        if ($vpsOrderObject->isLoadedObject()) {

            try {
                $api = VPSAPI::selectServer($vpsOrderObject->server_id);
                //try delete orders from server
                $api->removeVM("pve", $vpsOrderObject->vmid, $vpsOrderObject->username, $vpsOrderObject->type);
                $api->removeUser($vpsOrderObject->username);
            } catch (\System\Exception $e) {
                //nothing to do
            }

            $vpsOrderObject->remove();

            $this->returnAjaxAnswer(1, 'Заказ успешно удален');
        }

        $this->returnAjaxAnswer(0, 'Возникла ошибка удаления');
    }

    public function actionNew(){

        $this->layout->import('content', $v = $this->getView('vps/order/new.php'));

        $plans = VpsPlan::factory()->getRows();
        foreach ($plans as $row) {
            $row->details = VpsPlanDetail::factory()->select('*')->select(VpsPlanParam::factory(),'name')->where('plan_id', $row->id)->join(VpsPlanParam::factory(), 'param_id', 'id')->getRows();
        }

        $v->plans = $plans;
    }

    public function actionValidateAjax(){
        $field = Tools::rPOST('field');
        $val   = Tools::rPOST('value');
        if ($field == "promocode"){
            $promocode = new Promocode();
            $promocode->where("code", $val);
            $promocode= new Promocode($promocode->getRow());
            if($promocode->isAvailable('-3')){
                echo json_encode(['result' => 1]);
            } else {
                echo json_encode(['result' => 0]);
            }
        }
    }

    public function actionPlan(){
        $this->layout->import('content', $v = $this->getView('vps/order/plan.php'));

        $plan = new VpsPlan(Router::getParam('plan_id'));
        $servers = $plan->getServers();

        if($plan->hidden) Tools::display404Error();

        foreach ($servers as &$server) {
            $server = new VpsServer($server);
        }

        $v->servers = $servers;
        $v->rules_page = false;
        $v->plan = $plan;

    }

    public function actionProlong(){
        $order    = new VpsOrder(Tools::rGET('id_order'));

        $this->checkAccess($order);

        $v = $this->getView('vps/order/prolong.php');
        $v->error       = array();
        $plan           = new VpsPlan($order->plan_id);
        $v->plan        = $plan;
        $v->order       = $order;
        $v->server      = new VpsServer($order->server_id);
        $v->username    = $order->username;

        $this->layout->import('content', $v);

        $hb    = new Bill();
        $bills = $hb->where('hosting_account_id', $order->id)->where('type', Bill::TYPE_VPS)->where('is_paid', 0)->getRow();
        if ($bills) {
            $v->error = ('bill_exist');
        }
        if ($order->isLoadedObject() && Tools::rPOST('pay_period')) {


            if ($bills) {
                $v->error = ('bill_exist');
            } else {
                $total = Tools::rPOST('pay_period') * $plan->price;

                //calculate price with promocode discount
                $promocode = new Promocode();
                $promocode = new Promocode($promocode->where("code", Tools::rPOST("promocode"))->getRow());
                $total = $promocode->calcPrice($total, -3);
                
                $bill                     = new Bill();
                $bill->client_id          = $this->client->id;
                $bill->hosting_account_id = $order->id;
                $bill->hosting_plan_id    = $plan->id;
                $bill->price              = $plan->price;
                $bill->pay_period         = Tools::rPOST('pay_period');
                $bill->total              = $total;
                $bill->date               = date('Y-m-d');
                $bill->type               = Bill::TYPE_VPS;

                //  print_r($bill);exit();
                if ($bill->save()) {
                    Notifier::NewBill($this->client, $bill);

                    Tools::redirect('/bill/' . $bill->id);
                }
            }
        }

    }

    public function actionPlanAjax(){
        $plan = new VpsPlan(Router::getParam('plan_id'));

        $VpsOrder = new VpsOrder();

        $VpsOrder->username = Tools::rPOST('login');
        $VpsOrder->password = Tools::rPOST('pass');
        $VpsOrder->plan_id  = Tools::rPOST('id_plan');
        $VpsOrder->server_id  = Tools::rPOST('server');
        $VpsOrder->image  = Tools::rPOST('image');
        $VpsOrder->paid_to  = date('Y-m-d');

        if(Tools::rPOST('pay_period') == 'test' && $plan->test_days > 0){
            $VpsOrder->paid_to = date('Y-m-d', time()+$plan->test_days*Cookie::ONE_DAY+1*Cookie::ONE_DAY);
            $VpsOrder->active = 1;
        }

        $VpsOrder->client_id = $this->client->id;

        $vpsServer = new VpsServer($VpsOrder->server_id);
        if($vpsServer->hidden || !$vpsServer->isLoadedObject()) {
            echo json_encode(array('result' => 0, 'error' => 'system_error'));
            exit;
        }

        $periods = array('test', '1','2','6','12');
        if (!in_array(Tools::rPOST('pay_period'), $periods)) {
            echo json_encode(array('result' => 0, 'error' => 'system_error'));
            exit;
        }

        $server = VPSAPI::selectServer($vpsServer);

        $create_user = $server->createUser($VpsOrder->username, $this->client->name, $this->client->email, $VpsOrder->password);
       // print_r($create_user);

        if($create_user->code == VPSAPI::ANSWER_CREATE_USER_SUCCESS){
            $net = '';

            if ($vpsServer->type == VpsServer::PANEL_PROXMOX) {
                if ($plan->net_type == 1) {
                    $net = '';
                } else if ($plan->net_type == 2) {
                    $ip = VpsServerIp::factory()
                        ->where('server_id', $VpsOrder->server_id)
                        ->where('type', 0)
                        ->where('used', '0')
                        ->getRow();

                    if ($ip) {
                        $Ip = new VpsServerIp($ip);
                        $net = $Ip->vlan;
                    }
                } else if ($plan->net_type == 3) { // static ip from list

                    $ip = VpsServerIp::factory()
                        ->where('server_id', $VpsOrder->server_id)
                        ->where('type', 1)
                        ->where('used', '0')
                        ->getRow();

                    if ($ip) {
                        $Ip = new VpsServerIp($ip);
                        $net = array('ip' => $Ip->ip, 'gateway' => $Ip->gateway, 'mask' => $Ip->mask);
                    }
                }
            } else if ($vpsServer->type == VpsServer::PANEL_VMMANAGER) {

                if ($plan->net_type == 5) {
                    $ip = VpsServerIp::factory()
                        ->where('server_id', $VpsOrder->server_id)
                        ->where('type', 4)
                        ->where('used', '0')
                        ->getRow();

                    if ($ip) {
                        $Ip = new VpsServerIp($ip);
                        $net = array('ip' => $Ip->ip);
                    }
                } else if ($plan->net_type == 3) { // static ip from list
                    $ip = VpsServerIp::factory()
                        ->where('server_id', $VpsOrder->server_id)
                        ->where('type', 2)
                        ->where('used', '0')
                        ->getRow();

                    if ($ip) {
                        $Ip = new VpsServerIp($ip);
                        $net = array('ip' => $Ip->ip);
                    }
                } else if ($plan->net_type == 7) { // static ip from list

                    $ip = VpsServerIp::factory()
                        ->where('server_id', $VpsOrder->server_id)
                        ->where('type', 3)
                        ->where('used', '0')
                        ->getRow();

                    if ($ip) {
                        $Ip = new VpsServerIp($ip);
                        $net = array('ip' => $Ip->ip);
                    }
                }

            }


            $domain = Tools::rPOST('domain');
            $create_vm = $server->createVM($plan->node, $plan->type, $plan->memory, $plan->hdd, $plan->cores, $VpsOrder->image,$plan->sockets, $create_user->data, $VpsOrder->password,$plan->net_type, $net, $domain, $plan->recipe);
            if($create_vm->code == VPSAPI::ANSWER_CREATE_VM_SUCCESS){

                $VpsOrder->vmid = $create_vm->data;
                $VpsOrder->type = $plan->type;

                $VpsOrder->save();


                if (isset($Ip) && $Ip->isLoadedObject()) {

                    $Ip->used = 1;
                    $Ip->save();

                    $VpsOrderIp = new VpsOrderIp();
                    $VpsOrderIp->ip_id = $Ip->id;
                    $VpsOrderIp->order_id = $VpsOrder->id;
                    $VpsOrderIp->save();
                }

                if(Tools::rPOST('pay_period') != 'test') {
                    $suspend_vm = $server->suspendVM($plan->node, $create_vm->data, $create_user->data, $VpsOrder->type);
                }

                $Bill = new Bill();

                if(Tools::rPOST('pay_period') != 'test') {
                    $total = Tools::rPOST('pay_period') * $plan->price;

                    //calculate price with promocode discount
                    $promocode = new Promocode();
                    $promocode = new Promocode($promocode->where("code", Tools::rPOST("promocode"))->getRow());
                    $total = $promocode->calcPrice($total, -3);

                    //create bill
                    $Bill->type = Bill::TYPE_VPS;
                    $Bill->hosting_account_id = $VpsOrder->id;
                    $Bill->client_id = $this->client->id;
                    $Bill->price = $plan->price;
                    $Bill->pay_period = Tools::rPOST('pay_period');
                    $Bill->total = $total;
                    $Bill->date = date('Y-m-d');
                    $Bill->save();

                    Notifier::NewBill($this->client, $Bill);
                }

                echo json_encode(array('result' => 1 , 'error' => '', 'id_bill' => $Bill->id));
            } else {
                $server->removeUser($VpsOrder->username);

                echo json_encode(array('result' => 0, 'error' => 'system_error'));
            }
        } else{
            if(isset($create_user->data['field']) && $create_user->data['field'] == 'user' && $create_user->data['type'] == 'exist'){
                echo json_encode(array('result' =>0, 'error' => 'user_exist'));
            } else if(isset($create_user->data['field']) && $create_user->data['field'] == 'password'){
                echo json_encode(array('result' =>0, 'error' => 'password'));
            } else if(isset($create_user->data['field']) && $create_user->data['field'] == 'name' && $create_user->data['type'] == 'length'){
                echo json_encode(array('result' =>0, 'error' => 'user_length'));
            }else {
                //  print_r($create_vm);
                echo json_encode(array('result' => 0, 'error' => 'system_error'));
            }

        }

    }

}