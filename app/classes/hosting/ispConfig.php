<?php

namespace hosting;

use System\Exception;

class ispConfig implements IHostingAPI{


    private $cp = null;
    private $is_connected = false;
    private $_error;

    public function __construct($server)
    {

        try {
            $this->cp = new \hosting\ispConfig\SoapClient($server->host . '/remote/index.php', $server->login, $server->pass);
            $this->is_connected = true;
        } catch (\Exception $e){
           // $this->cp = null;

            $this->is_connected = false;
        }
        // print_r($this->cp);
    }
    public function getErrorDetails(){
        return $this->_error;
    }
    /**
     * Check connection with the hosting panel
     * @return integer result code
     */
    public function checkConnection(){

        if($this->cp)
            return HostingAPI::ANSWER_OK;

        return HostingAPI::ANSWER_CONNECTION_ERROR;

    }

    /**
     * Suspending user account in the hosting panel
     * @param string $userName
     * @return integer result code
     */
    public function suspendUser($userName){

        if(!$this->is_connected)
            return HostingAPI::ANSWER_CONNECTION_ERROR;

        $client = $this->cp->clientGetByUsername( $userName );

        $sys_userid = $client['client_id'];
        $sys_groupid = $client['groups'];


        $client = $this->cp->clientGet($sys_userid);

        $client['password'] = '';
        $client['canceled'] = 'y';
        $client['locked'] = 'y';

        //  print_r($client);
        $res = $this->cp->clientUpdate($client['client_id'], $client['parent_client_id'], $client );


        $clientsites = $this->cp->clientGetSitesByUser($sys_userid, $sys_groupid);

        $i = 0;
        $j = 1;

        while ($j <= count($clientsites)) {
            $domainres = $this->cp->sitesWebDomainSetStatus($clientsites[$i]['domain_id'], 'inactive');
            $i++;
            $j++;
        }


        $ftpclient = $this->cp->sitesFtpUserGet(array('username' => $userName . '%'));
        $i = 0;
        $j = 1;

        while ($j <= count($ftpclient)) {
            $ftpclient[$i]['active'] = 'n';
            $ftpclient[$i]['password'] = '';
            $ftpid = $this->cp->sitesFtpUserUpdate($sys_userid, $ftpclient[$i]['ftp_user_id'], $ftpclient[$i]);

            $i++;
            $j++;
        }

        $domain_id = $this->cp->dnsZoneGetByUser($sys_userid, 1);
        $i = 0;
        $j = 1;
        while ($j <= count($domain_id)) {
            $affected_rows = $this->cp->dnsZoneSetStatus($domain_id[$i]['id'],
                'inactive');
            $i++;
            $j++;
        }



        return HostingAPI::ANSWER_OK;
    }


    /**
     * Unsuspending user account in the hosting panel
     * @param string $userName
     * @return integer result code
     */
    public function unsuspendUser($userName){
        if(!$this->is_connected)
            return HostingAPI::ANSWER_CONNECTION_ERROR;

        $client = $this->cp->clientGetByUsername( $userName );

        $sys_userid = $client['client_id'];
        $sys_groupid = $client['groups'];


        $client = $this->cp->clientGet($sys_userid);



        $client['password'] = '';
        $client['canceled'] = '';
        $client['locked'] = '';

        //  print_r($client);
        $res = $this->cp->clientUpdate($client['client_id'], $client['parent_client_id'], $client );


        $clientsites = $this->cp->clientGetSitesByUser($sys_userid, $sys_groupid);

        $i = 0;
        $j = 1;

        while ($j <= count($clientsites)) {
            $domainres = $this->cp->sitesWebDomainSetStatus($clientsites[$i]['domain_id'], 'active');
            $i++;
            $j++;
        }


        $ftpclient = $this->cp->sitesFtpUserGet(array('username' => $userName . '%'));
        $i = 0;
        $j = 1;

        while ($j <= count($ftpclient)) {
            $ftpclient[$i]['active'] = 'y';
            $ftpclient[$i]['password'] = '';
            $ftpid = $this->cp->sitesFtpUserUpdate($sys_userid, $ftpclient[$i]['ftp_user_id'], $ftpclient[$i]);

            $i++;
            $j++;
        }


        $domain_id = $this->cp->dnsZoneGetByUser($sys_userid, 1);
        $i = 0;
        $j = 1;
        while ($j <= count($domain_id)) {
            $affected_rows = $this->cp->dnsZoneSetStatus($domain_id[$i]['id'],
                'active');
            $i++;
            $j++;
        }

        return HostingAPI::ANSWER_OK;
    }


    /**
     * Checking existence of the user account in the hosting panel
     * @param $userName
     * @return integer result code
     */
    public function userExist($userName){
        if(!$this->is_connected)
            return HostingAPI::ANSWER_CONNECTION_ERROR;

        $client = $this->cp->clientGetByUsername($userName);
        if(isset($client['userid'])){
            return HostingAPI::ANSWER_USER_EXIST;
        }

        return HostingAPI::ANSWER_USER_NOT_EXIST;
    }


    /**
     * Change user account password in the hosting panel
     * @param string $userName
     * @param string $newPassword
     * @return integer result code
     */
    public function changeUserPassword($userName, $newPassword){
        if(!$this->is_connected)
            return HostingAPI::ANSWER_CONNECTION_ERROR;

        $client = $this->cp->clientGetByUsername($userName);
        $res = $this->cp->clientChangePassword($client['client_id'], $newPassword);

        if( $res ==  1){
            return HostingAPI::ANSWER_OK;
        }

        return HostingAPI::ANSWER_SYSTEM_ERROR;
    }

    /**
     * Checking existence of the plan in the hosting panel
     * @param string $planName
     * @return integer result code
     */
    public function planExist($planName){
        if(!$this->is_connected)
            return HostingAPI::ANSWER_CONNECTION_ERROR;

       $templates = ($this->cp->makeCall('client_templates_get_all', $this->cp->getSessionId()));

        foreach ($templates as $template) {
            if($template['template_name'] == $planName){
                return HostingAPI::ANSWER_PLAN_ALREADY_EXIST;
            }
        }

        return HostingAPI::ANSWER_PLAN_NOT_EXIST;
    }


    /**
     * Get all plans of the hosting panel
     * @return mixed plans
     */
    public function getPlans(){
        if(!$this->is_connected)
            return HostingAPI::ANSWER_CONNECTION_ERROR;

        if($this->cp) {
            $templates = ($this->cp->makeCall('client_templates_get_all', $this->cp->getSessionId()));
            //  print_r($templates);
            $plans = array();

            foreach ($templates as $template) {
                $plans[$template['template_name']] = $template['template_name'];

            }

            return $plans;
        }

        return HostingAPI::ANSWER_CONNECTION_ERROR;
    }


    /**
     * @param string $userName
     * @param $newPlanName
     * @return integer result code
     */
    public function changePlan($userName, $newPlanName){
        if(!$this->is_connected)
            return HostingAPI::ANSWER_CONNECTION_ERROR;

        $client = $this->cp->clientGetByUsername($userName);
        $client = $this->cp->clientGet($client['client_id']);
        $client['template_master'] = $this->getPlanIdByName($newPlanName);
        unset($client['password']);
        $res = $this->cp->clientUpdate($client['client_id'], $client['parent_client_id'], $client );
        if($res == 1 ){
            return HostingAPI::ANSWER_OK;
        }

        return HostingAPI::ANSWER_SYSTEM_ERROR;
    }


    /**
     * Creating new account in the hosting panel
     * @param array $data
     * @return integer result code
     */
    public function createUser($data){
        if(!$this->is_connected)
            return HostingAPI::ANSWER_CONNECTION_ERROR;

       $res = $this->cp->clientAdd(
           $data['last_name'].' '.$data['first_name'],
           '',
           $data['username'],
           $data['password'],
           $data['email'],
           $this->getPlanIdByName($data['package'])
       );

        if($res){
            return HostingAPI::ANSWER_OK;
        }

       $error = $this->cp->getLastException()->getMessage();

       // echo $error;
        if(preg_match("/(.*)username_error_unique(.*)/", $error)){
            return HostingAPI::ANSWER_USER_ALREADY_EXIST;
        }
        if(preg_match("/(.*)chosen password does not match the security(.*)/", $error)){
            return HostingAPI::ANSWER_USER_PASSWORD_NOT_VALID;
        }

        return HostingAPI::ANSWER_SYSTEM_ERROR;
    }


    /**
     * Removing the user account in the hosting panel
     * @param string $userName
     * @return integer result code
     */
    public function removeUser($userName){
        if(!$this->is_connected)
            return HostingAPI::ANSWER_CONNECTION_ERROR;

        $client = $this->cp->clientGetByUsername($userName);
        $res = $this->cp->clientDeleteEverything($client['client_id']);
        if($res == 1){
            return HostingAPI::ANSWER_OK;
        }

        return HostingAPI::ANSWER_SYSTEM_ERROR;
    }


    private function getPlanIdByName($planName){
        if(!$this->is_connected)
            return HostingAPI::ANSWER_CONNECTION_ERROR;

        $templates = ($this->cp->makeCall('client_templates_get_all', $this->cp->getSessionId()));

        foreach ($templates as $template) {
            if($template['template_name'] == $planName){
                return $template['template_id'];
            }
        }
        return null;
    }
}