<?php

namespace modules\ukrnames;

use model\DomainRegistrar;
use modules\ukrnames\classes\domain\UkrNamesAPI;
use System\Module;


class ukrnames extends Module{
    /*
     * Module name
     * */
    public $name = 'Регистратор доменов ukrnames.com.ua';

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
            $data['Class'] = new UkrNamesAPI($Registrar);
        }
        return null;
    }

    /*
     * Get
     * */
    public function getListRegistrar(&$data){
        $data[$this->getUniqID()] = 'UkrNames.com.ua';
    }

    public function getUniqID(){
        return 4; // only for ukrnames, integration app functionality as module
    }

}