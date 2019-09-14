<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 09.02.2016
 * Time: 18:51
 */

namespace model;


use System\ObjectModel;

class VpsServer extends ObjectModel
{

    public static $table = 'vps_servers';

    const PANEL_PROXMOX     = 1;
    const PANEL_VMMANAGER   = 2;
    const PANEL_HYPERVM     = 3;

}