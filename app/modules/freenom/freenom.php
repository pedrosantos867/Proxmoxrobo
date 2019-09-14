<?php

namespace modules\freenom;

use model\DomainRegistrar;
use modules\freenom\classes\FreenomAPI;
use System\Module;


class freenom extends Module{
    /*
     * Module name
     * */
    public $name = 'Регистратор доменов freenom.com';

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
            $data['Class'] = new FreenomAPI($Registrar);
        }
        return null;
    }

    /*
     * Get
     * */
    public function getListRegistrar(&$data){
        $data[$this->getUniqID()] = 'freenom.com';
    }


}