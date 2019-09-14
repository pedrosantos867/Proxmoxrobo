<?php

namespace modules\resello\classes\domain;
use domain\DomainAPI;
use domain\IDomainAPI;
use model\DomainOrder;
use model\DomainOwner;
use System\Exception;
use System\Logger;
use System\Tools;

class ReselloAPI extends DomainAPI implements IDomainAPI
{
    private $session_id = 0;

    private $socket = null;

    private $url = '';
    private $port = '';

    private $baseurl='';
    private $apikey='';
    private $error = null;

    const HTTP_METHOD_GET = 'GET';
    const HTTP_METHOD_POST = 'POST';
    const HTTP_METHOD_PUT = 'PUT';
    const HTTP_METHOD_DELETE = 'DELETE';

    private $allowed_methods = array(
        self::HTTP_METHOD_GET,
        self::HTTP_METHOD_POST,
        self::HTTP_METHOD_PUT,
        self::HTTP_METHOD_DELETE
    );
    protected $d_paths = array(
        'collection' => '/domain',
        'transfer' => '/domain-transfer',
        'transferstatus' => '/domain-transfer/%d',
        'single' => '/domain/%s',
        'renew' => '/domain/%s/renew',
        'nameserver' => '/domain/%s/name-server',
        'auth_code' => '/domain/%s/auth-code',
        'dnsrecords' => '/domain/%s/zone',
        'is' => '/domain-is',
    );
    protected $c_paths = array(
        'collection' => '/customer',
        'single' => '/customer/%s',
        'lookup' => '/customer?email=%s',
    );
    protected $o_paths = array(
        'collection' => '/order',
    );
    public function __construct($Registrar)
    {
        $baseurl = $Registrar->url ? $Registrar->url: "https://backoffice.hostcontrol.com/api/v1";
        $apikey = $Registrar->password;

        $this->baseurl = preg_replace('#/$#', '', $baseurl);
        $this->apikey = $apikey;


    }
    public function createPerson(DomainOwner $owner){
        return null;
    }

    public function changeContactPerson(DomainOrder $DomainOrder, DomainOwner $owner)
    {
        $customer_id = $this->get($this->get_request_path('lookup', $this->c_paths, array($owner->email)));
        if($customer_id === DomainAPI::ANSWER_CONTACT_CREATE_FAIL)
        {
            return DomainAPI::ANSWER_CONTACT_CREATE_FAIL;
        }
        $phone = str_replace('+380', '+380.', $owner->mobile_phone);
        if(isset($_SERVER["REMOTE_ADDR"]))
        {
            $ip="8.8.8.8";
        }
        else
        {
            $ip=$_SERVER["REMOTE_ADDR"];
        }
        $customer_info = array(
            'name'              => $owner->fio,
            'address'           => Tools::transliteration($owner->address, 1),
            'zipcode'           => $owner->zip_code,
            'city'              => Tools::transliteration($owner->city, 1),
            'state'             => Tools::transliteration($owner->region, 1),
            'country'           => $owner->country,
            'voice'             => $phone,
            'password'          => Tools::generateCode(),
            'email'             => $owner->email,
            'registration_ip'   => $ip,
        );

        Logger::log('change customer_info '.json_encode($customer_info));
        try{
            $cont= $this->get($this->get_request_path('get_cont', array('get_cont'=>'/contact?customer=%s'),array($customer_id)));
            Logger::log('cont_id '.json_encode($cont[0]->id));
            $path = $this->get_request_path('contact', array('contact'=>'/contact/%s'),array($cont[0]->id));
            Logger::log('path '.json_encode($path));
            $contact = $this->put($path, $customer_info);
            Logger::log('contact '.json_encode($contact));
            return $customer_id;

        }
        catch(Exception $e)
        {

            return DomainAPI::ANSWER_CONTACT_CREATE_FAIL;
        }
    }

    public function createContactPerson(DomainOwner $owner, $c_id=null){
        $hostcontrol_customer = $this->get($this->get_request_path('lookup', $this->c_paths, array($owner->email)));
        if(isset($hostcontrol_customer[0]->id))
        {
            Logger::log('c_id ex '.$hostcontrol_customer[0]->id);
            return $hostcontrol_customer[0]->id;
        }

        $phone = str_replace('+380', '+380.', $owner->mobile_phone);
        if(isset($_SERVER["REMOTE_ADDR"]))
        {
            $ip="8.8.8.8";
        }
        else
        {
            $ip=$_SERVER["REMOTE_ADDR"];
        }
        $customer_info = array(
            'name'              => $owner->fio,
            'address'           => Tools::transliteration($owner->address, 1),
            'zipcode'           => $owner->zip_code,
            'city'              => Tools::transliteration($owner->city, 1),
            'state'             => Tools::transliteration($owner->region, 1),
            'country'           => $owner->country,
            'voice'             => $phone,
            'password'          => Tools::generateCode(),
            'email'             => $owner->email,
            'registration_ip'   => $ip,
        );

        Logger::log('customer_info'.$customer_info);
        try
        {
            $hostcontrol_customer = $this->post($this->get_request_path('collection', $this->c_paths), $customer_info);
            $hostcontrol_customer_id = $hostcontrol_customer->id;
            Logger::log('c_id '.$hostcontrol_customer_id);
            return $hostcontrol_customer_id;

        }
        catch(Exception $e)
        {

            return DomainAPI::ANSWER_CONTACT_CREATE_FAIL;
        }
    }
    public function reqPool(){}
    public function prolongDomain(DomainOrder $DomainOrder,DomainOwner $owner){
        try
        {
            $path = $this->get_request_path('renew', $this->d_paths ,array($DomainOrder->domain));
            $rez= $this->post($path, array(
                'interval'  => 12
            ));
            Logger::log('prolongDomain '.json_encode($rez));
            return DomainAPI::ANSWER_DOMAIN_PROLONG_SUCCESS;
        }
        catch (Exception $e)
        {
            return DomainAPI::ANSWER_DOMAIN_PROLONG_FAIL;
        }

    }
    public function registerDomain( DomainOrder $DomainOrder, DomainOwner $Owner){
        $customer_id = $this->createContactPerson($Owner);
        if($customer_id === DomainAPI::ANSWER_CONTACT_CREATE_FAIL)
        {
            return DomainAPI::ANSWER_DOMAIN_REG_FAIL;
        }
        try
        {

            $path = $this->get_request_path('collection', $this->d_paths);
            $rez_domain = $this->post($path, array(
                'customer'  => intval($customer_id),
                'domain'    => $DomainOrder->domain,
                'interval'  => 12
            ));
            Logger::log('rez_domain '.json_encode($rez_domain));
        }
        catch(Exception $e)
        {
            Logger::log($e);
            return DomainAPI::ANSWER_DOMAIN_REG_FAIL;
        }
        $rez_dns='';
        try
        {
            $ns = array( $DomainOrder->dns1, $DomainOrder->dns2, $DomainOrder->dns3,$DomainOrder->dns4);
            $records='';
            $i=0;
            for($a=0; $a<4;$a++)
            {
                if($ns[$a] != "")
                {
                    $records[$i++] = array('hostname'=>$ns[$a]);

                }
            }
            Logger::log('dns '.json_encode($records));
            $path = $this->get_request_path('nameserver', $this->d_paths, array($DomainOrder->domain));
            $rez_dns= $this->post($path, array(
                'name_servers' => $records
            ));
        }
        catch(Exception $e)
        {

            Logger::log('rez_dns '.$rez_dns);
        }
        return DomainAPI::ANSWER_DOMAIN_REG_SUCCESS;

    }
    public function changeNS(DomainOrder $DomainOrder, $old_ns_array){

        $ns = array( $DomainOrder->dns1, $DomainOrder->dns2, $DomainOrder->dns3,$DomainOrder->dns4);
        $records='';
        $i=0;
        for($a=0; $a<4;$a++)
        {
            if($ns[$a] != "")
            {
                $records[$i++] = array('hostname'=>$ns[$a]);

            }
        }
        try{
            $path = $this->get_request_path('nameserver', $this->d_paths, array($DomainOrder->domain));
            $rez_dns= $this->post($path, array(
                'name_servers' => $records
            ));
            Logger::log('dns '.json_encode($rez_dns));
        }
        catch(Exception $e) {
            Logger::log($e);
            return DomainAPI::ANSWER_DOMAIN_CHANGE_NS_FAIL;
        }

        return DomainAPI::ANSWER_DOMAIN_CHANGE_NS_SUCCESS;

    }

    public function getErrorCode(){
        return $this->error;
    }

    public function checkDomainAvailable( $domain){
        $path = $this->get_request_path('is', $this->d_paths);
        $result = $this->post($path, array(
            'domain' => $domain
        ));
        if ($result =='free')
        {
            return DomainAPI::ANSWER_DOMAIN_AVAILABLE;
        }
        else
        {
            return DomainAPI::ANSWER_DOMAIN_UNAVAILABLE;
        }
    }
    protected function get_request_path($path_name, $paths, $parameters = array())
    {
        if(! is_array($paths) || count($paths) == 0)
        {
            throw new Exception('Please provide a paths attribute.');
        }
        foreach($parameters as $parameter)
        {
            if((! is_string($parameter) && ! is_int($parameter)) || strstr('/', $parameter))
            {
                throw new Exception('Invalid parameter for URL resolving.');
            }
        }

        if(! array_key_exists($path_name, $paths))
        {
            throw new Exception('Path does not exist.');
        }

        $path = $paths[$path_name];

        if(is_array($parameters) && count($parameters) > 0)
        {
            $path = vsprintf($path, $parameters);
        }

        return $path;
    }
    public function get($path, $parameters = array())
    {
        return $this->execute_request(self::HTTP_METHOD_GET, $path, $parameters);
    }

    public function post($path, $parameters = array())
    {
        return $this->execute_request(self::HTTP_METHOD_POST, $path, $parameters);
    }

    public function put($path, $parameters = array())
    {
        return $this->execute_request(self::HTTP_METHOD_PUT, $path, $parameters);
    }

    public function delete($path, $parameters = array())
    {
        return $this->execute_request(self::HTTP_METHOD_DELETE, $path, $parameters);
    }

    private function execute_request($method, $path, $parameters)
    {
        if(! in_array($method, $this->allowed_methods))
        {
            throw new Exception('Unsupported request method.');
        }

        $handler = curl_init();
        $url = $this->baseurl . $path;

        if($method == self::HTTP_METHOD_GET)
        {
            if(is_array($parameters) && count($parameters) > 0)
            {
                $query_string = http_build_query($parameters);
                $url .= $query_string;
            }
        }
        else
        {
            $parameters = json_encode($parameters);

            if($parameters === false)
            {
                throw new Exception('Unable to encode parameters.');
            }

            curl_setopt($handler, CURLOPT_POSTFIELDS, $parameters);
        }
        Logger::log('request url: '.json_encode($url));
        Logger::log('parameters: '.json_encode($parameters));

        curl_setopt($handler, CURLOPT_URL, $url);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handler, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($handler, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($handler, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($handler, CURLOPT_HTTPHEADER, array("X-APIKEY: $this->apikey"));

        $response = curl_exec($handler);
        Logger::log('response '.$response);
        if(strlen($response) == 0)
        {
            throw new Exception('No response received.');
        }

        $status_code = curl_getinfo($handler, CURLINFO_HTTP_CODE);

        if($status_code != 200)
        {
            throw new Exception("Received status code '$status_code' .");
        }

        $response = json_decode($response);

        if(! is_object($response))
        {
            throw new Exception('Invalid response received.');
        }

        if(! property_exists($response, 'success') || ! $response->success)
        {
            if(is_object($response->error->message))
            {
                $nested_errors = get_object_vars($response->error->message);
                $messages = array();
                foreach($nested_errors as $attribute => $error)
                {
                    $messages[] = ucfirst($attribute) . ": " . join(', ', $error);
                }
                $error = join(", ", $messages);
            }
            else
            {
                $error = $response->error->message;
            }

            throw new Exception($error);
        }

        if(property_exists($response, 'result'))
        {
            return $response->result;
        }

        return null;
    }
}