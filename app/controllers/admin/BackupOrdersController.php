<?php

namespace admin;

use System\Router;
use model\BackupOrder;
use Model\Client;
use Model\VpsServer;
use Model\VpsOrder;
use Model\BackupServer;
use System\Exception;

class BackupOrdersController extends FrontController{
    public function actionList()
    {
        $view = $this->getView('empty-ajax-block.php');
        $this->layout->import('content', $view);
    }

    public function actionListAjax()
    {
        $view = $this->getView('backup/order/list.php');
        
        $backupOrderObject = new BackupOrder();
        
        $backupOrderObject->select('*')
            //add clients table
            ->join(Client::getInstance(), 'client_id', 'id') //client_id => PK(backup_orders), id => PK(Clients)   Todos os joins são LEFT JOIN
            ->select(Client::getInstance(), 'name') 

            //add BackupServer table
            //->join(BackupServer::getInstance(), 'backup_server_id', 'id') //id do BackupServer
            //->select(BackupServer::getInstance(), 'name', 'backup_server_name') //renomeação de name para backup_server_name

            //add VpsServer table
            ->join(VpsOrder::getInstance(), 'vps_order_id', 'id')//id do VPS server
            ->select('bm_vps_orders', 'vmid')
            
            ->select('id')
            //->select('backup_server_id')
            ->select('vps_order_id')
            ->select('client_id')
            ->select('sunday')
            ->select('monday')
            ->select('tuesday')
            ->select('wednesday')
            ->select('thursday')
            ->select('friday')
            ->select('saturday')
            ->select('time');
            
        $rows = $backupOrderObject->getRows();
            
        $view->backupOrders = $rows;
        $this->layout->import('content', $view);
    }
}

