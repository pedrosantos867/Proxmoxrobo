<?php
namespace model;

use System\ObjectModel;

class Service extends ObjectModel{
    public static $table = 'services';




    public function remove(){
        $ServiceField = new ServiceField();
        $ServiceField->where('service_id', $this->id)->removeRows();

        $ServiceFieldValue = new ServiceFieldValue();
        $ServiceFieldValue->where('service_id', $this->id)->removeRows();


       $ServiceOrder = new ServiceOrder();
        $ServiceOrder->where('service_id', $this->id)->removeRows();

        return parent::remove();
    }
}