<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 08.02.2016
 * Time: 21:55
 */

namespace model;


use System\ObjectModel;

class VpsPlan extends ObjectModel
{
    public static $table = 'vps_plans';

    public function getServers()
    {
        return explode('|', trim($this->available_servers, '|'));
    }

    public function setServers($servers){
        $this->available_servers = '|'.implode('|',$servers).'|';
    }


    public function getImages()
    {
        return explode('|', trim($this->images, '|'));
    }

    public function setImages($images){
        if(is_array($images)) {
            $this->images = '|' . implode('|', $images) . '|';
        }
    }
}