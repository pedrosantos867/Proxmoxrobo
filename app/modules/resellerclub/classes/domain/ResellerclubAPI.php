<?php

namespace modules\resellerclub\classes\domain;


use domain\DomainAPI;
use domain\IDomainAPI;
use model\DomainOrder;
use model\DomainOwner;
use System\Logger;
use System\Tools;

class ResellerclubAPI extends DomainAPI implements IDomainAPI
{

    const GET = 1;
    const POST = 2;

    private $url = 'https://httpapi.com';
    private $reseller;
    private $api_key;

    public function __construct($Registrar)
    {
        $this->url = $Registrar->url ? $Registrar->url : $this->url;
        $this->reseller = $Registrar->login;
        $this->api_key = $Registrar->password;
    }

    public function checkDomainAvailable($domain)
    {
        $domain_tlds = array();

        preg_match('/(.*)\.(.*)/', $domain, $domain_tlds);

        if (!isset($domain_tlds[2])) {
            return DomainAPI::ANSWER_DOMAIN_UNAVAILABLE;
        }

        $domain_name = $domain_tlds[1];
        $domain_zone = ($domain_tlds[2]);

        $res = $this->exec('domains/available.json', 'domain-name=' . $domain_name . '&tlds=' . $domain_zone);

        if (isset($res[$domain]['status']) && $res[$domain]['status'] == 'available') {
            return DomainAPI::ANSWER_DOMAIN_AVAILABLE;
        }

        return DomainAPI::ANSWER_DOMAIN_UNAVAILABLE;


    }

    public function createPerson(DomainOwner $Owner)
    {

        $data = [
            'username' => uniqid() . '_' . $Owner->email,
            'passwd' => uniqid(),
            'name' => Tools::transliteration($Owner->fio, 1),
            'company' => $Owner->type == 2 ? $Owner->organization_name : 'NA',
            'email' => $Owner->email,
            'address-line-1' => Tools::transliteration($Owner->address, 1),
            'city' => Tools::transliteration($Owner->city, 1),
            'state' => Tools::transliteration($Owner->region, 1),
            'country' => ($Owner->country),
            'zipcode' => $Owner->zip_code,
            'phone-cc' => substr($Owner->phone, 1, 2),
            'phone' => substr($Owner->phone, 2),
            'lang-pref' => 'en',
        ];

        $res = $this->exec('customers/signup.json', http_build_query($data));


        if (!isset($res['status'])) {
            return $res;
        }

        return false;
    }

    public function createContactPerson(DomainOwner $Owner, $customer_id = null)
    {


        $data = [
            'name' => Tools::transliteration($Owner->fio, 1),
            'company' => $Owner->type == 2 ? $Owner->organization_name : 'NA',
            'email' => $Owner->email,
            'address-line-1' => Tools::transliteration($Owner->address, 1),
            'city' => Tools::transliteration($Owner->city, 1),
            'country' => ($Owner->country),
            'zipcode' => $Owner->zip_code,
            'phone-cc' => substr($Owner->phone, 1, 2),
            'phone' => substr($Owner->phone, 2),
            'customer-id' => $customer_id,
            'type' => 'Contact',
        ];

        $res = $this->exec('contacts/add.json', http_build_query($data), self::POST);
        if ($res && !isset($res['status'])) {
            return $res;
        }

        return DomainAPI::ANSWER_CONTACT_CREATE_FAIL;
    }

    public function changeContactPerson(DomainOrder $DomainOrder, DomainOwner $DomainOwner)
    {
        //https://test.httpapi.com/api/domains/modify-ns.json?auth-userid=0&api-key=key&order-id=0&ns=ns1.domain.asia&ns=ns2.domain.asia
        //   $this->getOrderIdByDomainName($DomainOrder->name);
//https://test.httpapi.com/api/domains/modify-contact.json?auth-userid=0&api-key=key&order-id=0&reg-contact-id=0&admin-contact-id=0&tech-contact-id=0&billing-contact-id=0
        $contact_id = $this->createContactPerson($DomainOwner, $DomainOrder->contract_id);
        $res = $this->exec('domains/modify-contact.json', http_build_query(['order-id' => $DomainOrder->domain_reg_id, 'reg-contact-id' => $contact_id, 'admin-contact-id' => $contact_id, 'tech-contact-id' => $contact_id, 'billing-contact-id' => $contact_id]));
        Logger::log('resellerclub changeContactPerson:' . json_encode($res));
        if ($res['status'] == 'Success') {
            return $contact_id;
        }

        return DomainAPI::ANSWER_CONTACT_CREATE_FAIL;
    }

    public function prolongDomain(DomainOrder $DomainOrder, DomainOwner $owner)
    {
        //https://test.httpapi.com/api/domains/renew.json?auth-userid=0&api-key=key&order-id=562994&years=1&exp-date=1279012036&invoice-option=NoInvoice
        $domain_info = $this->getDomainInfo($DomainOrder->domain_reg_id);
        $endtime = $domain_info['endtime'];

        $res = $this->exec('domains/renew.json', http_build_query(['order-id' => $DomainOrder->domain_reg_id, 'years' => $DomainOrder->period, 'exp-date' => $endtime, 'invoice-option' => 'NoInvoice']));

        Logger::log('resellerclub prolongDomain:' . json_encode($res));

        if ($res['status'] == 'Success') {
            return DomainAPI::ANSWER_DOMAIN_PROLONG_SUCCESS;
        }

        return DomainAPI::ANSWER_DOMAIN_PROLONG_FAIL;

    }

    public function changeNS(DomainOrder $DomainOrder, $old_ns)
    {
        $res = $this->exec('domains/modify-ns.json', 'order-id=' . $DomainOrder->domain_reg_id . '&ns=' . $DomainOrder->dns1 . '&ns=' . $DomainOrder->dns2 . '');

        Logger::log('resellerclub changeNS:' . json_encode($res));

        if ($res['status'] == 'SUCCESS') {
            return DomainAPI::ANSWER_DOMAIN_CHANGE_NS_SUCCESS;
        }

        return DomainAPI::ANSWER_DOMAIN_CHANGE_NS_FAIL;
    }


    public function registerDomain(DomainOrder $DomainOrder, DomainOwner $Owner)
    {
        $data = [
            'domain-name' => $DomainOrder->domain,
            'years' => $DomainOrder->period,
            'reg-contact-id' => $DomainOrder->nic_hdl,
            'admin-contact-id' => $DomainOrder->nic_hdl,
            'tech-contact-id' => $DomainOrder->nic_hdl,
            'billing-contact-id' => $DomainOrder->nic_hdl,
            'customer-id' => $DomainOrder->contract_id,
            'invoice-option' => 'NoInvoice',
        ];

        $res = $this->exec('domains/register.json', http_build_query($data) . '&ns=' . $DomainOrder->dns1 . '&ns2=' . $DomainOrder->dns2);
        Logger::log('ResellerClub registerDomain:' . json_encode($res));

        if (isset($res['status']) && $res['status'] == 'Success') {

            $DomainOrder->domain_reg_id = $res['entityid'];
            $DomainOrder->save();

            return DomainAPI::ANSWER_DOMAIN_REG_SUCCESS;
        }
        return DomainAPI::ANSWER_DOMAIN_REG_FAIL;
    }

    private function getDomainInfo($order_id)
    {
        $res = $this->exec('domains/details.json', 'order-id=' . $order_id . '&options=OrderDetails');
        return $res;
    }

    public function getErrorCode()
    {
        // TODO: Implement getErrorCode() method.
    }

    public function reqPool()
    {
        // TODO: Implement reqPool() method.
    }


    private function exec($function, $params, $method = self::GET)
    {
        if($method == self::GET) {
            $url = $this->url . '/api/' . $function . '?auth-userid=' . $this->reseller . '&api-key=' . $this->api_key . '&' . $params;
        } else {
            $url = $this->url . '/api/' . $function . '?auth-userid=' . $this->reseller . '&api-key=' . $this->api_key ;
        }
//echo $url;
        Logger::log('resellerclub request: ' . $url);
        $fp = curl_init();
        curl_setopt($fp, CURLOPT_URL, $url);
        curl_setopt($fp, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($fp, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($fp, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($fp, CURLOPT_FAILONERROR, false);

        if($method== self::POST){
            curl_setopt($fp, CURLOPT_POST, 1);
            curl_setopt($fp, CURLOPT_POSTFIELDS, 'auth-userid=' . $this->reseller . '&api-key=' . $this->api_key.'&'.$params);
        }

        curl_setopt($fp, CURLOPT_TIMEOUT, 120);
        $result = curl_exec($fp);

        Logger::log('resellerclub response: ' . $result);
        $result = json_decode($result, 1);
        return $result;
    }


}