<?php
/**
 * Created by PhpStorm.
 * Client: Viktor
 * Date: 20.06.15
 * Time: 14:10
 */

namespace admin;


use admin\FrontController;
use hosting\HostingAPI;
use model\HostingServer;
use model\Languages;
use System\Router;
use System\Tools;
use System\View\View;

class ServerController extends FrontController
{
    public function actionList()
    {

        $view = $this->getView('empty-ajax-block.php');
        $this->layout->import('content', $view);
    }

    public function actionListAjax()
    {
        $view = $this->getView('hosting/server/list.php');
        $Server = new HostingServer;
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

    public function actionEditAjax()
    {
        $this->actionEdit(true);
    }

    public function actionEdit($ajax = false)
    {
        $view = $this->getView('hosting/server/edit.php');
        $server = new HostingServer(Router::getParam('id_server'));

        if (Tools::rPOST()) {
            $server->name  = Tools::rPOST('name');

            $host = Tools::rPOST('host');
            if(substr($host, strlen($host)-1, 1) == '/'){
                $host = substr($host,0, strlen($host)-1);

            }
            $server->host  = $host;


            $server->login = Tools::rPOST('login');
            $server->pass  = Tools::rPOST('pass');
            $server->panel = Tools::rPOST('panel');
            $server->ip    = Tools::rPOST('ip');

            if(HB_DEMO_MODE && $ajax){
                $this->returnAjaxAnswer(0, 'Функция не доступна в демо режиме!');
            } elseif(HB_DEMO_MODE){
                Tools::redirect('/admin/servers');
            }

            if ($server->save()) {
                if ($ajax) {

                    echo json_encode(array('result' => 1, 'message' =>
                        Languages::translate('Сервер успешно сохранен!', 'admin/default', 'popup-messages')

                    ));
                    exit;
                } else {
                    Tools::redirect('/admin/servers');
                }
            }

        }

        $view->server = $server;
        $this->layout->import('content', $view);

    }


    public function actionCheckAjax()
    {
        $id_server = Router::getParam('id_server');
        $server    = new HostingServer($id_server);
        $connect = HostingAPI::selectServer($server)->checkConnection();

         if ($connect == HostingAPI::ANSWER_OK) {
          $this->returnAjaxAnswer(1,'Соединение установленно!');
         } elseif ($connect == HostingAPI::ANSWER_AUTH_ERROR) {
             $this->returnAjaxAnswer(0,  'Ошибка авторизации!');
        } else {
             $this->returnAjaxAnswer(0,  'Сервер не отвечает!');
         }

    }

    public function actionRemoveAjax()
    {
        if(HB_DEMO_MODE ){
            $this->returnAjaxAnswer(0, 'Функция не доступна в демо режиме!');
        }

        $server = new HostingServer(Router::getParam('id_server'));
        $server->remove();
        echo json_encode(array('result' => 1));
    }
    public function actionRemove()
    {
        if(HB_DEMO_MODE ){
            Tools::redirect('/admin/servers/');
        }

        $server = new HostingServer(Router::getParam('id_server'));
        $server->remove();
        Tools::redirect('/admin/servers/');

    }

    public function actionHideShowRowAjax(){
        if(HB_DEMO_MODE ){
            $this->returnAjaxAnswer(0, 'Функция не доступна в демо режиме!');
        }

        $id_server = Tools::rPOST('id');
        $HostingServer = new HostingServer($id_server);
        if($HostingServer->isLoadedObject()){
            $HostingServer->hidden = Tools::rPOST('type', 0);
            $HostingServer->save();
            $this->returnAjaxAnswer(1);
        }
    }
} 