<?php

namespace admin;

use domain\DomainAPI;
use model\Bill;
use model\Client;
use model\Domain;
use model\DomainOrder;
use model\DomainOwner;
use model\DomainRegistrar;
use model\Languages;
use System\Config;
use System\Db\Schema\Schema;
use System\Db\Schema\Table;
use System\Exception;
use System\Router;
use System\Tools;
use System\View\View;

class DomainOrdersController extends FrontController
{



    public function actionList()
    {
        $view = $this->getView('empty-ajax-block.php');
        $this->layout->import('content', $view);
    }

    public function actionListAjax()
    {
        $this->layout->import('content', $v = $this->getView('domain/order/list.php'));
        $DomainOrder = new DomainOrder;
        $DomainOrder
            ->select('*')
            ->select(DomainOwner::factory(),'fio', 'owner')
            ->select(DomainOwner::factory(), 'organization_name', 'owner_organization')
            ->select(Client::factory(), 'name', 'client_name')
            ->join(Client::factory(), 'client_id', 'id')
            ->join(DomainOwner::factory(), 'owner_id', 'id');

        $order = Tools::rPOST('order');
        if ($order['field'] == 'client_name' && $order['type']) {
            $DomainOrder->order(Client::factory(), 'name', $order['type']);
        } else
        if ($order['field'] && $order['type']) {
            $DomainOrder->order($order['field'], $order['type']);
        } else{
            $DomainOrder->order('id', 'desc');
        }

        $filter  = Tools::rPOST('filter');
        $vfilter = array();
        if (isset($filter) && $filter != '') {
            foreach ($filter as $field => $option) {


                $value           = Tools::clearXSS($option['value']);
                $type            = isset($option['type']) ? $option['type'] : 'like';
                $vfilter[$field] = $value;
                if($field == 'client_name'){
                    $DomainOrder->where(Client::factory(), 'name', 'LIKE', '%' . $value . '%');
                }
                elseif ($field && $value != '') {
                    if ($type == 'like') {
                        $DomainOrder->where($field, 'LIKE', '%' . $value . '%');
                    } else if ($type == 'equal') {
                        $DomainOrder->where($field, $value);
                    }
                }
            }
        }
        $DomainOrder->limit($this->from, $this->count);
        $v->filter = $vfilter;
        $v->orders = $DomainOrder->getRows();


        $this->pagination($DomainOrder->lastQuery()->getRowsCount());

    }

    public function actionChangeOwnerAjax(){
        $view = $this->getView('domain/order/change-owner.php');
        $this->carcase->import('content', $view);

        if(Tools::rPOST('id_order')){
            $DomainOrder = new DomainOrder(Tools::rPOST('id_order'));
            $DomainOwner = new DomainOwner(Tools::rPOST('owner_id'));


            if(!$DomainOwner->isLoadedObject()){
                $this->returnAjaxAnswer(0, 'Возникла ошибка.');
            }

            if($DomainOrder->owner_id == Tools::rPOST('owner_id')){
                $this->returnAjaxAnswer(0, 'Этот контакт уже является владельцем этого домена.');
            }

            $DomainOrder->owner_id = Tools::rPOST('owner_id');

            if($DomainOrder->status == 1){
                $contact_id = DomainAPI::getRegistrar($DomainOrder->registrant_id)->changeContactPerson($DomainOrder, $DomainOwner);

                if($contact_id === DomainAPI::ANSWER_CONTACT_CREATE_FAIL){
                    $this->returnAjaxAnswer(0, 'Возникла ошибка смены владельца домена.');
                }

                $DomainOrder->nic_hdl = $contact_id;

            }


            if($DomainOrder->save()) {
                $this->returnAjaxAnswer(1, 'Владелец успешно изменен');
            }

            $this->returnAjaxAnswer(0, 'Возникла ошибка');
        }

        $DomainOrder = new DomainOrder(Tools::rGET('id_order'));
        $view->owners = DomainOwner::factory()->where('client_id', $DomainOrder->client_id)->getRows();
    }

    public function actionReregAjax(){
        $id_order = Tools::rGET('id_order');
        $DomainOrder = new DomainOrder($id_order);
        $Owner       = new DomainOwner($DomainOrder->owner_id);

        $r = DomainAPI::getRegistrar($DomainOrder->registrant_id)->registerDomain(
            $DomainOrder,
            $Owner
        );

        if($r == DomainAPI::ANSWER_DOMAIN_REG_SUCCESS) {
            $DomainOrder->status = 1;
            $DomainOrder->save();
            $this->returnAjaxAnswer(1, 'Домен успешно зарегистрирован');
        }

        $this->returnAjaxAnswer(0, 'Ошибка регистрации');



    }

    public function actionChangeNSAjax()
    {
        $id_order = Tools::rGET('id_order');

        $DomainOrder = new DomainOrder($id_order);



        if (Tools::rPOST()) {

            $old_ns = array(
                $DomainOrder->dns1 => $DomainOrder->ip1,
                $DomainOrder->dns2 => $DomainOrder->ip2,
                $DomainOrder->dns3 => $DomainOrder->ip3,
                $DomainOrder->dns4 => $DomainOrder->ip4,
            );

            $DomainOrder->dns1 = Tools::rPOST('dns1');
            $DomainOrder->dns2 = Tools::rPOST('dns2');
            $DomainOrder->dns3 = Tools::rPOST('dns3');
            $DomainOrder->dns4 = Tools::rPOST('dns4');



            $res = DomainAPI::getRegistrar($DomainOrder->registrant_id)->changeNS($DomainOrder, $old_ns);

            if ($res == DomainAPI::ANSWER_DOMAIN_CHANGE_NS_SUCCESS) {
                $DomainOrder->save();
                exit(json_encode(['result' => 1, 'message' =>
                    Languages::translate('NS сервера были изменены', 'admin/default', 'popup-messages')
                ]));
            }

            exit(json_encode(['result' => 0, 'message' =>
                Languages::translate('Не удалось сменить NS сервера, возможно домен находится на стадии обработки, попробуйте повторить операцию немножко позже!', 'admin/default', 'popup-messages')
                ]));
        }

        $this->carcase->import('content', $v = $this->getView('domain/order/change-ns.php'));
        $v->order = $DomainOrder;

    }

    public function actionProlongAjax()
    {
        $id_order    = Tools::rGET('id_order');
        $DomainOrder = new DomainOrder($id_order);

        $Domain = new Domain($DomainOrder->domain_id);

        $Bill                  = new Bill();
        $Bill->price           = $Domain->extension_price;
        $Bill->total           = $Domain->extension_price;
        $Bill->type            = Bill::TYPE_DOMAIN_PROLONG;
        $Bill->domain_order_id = $DomainOrder->id;
        $Bill->client_id       = $this->client->id;
        $Bill->save();


    }

    public function actionProlong()
    {
        $id_order    = Tools::rGET('id_order');
        $DomainOrder = new DomainOrder($id_order);
        $this->carcase->import('content', $v = $this->getView('domain/order/prolong.php'));
        $v->order  = $DomainOrder;
        $v->domain = new Domain($DomainOrder->domain_id);
    }

    public function actionRemoveAjax()
    {
        $id_order    = Tools::rGET('id_order');
        $DomainOrder = new DomainOrder($id_order);
        $DomainOrder->remove();
        exit(json_encode(['result' => 1, 'message' => 'Заказ успешно отменен']));
    }
}