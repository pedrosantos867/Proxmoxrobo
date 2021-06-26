<?php
namespace vps\proxmox;



use System\Logger;
use vps\IVPSAPI;
use vps\VPSAPI;

class ProxMoxAPI extends VPSAPI implements IVPSAPI{

    private $pve = null;

    private  $realm     = 'pam';

    private $is_logged = false;
    public function __construct($server)
    {


        $this->pve = new PVE2_API($server->host, $server->username, $this->realm, $server->password);

        try {
            if($this->pve->login()){
                $this->is_logged = true;
            }

        } catch(PVE2_Exception $e){
            $this->is_logged = false;
        }
    }


    public function checkConnection()
    {

        //echo $this->is_logged;
        if($this->is_logged){
            return $this->result(VPSAPI::ANSWER_CONNECTION_OK);
        }
        return $this->result(VPSAPI::ANSWER_CONNECTION_ERROR);
    }

    public function returnNodesList(){
        if(!$this->is_logged){
            return $this->result(VPSAPI::ANSWER_CONNECTION_ERROR);
        }
        $nodes =  $this->pve->get_node_list();
        $rnodes = array();
        foreach ($nodes as $node) {
            $rnodes[$node] = $node;
        }
        return $rnodes;
    }

    public function suspendVM($node, $vmid, $user, $type){
        if(!$this->is_logged){
            return $this->result(VPSAPI::ANSWER_CONNECTION_ERROR);
        }
        $this->pve->put('/access/users/'.$user.'@pve', array('enable'=>false));

        $res = $this->pve->put('access/acl', array(
            'path' => '/vms/' . $vmid,
            'roles' => 'PVEVMUser',
            'users' => $user . '@pve',
            'delete' => true
        ));

        if($type == 1){
            return $this->pve->post('nodes/' . $node . '/lxc/' . $vmid . '/status/stop', array());
        }else {
            return $this->pve->post('nodes/' . $node . '/qemu/' . $vmid . '/status/stop', array());
        }
    }

    public function unsuspendVM($node, $vmid, $user, $type){
        if(!$this->is_logged){
            return $this->result(VPSAPI::ANSWER_CONNECTION_ERROR);
        }
        $this->pve->put('/access/users/'.$user.'@pve', array('enable'=>true));
        $res = $this->pve->put('access/acl', array(
            'path' => '/vms/' . $vmid,
            'roles' => 'PVEVMUser',
            'users' => $user . '@pve',
            'delete' => false
        ));

        if($type == 1){
            return $this->pve->post('nodes/' . $node . '/lxc/' . $vmid . '/status/start', array());
        } else {
            return $this->pve->post('nodes/' . $node . '/qemu/' . $vmid . '/status/start', array());
        }
    }

    public function returnImagesList($node){
        if(!$this->is_logged){
            return $this->result(VPSAPI::ANSWER_CONNECTION_ERROR);
        }

        //Get images from NAS 
        $storage_list = array();
        $storage_list = $this->pve->get('nodes/'.$node.'/storage');

        //Get images from Local Storage
        $images_list = array();
        $images =  $this->pve->get('nodes/'.$node.'/storage/local/content');
        foreach($images['data'] as $image){

            if($image['content'] == 'iso') {
                $images_list[$image['volid']] = $image['volid'];
            }
        }
        return $images_list;
    }

    public function returnContainersList($node){
        if(!$this->is_logged){
            return $this->result(VPSAPI::ANSWER_CONNECTION_ERROR);
        }

        //Get images from Local Storage
        $images_list = array();
        $images =  $this->pve->get('nodes/'.$node.'/storage/iso/content');


        foreach($images['data'] as $image){
            if($image['content'] == 'vztmpl') {
                $images_list[$image['volid']] = $image['volid'];
            }
        }
        return $images_list;
    }

    public function returnRecipesList($node){
        return [];
    }

    public function createUser($login, $full_name, $email, $password){
        if(!$this->is_logged){
            return $this->result(VPSAPI::ANSWER_CONNECTION_ERROR);
        }

        $name = explode(' ', $full_name);

       $res = $this->pve->post('access/users', array(
            'userid' => $login.'@pve',
            'lastname' => isset($name[0]) ? $name[0] : '',
            'firstname' => isset($name[1]) ? $name[1] : '',
            'password' => $password,
            'email' => $email
        ));


        if($res && !isset($res['errors'])){
            return $this->result(self::ANSWER_CREATE_USER_SUCCESS, $login);
        } else{

            Logger::log('ProxMox createUser error: '. $res);

            if(!$res){
                return $this->result(self::ANSWER_CREATE_USER_FAIL, array('field' => 'user', 'type' => 'exist'));
            }



            if(isset($res['errors']['password'])){
                return $this->result(self::ANSWER_CREATE_USER_FAIL, array('field' => 'password', 'type' => 'validate', 'minlength' => 5));
            }

        }

        return $this->result(self::ANSWER_CREATE_USER_FAIL);
    }

    
    public function getVMStatus($node, $vmid){
        if(!$this->is_logged){
            return $this->result(VPSAPI::ANSWER_CONNECTION_ERROR);
        }

        $response = $this->pve->get('/nodes/' . $node . '/qemu/' . $vmid . '/status/current');

        return $status = $response["data"]["status"];
    }

    public function hasQemuGestAgentConfigured($node, $vmid){
        if(!$this->is_logged){
            return $this->result(VPSAPI::ANSWER_CONNECTION_ERROR);
        }

        $response = $this->pve->get('/nodes/' . $node . '/qemu/' . $vmid . '/status/current');

        if($response == false){
            return;
        }

        if(array_key_exists("agent", $response["data"])){
            return true;
        }
        return false;
    }

    public function manageVM($node, $vmid, $command){
        if(!$this->is_logged){
            return $this->result(VPSAPI::ANSWER_CONNECTION_ERROR);
        }

        return $response = $this->pve->post('/nodes/'.$node.'/qemu/'.$vmid.'/status/'.$command, array());
    }

    public function createVM($node, $type, $memory, $hdd, $cores, $image, $socket, $user, $password, $bandwith, $net_type, $net=''){
        if(!$this->is_logged){
            return $this->result(VPSAPI::ANSWER_CONNECTION_ERROR);
        }

        /*
        if($type == 2){ //If is a Template we need to clone 
            
        }else{

        }
        */
        
        //Verifies if Ceph storage is configured
        $res = $this->pve->get('/cluster/resources?type=storage');
        $ceph = null;

        foreach($res["data"] as $storage){
            if($storage["plugintype"] == "rbd"){
                $ceph = $storage["storage"];
            }
        }


        $vmid = $this->pve->get('cluster/nextid');
        $vmid = $vmid['data'];

        if($type == 0) { //if is a vm
            if($ceph){
                $res_create_disk = $this->pve->post('/nodes/' . $node . '/storage/'.$ceph.'/content', array(
                    'filename' => 'vm-' . $vmid . '-disk-1.qcow2',
                    'format' => 'qcow2',
                    'size' => $hdd . 'G',
                    'vmid' => $vmid
                ));
            }else{
                $res_create_disk = $this->pve->post('/nodes/' . $node . '/storage/local/content', array(
                    'filename' => 'vm-' . $vmid . '-disk-1.qcow2',
                    'format' => 'qcow2',
                    'size' => $hdd . 'G',
                    'vmid' => $vmid
                ));
            }
            
            if(isset($res_create_disk['errors'])) {
                Logger::log('ProxMox createVM error: ' . $res_create_disk['errors']);
            }
        }

        if($type == 0 && !isset($res_create_disk['errors']) || $type==1 || $type==2) {

            if($type == 0) { //If is a vm
                $new_container_settings = array();

                if($ceph){
                    $new_container_settings['ide0'] = $ceph.':' . 'vm-' . $vmid . '-disk-1.qcow2';
                }else{
                    $new_container_settings['ide0'] = 'local:' . $vmid . '/vm-' . $vmid . '-disk-1.qcow2';
                }
                
                if ($image) {
                    $new_container_settings['ide2'] = $image.',media=cdrom';//"local:iso/CentOS-6.5-x86_64-minimal.iso,media=cdrom";
                }
                $new_container_settings['vmid'] = $vmid;
                $new_container_settings['cores'] = $cores;
                $new_container_settings['sockets'] = $socket;
                $new_container_settings['memory'] = $memory;
                if ($net_type==1) {
                    $new_container_settings['net0'] = 'e1000' ;
                } else if($net_type==2){
                    $new_container_settings['net0'] = 'e1000,bridge=vmbr0,tag=' .$net;
                }

                $aux = $new_container_settings['net0']; 

                $new_container_settings['net0'] .= ",rate=".$bandwith;

                $res = $this->pve->post("/nodes/$node/qemu", $new_container_settings);
            } else if($type == 1) { //If is a container
                $new_container_settings = array();
                $new_container_settings['storage'] = 'local';
                $new_container_settings['rootfs'] = $hdd;
                $new_container_settings['ostemplate'] = $image;

                $new_container_settings['vmid']     = $vmid;
                $new_container_settings['password'] = $password;

                $new_container_settings['sockets']  = $socket;
                $new_container_settings['memory']   = $memory;
                if ($net_type==1) {
                    $new_container_settings['net0'] = 'bridge=vmbr0,name=eth0';
                } else if($net_type == 2){
                    $new_container_settings['net0'] = 'bridge=vmbr0,name=eth0,tag=' .$net;
                } else if($net_type == 3){

                    $ip = $net['ip'];
                    $mask = $net['mask'];
                    $long = ip2long($mask);
                    $base = ip2long('255.255.255.255');
                    $cidr = 32-log(($long ^ $base)+1,2);

                    $gw = $net['gateway'];

                    $new_container_settings['net0'] = "bridge=vmbr0,name=eth0,ip=$ip/$cidr,gw=$gw";
                    //echo  $new_container_settings['net0'];
                }

                $res = $this->pve->post("/nodes/$node/lxc", $new_container_settings);
               
            }else{
                $nodeFrom = $this->getNodeFromVMID($image);
                $params = [
                    "description" => "Clone of vm ". $image,
                    "format" => "qcow2",
                    "full" => 1,
                    "storage" => $ceph,
                    "target" => $node,
                    "newid" => $vmid
                ];

                $res = $this->pve->post("/nodes/$nodeFrom/qemu/$image/clone", $params);
            }
           

            if(!isset($res['errors'])) {

                $res = $this->pve->put('access/acl', array(
                    'path' => '/vms/' . $vmid,
                    'roles' => 'PVEVMUser',
                    'users' => $user . '@pve'
                ));


                if(!isset($res['errors'])) {
                    return $this->result(self::ANSWER_CREATE_VM_SUCCESS, $vmid);
                }

                if(isset($res['errors'])) {
                    Logger::log('ProxMox createVM error: ' . $res['errors']);
                }

                return $this->result(self::ANSWER_CREATE_VM_FAIL);


            }

            Logger::log('ProxMox createVM error: '. $res['errors']);

        }
        return $this->result(self::ANSWER_CREATE_VM_FAIL, null);
    }


    public function removeVM($node, $vmid, $username, $type){
        if(!$this->is_logged){
            return $this->result(VPSAPI::ANSWER_CONNECTION_ERROR);
        }

        if($type==1) {
            $res = $this->pve->delete("/nodes/$node/lxc/$vmid");
        } else{
            $res = $this->pve->delete("/nodes/$node/qemu/$vmid?purge=1");
        }

        if(!isset($res['errors'])) {
            return $this->result(self::ANSWER_REMOVE_VM_FAIL);
        }

        return $this->result(self::ANSWER_REMOVE_VM_SUCCESS);
    }

    public function removeUser($username){
        if(!$this->is_logged){
            return $this->result(VPSAPI::ANSWER_CONNECTION_ERROR);
        }

        $res =  $this->pve->delete("/access/users/$username@pve");

        if(!isset($res['errors'])) {
            return $this->result(self::ANSWER_REMOVE_USER_FAIL);
        }

        return $this->result(self::ANSWER_REMOVE_USER_SUCCESS);

    }

    public function enableBackupJob($vmid){
        $backup_jobs = $this->pve->get('/cluster/backup');

        $backup_job = null;

        foreach($backup_jobs as $b){
            if($backup_job["vmid"] == $vmid){
                $backup_job = $b;
                break;
            }
        }

        $parameters = [
            'starttime' => $backup_job["starttime"],
            'enabled' => 1
        ];
        
        return $this->pve->put('/cluster/backup/'.$backup_job["id"], $parameters);
    }

    
    public function disableBackupJob($vmid){
        $backup_jobs = $this->pve->get('/cluster/backup')["data"];

        $backup_job = array();

        foreach($backup_jobs as $b){
            if($b["vmid"] == $vmid){
                $backup_job = $b;
                break;
            }
        }

        $parameters = [
            'starttime' => $backup_job["starttime"],
            'enabled' => 0
        ];
        
        return $this->pve->put('/cluster/backup/'.$backup_job["id"], $parameters);
    }

    public function createBackupJobForPBS($starttime, $dow, $vmid, $storage, $mode, $retention, $speedLimit){ //TODO: add mode (snapshot, suspend or stop)
        if($starttime == null || $dow == null || $vmid == null){
            return;
        }

        $days = "";
        foreach($dow as $day){
            $days .= substr($day, 0, 3) . ",";
        }

        $payload = [
            'starttime' => $starttime,
            'dow' => $days,
            'storage' => $storage,
            'enabled' => 1,
            'mode' => $mode,
            'vmid' => $vmid,
            'bwlimit' => $speedLimit,
            'enabled' => 1
            //'prune-backups' => "keep-last".$retention
        ];

        $this->pve->post("/cluster/backup", $payload);
    }

    public function getPBSWithMostStorageAvailable(){
        //Get all available storages
        $array = $this->pve->get('/cluster/resources?type=storage')["data"];

        //Get only storages with plugintype pbs (são servidores pbs)
        $pbs_server_list = array_filter($array, function($obj){
            if(strstr($obj['plugintype'], 'pbs', 1) !== false){
                return true;
            }
        });
        
        if(count($pbs_server_list) == 0){
            return;
        }

        $chosen_server = array_values($pbs_server_list)[0]; //array_values() to reorder indexes 

        foreach($pbs_server_list as $server){
            if(($server["disk"] / $server["maxdisk"]) < ($chosen_server["disk"] / $chosen_server["maxdisk"])){
                $chosen_server = $server;
            }
        }

        return $chosen_server["storage"]; 
    }

    public function getNFSWithMostStorageAvailable(){
        $array = $this->pve->get('/cluster/resources?type=storage')["data"];

        //Gets only NFS servers
        $nfs_server_list = array_filter($array, function($obj){
            if(strpos($obj['plugintype'], 'nfs', 0) !== false){
                return true;
            }
        });

        if(count($nfs_server_list) == 0){
            return;
        }

        $chosen_server = array_values($nfs_server_list)[0]; //array_values() to reorder indexes 

        foreach($nfs_server_list as $server){
            if(($server["disk"] / $server["maxdisk"]) < ($chosen_server["disk"] / $chosen_server["maxdisk"])){
                $chosen_server = $server;
            }
        }

        return $chosen_server["storage"]; 
    }

    public function getBackupsByVMID($node, $vmid, $storage){
        $backup_list = $this->pve->get("/nodes/". $node ."/storage/". $storage ."/content?content=backup&vmid=" . $vmid);
        return $backup_list;
    }

    public function restoreBackup($node, $parameters){
        $response = $this->pve->post("/nodes/". $node."/qemu", $parameters);
        if($response != null){
            return http_response_code(200);
        }
        return http_response_code(404);
    }

    public function deleteBackup($node, $storage, $volume){
        $this->pve->delete("/nodes/". $node ."/storage/".$storage."/content/".$volume);
    }

    public function checkIfUserExists($client_name){
        if($this->pve->get("/access/users/".$client_name."@pve") == null){
            return false;
        }
        return true;
    }   

    public function vncProxy($node, $vmid){
        $PVEAuthCookie = $this->pve->post("/nodes/pve1/qemu/102/vncproxy", array("websocket" => 1))["data"]["ticket"];
        return urlencode($PVEAuthCookie);
    }

    public function createVNCConnection($node, $vmid)
    {
        $validCharacters = "abcdefghijklmnopqrstuxyvwzABCDEFGHIJKLMNOPQRSTUXYVWZ0123456789";
        $validCharNumber = strlen($validCharacters);
     
        $password = "";

        $length = 7;
     
        for ($i = 0; $i < $length; $i++) {
            $index = mt_rand(0, $validCharNumber - 1);
            $password .= $validCharacters[$index];
        }

        $firstParam = [
            "command" => "change vnc 0.0.0.0:".$vmid.",password" 
        ];

        $secondParam = [
            "command" => "set_password vnc ".$password 
        ];

        $res = $this->pve->post("/nodes/".$node."/qemu/".$vmid."/monitor", $firstParam);
        $res2 = $this->pve->post("/nodes/".$node."/qemu/".$vmid."/monitor", $secondParam);

        return $password;
    }

    public function checkVPSNetworkingCap($node, $vmid){
        $entries = $this->pve->get("/nodes/".$node."/qemu/".$vmid."/rrddata?timeframe=hour&cf=AVERAGE");

        foreach($entries as $entrie){
            
        }
    }

    public function createNoVNCSocket(){
        $res = $this->pve->post('/nodes/pve1/qemu/102/vncproxy', ["websocket" => 1]);

        $vncticketDecoded = $res["data"]["ticket"];
        $vncticket = urlencode($res["data"]["ticket"]);
        $params = [
            "port" => "5900",
            "vncticket" => $vncticket
        ];

        $res2 = $this->pve->get('/nodes/pve1/qemu/102/vncwebsocket?'.urlencode("port=5900&vncticket=".$vncticket));
        //return "192.168.232.11:8006/api2/json/nodes/pve1/qemu/102/vncwebsocket?port=5900&vncticket=".$vncticket;
        return "http://hopebilling.test:8082/app/modules/novnc/vnc.html?host=192.168.232.11&port=8006&encrypt=true";
    }

    public function getVMTemplates(){
        $vps_server_list = $this->pve->get('/nodes')["data"]; 

        $templates = array();
        $vps_list = array();

        foreach($vps_server_list as $vps_server){// List of Nodes
            $vps_list = $this->pve->get('/nodes/'.$vps_server["node"].'/qemu')["data"];
        
            foreach($vps_list as $vps){ // List of vms 
                $vps_info = $this->pve->get('/nodes/'.$vps_server["node"].'/qemu/'.$vps["vmid"].'/config');
                if(array_key_exists("template", $vps_info["data"]) && $vps_info["data"]["template"] == 1){
                    array_push($templates, [
                        "node"      => $vps_server["node"],
                        "vmid"      => $vps,
                        "cores"     => $vps_info["data"]["cores"],
                        "sockets"   => $vps_info["data"]["sockets"]
                    ]);
                }
            }         
        }

        return $templates;
    }

    public function getNodeFromVMID($vmid){
        $nodes = $this->pve->get('/nodes')["data"];
        $nodeName = "";

        foreach($nodes as $node){
            $vms = $this->pve->get('/nodes/'.$node['node'].'/qemu')["data"];
            foreach($vms as $vm){
                if($vmid == $vm["vmid"]){
                    $nodeName = $node["node"];
                    return $nodeName;
                }
            }
        }
        return "";
    }


    public function getBackupJobByVMID($vmid){
        $jobs = $this->pve->get('/cluster/backup')["data"];
        
        foreach($jobs as $job){
            if($job["vmid"] == $vmid){
                return $job;                    
            }
        }
    }

    public function backupNow($node, $job){
       $res =  $this->pve->post('/nodes/'.$node.'/vzdump/',$job);
        return $res;
    }
}