<?php


namespace hosting;


use System\Tools;
use System\Logger;

class ISPManager4API implements IHostingAPI
{
    private $hostname;
    private $query = '';
    private $username;
    private $password;
    private $res = null;

    private $link = '';

    private $execError = null;
    private $_error;

    public function __construct($server)
    {
        $this->curl     = curl_init();
        $this->hostname = $server->host . '/manager/ispmgr?authinfo=' . $server->login . ':' . $server->pass . '&out=json';
        $this->query = '';
        $this->link = $server->ip ? $server->ip : $server->host;
        $this->username = $server->login;
        $this->password = $server->pass;

    }
    public function getErrorDetails(){
        return $this->_error;
    }
    public function changeUserPassword($user, $password)
    {
        // echo 99;
        $this->query .= '&' . http_build_query(array('func' => 'user.edit', 'elid' => $user, 'sok' => 'ok', 'passwd' => $password));

        if ($this->exec()) {

            if (isset($this->res->ok)) {
                return HostingAPI::ANSWER_OK;
            }

            if (isset($this->res->error)) {
                if ($this->res->error->code == 4) {
                    return HostingAPI::ANSWER_USER_NOT_EXIST;
                }

            }

            return HostingAPI::ANSWER_SYSTEM_ERROR;
        } else {
            return $this->execError;
        }
    }

    public function userExist($user)
    {
        $this->query .= '&' . http_build_query(array('func' => 'user.edit', 'elid' => $user));

        if ($this->exec()) {
            if (isset($this->res->elid)) {
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
                    'func'     => 'user.edit',
                    'name'     => $data['username'],
                    'fullname' => $data['last_name'] . ' ' . $data['first_name'],
                    'preset'   => $data['package'],
                    'status' => 1,
                    'owner' => 'root',
                    'passwd'   => $data['password'],
                    'confirm'  => $data['password'],
                    'sok'      => 'ok'
                )
            );

        if ($this->exec()) {

            if(isset($this->res->ok)){
                return HostingAPI::ANSWER_OK;
            }
            if ($this->res->error->code == 2) {
                return HostingAPI::ANSWER_USER_ALREADY_EXIST;
            }

            return HostingAPI::ANSWER_SYSTEM_ERROR;


        } else {
            return $this->execError;
        }

    }

    public function suspendUser($user)
    {
        $this->query .= '&' . http_build_query(array('func' => 'user.disable', 'elid' => $user));
        if ($this->exec()) {


            if (isset($this->res->error)) {
                if ($this->res->error->code == 3) {
                    return HostingAPI::ANSWER_USER_NOT_EXIST;
                }
            }

            if (isset($this->res->ok)) {
                return HostingAPI::ANSWER_OK;
            }

        } else {
            return $this->execError;
        }


    }

    public function unsuspendUser($user)
    {
        $this->query .= '&' . http_build_query(array('func' => 'user.enable', 'elid' => $user));
        if ($this->exec()) {
            if (isset($this->res->ok)) {
                return HostingAPI::ANSWER_OK;
            }

            if (isset($this->res->error)) {
                if ($this->res->error->code == 3 ) {
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


        if ($this->res != '' && is_object($this->res) && !isset($this->res->authfail)) {

            return HostingAPI::ANSWER_OK;

        } else if (isset($this->res->authfail)) {


            return HostingAPI::ANSWER_AUTH_ERROR;

        } else if ($this->res == '') {
            return HostingAPI::ANSWER_CONNECTION_ERROR;
        }

        return HostingAPI::ANSWER_CONNECTION_ERROR;
    }


    public function removeUser($user)
    {
        $this->query .= '&' . http_build_query(array('func' => 'user.delete', 'elid' => $user));

        if ($this->exec()) {

            if (isset($this->res->ok)) {
                return HostingAPI::ANSWER_OK;
            }

            if ($this->res->error->code == 3) {
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


            if (isset($this->res->elid)) {
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

        $this->query .= '&' . http_build_query(array('func' => 'preset.edit', 'elid' => $plan));
        $this->exec();

        Logger::log('ispmanager:'.json_encode($this->res));

        if(!isset($this->res->disklimit)){
            return HostingAPI::ANSWER_PLAN_NAME_NOT_VALID;
        }



        $this->query = '';
        $this->query .= '&' . http_build_query(
                array(
                    'func' => 'user.edit',
                    'elid' => $user,
                    'name' => $user,
                    'sok' => 'ok',
                    'preset' => $plan,
                    'phpcgi' => $this->res->phpcgi,
                    'cgi' => $this->res->cgi,
                    'ssl' => $this->res->ssl,

                    'disklimit' => $this->res->disklimit,
                    'bandwidthlimit' => $this->res->bandwidthlimit,
                    'baselimit' => $this->res->baselimit,
                    'baseuserlimit' => $this->res->baseuserlimit,
                    'domainlimit' => $this->res->domainlimit,
                    'ftplimit' => $this->res->ftplimit,
                    'maildomainlimit' => $this->res->maildomainlimit,
                    'maillimit' => $this->res->maillimit,
                    'mailrate' => $this->res->mailrate,
                    'webdomainlimit' => $this->res->webdomainlimit,
                    'phphandler' => $this->res->phphandler,
                    'wwwcharset' => $this->res->wwwcharset,
                    'wwwindexpage' => $this->res->wwwindexpage
                )
            );

        if ($this->exec()) {

            Logger::log('ispmanager:'.json_encode($this->res));

            if (isset($this->res->ok)) {
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

            if (isset($obj->elem)) {
                foreach ($obj->elem as $elem) {
                    $plans[] = $elem->name;
                }
            }
        }
        return $plans;

    }

    private $curl;

    public function setTimeout($time)
    {
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT_MS, $time);
    }

    public function userAuth($username){
        $key = Tools::generateCode(17);
        $this->query .= '&' . http_build_query(array('func' => 'session.newkey', 'username' => $username, 'key' => $key));
        $this->exec();

        $link = $this->link.'/ispmgr?func=auth&username='.$username.'&key='.$key.'&checkcookie=no';
        return $link;

    }

    private function exec()
    {
        $this->execError = null;
//echo $this->hostname . $this->query;
        curl_setopt($this->curl, CURLOPT_URL, $this->hostname . $this->query);

        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
        $this->res = json_decode(curl_exec($this->curl));
        //   print_r($this->res);
        $this->query = '';
        // echo curl_error($this->curl);
        if ($this->res == '') {
            $this->execError = HostingAPI::ANSWER_CONNECTION_ERROR;

            return false;
        }

        if (isset($this->res->doc->error)) {
            if (($this->res->doc->error->{'$type'}) == 'auth') {
                $this->execError = HostingAPI::ANSWER_CONNECTION_ERROR;

                return false;
            }

            if ($this->res->doc->error->{'$object'} == 'badpassword') {
                $this->execError = HostingAPI::ANSWER_CONNECTION_ERROR;

                return false;
            }
        }

        return true;
    }





}