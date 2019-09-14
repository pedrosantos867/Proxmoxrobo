<?php

namespace admin;

use model\Languages;
use model\VpsServer;
use System\Exception;
use System\Router;
use System\Tools;
use vps\VPSAPI;

class VpsServersController extends FrontController{

    public function actionListAjax()
    {
        $view = $this->getView('vps/server/list.php');
        $Server = new VpsServer();
        $filter = Tools::rPOST('filter');
        $vfilter = array();
        if (isset($filter) && $filter != '') {
            foreach ($filter as $field => $option) {

                $value = Tools::clearXSS($option['value']);
                $type  = isset($option['type']) ? $option['type'] : 'like';

                $vfilter[$field] = $value;

                if ($field && $value != '') {

                    if ($type == 'like') {
                        $Server->where($field, 'LIKE', '%' . $value . '%');
                    } else if ($type == 'equal') {
                        $Server->where($field, $value);
                    }


                }
            }
        }
        $order = Tools::rPOST('order');
        if ($order['field']) {

            $Server->order($order['field'], $order['type']);

        } else {
            $Server->order('id', 'desc');
        }

        $view->filter = $vfilter;
        $servers      = $Server->limit($this->from, $this->count)->getRows();
        $view->servers = $servers;
        $this->layout->import('content', $view);
    }

    public function actionEdit($ajax=false){
        $view = $this->getView('vps/server/edit.php');
        $server = new VpsServer(Tools::rGET('server_id'));

        if (Tools::rPOST()) {
            $server->name  = Tools::rPOST('name');
            $server->host  = Tools::rPOST('host');
            $server->username = Tools::rPOST('username');
            $server->password  = Tools::rPOST('password');
            $server->type = Tools::rPOST('type');

            if ($server->save()) {
                if ($ajax) {
                    echo json_encode(array('result' => 1, 'message' =>
                        Languages::translate('Сервер успешно сохранен!', 'admin/default', 'popup-messages')

                    ));
                    exit;
                } else {
                    Tools::redirect('/admin/vps-servers');
                }
            }

        }

        $view->server = $server;
        $this->layout->import('content', $view);
    }

    public function actionEditAjax(){
        $this->actionEdit(true);
    }

    public function actionHideShowRowAjax(){
        if(HB_DEMO_MODE ){
            $this->returnAjaxAnswer(0, 'Функция не доступна в демо режиме!');
        }
        $id_server = Tools::rPOST('id');
        $VpsServer = new VpsServer($id_server);
        if($VpsServer->isLoadedObject()){
            $VpsServer->hidden = Tools::rPOST('type', 0);
            $VpsServer->save();
            $this->returnAjaxAnswer(1);
        }
    }

    public function actionRemoveAjax(){
        $server_id = Tools::rGET('server_id');
        $VpsServer = new VpsServer($server_id);
        $VpsServer->remove();
        echo json_encode(array('result' => 1));

    }

    public function actionCheckAjax()
    {
        $id_server = Tools::rGET('server_id');
        $server    = new VpsServer($id_server);
        $connect   = 0;
        try {
            $connect = VPSAPI::selectServer($server)->checkConnection();
        } catch(Exception $e){

        }

        if ($connect && $connect->code == VPSAPI::ANSWER_CONNECTION_OK) {
            $this->returnAjaxAnswer(1, 'Соединение установленно!');
        } else {
            $this->returnAjaxAnswer(0, 'Сервер не отвечает!');
        }

    }
}