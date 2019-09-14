<?php
/**
 * Created by PhpStorm.
 * User: Viktor
 * Date: 13.10.2015
 * Time: 17:32
 */

namespace domain;


use tools\IdnaConvertor;
use model\DomainOrder;
use model\DomainOwner;
use System\Logger;
use System\Tools;

class REGRUAPI extends DomainAPI implements IDomainAPI
{
    private $user;
    private $password;
    private $url = 'https://api.reg.ru/api/regru2';
    public $type ;


    public function __construct($Registrar)
    {

        $this->user = $Registrar->login;
        $this->password = $Registrar->password;
        $this->type = $Registrar->type;

        if($Registrar->url){
            $this->url = $Registrar->url;
        }
    }


    public function getErrorCode(){
        return 0;
    }


    public function prolongDomain(DomainOrder $DomainOrder, DomainOwner $Owner)
    {
        $req = "service/renew?";

        $convertor = new IdnaConvertor();
        $domain = $convertor->encode($DomainOrder->domain);

        $req .= http_build_query([
            'period' => $DomainOrder->period,
            'domain_name' => $domain,
        ]);

        $res = $this->exec($req);

        if($res->result == 'success'){
            return DomainAPI::ANSWER_DOMAIN_PROLONG_SUCCESS;
        }
        return DomainAPI::ANSWER_DOMAIN_PROLONG_FAIL;
    }


    public function createContactPerson(DomainOwner $owner, $contract_id)
    {
        return DomainAPI::ANSWER_CONTACT_CREATE_SUCCESS;
    }


    public function reqPool(){}
    public function registerDomain( DomainOrder $DomainOrder, DomainOwner $Owner)
    {

        $name = explode(' ', $Owner->fio);
        $first_name = $name[1];
        $last_name = $name[0];
        if(isset($name[2])) {
            $middle_name = $name[2];
        } else {
            $middle_name = '';
        }

        $p_addr = "$Owner->zip_code, $Owner->city, $Owner->address";
        $passport = "$Owner->passport выдан $Owner->passport_issued ".date('d.m.Y', strtotime($Owner->passport_date));

        $req = "domain/create?";

        $convertor = new IdnaConvertor();
        $domain_encoded = $convertor->encode($DomainOrder->domain);

        $rd = [
            'birth_date'    => date('d.m.Y', strtotime($Owner->birth_date)),
            'country'       => $Owner->country,
            'descr'         => 'Register new domain',
            'domain_name'   => $domain_encoded,
            'e_mail'        => $Owner->email,
            'ns0'           => $DomainOrder->dns1,
            'ns1'           => $DomainOrder->dns2,
            'ns3'           => $DomainOrder->dns3,
            'ns4'           => $DomainOrder->dns4,
            'ns0ip'         => $DomainOrder->ip1,
            'ns1ip'         => $DomainOrder->ip2,
            'ns2ip'         => $DomainOrder->ip3,
            'ns3ip'         => $DomainOrder->ip4,
            'p_addr'        => $p_addr,
            'person'        => Tools::transliteration($Owner->fio),
            'person_r'      => $Owner->fio,
            'phone'         => $Owner->phone,
            'passport'      => $passport,
            'enduser_ip'    => '5.92.34.1',

            'private_person_flag' => '0',

            'a_contype' => 'pp',
            'a_addr'    => Tools::transliteration($p_addr, 1),
            'a_city'    => Tools::transliteration($Owner->city),
            'a_state'   =>Tools::transliteration($Owner->region, 1),
            'a_country_code' => $Owner->country,
            'a_postcode' => $Owner->zip_code,
            'a_company' => $Owner->type == 2 ? Tools::transliteration($Owner->organization_name, 1) : 'Private Person',
            'a_email' => $Owner->email,
            'a_first_name' => Tools::transliteration($first_name),
            'a_last_name' => Tools::transliteration($last_name),
            'a_phone' => $Owner->phone,
            'a_fax'   => $Owner->fax,

            't_addr' => Tools::transliteration($p_addr, 1),
            't_city' => Tools::transliteration($Owner->city),
            't_company' => $Owner->type == 2 ? Tools::transliteration($Owner->organization_name, 1) :'Private Person',
            't_country_code' => $Owner->country,
            't_email' => $Owner->email,
            't_first_name' => Tools::transliteration($first_name),
            't_last_name' => Tools::transliteration($last_name),
            't_phone' => $Owner->phone,
            't_state' => Tools::transliteration($Owner->region, 1),
            't_postcode' => $Owner->zip_code,

            'b_addr' => Tools::transliteration($p_addr, 1),
            'b_city' => Tools::transliteration($Owner->city),
            'b_company' => $Owner->type == 2 ? Tools::transliteration($Owner->organization_name, 1) :'Private Person',
            'b_country_code' => $Owner->country,
            'b_email' => $Owner->email,
            'b_first_name' => Tools::transliteration($first_name),
            'b_last_name' => Tools::transliteration($last_name),
            'b_phone' => $Owner->phone,
            'b_postcode' => $Owner->zip_code,
            'b_state' => Tools::transliteration($Owner->region, 1),


            'o_addr'         => Tools::transliteration($p_addr, 1),
            'o_city'         => Tools::transliteration($Owner->city),
            'o_company'      => $Owner->type == 2 ? Tools::transliteration($Owner->organization_name, 1) :'Private Person',
            'o_country_code' => $Owner->country,
            'o_email'        => $Owner->email,
            'o_first_name'   => Tools::transliteration($first_name),
            'o_last_name'    => Tools::transliteration($last_name),
            'o_phone'        => $Owner->phone,
            'o_postcode'     => $Owner->zip_code,
            'o_state'        => Tools::transliteration($Owner->region, 1),



        ];

        $domain = strtolower($DomainOrder->domain);
        preg_match('/\.(.*)/', $domain, $res);
        $domain_zone = $res[1];

        if($domain_zone == 'moscow' || $domain_zone == 'москва'){
            $rd['a_first_name_ru']  = $first_name;
            $rd['a_last_name_ru']   = $last_name;

            $rd['a_code']           = $Owner->inn;
            $rd['a_patronimic']     = $middle_name;
            $rd['a_patronimic_ru']  = $middle_name;
            $rd['a_addr_ru']            = $p_addr;
            $rd['a_city_ru']            = $Owner->city;
            $rd['a_company_ru']         = $Owner->type == 2 ? ($Owner->organization_name) : 'Частное лицо';


            $rd['a_l_postcode']        = $Owner->zip_code;
            $rd['a_l_addr']            = Tools::transliteration($p_addr, 1);
            $rd['a_l_addr_ru']         = $p_addr;
            $rd['a_l_city']            = Tools::transliteration($Owner->city, 1);
            $rd['a_l_city_ru']         = $Owner->city;

            $rd['o_l_postcode']        = $Owner->zip_code;
            $rd['o_l_addr']            = Tools::transliteration($p_addr, 1);
            $rd['o_l_addr_ru']         = $p_addr;
            $rd['o_l_city']            = Tools::transliteration($Owner->city, 1);
            $rd['o_l_city_ru']         = $Owner->city;

            $rd['t_l_postcode']        = $Owner->zip_code;
            $rd['t_l_addr']            = Tools::transliteration($p_addr, 1);
            $rd['t_l_addr_ru']         = $p_addr;
            $rd['t_l_city']            = Tools::transliteration($Owner->city, 1);
            $rd['t_l_city_ru']         = $Owner->city;

            $rd['o_first_name_ru']  = $first_name;
            $rd['o_last_name_ru']   = $last_name;
            $rd['o_patronimic']     = $middle_name;
            $rd['o_patronimic_ru']  = $middle_name;
            $rd['o_company_ru']         = $Owner->type == 2 ? ($Owner->organization_name) :'Частное лицо';
            $rd['t_company_ru']         = $Owner->type == 2 ? ($Owner->organization_name) :'Частное лицо';

            $rd['o_code'] = $Owner->inn;
            $rd['o_addr_ru']            = $p_addr;
            $rd['o_city_ru']            = $Owner->city;
            $rd['o_state_ru']           = $Owner->region;
            $rd['o_passport_place']     = $Owner->passport_issued;
            $rd['o_passport_number'] = $Owner->passport;
            $rd['o_passport_date'] = date('d.m.Y', strtotime($Owner->passport_date));

            $rd['t_code']               = $Owner->inn;
            $rd['t_first_name_ru']      = $first_name;
            $rd['t_last_name_ru']       = $last_name;
            $rd['t_addr_ru']            = $p_addr;
            $rd['t_patronimic']         = $middle_name;
            $rd['t_patronimic_ru']      = $middle_name;
            $rd['t_city_ru']            = $Owner->city;
            $rd['t_state_ru']           = $Owner->region;
            $rd['t_passport_place']     = $Owner->passport_issued;
            $rd['t_passport_number'] = $Owner->passport;
            $rd['t_passport_date'] = date('d.m.Y', strtotime($Owner->passport_date));

        } elseif($domain_zone == 'tj'){
            $rd['a_fax']           = $Owner->fax;
            $rd['t_fax']           = $Owner->fax;
            $rd['o_fax']           = $Owner->fax;
            $rd['o_type']          = '1';
            $rd['o_whois']         = 'Domain Description'; // description

            $rd['t_nic_name']      = Tools::transliteration($last_name, 1);
            $rd['a_nic_name']      = Tools::transliteration($last_name, 1);

        } elseif($domain_zone == 'kz'){
            $rd['srvloc_state']             = 'KAR';
            $rd['srvloc_city']              = 'Karaganda';
            $rd['srvloc_street']            = 'Chizhevskogo, 17';
        } elseif($domain_zone == 'сайт' || $domain_zone == 'онлайн'){
            $rd['a_addr_r']            = $p_addr;
            $rd['b_addr_r']            = $p_addr;
            $rd['o_addr_r']            = $p_addr;
            $rd['t_addr_r']            = $p_addr;

            $rd['a_city_r']            = $Owner->city;
            $rd['b_city_r']            = $Owner->city;
            $rd['o_city_r']            = $Owner->city;
            $rd['t_city_r']            = $Owner->city;

            $rd['a_company_r']         = $Owner->type == 2 ? ($Owner->organization_name) :'Частное лицо';
            $rd['b_company_r']         = $Owner->type == 2 ? ($Owner->organization_name) :'Частное лицо';
            $rd['o_company_r']         = $Owner->type == 2 ? ($Owner->organization_name) :'Частное лицо';
            $rd['t_company_r']         = $Owner->type == 2 ? ($Owner->organization_name) :'Частное лицо';

            $rd['a_first_name_r']  = $first_name;
            $rd['a_last_name_r']   = $last_name;

            $rd['b_first_name_r']  = $first_name;
            $rd['b_last_name_r']   = $last_name;

            $rd['o_first_name_r']  = $first_name;
            $rd['o_last_name_r']   = $last_name;

            $rd['t_first_name_r']  = $first_name;
            $rd['t_last_name_r']   = $last_name;

        }

        $req .= http_build_query($rd);


        $res = $this->exec($req);

        Logger::log('REGRU: registerDomain Req: '. json_encode($req));
        Logger::log('REGRU: registerDomain Answer: '. json_encode($res));


        if($res->result == 'success'){
            Logger::log('REGRU: registerDomain: ANSWER_DOMAIN_REG_SUCCESS');
            return DomainAPI::ANSWER_DOMAIN_REG_SUCCESS;
        } else {
            Logger::log('REGRU: registerDomain: ANSWER_DOMAIN_REG_FAIL');
            return DomainAPI::ANSWER_DOMAIN_REG_FAIL;
        }

    }



    public function checkDomainAvailable($domain)
    {


       // echo $domain;
        $res = $this->exec('domain/check?domain_name='.$domain.'');
        Logger::log('reg.ru: ' . json_encode($res));
       // print_r($res);
        if(isset($res->answer->domains[0]->result) && $res->answer->domains[0]->result == 'Available'){
            return DomainAPI::ANSWER_DOMAIN_AVAILABLE;
        } elseif(isset($res->answer->domains[0]->result) &&  $res->answer->domains[0]->result == 'error') {
            return DomainAPI::ANSWER_DOMAIN_UNAVAILABLE;
        }
        return DomainAPI::ANSWER_SYSTEM_ERROR;
    }


    public function changeNS(DomainOrder $DomainOrder, $old_ns_array)
    {
        $ns1 = $DomainOrder->dns1; $ns1ip = $DomainOrder->ip1;
        $ns2 = $DomainOrder->dns2; $ns2ip = $DomainOrder->ip2;
        $ns3 = $DomainOrder->dns3; $ns3ip = $DomainOrder->ip3;
        $ns4 = $DomainOrder->dns4; $ns4ip = $DomainOrder->ip4;



        $convertor = new IdnaConvertor();
        $domain = $convertor->encode($DomainOrder->domain);

        $req = "domain/update_nss?";
        $req .= http_build_query([
            'domain_name'   => $domain,
            'ns0'           => $ns1,
            'ns1'           => $ns2,
            'ns3'           => $ns3,
            'ns4'           => $ns4,
            'ns0ip'         => $ns1ip,
            'ns1ip'         => $ns2ip,
            'ns2ip'         => $ns3ip,
            'ns3ip'         => $ns4ip
        ]);

        $res = $this->exec($req);
        Logger::log('REGRU: changeNS: '.$res->result);
        if($res->result == 'success'){
            return DomainAPI::ANSWER_DOMAIN_CHANGE_NS_SUCCESS;
        }
        return DomainAPI::ANSWER_DOMAIN_CHANGE_NS_FAIL;
    }

    public function createPerson(DomainOwner $owner)
    {
        return null;
    }

    private function exec($request){

        $request .= '&username='.$this->user.'&password='.$this->password;


        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);       // Allow self-signed certs
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);       // Allow certs that do not match the hostname
        curl_setopt($curl, CURLOPT_HEADER, 0);               // Do not include header in output
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);       // Return contents of transfer on curl_exec
        curl_setopt($curl, CURLOPT_URL, $this->url.'/'.$request);            // execute the query
        curl_setopt($curl, CURL_HTTP_VERSION_1_0, 1);

        if(curl_error($curl)){
            return false;
        }

        $res    = curl_exec($curl);



        return json_decode($res);
    }


    public function changeContactPerson(DomainOrder $DomainOrder, DomainOwner $Owner)
    {
        $name = explode(' ', $Owner->fio);
        $first_name = $name[1];
        $last_name = $name[0];
        if(isset($name[2])) {
            $middle_name = $name[2];
        } else {
            $middle_name = '';
        }

        $p_addr = "$Owner->zip_code, $Owner->city, $Owner->address";
        $passport = "$Owner->passport выдан $Owner->passport_issued ".date('d.m.Y', strtotime($Owner->passport_date));

        $req = "domain/update_contacts?";

        $convertor = new IdnaConvertor();
        $domain_encoded = $convertor->encode($DomainOrder->domain);

        $rd = [
            'birth_date'    => date('d.m.Y', strtotime($Owner->birth_date)),
            'country'       => $Owner->country,
            'domain_name'   => $domain_encoded,
            'e_mail'        => $Owner->email,
            'p_addr'        => $p_addr,
            'person'        => Tools::transliteration($Owner->fio),
            'person_r'      => $Owner->fio,
            'phone'         => $Owner->phone,
            'passport'      => $passport,

            'private_person_flag' => '0',

            'a_contype' => 'pp',
            'a_addr'    => Tools::transliteration($p_addr, 1),
            'a_city'    => Tools::transliteration($Owner->city),
            'a_state'   =>Tools::transliteration($Owner->region, 1),
            'a_country_code' => $Owner->country,
            'a_postcode' => $Owner->zip_code,
            'a_company' => $Owner->type == 2 ? Tools::transliteration($Owner->organization_name, 1) : 'Private Person',
            'a_email' => $Owner->email,
            'a_first_name' => Tools::transliteration($first_name),
            'a_last_name' => Tools::transliteration($last_name),
            'a_phone' => $Owner->phone,
            'a_fax'   => $Owner->fax,

            't_addr' => Tools::transliteration($p_addr, 1),
            't_city' => Tools::transliteration($Owner->city),
            't_company' => $Owner->type == 2 ? Tools::transliteration($Owner->organization_name, 1) :'Private Person',
            't_country_code' => $Owner->country,
            't_email' => $Owner->email,
            't_first_name' => Tools::transliteration($first_name),
            't_last_name' => Tools::transliteration($last_name),
            't_phone' => $Owner->phone,
            't_state' => Tools::transliteration($Owner->region, 1),
            't_postcode' => $Owner->zip_code,

            'b_addr' => Tools::transliteration($p_addr, 1),
            'b_city' => Tools::transliteration($Owner->city),
            'b_company' => $Owner->type == 2 ? Tools::transliteration($Owner->organization_name, 1) :'Private Person',
            'b_country_code' => $Owner->country,
            'b_email' => $Owner->email,
            'b_first_name' => Tools::transliteration($first_name),
            'b_last_name' => Tools::transliteration($last_name),
            'b_phone' => $Owner->phone,
            'b_postcode' => $Owner->zip_code,
            'b_state' => Tools::transliteration($Owner->region, 1),


            'o_addr' => Tools::transliteration($p_addr, 1),
            'o_city' => Tools::transliteration($Owner->city),
            'o_company' => $Owner->type == 2 ? Tools::transliteration($Owner->organization_name, 1) :'Private Person',
            'o_country_code' => $Owner->country,
            'o_email' => $Owner->email,
            'o_first_name' => Tools::transliteration($first_name),
            'o_last_name' => Tools::transliteration($last_name),
            'o_phone' => $Owner->phone,
            'o_postcode' => $Owner->zip_code,
            'o_state' => Tools::transliteration($Owner->region, 1),



        ];

        $domain = strtolower($DomainOrder->domain);
        preg_match('/\.(.*)/', $domain, $res);
        $domain_zone = $res[1];

        if($domain_zone == 'moscow' || $domain_zone == 'москва'){
            $rd['a_first_name_ru']  = $first_name;
            $rd['a_last_name_ru']   = $last_name;

            $rd['a_code']           = $Owner->inn;
            $rd['a_patronimic']     = $middle_name;
            $rd['a_patronimic_ru']  = $middle_name;
            $rd['a_addr_ru']            = $p_addr;
            $rd['a_city_ru']            = $Owner->city;
            $rd['a_company_ru']         = $Owner->type == 2 ? ($Owner->organization_name) :'Частное лицо';


            $rd['a_l_postcode']        = $Owner->zip_code;
            $rd['a_l_addr']            = Tools::transliteration($p_addr, 1);
            $rd['a_l_addr_ru']         = $p_addr;
            $rd['a_l_city']            = Tools::transliteration($Owner->city, 1);
            $rd['a_l_city_ru']         = $Owner->city;

            $rd['o_l_postcode']        = $Owner->zip_code;
            $rd['o_l_addr']            = Tools::transliteration($p_addr, 1);
            $rd['o_l_addr_ru']         = $p_addr;
            $rd['o_l_city']            = Tools::transliteration($Owner->city, 1);
            $rd['o_l_city_ru']         = $Owner->city;

            $rd['t_l_postcode']        = $Owner->zip_code;
            $rd['t_l_addr']            = Tools::transliteration($p_addr, 1);
            $rd['t_l_addr_ru']         = $p_addr;
            $rd['t_l_city']            = Tools::transliteration($Owner->city, 1);
            $rd['t_l_city_ru']         = $Owner->city;

            $rd['o_first_name_ru']  = $first_name;
            $rd['o_last_name_ru']   = $last_name;
            $rd['o_patronimic']     = $middle_name;
            $rd['o_patronimic_ru']  = $middle_name;
            $rd['o_company_ru']         = $Owner->type == 2 ? ($Owner->organization_name) :'Частное лицо';
            $rd['t_company_ru']         = $Owner->type == 2 ? ($Owner->organization_name) :'Частное лицо';

            $rd['o_code']           = $Owner->inn;
            $rd['o_addr_ru']            = $p_addr;
            $rd['o_city_ru']            = $Owner->city;
            $rd['o_state_ru']           = $Owner->region;
            $rd['o_passport_place']     = $Owner->passport_issued;
            $rd['o_passport_number'] = $Owner->passport;
            $rd['o_passport_date'] = date('d.m.Y', strtotime($Owner->passport_date));

            $rd['t_code']               = $Owner->inn;
            $rd['t_first_name_ru']      = $first_name;
            $rd['t_last_name_ru']       = $last_name;
            $rd['t_addr_ru']            = $p_addr;
            $rd['t_patronimic']         = $middle_name;
            $rd['t_patronimic_ru']      = $middle_name;
            $rd['t_city_ru']            = $Owner->city;
            $rd['t_state_ru']           = $Owner->region;
            $rd['t_passport_place']     = $Owner->passport_issued;
            $rd['t_passport_number'] = $Owner->passport;
            $rd['t_passport_date'] = date('d.m.Y', strtotime($Owner->passport_date));

        } elseif($domain_zone == 'tj'){
            $rd['a_fax']           = $Owner->fax;
            $rd['t_fax']           = $Owner->fax;
            $rd['o_fax']           = $Owner->fax;
            $rd['o_type']          = '1';
            $rd['o_whois']         = 'Domain Description'; // description

            $rd['t_nic_name']      = Tools::transliteration($last_name, 1);
            $rd['a_nic_name']      = Tools::transliteration($last_name, 1);

        } elseif($domain_zone == 'kz'){
            $rd['srvloc_state']             = 'KAR';
            $rd['srvloc_city']              = 'Karaganda';
            $rd['srvloc_street']            = 'Chizhevskogo, 17';
        } elseif($domain_zone == 'сайт' || $domain_zone == 'онлайн'){
            $rd['a_addr_r']            = $p_addr;
            $rd['b_addr_r']            = $p_addr;
            $rd['o_addr_r']            = $p_addr;
            $rd['t_addr_r']            = $p_addr;

            $rd['a_city_r']            = $Owner->city;
            $rd['b_city_r']            = $Owner->city;
            $rd['o_city_r']            = $Owner->city;
            $rd['t_city_r']            = $Owner->city;

            $rd['a_company_r']         = $Owner->type == 2 ? ($Owner->organization_name) :'Частное лицо';
            $rd['b_company_r']         = $Owner->type == 2 ? ($Owner->organization_name) :'Частное лицо';
            $rd['o_company_r']         = $Owner->type == 2 ? ($Owner->organization_name) :'Частное лицо';
            $rd['t_company_r']         = $Owner->type == 2 ? ($Owner->organization_name) :'Частное лицо';

            $rd['a_first_name_r']  = $first_name;
            $rd['a_last_name_r']   = $last_name;

            $rd['b_first_name_r']  = $first_name;
            $rd['b_last_name_r']   = $last_name;

            $rd['o_first_name_r']  = $first_name;
            $rd['o_last_name_r']   = $last_name;

            $rd['t_first_name_r']  = $first_name;
            $rd['t_last_name_r']   = $last_name;

        }

        $req .= http_build_query($rd);


        $res = $this->exec($req);

        Logger::log('REGRU: updateContact Req: '. json_encode($rd));
        Logger::log('REGRU: updateContact Answer: '. json_encode($res));


        if($res->result == 'success'){
            return '';
        }

        return DomainAPI::ANSWER_CONTACT_CREATE_FAIL;

    }
}