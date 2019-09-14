<?php

namespace admin;

use model\Languages;
use model\Page;
use System\Db\Schema\Schema;
use System\Db\Schema\Table;
use System\Tools;

class PagesController extends FrontController{

    public function actionListAjax()
    {
        $this->layout->import('content', $v = $this->getView('page/list.php'));
        $Page = new Page();
        $v->pages = $Page
            ->limit($this->from, $this->count)
            ->getRows();
        $this->pagination($Page->lastQuery()->getRowsCount());
    }

    public function actionEditAjax(){
        $this->actionEdit(true);
    }

    public function actionEdit($ajax = false){
        $this->layout->import('content', $v = $this->getView('page/edit.php'));
        $Page = new Page(Tools::rGET('id_page'));

        if (Tools::rPOST()) {
            $Page->name  = Tools::rPOST('name');
            $Page->url  = Tools::rPOST('url');
            $Page->desc = Tools::rPOST('desc');
            $Page->date = date('Y-m-d');

            if ($Page->save()) {
                if ($ajax) {
                    echo json_encode(array('result' => 1, 'message' =>
                        Languages::translate('Страница успешно сохранена!', 'admin/default', 'popup-messages')

                    ));
                    exit;
                } else {
                    Tools::redirect('/admin/pages');
                }
            }

        }

        $v->page = $Page;
    }

    public function actionRemoveAjax(){
        $Page = new Page(Tools::rGET('id_page'));
        if($Page->remove()){
            $this->returnAjaxAnswer(1, 'Успешно удалено');
        }

        $this->returnAjaxAnswer(0, 'Возникла ошибка при удалении');
    }

}