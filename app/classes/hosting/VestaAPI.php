<?php


namespace hosting;


use System\Exception;
use System\Tools;

class VestaAPI implements IHostingAPI
{
    private $vst_hostname;
    private $vst_username;
    private $vst_password;

    private $curl;
    private $res = '';
    private $postvars;
    private $execError = null;

    public function __construct($server)
    {
        $this->curl = curl_init();
        $this->vst_hostname = $server->host . '/api/';
        $this->vst_username = $server->login;
        $this->vst_password = $server->pass;
    }

    public function changeUserPassword($user, $password)
    {
        $this->postvars = array(
            'cmd'  => 'v-change-user-password',
            'arg1' => $user,
            'arg2' => $password,
            'arg3' => 'json'
        );

        if ($this->exec()) {
            if ($this->res == 'OK') {
                return HostingAPI::ANSWER_OK;
            }
            if (preg_match("/USER (.*) doesn't exist/", $this->res)) {
                return HostingAPI::ANSWER_USER_NOT_EXIST;
            }
        } else {
            return $this->execError;
        }
    }
    public function getErrorDetails(){
        return $this->res;
    }
    public function userExist($user)
    {
        $this->postvars = array(
            'cmd'  => 'v-list-user',
            'arg1' => $user,
            'arg2' => 'json'
        );

        if ($this->exec()) {

            $obj = $this->getObject();

            if (is_object($obj)) {
                return HostingAPI::ANSWER_USER_EXIST;
            } else {
                return HostingAPI::ANSWER_USER_NOT_EXIST;
            }
        } else {
            return $this->execError;
        }
    }



    public function createUser($data)
    {

        $this->postvars = array(
            'cmd'  => 'v-add-user',
            'arg1' => $data['username'],
            'arg2' => $data['password'],
            'arg3' => $data['email'],
            'arg4' => $data['package'],
            'arg5' => Tools::transliteration(  str_replace(' ', '', $data['first_name']) ),
            'arg6' => Tools::transliteration(str_replace(' ', '', $data['last_name']) )

        );

        if ($this->exec()) {

            //Error: argument package is not valid (empty)
            if (preg_match("/Error: package (.*) doesn't exist/", $this->res)) {
                return HostingAPI::ANSWER_PLAN_NOT_EXIST;
            }
            if (preg_match("/Error: user (.*) exists/", $this->res)) {
                return HostingAPI::ANSWER_USER_ALREADY_EXIST;
            }
            if (preg_match("/Error: email (.*) is not valid/", $this->res)) {
                return HostingAPI::ANSWER_USER_EMAIL_NOT_VALID;
            }
            if (preg_match("/Error: user (.*) is not valid/", $this->res)) {
                return HostingAPI::ANSWER_USER_NAME_NOT_VALID;
            }
            if(preg_match("/Error: password is too short/", $this->res)){
                return HostingAPI::ANSWER_USER_PASSWORD_NOT_VALID;
            }

            if ($this->res == 'OK') {
                return HostingAPI::ANSWER_OK;
            }

            return HostingAPI::ANSWER_SYSTEM_ERROR;
        }

        return $this->execError;

    }


    public function suspendUser($user)
    {
        $this->postvars = array(
            'cmd'  => 'v-suspend-user',
            'arg1' => $user

        );

        if ($this->exec()) {
            if (preg_match("/Error: user (.*) is not valid/", $this->res)) {
                return HostingAPI::ANSWER_USER_NAME_NOT_VALID;
            }
            if (preg_match("/USER (.*) doesn't exist/", $this->res)) {
                return HostingAPI::ANSWER_USER_NOT_EXIST;
            }

            if ($this->res == 'OK') {
                return HostingAPI::ANSWER_OK;
            }

            return HostingAPI::ANSWER_SYSTEM_ERROR;
        } else {
            return $this->execError;
        }

    }

    public function unsuspendUser($user)
    {
        $this->postvars = array(
            'cmd'  => 'v-unsuspend-user',
            'arg1' => $user

        );

        if ($this->exec()) {
            if (preg_match("/Error: user (.*) is not valid/", $this->res)) {
                return HostingAPI::ANSWER_USER_NAME_NOT_VALID;
            }
            if (preg_match("/USER (.*) doesn't exist/", $this->res)) {
                return HostingAPI::ANSWER_USER_NOT_EXIST;
            }

            if ($this->res == 'OK') {
                return HostingAPI::ANSWER_OK;
            }

            return HostingAPI::ANSWER_SYSTEM_ERROR;
        } else {
            return $this->execError;
        }
    }

    public function removeUser($user)
    {

        $this->postvars = array(
            'cmd'  => 'v-unsuspend-user',
            'arg1' => $user
        );
        $this->exec();

        $this->postvars = array(
            'cmd'  => 'v-delete-user',
            'arg1' => $user
        );
        if ($this->exec()) {
            if (preg_match("/Error: user (.*) is not valid/", $this->res)) {
                return HostingAPI::ANSWER_USER_NAME_NOT_VALID;
            }
            if (preg_match("/USER (.*) doesn't exist/", $this->res)) {
                return HostingAPI::ANSWER_USER_NOT_EXIST;
            }

            if ($this->res == 'OK') {
                return HostingAPI::ANSWER_OK;
            }

            return HostingAPI::ANSWER_SYSTEM_ERROR;
        } else {
            return $this->execError;
        }
    }


    public function planExist($name)
    {

        $postvars       = array(
            'cmd'  => 'v-list-user-package',
            'arg1' => $name,
            'arg2' => 'json'
        );
        $this->postvars = ($postvars);

        if ($this->exec()) {

            if ($this->getObject()) {
                return HostingAPI::ANSWER_PLAN_EXIST;
            }

            return HostingAPI::ANSWER_PLAN_NOT_EXIST;

        } else {
            return $this->execError;
        }
    }



    public function changePlan($user, $plan)
    {
        $postvars       = array(
            'cmd'  => 'v-change-user-package',
            'arg1' => $user,
            'arg2' => $plan
        );
        $this->postvars = ($postvars);


        if ($this->exec()) {
            // echo $this->res;
            if (preg_match("/Error: user (.*) is not valid/", $this->res)) {
                return HostingAPI::ANSWER_USER_NAME_NOT_VALID;
            }
            if (preg_match("/USER (.*) doesn't exist/", $this->res)) {
                return HostingAPI::ANSWER_USER_NOT_EXIST;
            }
            if (preg_match("/Error: package (.*) doesn't exist/", $this->res)) {
                return HostingAPI::ANSWER_PLAN_NOT_EXIST;
            }
            if (preg_match("/Error: package (.*) is not valid/", $this->res)) {
                return HostingAPI::ANSWER_PLAN_NAME_NOT_VALID;
            }

            if ($this->res == 'OK') {
                return HostingAPI::ANSWER_OK;
            }

            return HostingAPI::ANSWER_SYSTEM_ERROR;
        } else {
            return $this->execError;
        }

    }

    public function getPlans()
    {
        $postvars       = array(
            'cmd'  => 'v-list-user-packages',
            'arg1' => 'json'
        );
        $this->postvars = ($postvars);

        $this->exec();
        $plans = array();

        if(is_object($this->getObject())) {
            foreach ($this->getObject() as $name => $options) {
                $plans[$name] = $name;
            }
        }

        return $plans;

    }


    public function setTimeout($time)
    {
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT_MS, $time);
    }

    private function exec()
    {
        $this->postvars['user']     = $this->vst_username;
        $this->postvars['password'] = $this->vst_password;

        // Send POST query via cURL
        $postdata = http_build_query($this->postvars);
        // print_r($postdata);

        curl_setopt($this->curl, CURLOPT_URL,            $this->vst_hostname);

        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($this->curl, CURLOPT_POST,           true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $postdata);
        $this->res = curl_exec($this->curl);


        if ($this->res == '') {
            $this->execError = HostingAPI::ANSWER_CONNECTION_ERROR;

            return false;
        }
        if ($this->res == 'Error: authentication failed') {
            $this->execError = HostingAPI::ANSWER_CONNECTION_ERROR;

            return false;
        }

        return true;
    }


    private function getObject()
    {
        return json_decode($this->res);
    }


    public function checkConnection()
    {
        $postvars       = array(
            'cmd'  => 'v-list-sys-info',
            'arg1' => 'json'
        );
        $this->postvars = ($postvars);
        $this->exec();
        if ($this->getObject() && $this->getObject()->sysinfo) {
            return HostingAPI::ANSWER_OK;
        } else {
            return HostingAPI::ANSWER_CONNECTION_ERROR;
        }

    }




} 