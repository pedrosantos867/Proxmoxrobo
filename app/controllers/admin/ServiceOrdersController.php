<?php
namespace admin;

use model\Client;
use model\Languages;
use model\Service;
use model\ServiceCategories;
use model\ServiceField;
use model\ServiceFieldValue;
use model\ServiceOrder;
use System\Notifier;
use System\Tools;

class ServiceOrdersController extends FrontController{

    public function actionListAjax()
    {
        $ServiceOrder =  new ServiceOrder();
        $ServiceOrder->select('*');
        $ServiceOrder->select(Service::factory(), 'name');
        $ServiceOrder->select(Service::factory(), 'category_id');
        $ServiceOrder->select(ServiceCategories::factory(), 'name', 'category');
        $ServiceOrder->select(Client::factory(), 'name', 'user_name');
        $ServiceOrder->join(Client::factory(), 'client_id', 'id');
        $ServiceOrder->join(Service::factory(), 'service_id', 'id');
        $ServiceOrder->join(Service::factory(), ServiceCategories::factory(), 'category_id', 'id');
        $ServiceOrder->order('id', 'desc');

        $this->layout->import('content', $v = $this->getView('service/order/list.php'));


        $v->categories = ServiceCategories::factory()->getRows();

        $filter = Tools::rPOST('filter');
        $vfilter = array();
        if (isset($filter) && $filter != '') {
            foreach ($filter as $field => $option) {

                $value = Tools::clearXSS($option['value']);
                $type = isset($option['type']) ? $option['type'] : 'like';

                $vfilter[$field] = $value;

                if ($field && $value != '') {
                    if ($field == 'category_id') {
                        $ServiceOrder->where(ServiceCategories::factory(), 'id', $value);
                    } else if ($field == 'name') {
                        $ServiceOrder->where(Client::factory(), 'name', 'LIKE', '%' . $value . '%');
                    } else {
                        if ($type == 'like') {
                            $ServiceOrder->where($field, 'LIKE', '%' . $value . '%');
                        } else if ($type == 'equal') {
                            $ServiceOrder->where($field, $value);
                        }
                    }

                }
            }
        }
        $order = Tools::rPOST('order');
        if ($order['field']) {

            $ServiceOrder->order($order['field'], $order['type']);

        } else {
            $ServiceOrder->order('id', 'desc');
        }
        $v->orders = $ServiceOrder->limit($this->from, $this->count)->getRows();
        $v->filter = $vfilter;

        $this->pagination($ServiceOrder->lastQuery()->getRowsCount());
    }

    public function actionShowAjax(){
        $view = $this->getView('service/order/show.php');

        $ServiceOrder = new ServiceOrder(Tools::rGET('id_order'));
        $view->order = $ServiceOrder;

        $view->service = new Service($ServiceOrder->service_id);
        $ServiceFieldValue = new ServiceFieldValue();

        $view->fields = $ServiceFieldValue
            ->select('*')
            ->select(ServiceField::factory(), 'name')
            ->join(ServiceField::factory(), 'field_id', 'id')
            ->where('order_id', $ServiceOrder->id)->getRows();


        $this->carcase->import('content', $view);
    }

    public function actionInfoAjax(){
        $view = $this->getView('service/order/info.php');

        $ServiceOrder = new ServiceOrder(Tools::rGET('id_order'));

        if(Tools::rPOST()){

            $old_message=$ServiceOrder->admin_info ;

            $ServiceOrder->admin_info = Tools::rPOST('admin_info');

            if($ServiceOrder->save()) {
                $Client = new Client($ServiceOrder->client_id);
                $Service = new Service($ServiceOrder->service_id);

                if(!$old_message && Tools::rPOST('admin_info')){
                    Notifier::NewMessageToServiceOrder($Client, $ServiceOrder, $Service);
                } elseif($old_message && Tools::rPOST('admin_info')&& $old_message != Tools::rPOST('admin_info')){
                    Notifier::ChangeMessageToServiceOrder($Client, $ServiceOrder, $Service);
                }

                $this->returnAjaxAnswer(1, 'Информация сохранена');
            }
            $this->returnAjaxAnswer(0, 'Ошибка сохранения');
        }

        $view->order = $ServiceOrder;

        $this->carcase->import('content', $view);
    }

    public function actionRemoveAjax(){
        $ServiceOrder = new ServiceOrder(Tools::rGET('id_order'));
        if($ServiceOrder->remove()){
            $this->returnAjaxAnswer(1, 'Заказ успешно удален');
        }
        $this->returnAjaxAnswer(0, 'Возникла ошибка при удалении заказа');
    }

    public function actionEditAjax()
    {
        $view = $this->getView('service/order/edit.php');

        $service_id = Tools::rGET('id_service_order');
        $ServiceOrder = new ServiceOrder($service_id);


        $order = $ServiceOrder;
        if (Tools::rPOST()) {

            $order->paid_to = Tools::rPOST('paid_to');

            switch (Tools::rPOST('status')) {
                case "10":
                    $order->status = 1;
                    $order->type = 0;
                    break;
                case "00":
                    $order->status = 0;
                    $order->type = 0;
                    break;
                case "11":
                    $order->status = 1;
                    $order->type = 1;
                    break;
                case "01":
                    $order->status = 0;
                    $order->type = 1;
                    break;
            }
            $order->save();
            echo json_encode(array('result' => 1, 'message' =>
                Languages::translate('Информация сохранена', 'admin/default', 'popup-messages')
            ));
            exit();
        }
        $view->order = $order;

        $this->carcase->import('content', $view);
    }
}