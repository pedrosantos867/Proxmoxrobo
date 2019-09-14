<?php


namespace hosting;


class cPanelAPI implements IHostingAPI
{
    private $hostname;
    private $query = '';
    private $username;
    private $password;
    private $res = '';
    private $object;
    private $execError = null;
    private $curl;
    private $_error;

    public function __construct($server)
    {
        $this->curl = curl_init();

        $this->hostname = $server->host . '/json-api/';
        $this->query    = '';
        $this->username = $server->login;
        $this->password = $server->pass;

    }

    public function getErrorDetails(){
        return $this->_error;
    }
    public function changeUserPassword($user, $password){
        $this->query .= 'passwd?'. http_build_query(array('user' => $user, 'password' => $password));
        if ($this->exec()) {
            print_r($this->object);
            if ($this->object->metadata->result) {
                return HostingAPI::ANSWER_OK;
            }

            if (preg_match('/(.*) passwords must be at least (.*)/', $this->object->metadata->reason)) {
                return HostingAPI::ANSWER_USER_PASSWORD_NOT_VALID;
            }
            if (preg_match('/(.*) the user (.*) does not exist./', $this->object->metadata->reason)) {
                return HostingAPI::ANSWER_USER_NOT_EXIST;
            }
            if (preg_match("/(.*) the password you selected cannot be used (.*)/", $this->object->metadata->reason)) {
                return HostingAPI::ANSWER_USER_PASSWORD_NOT_VALID;
            }

            return HostingAPI::ANSWER_SYSTEM_ERROR;

        } else {
            return $this->execError;
        }
    }

    public function userExist($user)
    {
        $this->query .= 'accountsummary?' . http_build_query(array('user' => $user));

        if ($this->exec()) {
            if ($this->object->metadata->result) {
                return HostingAPI::ANSWER_USER_EXIST;
            } else {
                return HostingAPI::ANSWER_USER_NOT_EXIST;
            }
        } else {
            return $this->execError;
        }
    }

    public function planExist($name)
    {

        $this->query .= 'getpkginfo?' . http_build_query(array('pkg' => $name));

        if ($this->exec()) {

            if (isset($this->object->data->pkg)) {
                return HostingAPI::ANSWER_PLAN_EXIST;
            } else {
                return HostingAPI::ANSWER_PLAN_NOT_EXIST;
            }
        } else {
            return $this->execError;
        }

    }

    public function createUser($data)
    {

        $this->query .= 'createacct?' . http_build_query(array(
                    'username'     => $data['username'],
                    'domain'       => $data['domain'],
                    'plan'         => $data['package'],
                    'password'     => $data['password'],
                    'contactemail' => $data['email'],
                )
            );

        if ($this->exec()) {

            //  print_r($this->object);
            if ($this->object->metadata->result) {
                return HostingAPI::ANSWER_OK;
            }
            // print_r($this->object);
            if (preg_match('/(.*) is not a valid username on this system./', $this->object->metadata->reason)) {
                return HostingAPI::ANSWER_USER_NAME_NOT_VALID;
            }
            if (preg_match('/(.*) passwords must be at least (.*)/', $this->object->metadata->reason)) {
                return HostingAPI::ANSWER_USER_PASSWORD_NOT_VALID;
            }
            if (preg_match("/(.*) the password you selected cannot be used (.*)/", $this->object->metadata->reason) ||
                preg_match("/(.*)password may not contain the username for security reasons(.*)/", $this->object->metadata->reason)

            ) {
                return HostingAPI::ANSWER_USER_PASSWORD_NOT_VALID;
            }

            if (preg_match("/(.*) system already has an account (.*)/", $this->object->metadata->reason)
            ) {
                return HostingAPI::ANSWER_USER_ALREADY_EXIST;
            }


            if (preg_match('/The name of another account on this server has the same initial (.*)/', $this->object->metadata->reason)) {
                return HostingAPI::ANSWER_USER_NAME_NOT_VALID;
            }


            if (preg_match('/(.*) domain (.*) already exists(.*)/', $this->object->metadata->reason)) {
                return HostingAPI::ANSWER_DOMAIN_ALREADY_EXIST;
            }

            $this->_error = $this->object->metadata->reason;

            return HostingAPI::ANSWER_SYSTEM_ERROR;

        } else {
            return $this->execError;
        }
    }

    public function suspendUser($user)
    {
        $this->query .= 'suspendacct?' . http_build_query(array('user' => $user));
        if ($this->exec()) {
            if ($this->object->metadata->result) {
                return HostingAPI::ANSWER_OK;
            } else {
                return HostingAPI::ANSWER_SYSTEM_ERROR;
            }
        } else {
            return $this->execError;
        }
    }

    public function unsuspendUser($user)
    {
        $this->query .= 'unsuspendacct?' . http_build_query(array('user' => $user));
        // $this->exec();
        if ($this->exec()) {
            if ($this->object->metadata->result) {
                return HostingAPI::ANSWER_OK;
            } else {
                return HostingAPI::ANSWER_SYSTEM_ERROR;
            }
        }

        return $this->execError;
    }

    public function checkConnection()
    {
        $this->query .= '?gethostname';
        if ($this->exec()) {
            return HostingAPI::ANSWER_OK;
        }

        return $this->execError;
    }


    public function removeUser($user)
    {
        $this->query .= 'removeacct?' . http_build_query(array('username' => $user));


        if ($this->exec()) {

            if ($this->object->metadata->result) {
                return HostingAPI::ANSWER_OK;
            }

            return HostingAPI::ANSWER_SYSTEM_ERROR;
        }

        return $this->execError;
    }


    public function changePlan($user, $plan)
    {
        $this->query .= 'changepackage?' . http_build_query(array('user' => $user, 'pkg' => $plan));
        if ($this->exec()) {
            if ($this->object->metadata->result) {
                return HostingAPI::ANSWER_OK;
            }

            if (preg_match('/Sorry the user (.*) does not exist/', $this->object->metadata->reason)) {
                return HostingAPI::ANSWER_USER_NOT_EXIST;
            }
            if (preg_match('/Specified package (.*) does not exist/', $this->object->metadata->reason)) {
                return HostingAPI::ANSWER_PLAN_NOT_EXIST;
            }

            return HostingAPI::ANSWER_SYSTEM_ERROR;
        } else {
            return $this->execError;
        }
    }

    public function getPlans()
    {
        $this->query .= 'listpkgs?';
        $this->exec();

        $obj   = $this->object;

        $plans = array();

        if (isset($obj->data->pkg)) {
            foreach ($obj->data->pkg as $elem) {
                $plans[$elem->name] = $elem->name;
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

        $this->query .= '&api.version=1';
        // echo $this->hostname . $this->query;
        $this->execError = null;
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 0);       // Allow self-signed certs
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 0);       // Allow certs that do not match the hostname
        curl_setopt($this->curl, CURLOPT_HEADER, 0);               // Do not include header in output
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);       // Return contents of transfer on curl_exec
        $header[0] = "Authorization: Basic " . base64_encode($this->username . ":" . $this->password) . "\n\r";
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $header);    // set the username and password
        curl_setopt($this->curl, CURLOPT_URL, $this->hostname . $this->query);            // execute the query





        $this->res    = curl_exec($this->curl);
        $this->object = json_decode($this->res);

        //  echo curl_error($this->curl);

        // echo $this->res;

        curl_close($this->curl);


        $this->query = '';

        if ($this->res == '' || (isset($this->object->cpanelresult->data->reason) && $this->object->cpanelresult->data->reason == 'Access denied')) {
            $this->execError = HostingAPI::ANSWER_CONNECTION_ERROR;

            return false;
        }

        // print_r($this->object);

        return true;
    }




}