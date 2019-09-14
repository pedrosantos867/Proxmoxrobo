<?php

namespace admin;


use model\Bill;
use model\Domain;
use model\DomainOrder;
use model\DomainRegistrar;
use model\Languages;
use model\ModuleHook;
use System\Module;
use System\Router;
use System\Tools;

class DomainRegistrarsController extends FrontController
{



    public function actionList()
    {

        $view = $this->getView('empty-ajax-block.php');
        $this->layout->import('content', $view);
    }

    public function actionListAjax()
    {

        $this->layout->import('content', $v = $this->getView('domain/registrars/list.php'));

        $DomainRegistrar = new DomainRegistrar();


        $v->registrars = $DomainRegistrar->limit($this->from, $this->count)->getRows();
        $v->types = $this->getListRegistrars();

        $this->pagination($DomainRegistrar->lastQuery()->getRowsCount());

    }

    public function actionEditAjax()
    {
        $id_registrar = Router::getParam('id_registrar');
        $Registrar    = new DomainRegistrar($id_registrar);
        if (Tools::rPOST()) {
            $Registrar->name     = Tools::rPOST('name');
            $Registrar->type     = Tools::rPOST('type');
            $Registrar->login    = Tools::rPOST('login');
            $Registrar->password = Tools::rPOST('password');
            $Registrar->url    = Tools::rPOST('url');
            $Registrar->save();
            exit(json_encode(['result' => 1,
                              'message' => Languages::translate('Успешно сохранено!', 'admin/default', 'popup-messages')]));
        }

        $this->layout->import('content', $v = $this->getView('domain/registrars/edit.php'));
        $v->types = $this->getListRegistrars();
        $v->registrar = $Registrar;
    }

    public function actionRemoveAjax()
    {
        $id_registrar = Router::getParam('id_registrar');
        $Registrar    = new DomainRegistrar($id_registrar);
        $Registrar->remove();
        exit(json_encode(['result' => 1]));
    }

    public function getListRegistrars(){

        $data = array(
            \domain\DomainAPI::REGISTRANT_NIC_RU        => 'NIC.RU',
            \domain\DomainAPI::REGISTRANT_REG_RU        => 'REG.RU',
            \domain\DomainAPI::REGISTRANT_R01           => 'R01.RU',
            \domain\DomainAPI::REGISTRANT_2DOMAINS      => '2domains.ru',
            \domain\DomainAPI::REGISTRANT_DRSUA         => 'DRS.UA'
        );

        Module::extendMethod('getListRegistrar', $data);

        return $data;
    }
}