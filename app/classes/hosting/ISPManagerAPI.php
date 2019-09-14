<?php


namespace hosting;


use System\Logger;
use System\Tools;

class ISPManagerAPI implements IHostingAPI
{
    private $hostname;
    private $query = '';
    private $username;
    private $password;
    private $res = null;

    private $execError = null;
    private $link = '';
    private $_error;

    public function __construct($server)
    {
        $this->curl     = curl_init();
        $this->hostname = $server->host . '/ispmgr?authinfo=' . $server->login . ':' . $server->pass . '&out=json';
        $this->query = '';
        $this->username = $server->login;
        $this->password = $server->pass;
        $this->link = $server->ip ? $server->ip : $server->host;
    }

    public function changeUserPassword($user, $password)
    {
        // echo 99;
        $this->query .= '&' . http_build_query(array('func' => 'user.edit', 'elid' => $user, 'sok' => 'ok', 'passwd' => $password));

        if ($this->exec()) {
            //print_r($this->res);
            if (isset($this->res->doc->ok)) {
                return HostingAPI::ANSWER_OK;
            }

            if (isset($this->res->doc->error)) {
                if ($this->res->doc->error->{'$object'} == 'elid' && $this->res->doc->error->{'$type'} == 'value') {
                    return HostingAPI::ANSWER_USER_NOT_EXIST;
                }
                if ($this->res->doc->error->{'$object'} == 'passwd' && $this->res->doc->error->{'$type'} == 'value') {
                    return HostingAPI::ANSWER_USER_PASSWORD_NOT_VALID;
                }
            }

            return HostingAPI::ANSWER_SYSTEM_ERROR;
        } else {
            return $this->execError;
        }
    }
    public function getErrorDetails(){
        return $this->_error;
    }
    public function userExist($user)
    {
        $this->query .= '&' . http_build_query(array('func' => 'user.edit', 'elid' => $user));

        if ($this->exec()) {
            if (isset($this->res->doc->elid->{'$'})) {
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

        $this->query .= '&' . http_build_query(array(
                    'func'     => 'user.add',
                    'name'     => $data['username'],
                    'domain'   => $data['domain'],
                    'fullname' => $data['last_name'] . ' ' . $data['first_name'],
                    'preset'   => $data['package'],
                    'status' => 1,
                    'passwd'   => $data['password'],
                    'sok'      => 'ok'
                )
            );

        if ($this->exec()) {
           // print_r($this->res);
            if (isset($this->res->doc->error) && isset($this->res->doc->error->{'$object'})) {
                if ($this->res->doc->error->{'$object'} == 'name' && $this->res->doc->error->{'$type'} == 'value') {
                    return HostingAPI::ANSWER_USER_NAME_NOT_VALID;
                }

                if ($this->res->doc->error->{'$object'} == 'passwd') {
                    return HostingAPI::ANSWER_USER_PASSWORD_NOT_VALID;
                }

                if ($this->res->doc->error->{'$object'} == 'user' && $this->res->doc->error->{'$type'} == 'exists') {
                    return HostingAPI::ANSWER_USER_ALREADY_EXIST;
                }
            }

            if (isset($this->res->doc->ok)) {
                return HostingAPI::ANSWER_OK;
            }

        } else {
            return $this->execError;
        }

    }

    public function suspendUser($user)
    {
        $this->query .= '&' . http_build_query(array('func' => 'user.suspend', 'elid' => $user));
        Logger::log('Suspend user Isp5:'. $this->query);
        if ($this->exec()) {
            Logger::log('Suspend user Isp5:'. json_encode($this->res));
            if (isset($this->res->doc->error)) {
                if ($this->res->doc->error->{'$object'} == 'users' && $this->res->doc->error->{'$type'} == 'missed') {
                    return HostingAPI::ANSWER_USER_NOT_EXIST;
                }
            }

            if (isset($this->res->doc->ok)) {
                return HostingAPI::ANSWER_OK;
            }

        } else {
            return $this->execError;
        }


    }

    public function unsuspendUser($user)
    {
        $this->query .= '&' . http_build_query(array('func' => 'user.resume', 'elid' => $user));
        if ($this->exec()) {
            if (isset($this->res->doc->ok)) {
                return HostingAPI::ANSWER_OK;
            }

            if (isset($this->res->doc->error)) {
                if ($this->res->doc->error->{'$object'} == 'users' && $this->res->doc->error->{'$type'} == 'missed') {
                    return HostingAPI::ANSWER_USER_NOT_EXIST;
                }
            }
        } else {
            $this->execError;
        }

    }

    public function checkConnection()
    {
        $this->query .= '&' . http_build_query(array('func' => 'auth', 'username' => $this->username, 'password' => $this->password));
        $this->exec();
        if ($this->res != '' && is_object($this->res) && !isset($this->res->doc->error)) {

            return HostingAPI::ANSWER_OK;

        } else if (isset($this->res->doc->error)) {

            if (($this->res->doc->error->{'$type'}) == 'auth') {
                return HostingAPI::ANSWER_AUTH_ERROR;
            }
        } else if ($this->res == '') {
            return HostingAPI::ANSWER_CONNECTION_ERROR;
        }

        return HostingAPI::ANSWER_CONNECTION_ERROR;
    }

    public function removeUser($user)
    {
        $this->query .= '&' . http_build_query(array('func' => 'user.delete', 'elid' => $user));

        if ($this->exec()) {

            if (isset($this->res->doc->ok)) {
                return HostingAPI::ANSWER_OK;
            }

            if ($this->res->doc->error->{'$object'} == 'elid' && $this->res->doc->error->{'$type'} == 'value') {
                return HostingAPI::ANSWER_USER_NOT_EXIST;
            }

            return HostingAPI::ANSWER_SYSTEM_ERROR;

        } else {
            return $this->execError;
        }
    }

    public function planExist($name)
    {

        $this->query .= '&' . http_build_query(array('func' => 'preset.edit', 'elid' => $name));

        if ($this->exec()) {
            if (isset($this->res->doc->elid->{'$'})) {
                return HostingAPI::ANSWER_PLAN_EXIST;
            } else {
                return HostingAPI::ANSWER_PLAN_NOT_EXIST;
            }
        } else {
            return $this->execError;
        }

    }

    public function changePlan($user, $plan)
    {
        $this->query .= '&' . http_build_query(array('func' => 'user.edit', 'elid' => $user, 'sok' => 'ok', 'preset' => $plan));

        if ($this->exec()) {

            if (isset($this->res->doc->ok)) {
                return HostingAPI::ANSWER_OK;
            }

            if (isset($this->res->doc->error)) {
                if ($this->res->doc->error->{'$object'} == 'users' && $this->res->doc->error->{'$type'} == 'missed') {
                    return HostingAPI::ANSWER_USER_NAME_NOT_VALID;
                }
                if ($this->res->doc->error->{'$object'} == 'preset' && $this->res->doc->error->{'$type'} == 'missed') {
                    return HostingAPI::ANSWER_PLAN_NAME_NOT_VALID;
                }
            }
        } else {
            return $this->execError;
        }

    }

    public function getPlans()
    {
        $this->query .= '&' . http_build_query(array('func' => 'preset'));


        $plans = array();
        if ($this->exec() > 0) {
            $obj = ($this->res);

            if (isset($obj->doc->elem)) {
                foreach ($obj->doc->elem as $elem) {
                    $plans[$elem->name->{'$'}] = $elem->name->{'$'};
                }
            }
        }
        return $plans;

    }

    public function userAuth($username){
        $key = Tools::generateCode(17);
        $this->query .= '&' . http_build_query(array('func' => 'session.newkey','username' => strtolower($username), 'key' => $key));
        $this->exec();
      //  print_r($this->res);
     //   exit();
        $link = $this->link.'/ispmgr?func=auth&username='.strtolower($username).'&key='.$key.'&checkcookie=no';
       
        return $link;
    }

    private $curl;

    public function setTimeout($time)
    {
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT_MS, $time);
    }

    private function exec()
    {
        $this->execError = null;

        curl_setopt($this->curl, CURLOPT_URL, $this->hostname . $this->query);

        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
        $this->res = json_decode(curl_exec($this->curl));

        $this->query = '';

        if ($this->res == '') {
            $this->execError = HostingAPI::ANSWER_CONNECTION_ERROR;

            return false;
        }

        if (isset($this->res->doc->error)) {
            if (($this->res->doc->error->{'$type'}) == 'auth') {
                $this->execError = HostingAPI::ANSWER_CONNECTION_ERROR;

                return false;
            }

            if (isset($this->res->doc->error->{'$object'}) && $this->res->doc->error->{'$object'} == 'badpassword') {
                $this->execError = HostingAPI::ANSWER_CONNECTION_ERROR;

                return false;
            }
        }

        return true;
    }





}