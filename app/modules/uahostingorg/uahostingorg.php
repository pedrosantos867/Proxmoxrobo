<?php

namespace modules\uahostingorg;

use model\DomainRegistrar;

use modules\uahostingorg\classes\domain\UahostingorgAPI;
use System\Module;


class uahostingorg extends Module{
    /*
     * Module name
     * */
    public $name = 'Регистратор доменов ua-hosting.org';

    /*
     * Module author
     * */
    public $author = '<a target="_blank" href="http://hopebilling.com">hopebilling.com</a>';

    /*
     * Module category
     * */
    public $category = 2; // 2 for Registrars domains


    public function install(){

        $this->registerHook('getListRegistrar');
        $this->registerHook('getRegistrar');

        return parent::install();
    }

    public function uninstall(){
        DomainRegistrar::factory()->where('type', $this->getUniqID())->removeRows();
        return parent::uninstall();
    }

    public function getRegistrar(&$data){
        $Registrar = $data['Registrar'];
        if($Registrar->type == $this->getUniqID()){
            $data['Class'] = new UahostingorgAPI($Registrar);
        }
        return null;
    }

    /*
     * Get
     * */
    public function getListRegistrar(&$data){
        $data[$this->getUniqID()] = 'ua-hosting.org';
    }



}