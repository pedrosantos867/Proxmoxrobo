<?php

namespace front;

use model\Client;
use model\Currency;
use model\Domain;
use model\DomainOrder;
use model\HostingAccount;
use model\Bill;
use model\HostingPlan;
use model\Service;
use model\ServiceCategories;
use model\ServiceOrder;
use model\VpsOrder;
use model\VpsPlan;

use System\Cookie;
use System\Module;
use System\Notifier;
use System\Router;
use System\Tools;


class BillsController extends FrontController
{

    public function actionCheckBillsAjax()
    {
        $bills = Tools::rPOST('bills');

        if (count($bills) == 1) {
            $hb = new Bill($bills[0]);
            echo json_encode(array('id_bill' => $hb->id, 'link' => Tools::link('bill/' . $hb->id)));
            exit();
        }

        $hb = new Bill();
        $hb->client_id = $this->client->id;
        $total = 0;

        $inc_bills = array();
        foreach ($bills as &$bill) {
            $bill = new Bill($bill);

            $this->checkAccess($bill);

            $total += $bill->total;
            $inc_bills[] = $bill->id;
        }

        $hb->type = Bill::TYPE_INC;
        $hb->inc   = implode('|', $inc_bills);
        $hb->total = $total;
        $hb->save();

        echo json_encode(array('id_bill' => $hb->id, 'link' => Tools::link('bill/' . $hb->id)));
    }

    public function actionPayBalance(){

        $id_bill = Router::getParam('id_bill');
        $hb = new Bill($id_bill);

        $this->checkAccess($hb);

        if ($hb->type == Bill::TYPE_INC) {
            foreach (explode('|', $hb->inc) as $bill_id) {
                $bills[] = new Bill($bill_id);
            }
        } else {
            $bills = array($hb);
        }

        $client = $this->client;
        $summ   = $hb->total;

        if($summ < 0 ){
            Tools::redirect('/payment/status/balance/' . $id_bill . '/fail');
        }

        if ($client->balance < $summ) {
            Tools::redirect('/payment/status/balance/' . $id_bill . '/fail');
        }
        $client->balance -= $summ;
        $client->save();

        foreach ($bills as $bill) {
            $bill->pay();
        }

        $Client =  new Client($hb->client_id);
        if (count($bills) > 1) {
            Notifier::PaidBills($Client, $hb);
        } else {
            Notifier::PaidBill($Client, $hb);
        }

        Tools::redirect('/payment/status/balance/' . $id_bill . '/success');
    }

    public function actionIndexAjax()
    {

        $id_order        = Tools::rGET('id_order');
        $id_service_order = Tools::rGET('id_service_order');
        $id_domain_order = Tools::rGET('id_domain_order');
        $id_vps_order = Tools::rGET('id_vps_order');

        $page = Tools::rPOST('page');


        $view = $this->getView('bill/list.php');
        $view->order  = null;


        $hb = new Bill();
        $hb

            ->select('*')
            ->select(HostingAccount::getInstance(), 'login')
            ->select('bm_hap', 'name', 'hosting_plan')
            ->select('bm_vpsp', 'name', 'vps_plan')
            ->select('bm_vpso', 'username', 'vps_username')

            ->select(DomainOrder::factory(), 'domain', 'domain')
            ->select('bm_s', 'name', 'domain_zone')
            ->select('bm_hbp1', 'name', 'old_plan')
            ->select('bm_hbp2', 'name', 'new_plan')
            ->select('bm_sc', 'id', 'category_id')
            ->select('bm_sc', 'name', 'category')
            ->select('bm_serv', 'name', 'service')

            ->join(HostingAccount::getInstance(), 'hosting_account_id', 'id')
            ->join(HostingPlan::getInstance(), 'hosting_plan_id', 'id', 'bm_hap')
            ->join(VpsOrder::getInstance(), 'hosting_account_id', 'id', 'bm_vpso')
            ->join('bm_vpso', VpsPlan::getInstance(), 'plan_id', 'id', 'bm_vpsp')
            ->join(HostingPlan::getInstance(), 'hosting_plan_id', 'id', 'bm_hbp1')
            ->join(HostingPlan::getInstance(), 'hosting_plan_id2', 'id', 'bm_hbp2')
            ->join(DomainOrder::getInstance(), 'domain_order_id', 'id')
            ->join(DomainOrder::factory(), Domain::getInstance(), 'domain_id', 'id', 'bm_s')
            ->join(ServiceOrder::getInstance(), 'service_order_id', 'id', 'bm_so')
            ->join('bm_so', Service::getInstance(), 'service_id', 'id', 'bm_serv')
            ->join('bm_serv', ServiceCategories::getInstance(), 'category_id', 'id', 'bm_sc')
            ->where('client_id', $this->client->id)
            ->where('type', '!=', Bill::TYPE_INC);

        $moduleDate = ['billObject' => $hb, 'view' => $view];
        Module::extendMethod('getListBills', $moduleDate );


        $filter  = Tools::rPOST('filter');
        $vfilter = array();


        if (isset($filter) && $filter) {
            foreach ($filter as $field => $option) {

                $value = $option['value'];
                $type  = isset($option['type']) ? $option['type'] : 'like';

                $vfilter[$field] = $value;

                if ($field && $value != '') {

                    if ($field == 'date' && $value != '') {
                        $res   = explode(' - ', $value);
                        $time1 = strtotime($res[0]);
                        $time2 = strtotime($res[1]);
                        $d1    = date('Y-m-d', $time1);
                        $d2    = date('Y-m-d', $time2);

                        $hb->where($field, '>', $d1);
                        $hb->where($field, '<', $d2);
                        //  echo $d1;
                        //  echo $d2;
                    }
                    elseif($field == 'type' && $value != ''){

                        if(strpos($value, 'module')===false) {
                            if (strpos($value, 's') !== false) {
                                $value = str_replace('s', '', $value);
                                $hb->where('alias.category_id', $value);
                            } else {
                                $hb->where($field, $value);
                            }
                        }
                    }
                    else {
                        if ($type == 'like') {
                            $hb->where($field, 'LIKE', '%' . $value . '%');
                        } else if ($type == 'equal') {
                            $hb->where($field, $value);
                        }
                    }

                }
            }
        }

        $order = Tools::rPOST('order');

        if ($order['field']) {
            $hb->order($order['field'], $order['type']);
        } else {
            $hb->order('id', 'desc');
        }


        $view->filter = $vfilter;


        if ($id_order) {
            $view->order = new HostingAccount($id_order);
            $hb->where(HostingAccount::getInstance(), 'id', $id_order);
        }

        if ($id_service_order) {
            $view->order = new ServiceOrder($id_service_order);
            $hb->where('so', 'id', '=', $id_service_order);
        }

        if ($id_domain_order) {
            $view->order = new DomainOrder($id_domain_order);
            $hb->where(DomainOrder::getInstance(), 'id', $id_domain_order);
        }
        if ($id_vps_order) {
            $view->order = new VpsOrder($id_vps_order);
            $hb
                ->join(VpsOrder::getInstance(), 'hosting_account_id', 'id')
                ->where(VpsOrder::getInstance(), 'id', $id_vps_order);
        }

        // echo $this->from;
        $hb->limit($this->from, $this->count);

        $r = $hb->getRows();


        Module::extendMethod('afterGetListBills', $r);
      //  print_r($r);


        $view->bills = $r;
        $all         = $hb->lastQuery()->getRowsCount();
        // echo $all;
        $view->pagination = $this->pagination($all);
        $view->currency   = new Currency(Cookie::get('currency'));

        $view->service_categories = ServiceCategories::factory()->getRows();
        $services = [];
        Module::extendMethod('getListServicesForBills', $services);

        $view->services= $services;

        $this->layout->import('content', $view);
    }

    public function actionIndex()
    {
        $view = $this->getView('empty-ajax-block.php');
        $this->layout->import('content', $view);
    }


    public function actionPaymentStatus()
    {
        $psystem = Router::getParam('system');

        $success = 0;
        $id_bill = null;

        if ($psystem == 'interkassa') {
            $id_bill = Tools::rPOST('ik_pm_no');
            if (Tools::rPOST('ik_inv_st') == 'success') {
                $success = 1;
            }
        }
        else if ($psystem == 'balance') {
            $id_bill = Router::getParam('id_bill');
            if (Router::getParam('status') == 'success') {
                $success = 1;
            }
        }
        else if ($psystem == 'privat24') {
            if (Tools::rPOST('payment')) {
                $arr_payment = explode('&', Tools::rPOST('payment'));
                $res         = array();


                foreach ($arr_payment as $p) {
                    $r          = explode('=', $p);
                    $res[$r[0]] = $r[1];
                }
                $id_bill = $res['order'];
                if ($res['state'] == 'ok' || $res['state'] == 'test') {
                    $success = 1;
                }
            }
        }
        else if ($psystem == 'robokassa') {

            if (Tools::rPOST('SignatureValue')) {
                $success = 1;
                $id_bill = Tools::rPOST('InvId');
            } else {
                $success = 0;
                $id_bill = Tools::rPOST('InvId');
            }
        }
        else if($psystem == 'freekassa') {
            $id_bill = Tools::rRequest('MERCHANT_ORDER_ID');

            if(Tools::rGET('intid') || Tools::rPOST('intid')){
                $success = 1;
            }
        }
        else if($psystem == 'bepaid') {
            if(Tools::rGET('status') == 'successful'){
                $success=1;
            }
        }
        else if($psystem == 'webmoney'){
            if(Tools::rGET('status')){
                $success=1;
            }
        }

        if(Tools::rGET('success') == 1){
            $success = 1;
        }

        $view = $this->getView('bill/payment_status.php');

        $view->psystem = $psystem;
        $bill = new Bill($id_bill);

        $view->success = $success;

        if ($bill->type == Bill::TYPE_INC) {
            $bills = array();
            foreach (explode('|', $bill->inc) as $id_bill) {
                $bills[] = $id_bill;
            }
            $view->bills = $bills;
        }


        $view->bill = $bill;

        $this->layout->content = $view->fetch();
    }


    public function actionBill()
    {


        $bill_id = Router::getParam(0);
        $bill = new Bill($bill_id);

        if (!$bill->isLoadedObject()) {
            Tools::redirect('bills');
        }
        $this->checkAccess($bill);



        $view = $this->getView('bill/pay.php');
        $view->error = '';


        $total           = 0;
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

        $this->layout->import('content', $view);


    }

    public function actionOffAjax()
    {
        $id_bill = Router::getParam('id_bill');
        $hb = new Bill($id_bill);

        $this->checkAccess($hb);

        if ($hb->type == Bill::TYPE_DOMAIN_ORDER) {
           $this->returnAjaxAnswer(0, 'Вы не можете отменить счет на оплату домена');

        }

        $hb->is_paid = -1;

        if ($hb->save()) {
            $this->returnAjaxAnswer(1, 'Счет отменен');
        } else {
            $this->returnAjaxAnswer(0, 'Возникла ошибка');
        }
    }



}