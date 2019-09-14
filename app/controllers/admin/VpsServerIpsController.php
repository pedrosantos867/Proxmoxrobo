<?php

namespace admin;

use model\Languages;
use model\VpsServer;
use model\VpsServerIp;
use System\Exception;
use System\Router;
use System\Tools;
use vps\VPSAPI;

class VpsServerIpsController extends FrontController{

    public function actionListAjax()
    {
        $view = $this->getView('vps/server/ip/list.php');

        if(!Tools::rGET('server_id')){
            Tools::display404Error();
        }

        $VpsIp = new VpsServerIp();

        $filter = Tools::rPOST('filter');
        $vfilter = array();
        if (isset($filter) && $filter != '') {
            foreach ($filter as $field => $option) {

                $value = Tools::clearXSS($option['value']);
                $type  = isset($option['type']) ? $option['type'] : 'like';

                $vfilter[$field] = $value;

                if ($field && $value != '') {

                    if ($type == 'like') {
                        $VpsIp->where($field, 'LIKE', '%' . $value . '%');
                    } else if ($type == 'equal') {
                        $VpsIp->where($field, $value);
                    }


                }
            }
        }
        $order = Tools::rPOST('order');
        if ($order['field']) {

            $VpsIp->order($order['field'], $order['type']);

        } else {
            $VpsIp->order('id', 'desc');
        }

        $view->filter = $vfilter;
        $ips      = $VpsIp->limit($this->from, $this->count)->where('server_id', Tools::rGET('server_id'))->getRows();
        $view->ips = $ips;
        $this->layout->import('content', $view);
    }

    public function actionEdit($ajax=false){
        $view = $this->getView('vps/server/ip/edit.php');
        $ip = new VpsServerIp(Tools::rGET('ip_id'));
        if ($ip->isLoadedObject()) {
            $serverObject = new VpsServer($ip->server_id);
        } else {
            $serverObject = new VpsServer(Tools::rRequest('server_id'));
        }

        $view->server = $serverObject;

        if (Tools::rPOST()) {
            $ip->vlan  = Tools::rPOST('vlan');
            $ip->ip  = Tools::rPOST('ip');
            $ip->gateway  = Tools::rPOST('gateway');
            $ip->mask  = Tools::rPOST('mask');
            $ip->type = Tools::rPOST('type', 0);

            $ip->server_id = $serverObject->id;

            if ($ip->save()) {
                if ($ajax) {
                    echo json_encode(array('result' => 1, 'message' =>
                        Languages::translate('IP успешно сохранен!', 'admin/default', 'popup-messages')

                    ));
                    exit;
                } else {
                    Tools::redirect('/admin/vps-ips');
                }
            }

        }

        $view->ip = $ip;
        $this->layout->import('content', $view);
    }

    public function actionEditAjax(){
        $this->actionEdit(true);
    }


    public function actionRemoveAjax(){
        $ip_id = Tools::rGET('ip_id');
        $VpsServerIp = new VpsServerIp($ip_id);
        $VpsServerIp->remove();
        echo json_encode(array('result' => 1));

    }

}