<?php
namespace admin;

use hosting\HostingAPI;
use hosting\VestaAPI;
use model\HostingAccount;
use model\Bill;
use model\HostingPlan;
use model\HostingServer;
use model\Client;
use model\Languages;
use System\Router;
use System\Tools;
use System\Validation;
use System\View\View;

class OrdersController extends FrontController
{
    public function actionIndex()
    {
        $view = $this->getView('empty-ajax-block.php');
        $this->layout->import('content', $view);
    }

    public function actionIndexAjax()
    {
        $view = $this->getView('hosting/order/list.php');
        $ha           = new HostingAccount();
        $ha->select('*')
            ->select(HostingPlan::getInstance(), 'name')
            ->select(HostingPlan::getInstance(), 'price')
            ->select(HostingServer::getInstance(), 'name', 'server_name')
            ->select(HostingServer::getInstance(), 'host', 'server_host')
            ->select(HostingServer::getInstance(), 'ip', 'server_ip')
            ->select(Client::getInstance(), 'name', 'user_name')
            ->join(HostingPlan::getInstance(), 'plan_id', 'id')
            ->join(HostingServer::getInstance(), 'server_id', 'id')
            ->join(Client::getInstance(), 'client_id', 'id')
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
                            $ha->where(Client::getInstance(), $field, 'LIKE', '%' . $value . '%');
                        } else {
                            $ha->where($field, 'LIKE', '%' . $value . '%');
                        }
                    } else if ($type == 'equal') {

                        $ha->where($field, $value);

                    }


                }
            }
        }
        $view->filter = $vfilter;
        $order        = Tools::rPOST('order');

        if ($order['field']) {
            if ($order['field'] == 'name') {
                $ha->order(Client::getInstance(), $order['field'], $order['type']);
            } else {
                $ha->order($order['field'], $order['type']);
            }
        } else {
            $ha->order('id', 'desc');
        }

        $view->orders = $ha->getRows();


        $this->pagination($ha->lastQuery()->getRowsCount());

        $this->layout->import('content', $view);

    }

    public function actionRemoveAjax()
    {
        $id_order = Router::getParam('order');
        $ho       = new HostingAccount($id_order);
        $ho->remove();
        echo json_encode(['result' => 1, 'message' =>
            Languages::translate('Заказ удален!', 'admin/default', 'popup-messages')
        ]);
    }

    public function actionEditAjax()
    {
        switch (Tools::rPOST('type')) {
            case 'get_servers':
                $hp      = new HostingPlan(Tools::rPOST('plan_id'));
                $servers = $hp->getServers();

                $hs = new HostingServer();

                foreach ($servers as $server_id) {
                    $hs->where('id', $server_id)->whereOr();
                }

                $servers = $hs->getRows();


                echo json_encode($servers);
                break;
        }
    }

    public function actionEdit()
    {

        $view = $this->getView('hosting/order/edit.php');

        $id_order = Router::getParam('id_order');
        $order    = new HostingAccount($id_order);


        if (Tools::rPOST()) {
            $server = new HostingServer(Tools::rPOST('server_id'));
            $new_plan = new HostingPlan(Tools::rPOST('plan_id'));

            if (
                Tools::rPOST('plan_id')
                && Tools::rPOST('user_id')
                && Tools::rPOST('server_id')
            ) {


                if (!$order->id) {

                    $order->server_id       = $server->id;
                    $order->login           = Tools::rPOST('login');
                    $client                 = new Client(Tools::rPOST('user_id'));
                    if ($client->isLoadedObject()) {
                        if(Tools::rPOST('import_flag')){
                            $order->client_id   = Tools::rPOST('user_id');
                            $order->plan_id     = Tools::rPOST('plan_id');
                            $order->date        = date('Y-m-d');
                            $order->active      = 1;
                            $order->paid_to     = date('Y-m-d', strtotime(Tools::rPOST('paid_to')));

                            if ($order->save()) {
                                Tools::redirect('/admin/order/' . $order->id);
                            } else {
                                $this->errors[] = 'system_error';
                            }

                        } else {
                            $user_names = explode(' ', $client->name);
                            $req = HostingAPI::selectServer($server)->createUser(array(
                                'username' => Tools::rPOST('login'),
                                'password' => Tools::rPOST('pass'),
                                'domain' => Tools::rPOST('domain'),
                                'email' => $client->email,
                                'package' => $new_plan->panel_name,
                                'first_name' => isset($user_names[0]) ? $user_names[0] : 'User',
                                'last_name'  => isset($user_names[1]) ? $user_names[1] : 'User',
                            ));

                            if ($req == HostingAPI::ANSWER_OK) {
                                HostingAPI::selectServer($server)->suspendUser(Tools::rPOST('login'));
                                $order->client_id   = Tools::rPOST('user_id');
                                $order->plan_id     = Tools::rPOST('plan_id');
                                $order->date        = date('Y-m-d');

                                if ($order->save()) {
                                    Tools::redirect('/admin/order/' . $order->id);
                                } else {
                                    $this->errors[] = 'system_error';
                                }

                            } else {
                                if ($req === HostingAPI::ANSWER_USER_ALREADY_EXIST) {
                                    $this->errors[] = 'user_isset';
                                } elseif ($req == HostingAPI::ANSWER_USER_NAME_NOT_VALID) {
                                    $this->errors[] = 'login_not_valid';
                                } elseif ($req == HostingAPI::ANSWER_USER_PASSWORD_NOT_VALID) {
                                    $this->errors[] = 'pass_not_valid';
                                } elseif ($req == HostingAPI::ANSWER_CONNECTION_ERROR) {
                                    $this->errors[] = 'no_connection';
                                } else {
                                    echo $req;
                                    $this->errors[] = 'system_error';
                                }
                            }
                        }
                    }

                }


                if ($order->id && $order->plan_id != $new_plan->id) {

                    $server = new HostingServer($order->server_id);
                    $req_change_plan = HostingAPI::selectServer($server)->changePlan($order->login, $new_plan->panel_name);

                    if ($req_change_plan == HostingAPI::ANSWER_OK) {

                        $order->client_id       = Tools::rPOST('user_id');
                        $order->plan_id         = Tools::rPOST('plan_id');
                        $order->date            = Tools::rPOST('date');
                        $order->save();

                    } else {
                        if ($req_change_plan == HostingAPI::ANSWER_CONNECTION_ERROR) {
                            $this->errors[] = 'no_connection';
                        } else if ($req_change_plan == HostingAPI::ANSWER_USER_NOT_EXIST) {
                            $this->errors[] = 'user_not_exist';
                        } else if ($req_change_plan == HostingAPI::ANSWER_PLAN_NOT_EXIST) {
                            $this->errors[] = 'plan_not_exist';
                        }
                    }
                } else {
                    if ($order->id) {
                        $order->client_id = Tools::rPOST('user_id');
                        $order->paid_to   = date('Y-m-d', strtotime(Tools::rPOST('paid_to')));
                        $order->save();
                    }
                }
            } else {

                if (!Tools::rPOST('user_id') || !Validation::isInt(Tools::rPOST('user_id'))) {
                    $this->errors[] = 'field_user_no_valid';
                }
                if (!Tools::rPOST('server_id') || !Validation::isInt((Tools::rPOST('server_id')))) {
                    $this->errors[] = 'field_server_no_valid';
                }
                if (!Tools::rPOST('plan_id') || !Validation::isInt((Tools::rPOST('plan_id')))) {
                    $this->errors[] = 'field_plan_no_valid';
                }
            }
        }


        $view->order   = $order;
        $view->clients = Client::getInstance()->getRows();
        if (!$order->id) {
            $view->plans = HostingPlan::factory()->getRows();
            //   $view->servers = HostingServer::factory()->getRows();

        } else {
            $view->plans = HostingPlan::factory()->where('aviable_servers', 'LIKE', '%|' . $order->server_id . '|%')->getRows();
        }

        if ($id_order) {
            $view->server = new HostingServer($order->server_id);
        }

        $this->layout->import('content', $view);
    }

    public function actionValidateAjax()
    {
        $field = Tools::rPOST('field');
        $val   = Tools::rPOST('value');

    }


    public function actionGetClientsAjax()
    {
        $Client = new Client();

        $clients = $Client->where('username', 'LIKE', '%' . Tools::rPOST('q') . '%')
            ->whereOr()
            ->where('phone', 'LIKE', '%' . Tools::rPOST('q') . '%')
            ->whereOr()
            ->where('email', 'LIKE', '%' . Tools::rPOST('q') . '%')
            ->getRows();

        echo json_encode(['results' => $clients]);
    }

}