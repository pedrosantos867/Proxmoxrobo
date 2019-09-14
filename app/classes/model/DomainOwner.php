<?php
namespace model;


use System\ObjectModel;

class DomainOwner extends ObjectModel
{
    protected static $table = 'domain_owners';

 

    /*
    public function getNicHdl($type)
    {
        $nics = json_decode($this->nic_hdl);
        return isset($nics->$type) ? $nics->$type : '';
    }

    public function addData($r_id, $key, $data)
    {
        $add_data = json_decode($this->add_data);
        if(!is_object($add_data)){
            $add_data = new \stdClass();
        }
        if(!isset($add_data->$r_id)){
            $add_data->$r_id = (object)[];
        }

        $add_data->$r_id->$key = $data;
        $this->add_data = json_encode($add_data);

    }

    public function getData($r_id, $key){
        $add_data = json_decode($this->add_data);
        if(isset($add_data->$r_id->$key)){
            return $add_data->$r_id->$key;
        }
        return null;
    }

    public function setNicHdl($type, $nic)
    {
        $nic_hdl                = json_decode($this->nic_hdl);
        if(!is_object($nic_hdl)){
            $nic_hdl = new \stdClass();

        }
        $nic_hdl->$type         = $nic;
        $this->nic_hdl         = json_encode($nic_hdl);
    }
    */
}