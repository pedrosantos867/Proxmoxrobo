<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 05.02.2017
 * Time: 14:17
 */
namespace front;

use model\Page;
use System\Router;
use System\Tools;

class PageController extends FrontController
{
    public function actionDisplay()
    {
        $pageObject = new Page(Page::factory()->where('url', Router::getParam('page'))->getRow());

        if ($pageObject->isLoadedObject()) {
            $this->layout->content = $pageObject->desc;
        } else {
            Tools::display404Error();
        }
    }
}