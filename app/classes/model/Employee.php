<?php
/**
 * Created by PhpStorm.
 * User: Viktor
 * Date: 10.06.15
 * Time: 20:52
 */

namespace model;


use System\Config;
use System\ObjectModel;
use System\Validation;

class Employee extends ObjectModel
{
    protected static $table = 'employees';

    public function validationFields()
    {
        if(
            ($this->where('username', $this->username)->getRowsCount() > 1 && $this->id)
            ||
            ($this->where('username', $this->username)->getRowsCount() > 0 && !$this->id)
            ||
            !Validation::isUserName($this->username)){
            return false;
        }

        return true;
    }

    public function save($id_lang = 0){
       return parent::save($id_lang);
    }

    public function remove(){
        EmployeeSession::factory()->where('employee_id', $this->id)->removeRows();
        return parent::remove();

    }

    public function getDefaultLang(){
        $config = Config::factory();

        return $config->admin_default_lang;

    }
} 