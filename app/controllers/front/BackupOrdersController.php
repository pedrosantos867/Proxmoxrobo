<?php 

namespace front;

use System\Router;
use model\BackupOrder;
use Model\Client;
use Model\VpsServer;
use Model\VpsOrder;
use Model\BackupServer;
use System\Tools;
use vps\VPSAPI;

class BackupOrdersController extends FrontController {
    public function actionListAjax()
    {
        $view = $this->getView('backup/order/list.php');

        $backupOrderObject = new BackupOrder();
        
        $backupOrderObject->select('*')->where('client_id', '=', $this->client->id)
            //add clients table
            ->join(Client::getInstance(), 'client_id', 'id') //client_id => PK(backup_orders), id => PK(Clients)   Todos os joins são LEFT JOIN
            ->select(Client::getInstance(), 'name') 
            //add VpsServer table
            ->join(VpsOrder::getInstance(), 'vps_order_id', 'id')//id do VPS server
            ->select('bm_vps_orders', 'vmid')
            
            ->select('id')
            ->select('storage')
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
            if(!empty($_POST['check_list_vps']) && count($_POST['check_list_days']) > 0 && !empty(Tools::rPOST('backup_type')) && !empty(Tools::rPOST('backup_mode'))){
                foreach($_POST['check_list_vps'] as $vmid){
                    $vpsOrderAux = new VpsOrder();
                    $vpsOrderAux->select('*')->where('vmid', '=', $vmid);
                    
                    $row = $vpsOrderAux->getRow();
                    $dow = array();
                    
                    $vpsOrderToChange = new VpsOrder($row->id);

                    $vpsOrderToChange->has_backup_configured = 0; //TODO: CHANGE TO 1 !!!!!!!!!!!!!!!!!!!!!!!!!!!
                    $vpsOrderToChange->save();

                    //Create backup job via Proxmox API 
                    //Get the first Proxmox server available so we can make API requests
                    $VpsServerObject = new VpsServer();
                    $server = $VpsServerObject->select('*')->limit(1)->getRow();

                    $api = VPSAPI::selectServer($server->id);

                    $backup_type = Tools::rPOST('backup_type'); 
                    $retention = Tools::rPOST('retention');

                    if($backup_type == "incremental"){
                        $storage = $api->getPBSWithMostStorageAvailable();
                    }else{
                        $storage = $api->getNFSWithMostStorageAvailable();
                    }

                    if($retention < 1){
                        $retention = 1;
                    }elseif($retention > 5){
                        $retention = 5;
                    }

                    $backupOrder = new BackupOrder();
                    $backupOrder->storage = $api->getPBSWithMostStorageAvailable();
                    $backupOrder->vps_order_id = $row->id;
                    $backupOrder->client_id = $this->client->id;
                    $backupOrder->time = Tools::rPOST('time');
                    $backupOrder->mode = Tools::rPOST('backup_mode');
                    $backupOrder->retention = $retention;
                    $backupOrder->type = $backup_type;
                    $backupOrder->paid_to = '1970-01-01';
                    $backupOrder->date = date('Y-m-d H:m:s');
                    
                    foreach($_POST['check_list_days'] as $day){
                        if($day){
                            $backupOrder->$day = 1;
                            array_push($dow, $day);
                        }
                    }

                    $backupOrder->save();  

                    $api->createBackupJobForPBS($backupOrder->time, $dow, $vmid, $storage, $backupOrder->mode, $retention);                                      
                }
            }
        }
    }

    public function actionManage(){
        $view = $this->getView('backup/order/manage.php');
        
        $vmid = Router::getParam('vmid');

        $VpsServerObject = new VpsServer();
        $server = $VpsServerObject->select('*')->limit(1)->getRow();

        $api = VPSAPI::selectServer($server->id);

        $jobs = $api->getBackupsByVMID($vmid);
    
        $view->jobs = $jobs;
        $this->layout->import('content', $view);
    }

    public function actionRevertToAjax(){
        $job = Tools::rPOST('job');
        $job = urldecode($job[0]);
        $job = explode(',', $job);

        $jobAux = array();
        foreach($job as $j){
            $splited = explode('=', $j);
            $jobAux[$splited[0]]  = $splited[1];
        }
        $job = $jobAux;

        $VpsServerObject = new VpsServer();
        $server = $VpsServerObject->select('*')->limit(1)->getRow();

        $api = VPSAPI::selectServer($server->id);
        
        $paramenters = [
            'vmid' => $job["id"],
            'archive' => $job["upid"] 
        ];

        $this->pve->post("/nodes". $job["node"]."/qemu", $paramenters);
    }
}