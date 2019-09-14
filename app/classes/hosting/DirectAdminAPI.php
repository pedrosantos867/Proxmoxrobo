<?php

namespace hosting;

use tools\IdnaConvertor;
use tools\HTTPSocket;

class DirectAdminAPI implements IHostingAPI{

    private $username;

    private $password;
    private $hostname;
    private $_error;


    public function __construct($server)
    {
        $this->curl = curl_init();

        $this->hostname = $server->host ;
        $this->username = $server->login;
        $this->password = $server->pass;
    }

    public function checkConnection(){
        $res = $this->exec('CMD_API_SHOW_USER_USAGE');

        if(isset($res['bandwidth'])){
            return HostingAPI::ANSWER_OK;
        }

        return HostingAPI::ANSWER_CONNECTION_ERROR;
    }

    public function planExist($plan_name){
        $res = $this->exec('CMD_API_PACKAGES_USER', ['package' => $plan_name]);
        if(is_array($res) && !empty($res)){
            return HostingAPI::ANSWER_PLAN_EXIST;
        }

        return HostingAPI::ANSWER_PLAN_NOT_EXIST;
    }

    public function getPlans(){
        $res = $this->exec('CMD_API_PACKAGES_USER');
        $plans = array();
        if(isset($res['list'])) {

            foreach ($res['list'] as $name) {
                $plans[$name] = $name;
            }
        }
        return $plans;
    }

    public function changePlan($user_name, $new_plan_name){
        $res = $this->exec('CMD_API_MODIFY_USER', ['user' => $user_name, 'action' => 'package', 'package' => $new_plan_name], 'POST');
        if(isset($res['error']) && $res['error'] == 0){
            return HostingAPI::ANSWER_OK;
        }

        return HostingAPI::ANSWER_SYSTEM_ERROR;
    }

    public function createUser($data){

        $idna = new IdnaConvertor();
        $user_form = array(
            'action'    => 'create',
            'add'       => 'Submit',
            'username'  => $data["username"],
            'email'     => $data["email"],
            'passwd'    => $data["password"],
            'passwd2'   => $data["password"],
            'domain'    => $idna->encode($data["domain"]),
            'package'   => $data["package"],
            'ip'        => $this->getDefaultIP(),
            'notify'    => 'yes'
        );
        $result = $this->exec('CMD_API_ACCOUNT_USER', $user_form );

        if (isset($result['error']) && $result['error'] == "0")
        {
            return HostingAPI::ANSWER_OK;
        }
        elseif(isset($result['details'])) {
            if($result['details'] == 'Package not found'){
                return HostingAPI::ANSWER_PLAN_NOT_EXIST;
            } elseif($result['details'] == 'Invalid Email Address'){
                return HostingAPI::ANSWER_USER_EMAIL_NOT_VALID;
            } elseif ($result['details'] == 'That username already exists on the system'){
                return HostingAPI::ANSWER_USER_ALREADY_EXIST;
            } elseif ($result['details'] == 'Invalid Password'){
                return HostingAPI::ANSWER_USER_PASSWORD_NOT_VALID;
            }
            $this->_error = $result['details'];
        }

       // print_r($result);

        return HostingAPI::ANSWER_SYSTEM_ERROR;
    }

    public function getErrorDetails(){
        return $this->_error;
    }

    private function getDefaultIP(){
        $res = ($this->exec('CMD_API_SHOW_RESELLER_IPS?action=all'));

        foreach ($res as $ip => $data) {
            parse_str($data, $data);
            if(is_array($data) && isset($data['status']) && $data['status'] == 'shared'){
                return str_replace('_','.', $ip);
            }

        }
        foreach ($res as $ip => $data) {
            parse_str($data, $data);
            if (is_array($data) && isset($data['status']) && $data['status'] == 'server') {
                return str_replace('_', '.', $ip);
            }

        }
        foreach ($res as $ip => $data) {
            parse_str($data, $data);
            if (is_array($data) && isset($data['status']) && $data['status'] == 'free') {
                return str_replace('_', '.', $ip);
            }

        }
        return null;
    }

    public function removeUser($user_name){         //http://www.directadmin.com/api.html#delete POST
        $result = $this->exec('CMD_API_SELECT_USERS', ['select0' => $user_name, 'delete' => 'yes', 'confirmed' => 'Confirm'], 'POST');
        if (isset($result['error']) && $result['error'] == "0")
        {
            return HostingAPI::ANSWER_OK;
        }
        return HostingAPI::ANSWER_SYSTEM_ERROR;
    }

    public function suspendUser($user){             //http://www.directadmin.com/api.html#suspend POST

        $data = array(
            'select0' => $user,
            'location' => 'CMD_SELECT_USERS',
            'dosuspend' => 'Suspend',
        );

        $result = $this->exec('CMD_API_SELECT_USERS', $data, 'POST');


        if (isset($result['error']) && $result['error'] == "0")
        {
            return HostingAPI::ANSWER_OK;
        }
        return HostingAPI::ANSWER_SYSTEM_ERROR;
    }

    public function unsuspendUser($user){

        $data = array(
            'select0' => $user,
            'location' => 'CMD_SELECT_USERS',
            'dounsuspend' => 'Unsuspend',
        );

        $result = $this->exec('CMD_API_SELECT_USERS', $data, 'POST');

        if (isset($result['error']) && $result['error'] == "0")
        {
            return HostingAPI::ANSWER_OK;
        }
        return HostingAPI::ANSWER_SYSTEM_ERROR;

    }

    public function userExist($user){               //http://www.directadmin.com/api.html#showallusers GET/POST
        $res = $this->exec('CMD_API_SHOW_USER_CONFIG', ['user'=> $user]);

        if(isset($res['username']) && $res['username']){
            return HostingAPI::ANSWER_USER_EXIST;
        } else if(isset($res['details']) && $res['details'] == 'Error reading his/her user files') {
            return HostingAPI::ANSWER_USER_NOT_EXIST;
        }

        return HostingAPI::ANSWER_SYSTEM_ERROR;
    }

    public function changeUserPassword($user, $new_password){  //http://www.directadmin.com/api.html#email GET/POST
        $data = array(
            'username' => $user,                //НЕПОНЯТНО
            'passwd' => $new_password,
            'passwd2' => $new_password
        );
        $result = $this->exec('CMD_API_USER_PASSWD', $data);
        if ($result['error'] != "0"){
            HostingAPI::ANSWER_OK;
        }

        return HostingAPI::ANSWER_SYSTEM_ERROR;
    }

    private function exec($cmd, $data=array(), $method = 'GET'){

        $sock = new HTTPSocket();

        $protocol_host = explode("://", $this->hostname);
        $host = $protocol_host[1];
         $arr = explode(":", $host);
        $port = $arr[1];
        $host = $arr[0];

        $protocol = $protocol_host[0];

        if ($protocol == 'http') {
            $sock->connect($host, $port);
        } else {
            $sock->connect('ssl://' . $host, $port);
        }

        $sock->set_login($this->username, $this->password);
        $sock->set_method($method);

        $sock->query('/'.$cmd, $data);

        $result = $sock->fetch_parsed_body();

        return $result;

    }
}