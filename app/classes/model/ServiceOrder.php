<?php
namespace model;

use System\ObjectModel;

class ServiceOrder extends ObjectModel{
    public static $table = 'service_orders';


    public function remove(){

        $ServiceFieldValue = new ServiceFieldValue();
        $ServiceFieldValue->where('order_id', $this->id)->removeRows();

        $Bill = new Bill();
        $Bill->where('service_order_id', $this->id)->removeRows();

        return parent::remove();
    }

    public function sendEvent($event_type){
        $url = '';
        $Service = new Service($this->service_id);
        if($event_type == 'create') {
            $url = $Service->event_create;
        } elseif ($event_type == 'prolong'){
            $url = $Service->event_prolong;
        }elseif($event_type=='end'){
            $url = $Service->event_end;
        }else{
            return false;
        }
        $Fields = ServiceFieldValue::factory()
            ->select(ServiceField::factory(), 'name')
            ->select(ServiceFieldValue::factory(), 'value')
            ->where('order_id', $this->id)->join(ServiceField::factory(), 'field_id', 'id')->getRows();

        $data = array(
            'Event' => $event_type,
            'Service' => $Service,
            'Client'  => new Client($this->client_id),
            'Order' => $this,
            'Fields' => $Fields

        );

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);

        return true;

    }

}