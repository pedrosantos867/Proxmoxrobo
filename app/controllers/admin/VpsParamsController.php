<?php

namespace admin;

use model\VpsPlanDetail;
use model\VpsPlanParam;
use System\Tools;

class VpsParamsController extends FrontController{

    public function actionListAjax()
    {
        $view = $this->getView('vps/param/list.php');
        $property         = new VpsPlanParam();


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

    public function actionEditAjax(){

        $view = $this->getView('vps/param/edit.php');

        $param = new VpsPlanParam(Tools::rGET('id_param'));


        if ($_POST) {
            $param->name = Tools::rPOST('name');
            if ($param->save()) {
                   $this->returnAjaxAnswer(1, 'Свойство успешно добавлено');
            }
        }

        $view->param = $param;
        $this->layout->import('content', $view);

    }

    public function actionRemoveAjax(){
        $param = new VpsPlanParam(Tools::rGET('id_param'));
        $hd    = new VpsPlanDetail();
        $hd->where('param_id', $param->id)->removeRows();

        $param->remove();

        $this->returnAjaxAnswer(1, 'Свойство успешно удалено');

    }
}