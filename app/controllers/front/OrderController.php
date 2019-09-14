<?php
namespace front;

use email\Email;
use hosting\HostingAPI;
use hosting\ISPManager4API;
use hosting\ISPManagerAPI;
use model\ClientNotify;
use model\HostingAccount;
use model\Bill;
use model\HostingPlan;
use model\HostingPlanDetail;
use model\HostingPlanExtendedPrice;
use model\HostingPlanParams;
use model\HostingServer;
use model\Languages;
use model\Page;
use model\Promocode;
use sms\SMS;
use System\Cookie;
use System\Notifier;
use System\Router;
use System\Tools;
use System\View\View;


class OrderController extends FrontController
{

    public function actionValidateAjax(){
        $field = Tools::rPOST('field');
        $val   = Tools::rPOST('value');
        if ($field == "promocode"){
            $promocode = new Promocode();
            $promocode->where("code", $val);
            $promocode= new Promocode($promocode->getRow());
            if($promocode->isAvailable('-1')){
                echo json_encode(['result' => 1]);
            } else {
                echo json_encode(['result' => 0]);
            }
        }
    }

    public function actionHosting()
    {
        $view = $this->getView('hosting/order/hosting.php');
        $hp   = new HostingPlan();

        $plans = $hp->getRows();

        foreach ($plans as &$plan) {
            $hpd     = new HostingPlanDetail();
            $details = $hpd->where('plan_id', $plan->id)->getRows();
            foreach ($details as &$detail) {
                $hpp          = new HostingPlanParams($detail->param_id);
                $detail->name = $hpp->name;
                $detail->desc = $hpp->desc;
            }
            $plan->details = $details;
        }

        //   print_r($plans);
        $view->plans = $plans;
        $this->layout->import('content', $view);
    }

    public function actionListAjax()
    {
        $view = $this->getView('hosting/order/list.php');
        $ha   = new HostingAccount();
        $ha->select('*')
            ->select('hp', 'name')
            ->select('hp', 'price')
            ->select(HostingServer::getInstance(), 'name', 'server_name')
            ->select(HostingServer::getInstance(), 'host', 'server_host')
            ->select(HostingServer::getInstance(), 'ip', 'server_ip')
            ->join(HostingPlan::getInstance(), 'plan_id', 'id', 'hp')
            ->join(HostingServer::getInstance(), 'server_id', 'id')
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
                        $ha->where($field, 'LIKE', '%' . $value . '%');
                    } else if ($type == 'equal') {
                        $ha->where($field, $value);
                    }


                }
            }
        }

        $order = Tools::rPOST('order');

        if ($order['field']) {
            $ha->order($order['field'], $order['type']);
        } else {
            $ha->order('id', 'desc');
        }

        $view->filter = $vfilter;
        $view->plans  = HostingPlan::getInstance()->getRows();
        $orders           = $ha->getRows();
        $view->orders     = $orders;
        $all              = $ha->lastQuery()->getRowsCount();
        $view->pagination = $this->pagination($all);

        $view->currency = $this->currency;

        $this->layout->import('content', $view);

    }

    public function actionList()
    {
        $view = $this->getView('empty-ajax-block.php');
        $this->layout->import('content', $view);
    }

    public function actionRemoveAjax()
    {
        $id_order = Tools::rGET('id_order');
        $ho       = new HostingAccount($id_order);

        if ($ho->client_id != $this->client->id) {
            $this->returnAjaxAnswer(0, 'Доступ запрещен');
        }
        $ho->remove();


        $this->returnAjaxAnswer(1, 'Заказ успешно удален');
    }

    public function actionChangePlan()
    {
        $id_order = Router::getParam('id_order');

        $ha   = new HostingAccount($id_order);

        $this->checkAccess($ha);


        $view = $this->getView('hosting/order/change-plan.php');
        $this->layout->import('content', $view);
        $errors = array();


        $hp = new HostingPlan();
        $plan = new HostingPlan($ha->plan_id);

        if (Tools::rPOST()) {
            $new_plan = new HostingPlan(Tools::rPOST('id_plan'));


            if ($new_plan->isLoadedObject()) {

                $server = new HostingServer($ha->server_id);

                if ($new_plan->price > $plan->price) {
                    $bill       = new Bill();
                    $bill->type = Bill::TYPE_CHANGE_PLAN;
                    $bill->hosting_plan_id    = $plan->id;
                    $bill->hosting_plan_id2   = $new_plan->id;
                    $bill->client_id          = $this->client->id;
                    $bill->hosting_account_id = $ha->id;


                    $datetime1 = new \DateTime();
                    $datetime2 = new \DateTime($ha->paid_to);

                    if ($datetime1 <= $datetime2) {
                        $interval = $datetime1->diff($datetime2);
                    } else {
                        $interval = $datetime1->diff(new \DateTime());
                    }


                    $days = $interval->format('%a дней');
                    // echo $days;
                    if ($days > 0) {

                        $hb = new Bill();
                        $hb->where('hosting_account_id', $ha->id);
                        $bills = $hb->where('type', Bill::TYPE_ORDER)
                            ->whereOr()
                            ->where('type', Bill::TYPE_CHANGE_PLAN)->getRows();


                        foreach ($bills as $b) {
                            $hb = new Bill($b);
                            if ($b->is_paid == 0) {
                                $hb->is_paid = -1;
                                $hb->save();
                            }
                        }

                        $price_current     = ($plan->price / 29.4);
                        $price_new         = ($new_plan->price / 29.4);
                        $diff_price_on_day = ($price_new - $price_current);

                        $bill->price = $diff_price_on_day * 29.4;


                        $total       = $diff_price_on_day * $days;
                        $bill->total = $total;
                        $bill->date  = date('Y-m-d');

                        $bill->save();


                        Notifier::NewBill($this->client, $bill);



                        Tools::redirect('/bill/' . $bill->id);
                    } else {
                        $hb = new Bill();
                        $hb->where('hosting_account_id', $ha->id);
                        $bills = $hb->where('type', Bill::TYPE_ORDER)
                            ->whereOr()
                            ->where('type', Bill::TYPE_CHANGE_PLAN)->getRows();

                        $last_bill = null;
                        //print_r($bills);
                        foreach ($bills as $bill) {
                            $hb = new Bill($bill);

                            if ($hb->type == Bill::TYPE_ORDER && $hb->is_paid == 0) {
                                $last_bill = $hb;
                            }
                            if ($bill->is_paid == 0) {
                                $hb->is_paid = -1;
                                $hb->save();
                            }
                        }

                        $ha->plan_id = $new_plan->id;
                        $ha->save();
                        $api = HostingAPI::selectServer(new HostingServer($ha->server_id))->changePlan($ha->login, $new_plan->panel_name);

                        // echo $api->getCode();
                        //  exit();
                        if ($api == HostingAPI::ANSWER_OK && $last_bill) {
                            // print_r($last_bill);
                            $bill = new Bill();
                            $bill->client_id          = $this->client->id;
                            $bill->hosting_account_id = $ha->id;
                            $bill->hosting_plan_id    = $new_plan->id;
                            $bill->price              = $new_plan->price;
                            $bill->pay_period         = $last_bill->pay_period;
                            $bill->total              = $last_bill->pay_period * $new_plan->price;
                            $bill->date               = date('Y-m-d');
                            $bill->save();

                            Notifier::NewBill($this->client, $bill);
                        } else {
                            //exit($api->getString());
                        }
                        Tools::redirect('/');
                    }
                } else {
                    $hb = new Bill();
                    $hb->where('hosting_account_id', $ha->id);
                    $bills = $hb->where('type', Bill::TYPE_ORDER)
                        ->whereOr()
                        ->where('type', Bill::TYPE_CHANGE_PLAN)->getRows();

                    $last_bill = null;

                    foreach ($bills as $bill) {
                        $hb = new Bill($bill);

                        if ($hb->type == Bill::TYPE_ORDER && $hb->is_paid == 0) {
                            $last_bill = $hb;
                        }
                        if ($bill->is_paid == 0) {
                            $hb->is_paid = -1;
                            $hb->save();
                        }
                    }

                    $ha->plan_id = $new_plan->id;
                    $ha->save();
                    $api                 = HostingAPI::selectServer($server)->changePlan($ha->login, $new_plan->panel_name);

                    // echo $api->getCode();
                    //  exit();
                    if ($api == HostingAPI::ANSWER_OK && $last_bill) {
                        // print_r($last_bill);
                        $bill = new Bill();
                        $bill->client_id = $this->client->id;
                        $bill->hosting_account_id = $ha->id;
                        $bill->hosting_plan_id    = $new_plan->id;
                        $bill->price              = $new_plan->price;
                        $bill->pay_period         = $last_bill->pay_period;
                        $bill->total              = $last_bill->pay_period * $new_plan->price;
                        $bill->date               = date('Y-m-d');
                        $bill->save();

                        Notifier::NewBill($this->client, $bill);
                    } else {
                        //exit($api->getString());
                    }
                    Tools::redirect('/');
                    // Отменить не оплаченные счета по данному акку и сменить тариф без оплаты
                }


            } else {
                $errors = 'no_selected_plan';
            }

        }


        $view->current_plan = $plan;
        $view->plans        = $hp->where('aviable_servers', 'LIKE', '%|' . $ha->server_id . '|%')->getRows();
        $view->errors       = $errors;

    }

    public function actionProlong(){

        $order    = new HostingAccount(Tools::rGET('id_order'));

        $this->checkAccess($order);

        $v = $this->getView('hosting/order/continue.php');
        $v->error = array();
        $plan     = new HostingPlan($order->plan_id);
        $v->plan   = $plan;
        $v->order = $order;
        $v->server = new HostingServer($order->server_id);
        $v->login  = $order->login;
        $this->layout->import('content', $v);

        $HostingPrices = new HostingPlanExtendedPrice();
        $prices = $HostingPrices->where('plan_id', $plan->id)->where('enabled', 1)->getRows();

        $v->prices = $prices;

        $hb    = new Bill();
        $bills = $hb->where('hosting_account_id', $order->id)->where('type', Bill::TYPE_ORDER)->where('is_paid', 0)->getRow();
        if ($bills) {
            $v->error = ('bill_exist');
        }



        if ($order->isLoadedObject() && Tools::rPOST('pay_period')) {
            $periods = array('test', '1','2','6','12');
            $eprice = $HostingPrices->where('plan_id', $plan->id)->where('enabled', 1)->where('period', Tools::rPOST('pay_period'))->getRow();
            if($eprice){
                $total = $eprice->price;
            } else {
                $total = Tools::rPOST('pay_period') * $plan->price;

                if (!in_array(Tools::rPOST('pay_period'), $periods)) {
                    Tools::display404Error();
                }
            }

            if ($bills) {
                $v->error = ('bill_exist');
            } else {
                $bill                     = new Bill();
                $bill->client_id          = $this->client->id;
                $bill->hosting_account_id = $order->id;
                $bill->hosting_plan_id    = $plan->id;
                $bill->price              = $plan->price;
                $bill->pay_period         = Tools::rPOST('pay_period');
                $bill->total              = $total;
                $bill->date               = date('Y-m-d');
                $bill->type               = Bill::TYPE_ORDER;

                //  print_r($bill);exit();
                if ($bill->save()) {
                    Notifier::NewBill($this->client, $bill);

                    Tools::redirect('/bill/' . $bill->id);
                }
            }
        }

    }

    public function actionPlan()
    {


        $id_order = Router::getParam('order');
        $order = new HostingAccount($id_order);


        $plan = new HostingPlan(Router::getParam('plan'));

        if (!$plan->isLoadedObject() || $plan->hidden) {
            Tools::display404Error();
        }

        $v = $this->getView('hosting/order/plan.php');
        $v->error = array();
        $v->plan = $plan;

        $HostingPrices = new HostingPlanExtendedPrice();
        $prices = $HostingPrices->where('plan_id', $plan->id)->where('enabled', 1)->getRows();

        $v->prices = $prices;

        $servers = $plan->getServers();
        $servers_list = array();

        foreach ($servers as &$server) {
            $server = new HostingServer($server);
            if (!$server->hidden) {
                $servers_list[] = $server;
            }
        }


        $v->servers = $servers_list;


        if ($this->config->hosting_rules) {
            $v->rules_page = new Page($this->config->hosting_rules);
        } else {
            $v->rules_page = false;
        }
        $this->layout->import('content', $v);

    }

    public function actionPlanAjax()
    {
        $periods = array('test', '1','2','6','12');

        if (Tools::rPOST('login') && Tools::rPOST('pass')) {
            $plan = new HostingPlan(Tools::rPOST('id_plan'));

            $HostingPrices = new HostingPlanExtendedPrice();
            $eprice = $HostingPrices->where('plan_id', $plan->id)
                ->where('enabled', 1)
                ->where('period', Tools::rPOST('pay_period'))
                ->getRow();
            if($eprice){
                $total = $eprice->price;
            } else {
                $total = Tools::rPOST('pay_period') * $plan->price;

                if (!in_array(Tools::rPOST('pay_period'), $periods)) {
                    echo json_encode(
                        array('error' => 'system_error', 'result' => '0')
                    );
                    exit();
                }
            }
            $server = new HostingServer(Tools::rPOST('server'));
            if ($server->isLoadedObject()) {

                $servapi = HostingAPI::selectServer($server);
                $user_names = explode(' ', $this->client->name);
                $res = $servapi->createUser(array(
                        'username'   => Tools::rPOST('login'),
                        'password'   => Tools::rPOST('pass'),
                        'email'      => $this->client->email,
                        'package'    => $plan->panel_name,
                        'first_name' => isset($user_names[0]) ? $user_names[0] : 'User',
                        'last_name'  => isset($user_names[1]) ? $user_names[1] : 'User',
                        'domain'     =>  Tools::rPOST('domain')
                    )
                );

                if ($res == HostingAPI::ANSWER_OK) {

                    $account            = new HostingAccount();
                    $account->server_id = Tools::rPOST('server');
                    $account->plan_id   = Tools::rPOST('id_plan');
                    $account->login     = Tools::rPOST('login');
                    $account->client_id = $this->client->id;
                    $account->date = date('Y-m-d');


                    Notifier::NewHostingOrder($this->client, $account, $server, $plan);

                    if($plan->test_days > 0 && Tools::rPOST('pay_period') == 'test'){
                        $account->paid_to = date('Y-m-d', time()+$plan->test_days*Cookie::ONE_DAY+1*Cookie::ONE_DAY);
                        $account->active = 1;
                        $account->save();
                        echo json_encode(
                            array('error' => '', 'result' => '1')
                        );
                    } else {
                        $account->save();

                        $promocode = new Promocode();
                        $promocode = new Promocode($promocode->where("code", Tools::rPOST("promocode"))->getRow());
//                        if($promocode->isAvailable() && $total > 0){
//                            if($promocode->sale_type) {// procent
//                                $total = $total * (1-$promocode->sale/100);
//                            }
//                            else {
//                                if($total >= $promocode->sale) $total -= $promocode->sale;
//                                else $total = 0;
//                            }
//                            $promocode->used_count++;
//                            $promocode->save();
//                        }
                        $total = $promocode->calcPrice($total, -1);

                        $bill = new Bill();
                        $bill->client_id = $this->client->id;
                        $bill->hosting_account_id = $account->id;
                        $bill->hosting_plan_id = $plan->id;
                        $bill->price = $plan->price;
                        $bill->pay_period = Tools::rPOST('pay_period');
                        $bill->total = $total;
                        $bill->date = date('Y-m-d');

                        $suspend_requst = HostingAPI::selectServer($server)->suspendUser($account->login);

                        if ($bill->save()) {
                            Notifier::NewBill($this->client, $bill);
                            echo json_encode(
                                array('error' => '', 'result' => '1', 'bill' => $bill->id)
                            );
                        } else {
                            echo json_encode(
                                array('error' => 'system_error', 'result' => '0')
                            );
                        }
                    }
                } else {
                    if ($res == HostingAPI::ANSWER_CONNECTION_ERROR) {
                        echo json_encode(array('error' => 'no_connection', 'result' => '0', 'code' => $res));
                    } else if ($res == HostingAPI::ANSWER_USER_PASSWORD_NOT_VALID) {
                        echo json_encode(array('error' => 'pass_no_valid', 'result' => '0', 'code' => $res));
                    } else if ($res == HostingAPI::ANSWER_USER_NAME_NOT_VALID) {
                        echo json_encode(array('error' => 'login_no_valid', 'result' => '0', 'code' => $res));
                    } else if ($res == HostingAPI::ANSWER_USER_ALREADY_EXIST) {
                        echo json_encode(array('error' => 'user_exist', 'result' => '0', 'code' => $res));
                    } else {
                        echo json_encode(array('error' => 'system_error', 'result' => '0', 'code' => $res, 'message' => $servapi->getErrorDetails()));
                    }
                }
            } else {
                echo json_encode(array('error' => 'no_server', 'result' => ''));
            }
        } else {
            echo json_encode(array('error' => 'no_fields', 'result' => '0'));
        }
    }

    public function actionOpenServerPanel(){
        $order_id = Tools::rGET('id_order');


        $order = new HostingAccount($order_id);
        if (!$order->active) {
            Tools::redirect('hosting-orders');
        }

        $this->checkAccess($order);

        $Server = new HostingServer($order->server_id);
        $link = $Server->ip ? $Server->ip : $Server->host;

        if($Server->panel == HostingServer::PANEL_ISP){
            $API = new ISPManagerAPI($Server);
            $link = $API->userAuth($order->login);
            Tools::redirect($link);
            exit();
        }
        if($Server->panel == HostingServer::PANEL_ISP4){
            $API = new ISPManager4API($Server);
            $link = $API->userAuth($order->login);
            Tools::redirect($link);
            exit();
        }


        Tools::redirect($link);
        exit();
    }
}