<?php
/**
 * Created by PhpStorm.
 * User: Stanislav
 * Date: 29.09.2016
 * Time: 14:05
 */

namespace admin;

use admin\FrontController;
use model\Languages;
use model\Promocode;
use model\PromocodeServiceCategory;
use model\ServiceCategories;
use System\Db\Schema\Schema;
use System\Db\Schema\Table;
use System\Router;
use System\Tools;

class PromocodesController extends FrontController
{
    public function actionList()
    {
        $view = $this->getView('empty-ajax-block.php');
        $this->layout->import('content', $view);
    }

    public function actionEditAjax(){
        $this->actionEdit(true);
    }

    public function actionValidateAjax(){
        $field = Tools::rPOST('field');
        $val   = Tools::rPOST('value');
        if ($field == "code"){
            $promocode = new Promocode();
            $promocode->where("code", $val);
            if($promocode->getRow()){
                echo json_encode(['result' => 0]);
            } else {
                echo json_encode(['result' => 1]);
            }
        }
    }

    public function actionRemoveAjax(){
        $promocode = new Promocode(Tools::rGET("promocode_id"));
        if($promocode->remove()) {
            $this->returnAjaxAnswer(1);
        }
        else $this->returnAjaxAnswer(0);
    }
    
    public function actionEdit($ajax = false)
    {
        $view = $this->getView('promocode/edit.php');
        $promocode = new Promocode(Tools::rGET("promocode_id"));
        $ServiceCategories = new ServiceCategories();

        if($promocode->isLoadedObject()){
            $promocodeServiceCategory = new PromocodeServiceCategory();
            $promocodeServiceCategory->where('promocode_id', $promocode->id);
            $view->pSCs = $promocodeServiceCategory->getRows();
        }
        else $view->pSCs = array();

        if (Tools::rPOST()){

            $promocode->name = Tools::rPOST('name');
            $promocode->code = Tools::rPOST('code');
            $promocode->sale = Tools::rPOST('sale');
            $promocode->sale_type = Tools::rPOST('sale_type');
            $promocode->total_count = Tools::rPOST('total_count');
            $promocode->end_date = Tools::rPOST('end_date');

            if($promocode->isLoadedObject()){
                $promocodeServiceCategory = new PromocodeServiceCategory();
                $promocodeServiceCategory->where('promocode_id', $promocode->id)->removeRows();
            }
            
            if ($promocode->save()) {

                foreach (Tools::rPOST("service", array()) as $key => $service){
                    $promocodeServiceCategory = new PromocodeServiceCategory();
                    $promocodeServiceCategory->promocode_id = $promocode->id;
                    $promocodeServiceCategory->service_category_id = $key;
                    $promocodeServiceCategory->save();
                }

                if ($ajax) {
                    echo json_encode(array('result' => 1, 'message' =>
                        Languages::translate('Промокод успешно сохранен!', 'admin/default', 'popup-messages')
                    ));
                    exit;
                } else {
                    Tools::redirect('/admin/promocodes');
                }
            }

        }

        $view->promocode = $promocode;
        $view->ServiceCategories = $ServiceCategories->getRows();
        $this->layout->import('content', $view);
    }

    public function actionListAjax()
    {
        $view = $this->getView('promocode/list.php');
        
        $Promocode = new Promocode();
        //$PromocodeServiceCategory = new PromocodeServiceCategory();
        $view->promocodes = $Promocode
            ->limit($this->from, $this->count)
            ->getRows();
        $this->pagination($Promocode->lastQuery()->getRowsCount());
        
        $this->layout->import('content', $view);
    }
}