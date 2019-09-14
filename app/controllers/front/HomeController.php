<?php
namespace front;

use System\Tools;


class HomeController extends FrontController
{
    public function actionIndex()
    {
         Tools::redirect('/bills');
    }

    public function actionError404(){
        Tools::display404Error();
    }
}