<?php


namespace domain;


use model\DomainOrder;
use model\DomainOwner;
use System\Logger;
use System\Tools;

class RootPanel extends DomainAPI implements IDomainAPI
{
    public $url = '';
    private $login;
    private $apikey;
    private $error = null;

    public function __construct($Registrar)
    {

        $this->url = $Registrar->url ? $Registrar->url : $this->url;

        $this->login = $Registrar->login;
        $this->apikey = $Registrar->password;

    }



    public function createContactPerson(DomainOwner $Owner, $contract_id=null){
        $name = explode(' ', $Owner->fio);
        $first_name = $name[1];
        $last_name = $name[0];
        if(isset($name[2])) {
            $middle_name = $name[2];
        } else {
            $middle_name = '';
        }

       $mobile = $Owner->mobile_phone ? $Owner->mobile_phone : $Owner->phone;

        $passport = ($Owner->passport);

        if($Owner->country == 'UA'){
            $phone = substr($Owner->phone, 0, 4) . ' ' . substr($Owner->phone, 4, 2) . ' ' . substr($Owner->phone, 6);
            $mobile = substr($mobile, 0, 4) . ' ' . substr($mobile, 4, 2) . ' ' . substr($mobile, 6);
            $fax = $Owner->fax ? (substr($Owner->fax, 0, 4) . ' ' . substr($Owner->fax, 4, 2) . ' ' . substr($Owner->fax, 6)) : (substr($Owner->phone, 0, 4) . ' ' . substr($Owner->phone, 4, 2) . ' ' . substr($Owner->phone, 6));
            $passport = substr($Owner->passport, 0, 2) . ' ' . substr($Owner->passport, 2);

        } else if($Owner->country =='RU'){
            $phone = substr($Owner->phone, 0, 2) . ' ' . substr($Owner->phone, 2, 3) . ' ' . substr($Owner->phone, 5);
            $mobile = substr($mobile, 0, 2) . ' ' . substr($mobile, 2, 3) . ' ' . substr($mobile, 5);
            $fax = $Owner->fax ? substr($Owner->fax, 0, 2) . ' ' . substr($Owner->fax, 2, 3) . ' ' . substr($Owner->fax, 5) : substr($Owner->phone, 0, 4) . ' ' . substr($Owner->phone, 4, 6) . ' ' . substr($Owner->phone, 6);

            $passport = substr($Owner->passport, 0, 2) . ' ' . substr($Owner->passport, 2, 2) .' '. substr($Owner->passport, 4);
        } else {

            $phone = substr($Owner->phone, 0, 2) . ' ' . substr($Owner->phone, 2, 3) . ' ' . substr($Owner->phone, 5);
            $mobile = substr($mobile, 0, 2) . ' ' . substr($mobile, 2, 3) . ' ' . substr($mobile, 5);
            $fax = $Owner->fax ? substr($Owner->fax, 0, 2) . ' ' . substr($Owner->fax, 2, 3) . ' ' . substr($Owner->fax, 5) : substr($Owner->phone, 0, 4) . ' ' . substr($Owner->phone, 4, 6) . ' ' . substr($Owner->phone, 6);
        }


        if($Owner->type == 2) {

            $params = array('command' => 'createProfile',
                'org' => '3',
                'surname' => $last_name,
                'name' => $first_name,
                'otchestvo' => $middle_name,
                'country' => $Owner->country,
                'oblast' => $Owner->region ? $Owner->region : 'Unknown region',
                'post' => $Owner->zip_code,
                'city' => $Owner->city,
                'street' => $Owner->organization_postal_address,
                'phone'       => $phone,
                'mobile'      => $mobile,
                'fax'         => $fax,
                'email'       => $Owner->email,
                'firma'       => $Owner->organization_name,
                'firmaeng'    => Tools::transliteration($Owner->organization_name, true),
                'address_org' => $Owner->address,
                'inn'         => $Owner->organization_inn,

            );
        } else {



            $params = array('command' => 'createProfile',
                'org' => '1',
                'surname' => $last_name,
                'name' => $first_name,
                'otchestvo' => $middle_name,
                'country' => $Owner->country,
                'oblast' => $Owner->region ? $Owner->region : 'Unknown region',
                'post' => $Owner->zip_code,
                'city' => $Owner->city,
                'street' => $Owner->address,
                'phone' => $phone,
                'mobile' => $mobile,
                'fax' => $fax,
                'email' => $Owner->email,
                'seriya' => $passport,
                'by' => $Owner->passport_issued,
                'date' => date('d.m.Y', strtotime($Owner->passport_date)),
                'birthday' => date('d.m.Y', strtotime($Owner->birth_date)),
                'idnum' => $Owner->inn
            );
        }
     //   print_r($params);
        $res = $this->send($params);
      //  print_r($res);

        Logger::log('RootPanel createContact:'. json_encode($params));
        Logger::log('RootPanel createContact:'. json_encode($res));

        if($res['status'] == 'SUCCESS' && isset($res['profileid'])){
            return $res['profileid'];
        }

        return DomainAPI::ANSWER_CONTACT_CREATE_FAIL;
    }

    public function createPerson(DomainOwner $owner){
        return null;
    }

    public function prolongDomain(DomainOrder $DomainOrder, DomainOwner $owner){

        $params = array(
            'command'   => 'renewDomain',
            'domain'    => $DomainOrder->domain,
            'period'    => $DomainOrder->period
        );

        $res = $this->send($params);
        if($res['status'] == 'SUCCESS'){
            return self::ANSWER_DOMAIN_PROLONG_SUCCESS;
        }
        return self::ANSWER_DOMAIN_PROLONG_FAIL;
    }
    public function reqPool(){}
    public function checkDomainAvailable($domain)
    {

        $params = array('command' => 'checkDomain', 'domain' => $domain);
        $res = $this->send($params);

        if(isset($res['avail']) && $res['avail'] == '1'){
            return self::ANSWER_DOMAIN_AVAILABLE;
        } else {
            return self::ANSWER_DOMAIN_UNAVAILABLE;
        }

    }



    public function registerDomain( DomainOrder $DomainOrder, DomainOwner $DomainOwner){

        $ns1 = $DomainOrder->dns1; $ns1ip = $DomainOrder->ip1;
        $ns2 = $DomainOrder->dns2; $ns2ip = $DomainOrder->ip2;
        $ns3 = $DomainOrder->dns3; $ns3ip = $DomainOrder->ip3;
        $ns4 = $DomainOrder->dns4; $ns4ip = $DomainOrder->ip4;

        $params = array(
            'command'   => 'registerDomain',
            'domain'    => $DomainOrder->domain,
            'period'    => $DomainOrder->period,
            'profileid' => $DomainOrder->nic_hdl,
            'defaultns' => '0',
            'ns1'       => $ns1,
            'ns2'       => $ns2,
            'ns3'       => $ns3,
            'ns4'       => $ns4,
            'ns1ip'     => $ns1ip,
            'ns2ip'     => $ns2ip,
            'ns3ip'     => $ns3ip,
            'ns4ip'     => $ns4ip

        );

        $res = $this->send($params);

        //print_r($res);
        Logger::log('RootPanel reg result:'.json_encode($res));
        if($res['status'] == 'SUCCESS'){
            return self::ANSWER_DOMAIN_REG_SUCCESS;
        }

        return self::ANSWER_DOMAIN_REG_FAIL;
    }

    public function changeNS(DomainOrder $DomainOrder, $old_ns_array){

        $ns1 = $DomainOrder->dns1; $ns1ip = $DomainOrder->ip1;
        $ns2 = $DomainOrder->dns2; $ns2ip = $DomainOrder->ip2;
        $ns3 = $DomainOrder->dns3; $ns3ip = $DomainOrder->ip3;
        $ns4 = $DomainOrder->dns4; $ns4ip = $DomainOrder->ip4;

        $params = array(
            'command'   => 'updateDNS',
            'domain'    => $DomainOrder->domain,
            'defaultns' => '0',
            'ns1'       => $ns1,
            'ns2'       => $ns2,
            'ns3'       => $ns3,
            'ns4'       => $ns4,
            'ns1ip'     => $ns1ip,
            'ns2ip'     => $ns2ip,
            'ns3ip'     => $ns3ip,
            'ns4ip'     => $ns4ip
        );

        $res = $this->send($params);

        if($res['status'] == 'SUCCESS'){
            return self::ANSWER_DOMAIN_CHANGE_NS_SUCCESS;
        }
        return self::ANSWER_DOMAIN_CHANGE_NS_FAIL;
    }

    public function changeContactPerson(DomainOrder $DomainOrder, DomainOwner $DomainOwner){
        $profile_id = $this->createContactPerson($DomainOwner);
        $params = array(
            'command'   => 'updateDomainContacts',
            'domain'    => $DomainOrder->domain,
            'profileid' => $profile_id
        );

        $res = $this->send($params);

        if($res['status'] == 'SUCCESS'){
            return $profile_id;
        }

        return self::ANSWER_CONTACT_CREATE_FAIL;

    }

    public function getErrorCode(){
        return $this->error;
    }


    public function send($params)
    {
        $req = '';

        $params["login"] = $this->login;
        $params["apikey"] = $this->apikey;

        while ( list($k,$v) = @each($params)) {
            $req = $req."$k=".urlencode($v)."&";
        }

       // echo $req;
        $fp = curl_init();
        curl_setopt($fp, CURLOPT_URL, $this->url);
        curl_setopt($fp, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($fp, CURLOPT_POST, true);
        curl_setopt($fp, CURLOPT_POSTFIELDS, $req);
        curl_setopt($fp, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($fp, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($fp, CURLOPT_FAILONERROR, false);

        curl_setopt($fp, CURLOPT_TIMEOUT, 120);
        $result = curl_exec($fp);

        Logger::log('RootPanel: '.$result);
     //   echo curl_error($fp);

        if (curl_errno($fp)) {
            curl_close($fp);
            return false;
        } else {
            curl_close($fp);

            $result = @unserialize($result);
            if (is_array($result) and count($result) > 1) {
                return $result;
            } else {
                return false;
            }
        }
    }
}