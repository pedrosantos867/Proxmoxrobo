<?php

namespace domain;


use tools\IdnaConvertor;
use model\DomainOrder;
use model\DomainOwner;
use SoapClient;
use System\Exception;
use System\Logger;
use System\Tools;

class R1API extends DomainAPI implements IDomainAPI
{
    private $id;
    private $client;
    private $res;
    public $type;
    private $error;

    public function __construct($Registrar)
    {
        $this->id = $Registrar->id;
        $this->type = $Registrar->type;
        $this->client = new SoapClient(null, array
        (
            'location'   => $Registrar->url ? $Registrar->url : 'https://partner.r01.ru:1443/partner_api.khtml', // Адрес SOAP-сервера
            'uri'        => 'urn:RegbaseSoapInterface',
            'exceptions' => true,
            'user_agent' => 'RegbaseSoapInterfaceClient',
            'trace'      => 1
        ));

        try {
            $this->res = $this->client->logIn($Registrar->login, $Registrar->password);
            $this->client->__setCookie('SOAPClient', $this->res->status->message);

            if(isset($this->res->status->code) && $this->res->status->code == 0){
                throw new Exception($this->res->status->message);
            }


        } catch (\SoapFault $e) {

            throw new Exception;
        }
    }

    /*
        public function checkContactPersonExist(DomainOwner $Owner)
        {
            $res = $this->client->checkDadminExists($Owner->getNicHdl($this->id));

            Logger::log('R01: checkContactPersonExist ServerAnswer: '. $res->status->name);

            if ($res->status->name == 'DADMIN_EXIST') {
                Logger::log('R01: checkContactPersonExist: ANSWER_CONTACT_EXIST');

                return DomainAPI::ANSWER_CONTACT_EXIST;
            }

            Logger::log('R01: checkContactPersonExist: ANSWER_CONTACT_NOT_EXIST');

            return DomainAPI::ANSWER_CONTACT_NOT_EXIST;

        }
    */

    public function prolongDomain(DomainOrder $DomainOrder, DomainOwner $Owner)
    {
        $idna  = new IdnaConvertor();
        $res = $this->client->prolongDomain($idna->encode($DomainOrder->domain), $DomainOrder->period);
        if ($res->status->code) {
            return DomainAPI::ANSWER_DOMAIN_PROLONG_SUCCESS;
        } else {
            return DomainAPI::ANSWER_DOMAIN_PROLONG_FAIL;
        }
    }

    public function createPerson(DomainOwner $owner)
    {
        return null;
    }
    public function createContactPerson(DomainOwner $owner, $login)
    {


        $isresident = $owner->country == 'RU' ? 1 : 0;

        if($owner->type == 2) {
            $nic_hdl =  "RPL_" . mt_rand(1000000, 9999999) . "_" . $owner->id.'-ORG-R01' ;

            $phone = substr_replace(substr_replace($owner->phone, ' ', 3, 0), ' ', 7, 0);

            $fax = substr_replace(substr_replace($owner->fax, ' ', 3, 0), ' ', 7, 0);

            $organization_name = str_replace('»', '',str_replace('«', '', $owner->organization_name));
            $organization_name_en = Tools::transliteration($organization_name, true);
            //  echo $organization_name;
            //exit();

            $organization_bank = str_replace('»', '',str_replace('«', '', $owner->organization_bank));

            $res = $this->client->addDadminOrg(
                $nic_hdl,
                $organization_name,
                Tools::transliteration($organization_name, true) ? $organization_name_en : $owner->organization_name ,
                $owner->organization_inn, //INN
                $isresident ? $owner->organization_edrpou : '-', //KPP
                $isresident ? $owner->organization_ogrn : '-', //OGRN
                $owner->address,
                $owner->organization_postal_address,
                $phone,
                $fax,
                $owner->email,
                $owner->fio,
                $organization_bank,
                $owner->organization_rs, // Номер расчетного счета организации
                $owner->organization_rs, //Номер корреспондентского счета организации
                $isresident ? $owner->organization_mfo : '-', //БИК
                $isresident
            );
            ;


            Logger::log('R01: createContactPerson: '. $res->status->name);
            Logger::log('R01: createContactPerson Answer: '. json_encode($res));

        } else {
            $nic_hdl =  "RPL_" . mt_rand(1000000, 9999999) . "_" . $owner->id.'-R01' ;

            $fiorus = $owner->fio;
            $fioeng = str_replace('_', ' ', Tools::transliteration($owner->fio));


            $passport = $owner->passport;
            $birth_date = date('d.m.Y', strtotime($owner->birth_date)); //'17.08.1990'
            $postal_addr = $owner->address;

            $phone = substr_replace(substr_replace($owner->phone, ' ', 3, 0), ' ', 7, 0);


            $fax = substr_replace(substr_replace($owner->fax, ' ', 3, 0), ' ', 7, 0);

            $e_mail = $owner->email;


            $this->error = 0;

            $res = $this->client->addDadminPerson(
                $nic_hdl,
                $fiorus,
                $fioeng,
                $passport,
                $birth_date,
                $postal_addr,
                $phone,
                $fax,
                $e_mail,
                '',
                $isresident,
                $owner->inn ? $owner->inn : null
            );


        }
        Logger::log('R01: createContactPerson: '. $res->status->name);
        Logger::log('R01: createContactPerson Answer: '. json_encode($res));

        if ($res->status->code == '1') {
            return $nic_hdl;

        } else {
            if ($res->status->name == 'WRONG_BIRTH_DATE') {
                $this->error = DomainAPI::ANSWER_CONTACT_CREATE_ERROR_BDATE;
            }

            if ($res->status->message == 'PASSPORT not filled. You should fill all the necessary parameters to proceed.') {
                $this->error = DomainAPI::ANSWER_CONTACT_CREATE_ERROR_PASSPORT;
            }

        }

        return DomainAPI::ANSWER_CONTACT_CREATE_FAIL;

    }

    public function reqPool(){}
    public function getErrorCode()
    {
        return $this->error;
    }

    // функция ставит в очередь задание на регистрацию нового домена. При этом на счету блокируется соответствующая сумма.
    public function registerDomain( DomainOrder $DomainOrder, DomainOwner $Owner)
    {
        $nsservers = "";
        $ns_array = array(
            $DomainOrder->dns1 => $DomainOrder->ip1,
            $DomainOrder->dns2 => $DomainOrder->ip2,
            $DomainOrder->dns3 => $DomainOrder->ip3,
            $DomainOrder->dns4 => $DomainOrder->ip4
        );

        $i = 0;
        foreach ($ns_array as $name => $ip) {
            if ($i == 0) {
                $nsservers .= "$name";
                if ($ip) {
                    $nsservers .= " [$ip]";
                }
            } else {
                $nsservers .= "\n$name";
                if ($ip) {
                    $nsservers .= " [$ip]";
                }
            }
            $i++;
        }
        $idna  = new IdnaConvertor();
        $res = $this->client->addDomain($idna->encode($DomainOrder->domain), $nsservers, $DomainOrder->nic_hdl, "Register new domain", 1, -1, -1, -1, -1, '', $DomainOrder->period);


        Logger::log('R01: registerDomain: '. $res->status->name);

        if ($res->status->code == '1') {
            return DomainAPI::ANSWER_DOMAIN_REG_SUCCESS;
        } else {
            return DomainAPI::ANSWER_DOMAIN_REG_FAIL;
        }
    }


    public function checkDomainAvailable($domain)
    {
        $idna  = new IdnaConvertor();


        $res = $this->client->checkDomainAvailable($idna->encode($domain));
        if (isset($res->available) && $res->available) {
            return DomainAPI::ANSWER_DOMAIN_AVAILABLE;
        } else {
            return DomainAPI::ANSWER_DOMAIN_UNAVAILABLE;
        }
    }


    public function changeNS(DomainOrder $DomainOrder, $old_ns_array)
    {
        $nsservers = '';

        $ns_array = array(
            $DomainOrder->dns1 => $DomainOrder->ip1,
            $DomainOrder->dns2 => $DomainOrder->ip2,
            $DomainOrder->dns3 => $DomainOrder->ip3,
            $DomainOrder->dns4 => $DomainOrder->ip4
        );

        $i = 0;
        foreach ($ns_array as $name => $ip) {
            if ($i == 0) {
                $nsservers .= "$name";
                if ($ip) {
                    $nsservers .= " [$ip]";
                }
            } else {
                $nsservers .= "\n$name";
                if ($ip) {
                    $nsservers .= " [$ip]";
                }
            }
            $i++;
        }
        $idna  = new IdnaConvertor();
        $res = $this->client->updateDomain($idna->encode($DomainOrder->domain), $nsservers, $DomainOrder->nic_hdl);
        Logger::log('R01: changeNS: '. json_encode($res));
        Logger::log('R01: changeNS: '. $res->status->name);

        if ($res->status->code == '1') {
            return DomainAPI::ANSWER_DOMAIN_CHANGE_NS_SUCCESS;
        } else {
            return DomainAPI::ANSWER_DOMAIN_CHANGE_NS_FAIL;
        }
    }



    public function logOut()
    {
        $this->res = $this->client->logOut();
        return $this;
    }

    public function changeContactPerson(DomainOrder $DomainOrder, DomainOwner $DomainOwner)
    {

        $nic_hdl = $this->createContactPerson($DomainOwner, null);
        if($nic_hdl !== DomainAPI::ANSWER_CONTACT_CREATE_FAIL) {

            $nsservers = '';

            $ns_array = array(
                $DomainOrder->dns1 => $DomainOrder->ip1,
                $DomainOrder->dns2 => $DomainOrder->ip2,
                $DomainOrder->dns3 => $DomainOrder->ip3,
                $DomainOrder->dns4 => $DomainOrder->ip4
            );

            $i = 0;
            foreach ($ns_array as $name => $ip) {
                if ($i == 0) {
                    $nsservers .= "$name";
                    if ($ip) {
                        $nsservers .= " [$ip]";
                    }
                } else {
                    $nsservers .= "\n$name";
                    if ($ip) {
                        $nsservers .= " [$ip]";
                    }
                }
                $i++;
            }
            $idna  = new IdnaConvertor();
            $res = $this->client->updateDomain($idna->encode($DomainOrder->domain), $nsservers, $nic_hdl);

            Logger::log('R01: changeContact: ' . $res->status->name);

            if ($res->status->code == '1') {
                return $nic_hdl;
            }
        }
        return DomainAPI::ANSWER_CONTACT_CREATE_FAIL;
    }
}