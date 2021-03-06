<?php 

namespace front;

use System\Router;
use model\BackupOrder;
use Model\Client;
use Model\VpsServer;
use Model\VpsOrder;
use Model\VpsPlan;
use Model\BackupServer;
use Model\Bill;
use System\Notifier;
use System\Tools;
use System\Config;
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

        $backupsConfig = new Config('backup');

         //Get all user vps's 
         $vpsOrderObject = new VpsOrder();

         $vpsOrderObject->select('vmid')
                         ->where(VpsOrder::getInstance(), 'client_id', $this->client->id)
                         ->where(VpsOrder::getInstance(), 'has_backup_configured', 0);
 
         $vps_list = $vpsOrderObject->getRows();

         $VpsServerObject = new VpsServer();
         $server = $VpsServerObject->select('*')->limit(1)->getRow();
 
         $api = VPSAPI::selectServer($server->id);

         foreach($vps_list as $vps){
            $vps->disk_size = round($api->getVPSDiskSize($vps->vmid) / 1024 / 1024 / 1024, 2);
         }
         $view->backupsConfig = $backupsConfig->options;
         $view->vps_list = $vps_list;
         $this->layout->import('content', $view);
         
        
        if (Tools::rPOST()) {
            if(!empty($_POST['vps_to_backup']) && !empty(Tools::rPOST('backup_type')) && !empty(Tools::rPOST('backup_mode'))){
                $vmid = $_POST['vps_to_backup'];

                $vmid = explode(" ", $vmid);

                $vmid = $vmid[0];

                $vpsOrderAux = new VpsOrder();
                $vpsOrderAux->select('*')->where('vmid', '=', $vmid);
                
                $row = $vpsOrderAux->getRow();
                $dow = array();
                
                $vpsOrderToChange = new VpsOrder($row->id);

                $vpsOrderToChange->has_backup_configured = 1;
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

                $backupOrder                = new BackupOrder();
                $backupOrder->storage       = $storage;
                $backupOrder->vps_order_id  = $row->id;
                $backupOrder->client_id     = $this->client->id;
                $backupOrder->time          = Tools::rPOST('time');
                $backupOrder->expire_date   = date("Y-m-d H:i:s"); //time will be added when the bill is paid (app\classes\model\Bill.php)
                $backupOrder->timestamp     = date("Y-m-d H:i:s");
                $backupOrder->mode          = Tools::rPOST('backup_mode');
                $backupOrder->retention     = $retention;
                
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

                $disk_size = round($api->getVPSDiskSize($vps->vmid) / 1024 / 1024 / 1024, 2);
                $bill->total          =  $backupsConfig->options["pricePerGB"]*$disk_size*1+($backupsConfig->options["multiplierForRetention"])*$backupOrder->retention;
         
                $bill->price              = $bill->total;
                $bill->date               = date("Y-m-d H:i:s");
                $bill->type               = Bill::TYPE_BACKUP;
                $bill->backup_order_id    = $backupOrder->id;
                if ($bill->save()) {
                    Notifier::NewBill($this->client, $bill);
                }

                $api->createBackupJobForPBS($backupOrder->time, $dow, $vmid, $storage, $backupOrder->mode, $retention, $backupsConfig->options["IOBandwidthLimit"]);      
                Tools::redirect('/bill/'.$bill->id);
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

        $vps_plan = new VpsPlan();
        $vps_plan = $vps_plan->select('*')->where(VpsPlan::getInstance(), 'id', $vps->plan_id)->getRow();

        $backup = $backup_order_object->getRow();

        $backup_list = $api->getBackupsByVMID($vps_plan->node, $vmid, $backup->storage);
    
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

        $vps_plan = new VpsPlan();
        $vps_plan = $vps_plan->select('*')->where(VpsPlan::getInstance(), 'id', $vps_order->plan_id)->getRow();

        $api->removeVM($vps_plan->node, $backup["vmid"], "", 0);

        return $api->restoreBackup($vps_plan->node, $paramenters);
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

        $vps_plan = new VpsPlan();
        $vps_plan = $vps_plan->select('*')->where(VpsPlan::getInstance(), 'id', $vps_order->plan_id)->getRow();

        $api->deleteBackup($vps_plan->node, $backup_order->storage, $backup["volid"]);
    }
}