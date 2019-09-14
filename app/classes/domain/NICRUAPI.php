<?php
/**
 * Created by PhpStorm.
 * User: Viktor
 * Date: 12.10.2015
 * Time: 16:51
 */

namespace domain;



use model\DomainOrder;
use model\DomainOwner;
use System\Logger;
use System\Tools;

class NICRUAPI extends DomainAPI implements IDomainAPI
{

    private $id;
    private $user;
    private $password;
    private $url = 'https://www.nic.ru/dns/dealer';

    protected $error;

    public $type ;


    public function __construct($Registrar)
    {
        $this->id = $Registrar->id;
        $this->user = $Registrar->login;
        $this->password = $Registrar->password;
        $this->type = $Registrar->type;
    }


    public function getErrorCode()
    {
        return $this->error;
    }
    public function prolongDomain(DomainOrder $DomainOrder, DomainOwner $Owner)
    {
        $req = "lang:ru
request:order
operation:create
login:".$this->user."
password:".$this->password."
subject-contract:".$Owner->contract_id."
request-id:".microtime()."

[order-item]
action:prolong
template:prolong
service:domain
domain:".$DomainOrder->domain."
prolong:".$DomainOrder->period."
";

        $result = $this->exec($req);

        if($result->{'state:code'} == 200){
            return DomainAPI::ANSWER_DOMAIN_PROLONG_SUCCESS;
        } else {
            return DomainAPI::ANSWER_DOMAIN_PROLONG_FAIL;
        }

    }

    public function changeDomainOwner(DomainOrder $DomainOrder, DomainOwner $DomainOwner)
    {
        // TODO: Implement changeDomainOwner() method.
    }
    public function reqPool(){}
    public function createPerson(DomainOwner $owner)
    {

        if($owner->type == 2){
            if($owner->country == 'RU') {
                $req = "lang:ru
request:contract
operation:create
login:" . $this->user . "
password:" . $this->password . "
request-id:" . microtime() . "

[contract]
contract-type:ORG
password:" . Tools::generateCode(8) . "
org:" . Tools::transliteration($owner->organization_name, 1) . "
org-r:" . $owner->organization_name . "
code:" . $owner->organization_inn  . "
kpp:" . $owner->organization_edrpou . "
ogrn:" . $owner->organization_ogrn  . "
country:" . $owner->country . "
currency-id:RUR
address-r:" . $owner->address . "
p-addr:" . $owner->organization_postal_address . "
phone:" . urlencode($owner->phone) . "
fax-no:" . urlencode($owner->fax) . "
e-mail:" . $owner->email . "
mnt-nfy:" . $owner->email . "
";
            } else {
                $req = "lang:ru
request:contract
operation:create
login:" . $this->user . "
password:" . $this->password . "
request-id:" . microtime() . "

[contract]
contract-type:ORG
password:" . Tools::generateCode(8) . "
org:" . Tools::transliteration($owner->organization_name, 1) . "
org-r:" . $owner->organization_name . "
country:" . $owner->country . "
currency-id:RUR
address-r:" . $owner->address . "
p-addr:" . $owner->organization_postal_address . "
phone:" . urlencode($owner->phone) . "
fax-no:" . urlencode($owner->fax) . "
e-mail:" . $owner->email . "
mnt-nfy:" . $owner->email . "
";
            }
        } else {

            $req = "lang:ru
request:contract
operation:create
login:" . $this->user . "
password:" . $this->password . "
request-id:" . microtime() . "

[contract]
contract-type:PRS
password:" . Tools::generateCode(8) . "
person:" . Tools::transliteration($owner->fio, 1) . "
person-r:" . $owner->fio . "
country:" . strtoupper('ua') . "
currency-id:RUR
passport:" . $owner->passport . "
birth-date:" . date('d.m.Y', strtotime($owner->birth_date)) . "
p-addr:" . $owner->address . "
phone:" . urlencode($owner->phone) . "
fax-no:" . urlencode($owner->fax) . "
e-mail:" . $owner->email . "
mnt-nfy:" . $owner->email . "
";
        }
        $result = $this->exec($req);
        Logger::log('NICRU: createPerson: ' . json_encode($result));
        if(isset($result->login) && $result->login) {

            return $result->login;
        } else{
            return false;
        }

    }

    public function createContactPerson(DomainOwner $owner, $login)
    {

        $req = "login:".$this->user."
password:".$this->password."
subject-contract:".$login."
request:contact
operation:create
lang:ru
request-id:".microtime()."

[contact]
status:registrant
org: ". Tools::transliteration($owner->organization_name, 1) ."
name:".Tools::transliteration($owner->fio, 1)."
country:UA
region:".Tools::transliteration($owner->region, 1)."
city:".Tools::transliteration($owner->city, 1)."
street:".Tools::transliteration($owner->address, 1)."
zipcode:".$owner->zip_code."
phone:".urlencode($owner->phone)."
fax:".urlencode($owner->fax)."
email:".$owner->email."";


        $result = $this->exec($req);

        Logger::log('NICRU:'. json_encode($result));

        if(isset($result->{'nic-hdl'})) {
            Logger::log('NICRU: createContactPerson: ANSWER_CONTACT_CREATE_SUCCESS');
            return $result->{'nic-hdl'};

        }else{
            Logger::log('NICRU: createContactPerson: ANSWER_CONTACT_CREATE_FAIL');
            return DomainAPI::ANSWER_CONTACT_CREATE_FAIL;
        }

    }




    public function registerDomain( DomainOrder $DomainOrder, DomainOwner $Owner)
    {



        $req = "login:".$this->user."
password:".$this->password."
subject-contract:".$DomainOrder->contract_id."
request:order
operation:create
lang:ru
request-id:".microtime()."

[order-item]
action:new
service:domain
period:".$DomainOrder->period."
domain:".$DomainOrder->domain."
nserver:".$DomainOrder->dns1." ".$DomainOrder->ip1."
nserver:".$DomainOrder->dns2." ".$DomainOrder->ip2."
nserver:".$DomainOrder->dns3." ".$DomainOrder->ip3."
nserver:".$DomainOrder->dns4." ".$DomainOrder->ip4."
check-ns:OFF
admin-c:".$DomainOrder->nic_hdl."
bill-c:".$DomainOrder->nic_hdl."
tech-c:".$DomainOrder->nic_hdl."
";

        $result = $this->exec($req);

        Logger::log('NICRU: registerDomain ServerAnswer:'.json_encode($result, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));

        if($result->{'state:code'} == 200){
            Logger::log('NICRU: registerDomain: ANSWER_DOMAIN_REG_SUCCESS');
            return DomainAPI::ANSWER_DOMAIN_REG_SUCCESS;
        } else {
            Logger::log('NICRU: registerDomain: ANSWER_DOMAIN_REG_FAIL');
            return DomainAPI::ANSWER_DOMAIN_REG_FAIL;
        }

    }





    public function changeNS(DomainOrder $DomainOrder, $old_ns_array)
    {
        $ns1 = $DomainOrder->dns1; $ns1ip = $DomainOrder->ip1;
        $ns2 = $DomainOrder->dns2; $ns2ip = $DomainOrder->ip2;
        $ns3 = $DomainOrder->dns3; $ns3ip = $DomainOrder->ip3;
        $ns4 = $DomainOrder->dns4; $ns4ip = $DomainOrder->ip4;



        $req = "login:".$this->user."
            password:".$this->password."
            subject-contract:".$DomainOrder->contract_id."
            request:order
            operation:create
            lang:ru
            request-id:".microtime()."

            [order-item]
            action:update
            service:domain
            domain:".$DomainOrder->domain."
            nserver:".$ns1." ".$ns1ip."
            nserver:".$ns2." ".$ns2ip."
            nserver:".$ns3." ".$ns3ip."
            nserver:".$ns4." ".$ns4ip."
            admin-c:".$DomainOrder->nic_hdl."
            bill-c:".$DomainOrder->nic_hdl."
            tech-c:".$DomainOrder->nic_hdl."
            ";

        $result = $this->exec($req);
        if($result->{'state:code'} == 200){
            return DomainAPI::ANSWER_DOMAIN_CHANGE_NS_SUCCESS;
        } else {
            return DomainAPI::ANSWER_DOMAIN_CHANGE_NS_FAIL;
        }
    }


    private function exec($request){
        $request = iconv("utf-8","KOI8-R//IGNORE",htmlspecialchars_decode(("SimpleRequest=".$request), ENT_QUOTES));


        $fp = curl_init();
        curl_setopt($fp, CURLOPT_URL, $this->url);
        curl_setopt($fp, CURL_HTTP_VERSION_1_1, 1);
        curl_setopt($fp, CURLOPT_POST, 1);
        curl_setopt($fp, CURLOPT_POSTFIELDS, $request);
        curl_setopt($fp, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($fp, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($fp, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($fp, CURLOPT_TIMEOUT, 120);

        $result = curl_exec($fp);

        curl_close($fp);




        return (object)$this->parseResult(iconv("koi8-r","utf-8",$result));
    }

    private function parseResult($result)
    {
        $results = mb_split("\n",$result);

        $resArray = null;
        $nserver = 0;
        while (list($index,$res) = each($results)) {
            $res=trim($res);

            if (preg_match("/^State:\s+(\d{3})\s+(.*)$/ui",$res,$arr)) {
                $resArray["state:code"] = $arr[1];
                $resArray["state:msg"] = $arr[2];
                if ($arr[1] == "402") {
                    $tmp = mb_split("\[errors\]",$result);
                    $tmp[1]=preg_replace("/\n/ui","",$tmp[1]);
                    $resArray["state:error"] = "(".$tmp[1].")";
                }
            } else if (preg_match("/^([^:]+):\s*([^:]+)$/ui",$res,$arr)) {
                $newIndex = $arr[1];
                if ($newIndex == "nserver") {$nserver++; $newIndex = $newIndex.$nserver; }
                $newValue = $arr[2];
                $resArray[$newIndex] = $newValue;
            }
        }
        return $resArray;
    }


    public function changeContactPerson(DomainOrder $domainOrder, DomainOwner $DomainOwner)
    {
        $req = "login:".$this->user."
password:".$this->password."
subject-contract:".$domainOrder->contract_id."
request:contact
operation:create
lang:ru
request-id:".microtime()."

[contact]
status:registrant
org:
name:".Tools::transliteration($DomainOwner->fio, 1)."
country:UA
region:".Tools::transliteration($DomainOwner->region, 1)."
city:".Tools::transliteration($DomainOwner->city, 1)."
street:".Tools::transliteration($DomainOwner->address, 1)."
zipcode:".$DomainOwner->zip_code."
phone:".urlencode($DomainOwner->phone)."
fax:".urlencode($DomainOwner->fax)."
email:".$DomainOwner->email."";


        $result = $this->exec($req);

        if($result->{'nic-hdl'}) {
            Logger::log('NICRU: createContactPerson: ANSWER_CONTACT_CREATE_SUCCESS');

            $req2="login:$this->user
password:$this->password
subject-contract:$domainOrder->contract_id
request:order
operation:create
lang:ru
request-id:".microtime()."

[order-item]
action:update
service:domain
domain:$domainOrder->domain
admin-c:$result->{'nic-hdl'}
bill-c:$result->{'nic-hdl'}
tech-c:$result->{'nic-hdl'}";

            $result2 = $this->exec($req2);
            if($result2->{'state:code'} == 200) {
                return $result->{'nic-hdl'};
            }

        }

            Logger::log('NICRU: createContactPerson: ANSWER_CONTACT_CREATE_FAIL');



        return DomainAPI::ANSWER_CONTACT_CREATE_FAIL;
    }
}