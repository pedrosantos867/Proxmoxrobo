<?php

namespace admin;

use System\Router;

class BackupsController extends FrontController{
    public function actionList()
    {

        $view = $this->getView('empty-ajax-block.php');
        $this->layout->import('content', $view);
    }

    public function actionListAjax()
    {
        $view = $this->getView('backup/order/list.php');
        $this->layout->import('content', $view);
    }
}

