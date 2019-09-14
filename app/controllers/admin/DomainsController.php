<?php
namespace admin;


use model\Domain;
use model\DomainRegistrar;
use model\Languages;
use System\Db\Schema\Schema;
use System\Db\Schema\Table;
use System\Router;
use System\Tools;

class DomainsController extends FrontController
{

    public function actionIndexAjax()
    {
        $this->actionIndex();
    }

    public function actionSetPositionsAjax()
    {


        foreach (Tools::rPOST('data') as $id => $position) {
            $domainObject = new Domain($id);
            $domainObject->sort_position = $position;
            $domainObject->save();
        }

    }

    public function actionIndex()
    {
        $view = $this->getView('domain/list.php');
        $this->layout->import('content', $view);
        $Domine = new Domain();

        $Domine
            ->select('*')
            ->select(DomainRegistrar::factory(), 'name', 'registrar')
            ->join(DomainRegistrar::factory(), 'registrant_id', 'id');

        $filter  = Tools::rPOST('filter');
        $vfilter = array();
        if (isset($filter) && $filter != '') {
            foreach ($filter as $field => $option) {

                $value = Tools::clearXSS($option['value']);
                $type  = isset($option['type']) ? $option['type'] : 'like';

                $vfilter[$field] = $value;

                if ($field && $value != '') {

                    if ($type == 'like') {

                        $Domine->where($field, 'LIKE', '%' . $value . '%');
                    } else if ($type == 'equal') {
                        $Domine->where($field, $value);
                    }


                }
            }
        }
        $Domine->limit($this->from, $this->count);
        $view->filter = $vfilter;


        $view->domains    = $Domine->getRows();
        $view->registrars = DomainRegistrar::factory()->getRows();

        $this->pagination($Domine->getRowsCount());
    }

    public function actionEditAjax()
    {
        $id_domain = Router::getParam('id_domain');
        $view      = $this->getView('domain/edit.php');
        $this->layout->import('content', $view);
        $view->registrars = DomainRegistrar::factory()->getRows();
        $Domain           = new Domain($id_domain);
        $view->domain     = $Domain;

        if (Tools::rPOST()) {

            if(!$Domain->isLoadedObject() && $Domain->select('id')->where('name', Tools::rPOST('name'))->getRow()){
                echo json_encode(array('result' => 0, 'message' =>
                    Languages::translate('Домен уже существует', 'admin/default', 'popup-messages')
                ));
                exit();
            }

            $Domain->name                 = Tools::rPOST('name');
            $Domain->registrant_id        = Tools::rPOST('registrant_id');
            $Domain->min_period           = Tools::rPOST('min_period', 0);
            $Domain->max_period           = Tools::rPOST('max_period', 0);
            $Domain->min_extension_period = Tools::rPOST('min_extension_period', 0);
            $Domain->max_extension_period = Tools::rPOST('max_extension_period', 0);

            $Domain->min_length           = Tools::rPOST('min_length', 0);
            $Domain->max_length           = Tools::rPOST('max_length', 50);

            $Domain->price = round(Tools::rPOST('price', 0), 2);
            $Domain->price_with_hosting = round(Tools::rPOST('price_with_hosting', 0), 2);
            $Domain->extension_price = round(Tools::rPOST('extension_price'), 2);
            $Domain->extension_price_with_hosting = round(Tools::rPOST('extension_price_with_hosting', 0), 2);
            
            $Domain->save();

            echo json_encode(array('result' => 1, 'message' =>
                Languages::translate('Домен успешно отредактирован', 'admin/default', 'popup-messages')
            ));
            exit();

        }


    }

    public function actionRemoveAjax()
    {
        $id_domain = Router::getParam('id_domain');
        $Domain    = new Domain($id_domain);

        if ($Domain->remove()) {
            echo json_encode(array('result' => 1));
        } else {
            echo json_encode(array('result' => 0));
        }
    }


    public function actionValidateAjax(){

        $field = Tools::rPOST('field');

        if($field == 'name'){
            if(Domain::factory()->where('name', Tools::rPOST('value'))->getRowsCount() > 0 && !Tools::rPOST('data'))
            {
                echo json_encode(['result' => 0]);
            } else {
                echo json_encode(['result' => 1]);
            }
        }
    }
}