<?php 

namespace front;

use System\Router;
use model\BackupOrder;
use Model\Client;
use Model\VpsServer;
use Model\VpsOrder;
use Model\BackupServer;
use System\Tools;

class BackupOrdersController extends FrontController {
    public function actionListAjax()
    {
        $view = $this->getView('backup/order/list.php');

        $backupOrderObject = new BackupOrder();
        
        $backupOrderObject->select('*')->where('client_id', '=', $this->client->id)
            //add clients table
            ->join(Client::getInstance(), 'client_id', 'id') //client_id => PK(backup_orders), id => PK(Clients)   Todos os joins são LEFT JOIN
            ->select(Client::getInstance(), 'name') 

            //add BackupServer table
            ->join(BackupServer::getInstance(), 'backup_server_id', 'id') //id do BackupServer
            ->select(BackupServer::getInstance(), 'name', 'backup_server_name') //renomeação de name para backup_server_name

            //add VpsServer table
            ->join(VpsOrder::getInstance(), 'vps_order_id', 'id')//id do VPS server
            ->select('bm_vps_orders', 'vmid')
            
            ->select('id')
            ->select('backup_server_id')
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



        $view->backupOrders = $backupOrderObject->getRows();                            
        $this->layout->import('content', $view);
    }

    public function actionNew(){
        $view = $this->getView('backup/order/new.php');

         //Get all user vps's 
         $vpsOrderObject = new VpsOrder();

         $vpsOrderObject->select('vmid')
                         ->where(VpsOrder::getInstance(), 'client_id', $this->client->id)
                         ->where(VpsOrder::getInstance(), 'has_backup_configured', 0);
 
         $vps_list = $vpsOrderObject->getRows();
 
         $view->vps_list = $vps_list;
         $this->layout->import('content', $view);

        if (Tools::rPOST()) {
            if(!empty($_POST['check_list_vps']) && count($_POST['check_list_days']) > 0){
                foreach($_POST['check_list_vps'] as $vmid){
                    $vpsOrderAux = new VpsOrder();
                    $vpsOrderAux->select('*')->where('vmid', '=', $vmid);
                    
                    $row = $vpsOrderAux->getRow();
                    
                    $vpsOrderToChange = new VpsOrder($row->id);

                    $vpsOrderToChange->has_backup_configured = 1;
                    $vpsOrderToChange->save();
                            
                    $backupOrder = new BackupOrder();
                    $backupOrder->backup_server_id = 1; //alterar para ser dinâmico
                    $backupOrder->vps_order_id = $row->id;
                    $backupOrder->client_id = $this->client->id;
                    $backupOrder->time = Tools::rPOST('time');
                    $backupOrder->paid_to = '1970-01-01';
                    $backupOrder->date = date('Y-m-d H:m:s');
                    
                    foreach($_POST['check_list_days'] as $day){
                        if($day){
                            $backupOrder->$day = 1;
                        }
                    }

                    $backupOrder->save();
                }
            }
        }
    }
}