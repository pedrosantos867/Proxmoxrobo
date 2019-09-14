<?php
namespace modules\billmanagervps\controllers\admin;

use admin\FrontController;
use admin\ModuleFrontController;
use model\Module;
use modules\billmanagervps\classes\BillManagerAPI;
use modules\billmanagervps\classes\model\Plan;
use System\Config;
use System\Tools;

class PlanController extends ModuleFrontController  {
    public function actionListAjax(){
        $planObject = new Plan();
        $view = $this->getModuleView('plan/list.php');

        $this->layout->import('content', $view);


        $order = Tools::rPOST('order');
        $planObject->limit($this->from, $this->count);
        if ($order['field']) {
            $planObject->order($order['field'], $order['type']);
        } else {
            $planObject->order('id', 'desc');
        }


        $view->plans = $planObject->getRows();
        $this->pagination($planObject->lastQuery()->getRowsCount());
    }

    public function actionRemoveAjax(){
        $planObject = new Plan(Tools::rGET('plan_id'));
        if($planObject->isLoadedObject()){

            if($planObject->remove()){
                $this->returnAjaxAnswer(1, 'Успешно удалено');
            }

        }
        $this->returnAjaxAnswer(0);
    }

    public function actionEditAjax()
    {
        $view = $this->getModuleView('plan/edit.php');
        $planObject = new Plan(Tools::rGET('plan_id'));
        $view->plan = $planObject;

        if(Tools::rPOST()){
            $planObject->name = Tools::rPOST('name');
            $planObject->price = Tools::rPOST('price');
            $planObject->description = Tools::rPOST('description');
            $planObject->link = Tools::rPOST('link');
            $planObject->pricelist = Tools::rPOST('pricelist');
            $planObject->additions = Tools::rPOST('additions');
            $planObject->templates = Tools::rPOST('templates');
            if($planObject->save()){
                $this->returnAjaxAnswer(1, "Успешно сохранено");
            }
            $this->returnAjaxAnswer(0, "Ошибка сохранения");
        }

        $this->layout->import('content', $view);
    }
}