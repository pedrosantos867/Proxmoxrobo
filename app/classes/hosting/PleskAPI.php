<?php


namespace hosting;

class PleskAPI implements IHostingAPI {

    protected $hostname;
    private $username;
    private $password;
    private $_error;

    public function __construct($server)
    {
        $this->curl     = curl_init();
        $this->hostname = $server->host . '/enterprise/control/agent.php';
        $this->query = '';
        $this->username = $server->login;
        $this->password = $server->pass;

    }

    public function checkConnection(){
        $request = '
                <packet>
                  <server>
                    <get_protos/>
                  </server>
                </packet>';

        $res = $this->exec($request);

        if(isset($res->server->get_protos->result->status) && $res->server->get_protos->result->status == 'ok'){
            return HostingAPI::ANSWER_OK;
        }

        return HostingAPI::ANSWER_CONNECTION_ERROR;

    }
    public function getErrorDetails(){
        return $this->_error;
    }
    public function suspendUser($user){
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
                <packet>
                <customer>
                    <set>
                        <filter>
                            <login>'.$user.'</login>
                        </filter>
                        <values>
                            <gen_info>
                                <status>16</status>
                            </gen_info>
                        </values>
                    </set>
                </customer>
                </packet>';
        $res = $this->exec($xml);
        if(isset($res->customer->set->result->status) && $res->customer->set->result->status == 'ok'){
            return HostingAPI::ANSWER_OK;
        }

        return HostingAPI::ANSWER_SYSTEM_ERROR;

    }

    public function unsuspendUser($user){
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
                <packet>
                <customer>
                    <set>
                        <filter>
                            <login>'.$user.'</login>
                        </filter>
                        <values>
                            <gen_info>
                                <status>0</status>
                            </gen_info>
                        </values>
                    </set>
                </customer>
                </packet>';
        $res = $this->exec($xml);
        if(isset($res->customer->set->result->status) && $res->customer->set->result->status == 'ok'){
            return HostingAPI::ANSWER_OK;
        }

        return HostingAPI::ANSWER_SYSTEM_ERROR;
    }

    public function userExist($user){
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
                <packet version="1.6.3.0">
                <customer>
                   <get>
                      <filter>
                          <login>'.$user.'</login>
                      </filter>
                      <dataset>
                          <gen_info/>
                          <stat/>
                      </dataset>
                   </get>
                </customer>
                </packet>';

        $res = $this->exec($xml);
        if(isset($res->customer->get->result->status) && $res->customer->get->result->status == 'ok'){
            return HostingAPI::ANSWER_USER_EXIST;
        }
        return HostingAPI::ANSWER_USER_NOT_EXIST;
    }

    public function changeUserPassword($user, $new_password){
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
                <packet>
                <customer>
                    <set>
                        <filter>
                            <login>'.$user.'</login>
                        </filter>
                        <values>
                            <gen_info>
                                <passwd>'.$new_password.'</passwd>
                            </gen_info>
                        </values>
                    </set>
                </customer>
                </packet>';
        $res = $this->exec($xml);

        if(isset($res->customer->set->result->status) && $res->customer->set->result->status == 'ok'){
            return HostingAPI::ANSWER_OK;
        }
        if(isset($res->customer->set->result->status) && $res->customer->set->result->errcode == '1019'){
            HostingAPI::ANSWER_USER_PASSWORD_NOT_VALID;
        }
        if(isset($res->customer->set->result->status) && $res->customer->set->result->errcode == '1013'){
            HostingAPI::ANSWER_USER_NOT_EXIST;
        }

        return HostingAPI::ANSWER_SYSTEM_ERROR;


    }

    public function planExist($plan_name){
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
        <packet>
        <service-plan>
            <get>
                <filter>
                  <name>'.$plan_name.'</name>
               </filter>
            </get>
        </service-plan>
        </packet>';

        $res = $this->exec($xml);

        if(isset($res->{'service-plan'}->get->result)){
            if($res->{'service-plan'}->get->result->status == 'ok') {
                return HostingAPI::ANSWER_PLAN_EXIST;
            }else {
                return HostingAPI::ANSWER_PLAN_NOT_EXIST;
            }
        } else {
            return HostingAPI::ANSWER_SYSTEM_ERROR;
        }

    }

    public function getPlans(){
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
        <packet>
        <service-plan>
            <get>
                <filter/>
            </get>
        </service-plan>
        </packet>';

        $res = $this->exec($xml);

        $plans = array();
        if(isset($res->{'service-plan'}->get->result)){
            foreach($res->{'service-plan'}->get->result as $data){
                $plans[$data->name] = $data->name;
            }
        }

        return $plans;
    }

    public function changePlan($user_name, $new_plan_name){
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
        <packet>
        <service-plan>
            <get>
                <filter>
                  <name>'.$new_plan_name.'</name>
               </filter>
            </get>
        </service-plan>
        </packet>';
        $res = $this->exec($xml);
        if(!isset($res->{'service-plan'}->get->result->guid)){
            return HostingAPI::ANSWER_SYSTEM_ERROR;
        }

        $gid = ($res->{'service-plan'}->get->result->guid);
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
                    <packet>
                    <webspace>
                        <switch-subscription>
                            <filter>
                                <owner-login>'.$user_name.'</owner-login>
                            </filter>
                            <plan-guid>'.$gid.'</plan-guid>
                        </switch-subscription>
                    </webspace>
                    </packet>';
        $res = $this->exec($xml);

        if(isset($res->webspace->result->status) && $res->webspace->result->status == 'ok'){
            return HostingAPI::ANSWER_OK;
        }

       return HostingAPI::ANSWER_SYSTEM_ERROR;

    }

    public function createUser($data){

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
                <packet>
                <ip>
                  <get/>
                </ip>
                </packet>';
        $resip = $this->exec($xml);

        if(isset($resip->ip->get->result->addresses->ip_info)) {
            if (isset($resip->ip->get->result->addresses->ip_info->ip_address)) {

            $ip = $resip->ip->get->result->addresses->ip_info->ip_address;
            } else {
                $ips = $resip->ip->get->result->addresses->ip_info[0];
                $ip = $ips->ip_address;
            }

        } else {
            return HostingAPI::ANSWER_SYSTEM_ERROR;
        }

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
                <packet>
                    <customer>
                        <add>
                            <gen_info>
                                <pname>'.$data['last_name'].' '.$data['first_name'].'</pname>
                                <login>'.$data['username'].'</login>
                                <passwd>'.$data['password'].'</passwd>
                                <email>'.$data['email'].'</email>
                            </gen_info>

                        </add>
                    </customer>
                </packet>';
        $res = $this->exec($xml);



        if(isset($res->customer->add->result->errcode) && $res->customer->add->result->errcode == '1007'){
            return HostingAPI::ANSWER_USER_ALREADY_EXIST;
        }

        if(isset($res->customer->add->result->errcode) && $res->customer->add->result->errcode == '1019'){
            return HostingAPI::ANSWER_USER_PASSWORD_NOT_VALID;
        }

        if(isset($res->customer->add->result->errcode) && $res->customer->add->result->errcode == '1023') {
            return HostingAPI::ANSWER_USER_EMAIL_NOT_VALID;
        }

        if(!isset($res->customer) || isset($res->customer->add->result->errcode) ) {
            return HostingAPI::ANSWER_SYSTEM_ERROR;
        }

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
                <packet>
                <webspace>
                    <add>
                        <gen_setup>
                            <name>'.$data['domain'].'</name>
                            <owner-id>'.$res->customer->add->result->id.'</owner-id>
                            <htype>vrt_hst</htype>
                        </gen_setup>
                        <hosting>
                            <vrt_hst>
                                <property>
                                    <name>ftp_login</name>
                                    <value>'.$data['username'].'</value>
                                </property>
                                <property>
                                    <name>ftp_password</name>
                                    <value>'.$data['password'].'</value>
                                </property>
                                <ip_address>'.$ip.'</ip_address>
                            </vrt_hst>
                        </hosting>
                        <plan-name>'.$data['package'].'</plan-name>
                    </add>
                </webspace>
                </packet>';


        $res2 = $this->exec($xml);


        if(isset($res->customer->add->result->status) && $res->customer->add->result->status == 'ok'){
            return HostingAPI::ANSWER_OK;
        }

        return HostingAPI::ANSWER_SYSTEM_ERROR;
    }

    public function removeUser($user_name){
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
                <packet version="1.6.3.0">
                <customer>
                    <del>
                        <filter>
                            <login>'.$user_name.'</login>
                        </filter>
                    </del>
                </customer>
                </packet>';
        $res = $this->exec($xml);

        if(isset($res->customer->del->result->status) && $res->customer->del->result->status == 'ok'){
            return HostingAPI::ANSWER_OK;
        }
        if(isset($res->customer->del->result->status) && $res->customer->del->result->errcode == '1013'){
            return HostingAPI::ANSWER_USER_NOT_EXIST;
        }

        return HostingAPI::ANSWER_SYSTEM_ERROR;

    }

    private function exec($request)
    {
        $headers = array(
            "Content-Type: text/xml",
            "HTTP_PRETTY_PRINT: TRUE",
        );

        $headers[] = "HTTP_AUTH_LOGIN: $this->username";
        $headers[] = "HTTP_AUTH_PASSWD: ".str_replace('&amp;','&',$this->password);


        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->hostname);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
        $result = curl_exec($curl);
        curl_close($curl);
        $result = simplexml_load_string($result);
        return json_decode(json_encode($result));
    }






}