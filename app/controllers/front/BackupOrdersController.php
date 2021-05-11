<?php 

namespace front;

use System\Router;
use model\BackupOrder;
use Model\Client;
use Model\VpsServer;
use Model\VpsOrder;
use Model\BackupServer;
use Model\Bill;
use System\Notifier;
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


        $backupOrderObject->limit($this->from, $this->count);

        $rows = $backupOrderObject->getRows();
        $view->backupOrders = $rows;
        
        $all = $backupOrderObject->lastQuery()->getRowsCount();  
        $view->pagination = $this->pagination($all);

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
                    $total_price = 1;
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
                    $backupOrder->expire_date = $vpsOrderToChange->paid_to;
                    $backupOrder->timestamp = date("Y-m-d H:m:s");
                    $backupOrder->mode = Tools::rPOST('backup_mode');
                    $backupOrder->retention = $retention;
                    $backupOrder->type = $backup_type;
                    
                    foreach($_POST['check_list_days'] as $day){
                        if($day){
                            $backupOrder->$day = 1;
                            array_push($dow, $day);
                        }
                    }

                    $backupOrder->save();
                    
                    $bill                     = new Bill();
                    $bill->client_id          = $this->client->id;
                    $bill->total              = $total_price;
                    $bill->price              = $total_price;
                    $bill->date               = date("Y-m-d H:m:s");
                    $bill->type               = Bill::TYPE_BACKUP;

                    if ($bill->save()) {
                        Notifier::NewBill($this->client, $bill);
                    }

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

        $vps_order_object = new VPSOrder();
        $vps_order_object->select('*')->where(VPSOrder::getInstance(), 'vmid', $vmid);
        
        $vps = $vps_order_object->getRow();

        $backup_order_object = new BackupOrder();
        $backup_order_object->select('*')
                            ->where(BackupOrder::getInstance(), 'vps_order_id', $vps->id);

        $backup = $backup_order_object->getRow();

        $backup_list = $api->getBackupsByVMID($server->name, $vmid, $backup->storage);
    
        $view->backup_list = $backup_list["data"];
        $this->layout->import('content', $view);
    }

    public function actionRevertToAjax(){
        $backup = Tools::rPOST('backup');
        $backup = urldecode($backup[0]);
        $backup = explode(',', $backup);

        $backupAux = array();
        foreach($backup as $b){
            $splited = explode('=', $b);
            $backupAux[$splited[0]]  = $splited[1];
        }
        $backup = $backupAux;

        $VpsServerObject = new VpsServer();
        $server = $VpsServerObject->select('*')->limit(1)->getRow();

        $api = VPSAPI::selectServer($server->id);

        $vps_order_object = new VPSOrder();
        $vps_order_object->select('*')->where(VPSOrder::getInstance(), 'vmid', $backup["vmid"]);
        $vps_order = $vps_order_object->getRow();

        $vps_server_object = new VPSServer();
        $vps_server_object->select('*')->where(VPSServer::getInstance(), 'id', $vps_order->server_id);
        $vps_server = $vps_server_object->getRow();

        $paramenters = [
            'vmid' => $backup["vmid"],
            'archive' => $backup["volid"] 
        ];

        $api->removeVM($vps_server->name, $backup["vmid"], "", 0);

        return $api->restoreBackup($vps_server->name, $paramenters);
    }

    public function actionDeleteBackupAjax(){
        $backup = Tools::rPOST('backup');
        $backup = urldecode($backup);
        $backup = explode(',', $backup);

        $backupAux = array();
        foreach($backup as $b){
            $splited = explode('=', $b);
            $backupAux[$splited[0]]  = $splited[1];
        }
        
        $backup = $backupAux;

        $VpsServerObject = new VpsServer();
        $server = $VpsServerObject->select('*')->limit(1)->getRow();

        $api = VPSAPI::selectServer($server->id);

        $vps_order_object = new VpsOrder();
        $vps_order_object->select('*')->where(VpsOrder::getInstance(), 'vmid', $backup["vmid"]);
        $vps_order = $vps_order_object->getRow();

        $backup_order_object = new BackupOrder();
        $backup_order_object->select('*')->where('vps_order_id', $vps_order->id);
        $backup_order = $backup_order_object->getRow();

        $api->deleteBackup($server->name, $backup_order->storage, $backup["volid"]);
    }
}