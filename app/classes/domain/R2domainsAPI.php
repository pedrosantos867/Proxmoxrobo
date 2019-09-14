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

class R2domainsAPI extends DomainAPI implements IDomainAPI
{
    private $user;
    private $password;
    private $url = 'https://reg.2domains.ru/api2/';
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

        $textdm= array();
        $textdm['action']='service/renew';

        $textdm['period']='1';
        $textdm['input_data'] = '{"services":[{"service_id":"'.$DomainOrder->domain_reg_id.'"}]}';
        $textdm['__trusted']='1';
        $textdm['ok_if_no_money']='1';

        $res = $this->exec($textdm);

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
            // 'domain_name'   => $domain_encoded,
            'e_mail'        => $Owner->email,

            'p_addr'        => $p_addr,
            'person'        => Tools::transliteration($Owner->fio),
            'person_r'      => $Owner->fio,
            'phone'         => $Owner->phone,
            'passport'      => $passport,
            'enduser_ip'    => '5.92.34.1',
            'rp_profile_type' => 'g', // физ лицо, если юр то y
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
            't_company' => $Owner->type == 2 ? Tools::transliteration($Owner->organization_name, 1) : 'Private Person',
            't_country_code' => $Owner->country,
            't_email' => $Owner->email,
            't_first_name' => Tools::transliteration($first_name),
            't_last_name' => Tools::transliteration($last_name),
            't_phone' => $Owner->phone,
            't_state' => Tools::transliteration($Owner->region, 1),
            't_postcode' => $Owner->zip_code,

            'b_addr' => Tools::transliteration($p_addr, 1),
            'b_city' => Tools::transliteration($Owner->city),
            'b_company' => $Owner->type == 2 ? Tools::transliteration($Owner->organization_name, 1) : 'Private Person',
            'b_country_code' => $Owner->country,
            'b_email' => $Owner->email,
            'b_first_name' => Tools::transliteration($first_name),
            'b_last_name' => Tools::transliteration($last_name),
            'b_phone' => $Owner->phone,
            'b_postcode' => $Owner->zip_code,
            'b_state' => Tools::transliteration($Owner->region, 1),


            'o_addr' => Tools::transliteration($p_addr, 1),
            'o_city' => Tools::transliteration($Owner->city),
            'o_company' => $Owner->type == 2 ? Tools::transliteration($Owner->organization_name, 1) : 'Private Person',
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

        }
        elseif($domain_zone == 'ru' || $domain_zone == 'рф' || $domain_zone == 'su'){
            $rd['rp_profile_type'] = 'f'; //
        }
        elseif($domain_zone == 'tj'){
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

        $textdm= array();
        $textdm['action']='domain/create';

        $textdm['period']='1';


        $textdm['__trusted']='1';
        $textdm['ok_if_no_money']='1';



        $textdm['input_data'] = json_encode(
            array(
                'contacts' => $rd,
                'domains' =>
                    array(
                        array(
                            'dname'=> $domain_encoded
                        )
                    ),
                'nss' =>
                    array(
                        'ns0'           => $DomainOrder->dns1,
                        'ns1'           => $DomainOrder->dns2,
                        'ns3'           => $DomainOrder->dns3,
                        'ns4'           => $DomainOrder->dns4,
                        'ns0ip'         => $DomainOrder->ip1,
                        'ns1ip'         => $DomainOrder->ip2,
                        'ns2ip'         => $DomainOrder->ip3,
                        'ns3ip'         => $DomainOrder->ip4
                    ),
                'enduser_ip' => '80.90.240.184'
            ), JSON_UNESCAPED_UNICODE);


        $res = $this->exec($textdm);

        Logger::log('2domains: registerDomain Req: '. ($textdm['input_data']));
        Logger::log('2domains: registerDomain Answer: '. json_encode($res));


        if(isset($res->result) && $res->result == 'success' && isset($res->answer->domains[0]->result) && $res->answer->domains[0]->result == 'success' ){
            Logger::log('2domains: registerDomain: ANSWER_DOMAIN_REG_SUCCESS');
            $dom = $res->answer->domains[0];
            $DomainOrder->domain_reg_id = $dom->service_id;
            $DomainOrder->save();

            return DomainAPI::ANSWER_DOMAIN_REG_SUCCESS;
        } else {
            Logger::log('2domains: registerDomain: ANSWER_DOMAIN_REG_FAIL');
            return DomainAPI::ANSWER_DOMAIN_REG_FAIL;
        }

    }


    public function checkDomainsAvailable($domains)
    {
        $convertor = new IdnaConvertor();
        $domains_array = array(

        );

        foreach ($domains as $domain){
            $domains_array[] = array(
                'dname' => $convertor->encode($domain)
            );
        }


        $textdm= array();
        $textdm['action']='domain/check';


        $textdm['input_data'] = json_encode(
            array(
                'domains' => $domains_array
            )
        );

        $res = $this->exec($textdm);
        $return = array();
        if(isset($res->answer->domains)){
            foreach ($res->answer->domains as $domain) {
                if(isset($domain->result) && $domain->result == 'Available'){
                    $return[$convertor->decode($domain->dname)] = DomainAPI::ANSWER_DOMAIN_AVAILABLE;
                } else {
                    $return[$convertor->decode($domain->dname)] = DomainAPI::ANSWER_DOMAIN_UNAVAILABLE;
                }
            }
        }

        return $return ;
    }

    public function checkDomainAvailable($domain)
    {
        $convertor = new IdnaConvertor();
        $domain_encoded = $convertor->encode($domain);

        $textdm= array();
        $textdm['action']='domain/check';
        $textdm['domain_name'] = $domain;

        $textdm['input_data'] = json_encode(
            array(
                'domains' => array(
                    array(
                        'dname' => $domain_encoded
                    )
                )
            )
        );

        $res = $this->exec($textdm);
//        echo '<pre>';
//         print_r($res);
//        echo '</pre>';
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

        $textdm= array();
        $textdm['action']='domain/update_nss';

        $textdm['undelegate']='';
        $textdm['input_data'] = json_encode(
            array(
                'domains' => array(array('dname' => $domain)),
                'nss' => array(
                    'ns0'           => $ns1,
                    'ns1'           => $ns2,
                    'ns3'           => $ns3,
                    'ns4'           => $ns4,
                    'ns0ip'         => $ns1ip,
                    'ns1ip'         => $ns2ip,
                    'ns2ip'         => $ns3ip,
                    'ns3ip'         => $ns4ip
                )
            )
        );

        $textdm['__trusted']='1';
        $textdm['ok_if_no_money']='1';

        $res = $this->exec($textdm);


        Logger::log('2domains: changeNS: '.json_encode($res));
        if($res->answer->domains[0]->result == 'success'){
            return DomainAPI::ANSWER_DOMAIN_CHANGE_NS_SUCCESS;
        }
        return DomainAPI::ANSWER_DOMAIN_CHANGE_NS_FAIL;
    }

    public function createPerson(DomainOwner $owner)
    {
        return null;
    }

    private function exec($textdm){


        // $request .= '&input_format=json&username='.$this->user.'&password='.$this->password;

        $textdm['username'] = $this->user;
        $textdm['password'] = $this->password;
        $textdm['output_format'] = 'json';
        $textdm['input_format'] = 'json';
        $textdm['folder_name'] = 'mail@hopebilling.com';

        // $textdm['sub_user_folder_name'] = 'mail@hopebilling.com';

        //  Logger::log($request);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);       // Allow self-signed certs
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);       // Allow certs that do not match the hostname
        curl_setopt($curl, CURLOPT_HEADER, 0);               // Do not include header in output

        curl_setopt($curl, CURL_HTTP_VERSION_1_0, 1);
        curl_setopt($curl, CURLOPT_URL, $this->url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $textdm);

        $res = curl_exec($curl);






        if(curl_error($curl)){
            return false;
        }

        curl_close($curl);



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

        $req = "domain/create?";

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


            'a_contype' => 'pp',
            'a_addr'    => Tools::transliteration($p_addr, 1),
            'a_city'    => Tools::transliteration($Owner->city),
            'a_state'   =>Tools::transliteration($Owner->region, 1),
            'a_country_code' => $Owner->country,
            'a_postcode' => $Owner->zip_code,
            'a_company' => 'Private Person',
            'a_email' => $Owner->email,
            'a_first_name' => Tools::transliteration($first_name),
            'a_last_name' => Tools::transliteration($last_name),
            'a_phone' => $Owner->phone,
            'a_fax'   => $Owner->fax,

            't_addr' => Tools::transliteration($p_addr, 1),
            't_city' => Tools::transliteration($Owner->city),
            't_company' => 'Private Person',
            't_country_code' => $Owner->country,
            't_email' => $Owner->email,
            't_first_name' => Tools::transliteration($first_name),
            't_last_name' => Tools::transliteration($last_name),
            't_phone' => $Owner->phone,
            't_state' => Tools::transliteration($Owner->region, 1),
            't_postcode' => $Owner->zip_code,

            'b_addr' => Tools::transliteration($p_addr, 1),
            'b_city' => Tools::transliteration($Owner->city),
            'b_company' => 'Private Person',
            'b_country_code' => $Owner->country,
            'b_email' => $Owner->email,
            'b_first_name' => Tools::transliteration($first_name),
            'b_last_name' => Tools::transliteration($last_name),
            'b_phone' => $Owner->phone,
            'b_postcode' => $Owner->zip_code,
            'b_state' => Tools::transliteration($Owner->region, 1),


            'o_addr' => Tools::transliteration($p_addr, 1),
            'o_city' => Tools::transliteration($Owner->city),
            'o_company' => 'Private Person',
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
            $rd['a_company_ru']         = 'Частное лицо';


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
            $rd['o_company_ru']         = 'Частное лицо';
            $rd['t_company_ru']         = 'Частное лицо';

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

            $rd['a_company_r']         = 'Частное лицо';
            $rd['b_company_r']         = 'Частное лицо';
            $rd['o_company_r']         = 'Частное лицо';
            $rd['t_company_r']         = 'Частное лицо';

            $rd['a_first_name_r']  = $first_name;
            $rd['a_last_name_r']   = $last_name;

            $rd['b_first_name_r']  = $first_name;
            $rd['b_last_name_r']   = $last_name;

            $rd['o_first_name_r']  = $first_name;
            $rd['o_last_name_r']   = $last_name;

            $rd['t_first_name_r']  = $first_name;
            $rd['t_last_name_r']   = $last_name;

        }


        $rd['action']='domain/update_contacts';

        $rd['io_encoding']='utf';


        $rd['__trusted']='1';
        $rd['ok_if_no_money']='1';






        $res = $this->exec($rd);

        Logger::log('2domains: registerDomain Req: '. json_encode($rd));
        Logger::log('2domains: registerDomain Answer: '. json_encode($res));


        if($res->result == 'success'){
            return '';
        }

        return DomainAPI::ANSWER_CONTACT_CREATE_FAIL;

    }
}