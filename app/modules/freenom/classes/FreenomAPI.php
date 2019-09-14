<?php

namespace modules\freenom\classes;

use domain\DomainAPI;
use domain\IDomainAPI;
use model\DomainOrder;
use model\DomainOwner;
use System\Exception;
use System\Logger;
use System\Tools;

class FreenomAPI extends DomainAPI implements IDomainAPI
{
    private $free_domain=['tk','ml','ga','cf','gq'];
    private $client=null;
    private $error = null;
    protected $login;
    protected $password;
    public function __construct($Registrar)
    {
        $this->client=new Freenom_Client($Registrar->login, $Registrar->password);
        $this->login=$Registrar->login;
        $this->password=$Registrar->password;
    }
    public function createPerson(DomainOwner $owner){
        return null;
    }

    public function changeContactPerson(DomainOrder $DomainOrder, DomainOwner $owner)
    {
        try
        {
            $contact_id = $this->createContactPerson($owner);
            return $contact_id;
        }
        catch (Exception $e)
        {
            Logger::log(json_encode($e));
        }

        return DomainAPI::ANSWER_CONTACT_CREATE_FAIL;
    }

    public function createContactPerson(DomainOwner $owner, $c_id=null){

        $names = explode(' ', $owner->fio);
        $last_name  = isset($names[0]) ? $names[0] : 'Unknown' ;
        $first_name = isset($names[1]) ? $names[1] : 'Unknown';

        $data=array(
            'contact_firstname' => $first_name,
            'contact_lastname'  => $last_name,
            'contact_address'   => Tools::transliteration($owner->address, 1),
            'contact_city'      => Tools::transliteration($owner->city, 1),
            'contact_zipcode'   => $owner->zip_code,
            'contact_statecode' => $owner->country,
            'contact_countrycode' => $owner->country,
            'contact_phone'     => $owner->phone,
            'contact_email'     => $owner->email
        );
        $fcontact = new Freenom_Contact($this->client);

        $result ='';
        try{
            $result = $fcontact->createContact($data);
            if($result['result']=='CONTACT REGISTERED')
            {
                return $result['contact'][0]['contact_id'];
            }
        }
        catch (Exception $e)
        {
            Logger::log(json_encode($result));
        }
        return DomainAPI::ANSWER_CONTACT_CREATE_FAIL;
    }
    public function reqPool(){}
    public function prolongDomain(DomainOrder $DomainOrder,DomainOwner $owner){
        $fdomain = new Freenom_Domain($this->client);
        $data['domainname'] = $DomainOrder->domain;
        $data['period']= '12M';

        $result ='';
        try{
            $result = $fdomain->renew($data);
            if(isset($result['result']) && $result['result']=='DOMAIN RENEWED')
            {
                return DomainAPI::ANSWER_DOMAIN_PROLONG_SUCCESS;
            }
        }
        catch (Exception $e)
        {
            Logger::log(json_encode($result));
        }
        return DomainAPI::ANSWER_DOMAIN_PROLONG_FAIL;
    }
    public function registerDomain( DomainOrder $DomainOrder, DomainOwner $Owner){
        $fdomain = new Freenom_Domain($this->client);
        $owner_id = $this->createContactPerson($Owner);
        if ($owner_id == DomainAPI::ANSWER_CONTACT_CREATE_FAIL)
        {
            return DomainAPI::ANSWER_CONTACT_CREATE_FAIL;
        }
        $data = "domainname=$DomainOrder->domain".
            "&owner_id=$owner_id".
            "&admin_id=$owner_id".
            "&tech_id=$owner_id".
            "&billing_id=$owner_id".
            "&autorenew=disabled".
            "&nameserver=$DomainOrder->dns1".
            "&nameserver=$DomainOrder->dns2".
            "&domaintype=FREE".
            "&period=12M";
        if($DomainOrder->dns3 != '')
            $data.="&nameserver=$DomainOrder->dns3";
        if($DomainOrder->dns4 != '')
            $data.="&nameserver=$DomainOrder->dns4";
        $result ='';
        try {
            $result = $fdomain->register($data);
            if (isset($result['result']) && $result['result'] == 'DOMAIN REGISTERED') {
                return DomainAPI::ANSWER_DOMAIN_REG_SUCCESS;
            }
        }
        catch (Exception $e)
        {
            Logger::log(json_encode($result));
        }
        return DomainAPI::ANSWER_DOMAIN_REG_FAIL;
    }
    public function changeNS(DomainOrder $DomainOrder, $old_ns_array){
        $fdomain = new Freenom_Domain($this->client);
        $ns1 = $DomainOrder->dns1;
        $ns2 = $DomainOrder->dns2;

        $data = "domainname=$DomainOrder->domain".
            "&nameserver=$ns1".
            "&nameserver=$ns2";
        if($DomainOrder->dns3 != '')
            $data.="&nameserver=$DomainOrder->dns3";
        if($DomainOrder->dns4 != '')
            $data.="&nameserver=$DomainOrder->dns4";
        $result ='';

        try {
            $result = $fdomain->modify($data);
            if (isset($result['result']) && $result['result'] == 'DOMAIN MODIFIED') {
                return DomainAPI::ANSWER_DOMAIN_CHANGE_NS_SUCCESS;
            }
        }
        catch (Exception $e)
        {
            Logger::log(json_encode($result));
        }
        return DomainAPI::ANSWER_DOMAIN_CHANGE_NS_FAIL;
    }

    public function getErrorCode(){
        return $this->error;
    }

    public function checkDomainAvailable( $domain){

        $fdomain      = new Freenom_Domain($this->client);
        $domain_names = explode('.',$domain);
        $check=0;
        foreach ($this->free_domain as $name)
        {
            if ($name == $domain_names[count($domain_names)-1])
            {
                $check=1;
                break;
            }
        }
        $data['name'] = $domain;
        if($check)
        {
            $data['type'] = 'FREE';
        }
        else{
            $data['type'] = 'PAID';
        }
        $result ='';
        try {
            $result = $fdomain->search($data);
            if (isset($result['result']) && $result['result'] == 'DOMAIN AVAILABLE') {
                return DomainAPI::ANSWER_DOMAIN_AVAILABLE;
            }
        }
        catch (Exception $e)
        {
            Logger::log(json_encode($result));
        }
        return DomainAPI::ANSWER_DOMAIN_UNAVAILABLE;
    }

}