<?php
namespace admin;

use model\Service;
use model\ServiceCategories;
use model\ServiceField;
use System\Db\Schema\Schema;
use System\Db\Schema\Table;
use System\Tools;

class ServicesController extends FrontController{
    public function actionListAjax(){
        $Service =  new Service();


        $this->layout->import('content', $v = $this->getView('service/list.php'));
        $v->categories = ServiceCategories::factory()->getRows();

        $filter = Tools::rPOST('filter');
        $vfilter = array();
        if (isset($filter) && $filter != '') {
            foreach ($filter as $field => $option) {

                $value = Tools::clearXSS($option['value']);
                $type  = isset($option['type']) ? $option['type'] : 'like';

                $vfilter[$field] = $value;

                if ($field && $value != '') {

                    if ($type == 'like') {
                        $Service->where($field, 'LIKE', '%' . $value . '%');
                    } else if ($type == 'equal') {
                        $Service->where($field, $value);
                    }


                }
            }
        }
        $order = Tools::rPOST('order');
        if ($order['field']) {

            $Service->order($order['field'], $order['type']);

        } else {
            $Service->order('id', 'desc');
        }

        $v->filter = $vfilter;

        $v->services = $Service->select('*')->select(ServiceCategories::factory(), 'name', 'category')->join(ServiceCategories::factory(), 'category_id', 'id')->limit($this->from, $this->count)->getRows();
        $this->pagination($Service->lastQuery()->getRowsCount());
    }

    public function actionEditAjax(){
        $view = $this->getView('service/edit.php');
        $Service =  new Service(Tools::rGET('id_service'));

        if (Tools::rPOST() && $_POST) {

                $Service->name = Tools::rPOST('name');
                $Service->category_id = Tools::rPOST('category_id');
                $Service->price = Tools::rPOST('price');
                $Service->description = Tools::rPOST('description');
                $Service->type = Tools::rPOST('type');

            //events
            $Service->event_create        = Tools::rPOST('event_create');
            $Service->event_prolong       = Tools::rPOST('event_prolong');
            $Service->event_end           = Tools::rPOST('event_end');

                if ($Service->save()) {
                    $serviceField = new ServiceField();
                    $old_fields = $serviceField->where('service_id', $Service->id)->getRows();

                    $fields = json_decode(Tools::rPOST('fields', array(), false));
 
                    if (is_array($fields)) {

                        foreach ($old_fields as $old_field){

                            $search = false;

                            foreach ($fields as $data) {

                                if ( is_object($data) && isset($data->id) &&
                                    $old_field->id == $data->id
                                ) {

                                    $search = true;
                                    break;
                                }
                            }
                            if(!$search) {

                                $serviceFieldObject = new ServiceField($old_field);
                                $serviceFieldObject->remove();
                            }
                        }

                        foreach ($fields as $data) {
                            $search = false;
                            foreach ($old_fields as $old_field){
                                if(is_object($data) && isset($data->id) && $old_field->id         == $data->id 
                                ){
                                    $search = true;
                                    break;
                                }

                            }

                            if (!$search && isset($data->name)) {
                                $serviceField = new ServiceField();
                                $serviceField->name = $data->name;
                                $serviceField->service_id = $Service->id;
                                $serviceField->type = $data->type;
                                $serviceField->values = $data->values;
                                $serviceField->validate = $data->required ? 'required' : '';
                                $serviceField->save();
                            }
                        }
                    }
                    $this->returnAjaxAnswer(1, "Услуга успешно добавлена");
                }



        }






        $view->service_fields = ServiceField::factory()->where('service_id', $Service->id)->getRows();
        $view->service = $Service;
        $view->categories = ServiceCategories::factory()->getRows();
        $this->layout->import('content', $view);
    }


    public function actionRemoveAjax(){
        $Service =  new Service(Tools::rGET('id_service'));
        $Service->remove();
        $this->returnAjaxAnswer(1, "Услуга успешно удалена");
    }

    /*
    public function actionNewFieldAjax(){



        $serviceField = new ServiceField();

        foreach(Tools::rPOST('data') as $row){
            $serviceField->{$row['name']} = $row['value'];
        }
        $serviceField->save();

    }*/

}