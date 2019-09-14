<?php
/**
 * Created by PhpStorm.
 * Client: Viktor
 * Date: 13.06.15
 * Time: 22:53
 */

namespace admin;


use hosting\HostingAPI;
use model\HostingAccount;
use model\Bill;
use model\HostingPlan;
use model\HostingPlanDetail;
use model\HostingPlanExtendedPrice;
use model\HostingPlanParams;
use model\HostingServer;
use model\Languages;
use System\Db\Schema\Schema;
use System\Router;
use System\Tools;
use System\View\View;
use hosting\VestaAPI;

class PlansController extends FrontController
{

    public function actionIndex()
    {

        $view = $this->getView('empty-ajax-block.php');
        $this->layout->import('content', $view);
    }

    public function actionSetPositionsAjax()
    {

        if(Tools::rPOST('table_id') == 'plans'){
            foreach (Tools::rPOST('data') as $id => $position) {
                $HostingDetail                = new HostingPlan($id);
                $HostingDetail->sort_position = $position;
                $HostingDetail->save();
            }
        } else {
            foreach (Tools::rPOST('data') as $id => $position) {
                $HostingDetail                = new HostingPlanDetail($id);
                $HostingDetail->sort_position = $position;
                $HostingDetail->save();
            }
        }
    }

    public function actionHideShowRowAjax(){
        
        if(HB_DEMO_MODE ){
            $this->returnAjaxAnswer(0, 'Функция не доступна в демо режиме!');
        }
        $id_plan = Tools::rPOST('id');
        $HostingPlan = new HostingPlan($id_plan);
        if($HostingPlan->isLoadedObject()){
            $HostingPlan->hidden = Tools::rPOST('type', 0);
            $HostingPlan->save();
            $this->returnAjaxAnswer(1);
        }
    }

    public function actionIndexAjax()
    {
        $view = $this->getView('hosting/plan/list.php');
        $plan        = new HostingPlan();
        $filter = Tools::rPOST('filter');
        $vfilter = array();
        if (isset($filter) && $filter != '') {
            foreach ($filter as $field => $option) {

                $value = Tools::clearXSS($option['value']);
                $type  = isset($option['type']) ? $option['type'] : 'like';

                $vfilter[$field] = $value;

                if ($field && $value != '') {

                    if ($type == 'like') {
                        $plan->where($field, 'LIKE', '%' . $value . '%');
                    } else if ($type == 'equal') {
                        $plan->where($field, $value);
                    }


                }
            }
        }
        $order = Tools::rPOST('order');
        if ($order['field']) {

            $plan->order($order['field'], $order['type']);

        } else {
            $plan->order('sort_position', 'asc');
        }

        $view->filter = $vfilter;

        $view->plans = $plan->limit($this->from, $this->count)->getRows();
        $this->pagination($plan->lastQuery()->getRowsCount());
        $this->layout->import('content', $view);
    }

    public function actionAdd()
    {
        return $this->actionEdit();

        $view = $this->getView('hosting/plan/edit.php');
        $plan = new HostingPlan();
        if ($_POST) {
            $plan->name       = Tools::rPOST('name');
            $plan->panel_name = Tools::rPOST('panel_name');
            $plan->price      = Tools::rPOST('price');
            $plan->server_id = Tools::rPOST('server_id');
            if(Tools::rPOST('test_enabled')){
                $plan->test_days = Tools::rPOST('test_days');
            } else {
                $plan->test_days = 0;
            }

            $aservers        = Tools::rPOST('aviable_servers');
            if ($aservers) {
                foreach ($aservers as $k => $server_id) {
                    $server = new HostingServer($server_id);
                    if (HostingAPI::selectServer($server)->planExist($plan->panel_name) == HostingAPI::ANSWER_PLAN_NOT_EXIST) {
                        $view->error = array('message' => 'no_plan', 'server' => $server, 'plan' => $plan->panel_name);
                        unset($aservers[$k]);
                    }
                }
                $plan->aviable_servers = '|' . implode('|', $aservers) . '|';
            }
            if (!$view->error && $plan->save()) {
                Tools::redirect('/admin/plan/' . $plan->id);
            }

        }


        $packs = (object)[];
        $view->panel_plans = $packs;

        $view->plan = $plan;

        $view->servers = HostingServer::getInstance()->getRows();
        $this->layout->import('content', $view);
    }

    public function actionGetPlansAjax()
    {
        $server = new HostingServer(Tools::rPOST('server_id'));
        if (is_object($server) && $server->isLoadedObject()) {
            $plans = HostingAPI::selectServer($server)->getPlans();

            if(is_array($plans)) {
                echo json_encode(['result' => 1, 'data' => $plans]);
            } else {
                echo json_encode(['result' => 0, 'error' => $plans]);
            }
        } else {
            echo json_encode(['result' => 0, 'error' => 0]);
        }
    }

    public function actionAddAjax()
    {

    }

    public function actionEditAjax()
    {
        $this->actionEdit();
    }

    public function actionGetPlans()
    {
        $server = new HostingServer(Tools::rPOST('server_id'));
        $plans  = HostingAPI::selectServer($server)->getPlans();
        echo json_encode($plans);
    }
    
    public function actionEdit()
    {
        $view = $this->getView('hosting/plan/edit.php');

        $plan = new HostingPlan(Router::getParam('plan'));

        if (Tools::rPOST()) {
            $plan->name       = Tools::rPOST('name');
            if(Tools::rPOST('test_enabled')){
                $plan->test_days = Tools::rPOST('test_days', 0);
            } else {
                $plan->test_days = 0;
            }
            if ($plan->id && $plan->panel_name != Tools::rPOST('panel_name')) {
                $ha     = new HostingAccount();
                $orders = $ha->where('plan_id', $plan->id)->getRows();

                foreach ($orders as $order) {
                    $order = new HostingAccount($order);
                    $server = new HostingServer($order->server_id);
                    if (is_object($server) && $server->isLoadedObject()) {
                        HostingAPI::selectServer($server)->changePlan($order->login, Tools::rPOST('panel_name'));
                    }

                    // $plan->panel_name = Tools::rPOST('panel_name');
                }

            }

            $plan->panel_name = Tools::rPOST('panel_name');
            $plan->price = round(Tools::rPOST('price'), 2);




            $plan->server_id  = Tools::rPOST('server_id');
            $aservers         = Tools::rPOST('aviable_servers');
            if ($aservers) {
                foreach ($aservers as $k => $server_id) {
                    $server = new HostingServer($server_id);
                    if (HostingAPI::selectServer($server)->planExist($plan->panel_name) == HostingAPI::ANSWER_PLAN_NOT_EXIST) {
                        $view->error = array('message' => 'no_plan', 'server' => $server, 'plan' => $plan->panel_name);
                        unset($aservers[$k]);
                    }
                }
                $plan->aviable_servers = '|' . implode('|', $aservers) . '|';
            }

            $plan->save();

            /*Extend price params*/
           // if(Tools::rPOST()){
                $count = count(Tools::rPOST('prices_name', []));
                $prices_name = Tools::rPOST('prices_name');
                $prices_period = Tools::rPOST('prices_period');
                $prices_price = Tools::rPOST('prices_price');
                $prices_enabled = Tools::rPOST('prices_enabled');


                $HostingPrices = new HostingPlanExtendedPrice();
                $HostingPrices->where('plan_id', $plan->id)->removeRows();

                for($i =0; $i<$count; $i++){
                    $name = $prices_name[$i];
                    $period = $prices_period[$i];
                    $price = $prices_price[$i];
                    $enabled = isset($prices_enabled[$i]) ? $prices_enabled[$i] : 0;

                    $HostingPrices = new HostingPlanExtendedPrice();
                    $HostingPrices->plan_id = $plan->id;
                    $HostingPrices->name = $name;
                    $HostingPrices->period = $period;
                    $HostingPrices->period_type = 2;
                    $HostingPrices->price = round($price, 2);
                    $HostingPrices->enabled = $enabled ==1 ? $enabled : 0;
                    $HostingPrices->save();

                    // $extended_prices[] = array('name' => $name, 'period' => $period, 'price' => $price, 'enabled' => $enabled);
                }

                //$plan->extended_prices = json_encode($extended_prices);



        //    }


            $params_ids     = Tools::rPOST('params_ids', array());
            $params_values  = Tools::rPOST('param_values', array());
            $PlanDetail = new HostingPlanDetail();
            $PlanDetail->where('plan_id', $plan->id)->removeRows();

            $position = 0; $i=0;
            foreach($params_ids as $param_id){
                $PlanDetail = new HostingPlanDetail();
                $PlanDetail->plan_id = $plan->id;
                $PlanDetail->param_id = $param_id;
                $PlanDetail->value = $params_values[$i];
                $PlanDetail->sort_position = $position;
                $PlanDetail->save();
                $position++;
                $i++;
            }

                Tools::redirect('admin/plan/'.$plan->id);

        }

        $server = new HostingServer($plan->server_id);

        if (is_object($server) && $server->isLoadedObject()) {
            $packs = HostingAPI::selectServer($server)->getPlans();
        } else {
            $packs = (object)[];
        }
        $view->panel_plans = $packs;

        $HostingPrices = new HostingPlanExtendedPrice();
        $prices = $HostingPrices->where('plan_id', $plan->id)->getRows();

        $view->prices = $prices;
        $view->plan = $plan;

        $view->servers = HostingServer::getInstance()->getRows();





        $hpd           = new HostingPlanDetail();
       // $details       = $hpd->select('*')->select(HostingPlanParams::getInstance(), 'name')->where('plan_id', $plan->id)->join(HostingPlanParams::getInstance(), 'param_id', 'id')->getRows();
        $details = array();
        if($plan->isLoadedObject()) {

            $details = HostingPlanParams::factory()
                ->select('*')
                ->select(HostingPlanDetail::factory(), 'value')
                ->join(HostingPlanDetail::factory(), 'id', 'param_id')
                ->where(HostingPlanDetail::factory(), 'plan_id', $plan->id)
                ->order(HostingPlanDetail::factory(), 'sort_position')
                ->getRows();
        }

        // print_r($details);
        $view->details = ($details);

        $view->all_details = HostingPlanParams::factory()->getRows();
        $this->layout->import('content', $view);
    }

    public function actionAddProperty($ajax = false)
    {
        $id_property    = Router::getParam('id_property', 0);

        $id_plan      = Router::getParam('id_plan');
        $plan         = new HostingPlan($id_plan);
        $view = $this->getView('hosting/plan/property/add.php');
        $view->plan   = $plan;
        $param        = new HostingPlanParams();
        $view->params = $param->getRows();
        $messages     = array();
        $detail         = new HostingPlanDetail($id_property);
        $view->property = $detail;
        // print_r($detail);
        if (Tools::rPOST()) {
            // echo $id_property;

            if ($id_property) {
                $row = 0;
            } else {
                $row = $detail->where('plan_id', $plan->id)->where('param_id', Tools::rPOST('id_param'))->getRow();
            }
            if (!$row) {
                $messages[]       = array('type' => 'success', 'name' => 'property_save');
                $detail->plan_id  = $plan->id;
                $detail->param_id = Tools::rPOST('id_param');
                $detail->value    = Tools::rPOST('param_value');
                if ($detail->save()) {
                    exit(json_encode(['result' => 1, 'message' =>

                        Languages::translate('Успешно сохранено!', 'admin/default', 'popup-messages')
                    ]));
                } else {
                    exit(json_encode(['result' => 0, 'message' => 'Возникла ошибка!']));
                }

            } else {
                if ($ajax) {
                    exit(json_encode(['result' => 0, 'message' =>

                        Languages::translate('Свойство уже существует!', 'admin/default', 'popup-messages')
                    ]));
                } else {
                    $messages[] = array('type' => 'error', 'name' => 'property_isset');
                }
            }
        }
        $view->messages = $messages;


        $this->layout->import('content', $view);
    }

    public function actionRemovePropertyAjax()
    {
        $detail  = new HostingPlanDetail(Router::getParam('id_detail'));

        $detail->remove();
        echo json_encode(['result' => 1]);
    }

    public function actionParamsList()
    {
        $view = $this->getView('empty-ajax-block.php');
        $this->layout->import('content', $view);

    }

    public function actionParamsListAjax()
    {
        $view = $this->getView('hosting/plan/property/list.php');
        $property         = new HostingPlanParams();


        $filter  = Tools::rPOST('filter');
        $vfilter = array();
        if (isset($filter) && $filter != '') {
            foreach ($filter as $field => $option) {

                $value = Tools::clearXSS($option['value']);
                $type  = isset($option['type']) ? $option['type'] : 'like';

                $vfilter[$field] = $value;

                if ($field && $value != '') {

                    if ($type == 'like') {
                        $property->where($field, 'LIKE', '%' . $value . '%');
                    } else if ($type == 'equal') {
                        $property->where($field, $value);
                    }


                }
            }
        }
        $order = Tools::rPOST('order');
        if ($order['field']) {

            $property->order($order['field'], $order['type']);

        } else {
            $property->order('id', 'desc');
        }

        $view->filter = $vfilter;

        $view->properties = $property->limit($this->from, $this->count)->getRows();
        $this->pagination($property->lastQuery()->getRowsCount());
        $this->layout->import('content', $view);
    }

    public function actionEditParam($ajax = false)
    {
        $view = $this->getView('hosting/plan/param/edit.php');

        $param = new HostingPlanParams(Router::getParam('id_param'));

        if ($_POST) {
            $param->name = Tools::rPOST('name');
            $param->desc = Tools::rPOST('desc');
            if ($param->save()) {
                if (!$ajax) {
                    Tools::redirect('/admin/plan/params/');
                } else {
                    exit(json_encode(['result' => 1, 'message' =>
                        Languages::translate('Успешно сохранено!', 'admin/default', 'popup-messages')
                    ]));
                }
            }
        }
        $view->param = $param;
        $this->layout->import('content', $view);
    }

    public function actionRemoveParamAjax()
    {
        $param = new HostingPlanParams(Router::getParam('id_param'));
        $hd    = new HostingPlanDetail();
        $hd->where('param_id', $param->id)->removeRows();

        echo json_encode(['result' => $param->remove()]);

    }

    public function actionRemoveParam()
    {
        $param = new HostingPlanParams(Router::getParam('id_param'));
        $hd    = new HostingPlanDetail();
        $hd->where('param_id', $param->id)->removeRows();

        $param->remove();

        Tools::redirect('/admin/plan/params');
    }

    public function actionRemove()
    {
        $hp = new HostingPlan(Router::getParam('plan'));
        $hp->remove();
        Tools::redirect('/admin/plans');
    }

    public function actionEditParamAjax()
    {
        $this->actionEditParam(true);
    }

    public function actionAddParamAjax()
    {
        $this->actionEditParam(true);
    }

    public function actionAddPropertyAjax()
    {
        $this->actionAddProperty(true);
    }

} 