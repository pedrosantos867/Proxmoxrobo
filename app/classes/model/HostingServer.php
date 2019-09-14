<?php
/**
 * Created by PhpStorm.
 * Client: Viktor
 * Date: 09.06.15
 * Time: 15:42
 */
namespace model;
class HostingServer extends \System\ObjectModel
{
    const PANEL_VESTA = 1;
    const PANEL_ISP = 2;
    const PANEL_ISP4 = 4;
    const PANEL_CPANEl = 3;
    const PANEL_PLESK = 5;
    const PANEL_DIRECTADMIN = 6;
    const PANEL_ISPCONFIG = 7;

    protected static $table = 'hosting_servers';


    public function remove(){
        $ha = new HostingAccount();
        $ha->where('server_id', $this->id)->removeRows();
        return parent::remove();
    }
} 