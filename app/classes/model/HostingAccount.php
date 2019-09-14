<?php
/**
 * Created by PhpStorm.
 * Client: Viktor
 * Date: 09.06.15
 * Time: 15:42
 */
namespace model;

use hosting\HostingAPI;
use System\Exception;

class HostingAccount extends \System\ObjectModel
{
    protected static $table = 'hosting_accounts';

    public function remove()
    {

        $server = new HostingServer($this->server_id);

        if ($server->isLoadedObject()) {
                $api = HostingAPI::selectServer($server);
                $api->removeUser($this->login);
        }

        $hb = new Bill();
        $hb->where('hosting_account_id', $this->id)->where('type', Bill::TYPE_ORDER)->removeRows();


        return parent::remove();

    }


} 