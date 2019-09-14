<?php
namespace model;

use System\ObjectModel;
use vps\VPSAPI;

class VpsOrder extends ObjectModel{
    public static $table = 'vps_orders';

    public function remove($only_object = false)
    {
        if (!$only_object) {
            try {
                $api = VPSAPI::selectServer($this->server_id);
                //try delete orders from server
                $api->removeVM("pve", $this->vmid, $this->username, $this->type);
                $api->removeUser($this->username);
            } catch (\System\Exception $e) {
                //nothing to do
            }
        }

        $VpsOrderIp = new VpsOrderIp();
        $ips = $VpsOrderIp->where('order_id', $this->id)->getRows();
        foreach ($ips as $ip) {
            $VpsIp = new VpsServerIp($ip->ip_id);
            $VpsIp->used = 0;
            $VpsIp->save();

            $oi = new VpsOrderIp($ip);
            $oi->remove();
        }

        $hb = new Bill();
        $hb->where('hosting_account_id', $this->id)->where('type', Bill::TYPE_VPS)->removeRows();
        return parent::remove();
    }
}