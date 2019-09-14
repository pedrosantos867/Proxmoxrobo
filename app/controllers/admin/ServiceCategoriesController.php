<?php

namespace admin;

use model\ServiceCategories;
use System\Db\Schema\Schema;
use System\Db\Schema\Table;
use System\Tools;

class ServiceCategoriesController extends FrontController {

    public function actionListAjax(){
       $ServiceCategories =  new ServiceCategories();

       $this->layout->import('content', $v = $this->getView('service/category/list.php'));

        $v->categories = $ServiceCategories->limit($this->from, $this->count)->getRows();
        $this->pagination($ServiceCategories->lastQuery()->getRowsCount());
    }

    public function actionEditAjax(){
        $view = $this->getView('service/category/edit.php');
        $ServiceCategories =  new ServiceCategories(Tools::rGET('id_category'));

        if (Tools::rPOST() && $_POST) {
            $ServiceCategories->name       = Tools::rPOST('name');
            $ServiceCategories->icon       = Tools::rPOST('icon');
            if ($ServiceCategories->save()) {
               $this->returnAjaxAnswer(1, "Категория успешно добавлена");
            }

        }

        $view->category = $ServiceCategories;
        $this->layout->import('content', $view);
    }

    public function actionRemoveAjax(){
        $ServiceCategories =  new ServiceCategories(Tools::rGET('id_category'));
        $ServiceCategories->remove();
        $this->returnAjaxAnswer(1, "Категория успешно удалена");
    }

    public function actionSetPositionsAjax(){


        foreach(Tools::rPOST('data') as $id=>$position){
            $sv = new ServiceCategories($id);
            $sv->sort_position = $position;
            $sv->save();
        }

    }
}