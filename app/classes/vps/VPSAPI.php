<?php

namespace vps;


use model\VpsServer;
use System\Exception;
use vps\proxmox\ProxMoxAPI;
use vps\vmmanager\VMmanager;

class VPSAPI
{
    const ANSWER_CREATE_VM_SUCCESS      = 1;
    const ANSWER_CREATE_VM_FAIL         = -1;

    const ANSWER_CREATE_USER_SUCCESS    = 1;
    const ANSWER_CREATE_USER_FAIL       = -2;

    
    const ANSWER_REMOVE_USER_SUCCESS    = 1;
    const ANSWER_REMOVE_USER_FAIL       = -2;

    const ANSWER_REMOVE_VM_SUCCESS      = 1;
    const ANSWER_REMOVE_VM_FAIL         = -2;

    const ANSWER_SUSPEND_VM_SUCCESS      = 1;
    const ANSWER_SUSPEND_VM_FAIL         = -2;

    const ANSWER_UNSUSPEND_VM_SUCCESS      = 1;
    const ANSWER_UNSUSPEND_VM_FAIL         = -2;

    const ANSWER_CONNECTION_OK          = 11;
    const ANSWER_CONNECTION_ERROR       = -11;


    const ANSWER_OK = 1;


    public static function selectServer($server)
    {
      //  echo $server;
        if (!is_object($server)) {
            $server = new VpsServer($server);
        }



        switch ($server->type) {
            case VpsServer::PANEL_PROXMOX:
                return new ProxMoxAPI($server);
                break;
            case VpsServer::PANEL_VMMANAGER:
                return new VMmanager($server);
                break;
        }

        throw new Exception('Server not found');
    }



    protected function result($code, $data=null){
        return (object)array('data' => $data, 'code' => $code);
    }

}