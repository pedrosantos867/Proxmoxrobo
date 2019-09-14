<?php
namespace vps\vmmanager;

use System\Logger;
use vps\IVPSAPI;
use vps\VPSAPI;

class VMmanager extends VPSAPI implements IVPSAPI
{
    private $hostname;
    private $query = '';
    private $username;
    private $password;
    private $res = null;
    private $curl = null;

    private $execError = null;

    public function __construct($server)
    {
        $this->curl     = curl_init();
        $this->hostname = $server->host . '/vmmgr?authinfo=' . $server->username . ':' . $server->password . '&out=json';
        $this->query = '';
        $this->username = $server->login;
        $this->password = $server->pass;

    }




    public function checkConnection()
    {
        $res = $this->exec(array('func' => ''));

        if(isset($res->doc)){
            return $this->result(VPSAPI::ANSWER_CONNECTION_OK);
        }
        return $this->result(VPSAPI::ANSWER_CONNECTION_ERROR);
    }


    public function suspendVM($node, $vmid, $user, $type){
        $res = $this->exec(array('func' => 'vm.edit',
            'sok'           => 'ok',
            'elid'          => $vmid,
            'blocked'       => 'off'
        ));

        $this->exec(array('func' => 'vm.stop',
            'elid'          => $vmid
        ));

        if(isset($res->doc->ok)){
            return $this->result(VMmanager::ANSWER_SUSPEND_VM_SUCCESS );
        }
        return $this->result(VMmanager::ANSWER_SUSPEND_VM_FAIL);
    }

    public function unsuspendVM($node, $vmid, $user, $type){
        $res = $this->exec(array('func' => 'vm.edit',
            'sok'           => 'ok',
            'elid'          => $vmid,
            'blocked'       => 'off'
        ));
        $this->exec(array('func' => 'vm.start',
            'elid'          => $vmid
        ));

        if(isset($res->doc->ok)){
            return $this->result(VMmanager::ANSWER_UNSUSPEND_VM_SUCCESS );
        }
        return $this->result(VMmanager::ANSWER_UNSUSPEND_VM_FAIL);
    }

    private function getIsoIdByName($iso){
        $iso = $this->exec(array('func' => 'iso', 'name' => $iso));
        $id = 0;
        if(isset($iso->doc->elem)) {
            $image = isset($iso->doc->elem[0]) ? $iso->doc->elem[0] : null;
               if($image) {
                   $id = $image->id->{'$'};
               }
        }

        return $id;
    }

    public function returnImagesList($node){
        $iso = $this->exec(array('func' => 'iso'));
        $images = array();

        
        if(isset($iso->doc->elem)) {
            foreach ($iso->doc->elem as $image) {
                $images[$image->name->{'$'}] = $image->name->{'$'};
            }
        }
        return $images;
    }

    public function returnContainersList($node){
        $iso = $this->exec(array('func' => 'osmgr'));
        $images = array();


        if(isset($iso->doc->elem)) {
            foreach ($iso->doc->elem as $image) {
                $images[$image->name->{'$'}] = $image->name->{'$'};
            }
        }
        return $images;
    }

    public function returnNodesList(){
        $nodes = $this->exec(array('func' => 'vmhostnode'));
        $nodes_array = array();
        if(isset($nodes->doc->elem)) {
            foreach ($nodes->doc->elem as $item) {
                $nodes_array[$item->id->{'$'}] = $item->name->{'$'};
            }
        }

        return $nodes_array;
    }

    public function createUser($login, $full_name, $email, $password){
        $res = $this->exec(array('func' => 'user.edit',
            'sok' => 'ok',
            'name' => $login,
            'passwd' => $password,
            'confirm'=> $password,
            'isolimitsize' => '0',
            'isolimitnum' => '0',
            'snapshot_limit' => '0'
        ));

        if(isset($res->doc->ok)){
            return $this->result(VMmanager::ANSWER_CREATE_USER_SUCCESS,  $res->doc->id->{'$'});
        }

        if (isset($res->doc->error)) {
            if ($res->doc->error->{'$type'} == 'value' && $res->doc->error->{'$object'} == 'passwd') {
                return  $this->result(VMmanager::ANSWER_CREATE_USER_FAIL, array('field' => 'password', 'type' => 'validate'));
            }
            if ($res->doc->error->{'$type'} == 'exists' && $res->doc->error->{'$object'} == 'user') {
                return  $this->result(VMmanager::ANSWER_CREATE_USER_FAIL, array('field' => 'user', 'type' => 'exist'));
            }
            if ($res->doc->error->{'$type'} == 'value' && $res->doc->error->{'$object'} == 'name') {
                return  $this->result(VMmanager::ANSWER_CREATE_USER_FAIL, array('field' => 'name', 'type' => 'length'));
            }

            return $this->result(VMmanager::ANSWER_CREATE_USER_FAIL);
        }

        return $this->result(VMmanager::ANSWER_CREATE_USER_FAIL);


    }


    public function createVM($node, $type, $memory, $hdd, $cores, $image, $socket, $user, $password, $net_type, $net='', $domain = '', $recipe = ''){
        $params = array('func' => 'vm.edit',
            'sok'           => 'ok',
            'name'          => uniqid(),
            'domain'        => $domain,
            'hostnode'      => $node,
            'user'          => $user,
            'ip'            => '',
            'vmi'           => '0',
            'installtype'   => 'installiso',
            'osname'        => 'Default OS',
            'iptype'        => 'nat',
            'mem'           => $memory,
            'vcpu'          => $cores,
            'vsize' => $hdd * 1000,
            'status'        => 1,
            'password'      => $password,
            'confirm'       => $password
        );

        if($type==1){
            $params['installtype'] = 'installtemplate';
            $params['vmi']         = 'ISPsystem__'.$image;
            $params['osname']      = $image;
            if($recipe){
                $params['recipe']      = $recipe;
            }

        } else {
            $params['installtype'] = 'installiso';
            $params['osname']      = $image;
            $params['iso']         = $this->getIsoIdByName($image);
        }

        if ($net_type == 3) { // nat static ip
            $params['iptype'] = 'nat';
            $params['family'] = 'ipv4';
            if (isset($net['ip'])) {
                $params['family'] = 'special';
                $params['ip'] = $net['ip'];
            }
        }

        if ($net_type == 5) { // static ipv4 auto
            $params['iptype'] = 'public';
            $params['family'] = 'ipv4';
            if (isset($net['ip'])) {
                $params['family'] = 'special';
                $params['ip'] = $net['ip'];
            }
        }

        if ($net_type == 7) { // static private ipv4 auto
            $params['iptype'] = 'private';
            $params['family'] = 'ipv4';
            if (isset($net['ip'])) {
                $params['family'] = 'special';
                $params['ip'] = $net['ip'];
            }
        }

        if ($net_type == 1) { // nat ipv4 auto
            $params['iptype'] = 'nat';
            $params['family'] = 'ipv4';
        }

        if ($net_type == 4) { // public ipv4 auto
            $params['iptype'] = 'public';
            $params['family'] = 'ipv4';
        }

        if ($net_type == 6) { // private ipv4 auto
            $params['iptype'] = 'private';
            $params['family'] = 'ipv4';
        }


        $res = $this->exec($params);
        Logger::log('Vmmanager:' . json_encode($params));
        if (isset($res->doc->error)) {
            Logger::log('Vmmanager:' . json_encode($res->doc->error));
        }
        if(isset($res->doc->ok)){
            return $this->result(VMmanager::ANSWER_CREATE_VM_SUCCESS, $res->doc->elid->{'$'} );
        }
        return $this->result(VMmanager::ANSWER_CREATE_VM_FAIL);
    }

    public function returnRecipesList($node){

        $iso = $this->exec(array('func' => 'recipemgr'));
        $images = array();


        if(isset($iso->doc->elem)) {
            foreach ($iso->doc->elem as $image) {
                $images[$image->id->{'$'}] = $image->name->{'$'};
            }
        }
        return $images;
    }

    public function removeVM($node, $vmid, $username, $type){
        $res = $this->exec(array('func' => 'vm.edit',
            'elid'          => $vmid
        ));

        if(isset($res->doc->name->{'$'})) {
            $res = $this->exec(array('func' => 'vm.delete',
                'elid'          => $vmid,
                'sok'          => 'ok',
                'name'          => $res->doc->name->{'$'}
            ));

        }


        if(isset($res->doc->ok)){
            return $this->result(VMmanager::ANSWER_REMOVE_VM_SUCCESS );
        }
        return $this->result(VMmanager::ANSWER_REMOVE_VM_FAIL);
    }

    public function removeUser($username){

        $res = $this->exec(array('func' => 'user.edit',
            'elid'          => $username
        ));


        if(isset($res->doc->id->{'$'})) {
            $res = $this->exec(array('func' => 'user.delete',
                'elid' => $res->doc->id->{'$'},
                'sok' => 'ok'
            ));

        }

        if(isset($res->doc->ok)){
            return $this->result(VMmanager::ANSWER_REMOVE_USER_SUCCESS );
        }
        return $this->result(VMmanager::ANSWER_REMOVE_USER_FAIL);
    }



    private function exec($params = array())
    {


        $this->query .= '&' . http_build_query($params);

        $this->execError = null;

        curl_setopt($this->curl, CURLOPT_URL, $this->hostname . $this->query);

        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
        $this->res = json_decode(curl_exec($this->curl));
      //  echo 111;
       // echo $this->hostname . $this->query;
        $this->query = '';


        if ($this->res == '') {
            return null;
        }

        if (isset($this->res->doc->error)) {
                if (($this->res->doc->error->{'$type'}) == 'auth') {
                    return null;
                }

            if (isset($this->res->doc->error->{'$object'}) && $this->res->doc->error->{'$object'} == 'badpassword') {
                return null;
            }
        }

        return $this->res;
    }



}