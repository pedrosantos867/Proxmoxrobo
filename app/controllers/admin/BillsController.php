<?php
namespace admin;

use email\Email;
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
use System\View\View;

class BillsController extends FrontController
{


    public function actionListAjax()
    {

        $view = $this->getView('bill/list.php');
        $this->layout->import('content', $view);

/*
        for ($i=0;$i<10000; $i++ ) {
            $bill = new Bill();
            $bill->client_id          = 14;
            $bill->hosting_account_id = 49;
            $bill->hosting_plan_id    = 15;
            $bill->price              = 20;
            $bill->pay_period         = 1;
            $bill->total              = 20;
            $bill->date               = date('Y-m-d');
            $bill->save();

        }*/


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
            ->select(Client::getInstance(), 'id', 'client_id')
            ->select(Client::getInstance(), 'name', 'client')
            ->join(Client::getInstance(), 'client_id', 'id')
            ->where('type', '!=', Bill::TYPE_INC);


        $moduleDate = ['billObject' => $hb, 'view' => $view];
        Module::extendMethod('getListBills', $moduleDate);

        $filter = Tools::rPOST('filter');

        $vfilter = array();
        if (isset($filter) && $filter) {
            foreach ($filter as $field => $option) {

                $value = Tools::clearXSS($option['value']);
                $type  = isset($option['type']) ? $option['type'] : 'like';

                $vfilter[$field] = $value;

                if ($field && $value != '') {

                    if ($field == 'date' && $value != '') {
                        $res   = explode(' - ', $value);
                        if (isset($res[0]) && isset($res[1])) {
                            $time1 = strtotime($res[0]);
                            $time2 = strtotime($res[1]);
                            $d1 = date('Y-m-d', $time1);
                            $d2 = date('Y-m-d', $time2);

                            $hb->where($field, '>', $d1);
                            $hb->where($field, '<', $d2);
                        }
                        //  echo $d1;
                        //  echo $d2;
                    }
                    elseif($field == 'type' && $value != ''){

                        if (strpos($value, 'module') === false) {
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
        $view->order  = null;
        $id_order = Tools::rGET('id_order');
        if ($id_order) {
            $view->order = new HostingAccount($id_order);
            $hb->where(HostingAccount::getInstance(), 'id', $id_order);
        }

        $id_domain_order = Tools::rGET('id_domain_order');
        if ($id_domain_order) {
            $view->order = new DomainOrder($id_domain_order);
            $hb->where(DomainOrder::getInstance(), 'id', $id_domain_order);
        }

        $id_service_order = Tools::rGET('id_service_order');
        if ($id_service_order) {
            $view->order = new ServiceOrder($id_service_order);
            $hb->where('service_order_id', $id_service_order);
        }

        $hb->limit($this->from, $this->count);

        $r = $hb->getRows();


        Module::extendMethod('afterGetListBills', $r);


        $view->bills = $r;
        $all         = $hb->lastQuery()->getRowsCount();
        $view->service_categories = ServiceCategories::factory()->getRows();
        $view->pagination = $this->pagination($all);
        $view->currency   = new Currency(Cookie::get('currency'));

        $view->service_categories = ServiceCategories::factory()->getRows();
        $services = [];
        Module::extendMethod('getListServicesForBills', $services);

        $view->services = $services;

    }



    public function actionPayAjax()
    {
        $id_bill = Router::getParam('id_bill');
        $bill = new Bill($id_bill);
        $res     = $bill->pay();
        $Client =  new Client($bill->client_id);

        Notifier::PaidBill($Client, $bill);

        echo json_encode(array('result' => $res));
    }

    public function actionOffAjax()
    {
        $id_bill     = Router::getParam('id_bill');
        $hb = new Bill($id_bill);
        $hb->is_paid = -1;
        if ($hb->save()) {
            echo json_encode(array('result' => 1));
        } else {
            echo json_encode(array('result' => 0));
        }
    }

    public function actionOff()
    {
        $id_bill     = Router::getParam('id_bill');
        $hb = new Bill($id_bill);
        $hb->is_paid = -1;
        if ($hb->save()) {
            Tools::redirect('/admin/bills');
        }
    }

    public function actionRefundAjax()
    {
        $billObject = new Bill(Router::getParam('id_bill'));
        $clientObject = new Client($billObject->client_id);

        if ($clientObject->isLoadedObject() && $billObject->isLoadedObject()) {
            $billObject->is_paid = -2;
            $clientObject->balance += $billObject->total;

            $clientObject->ref_rev -= $billObject->total;
            $clientObject->rev -= $billObject->total;

            if ($billObject->save() && $clientObject->save()) {
                $this->returnAjaxAnswer(1, 'Возврат выполнен!');
            } else {
                $this->returnAjaxAnswer(0, 'Ошибка сохранения!');
            }
        }
        $this->returnAjaxAnswer(0, 'Ошибка сохранения!');
    }

    public function actionRemoveAjax()
    {
        $bill = new Bill(Router::getParam('id_bill'));
        if ($bill->remove()) {
            echo json_encode(array('result' => 1));
        } else {
            echo json_encode(array('result' => 0));
        }
    }


}