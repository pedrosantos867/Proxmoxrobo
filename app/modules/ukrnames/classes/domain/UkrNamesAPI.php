<?php

namespace modules\ukrnames\classes\domain;
use domain\DomainAPI;
use domain\IDomainAPI;
use model\DomainOrder;
use model\DomainOwner;
use System\Exception;
use System\Logger;
use System\Tools;
use tools\Xml2Array;

class UkrNamesAPI extends DomainAPI implements IDomainAPI
{

    private $session_id = 0;

    private $socket = null;

    private $url = '';
    private $port = '';

    private $error = null;

    public function __construct($Registrar)
    {


        $errorn = null;
        $errostr = null;


        $matches = array();

        preg_match('/(.*:\/\/)(.*):(.*)/', $Registrar->url, $matches);

        if(!isset($matches[3])){
            throw new Exception('Error url parsing');
        }

        $this->url = $matches[1].$matches[2];
        $this->port = $matches[3];

        $context = stream_context_create();

        stream_context_set_option($context, 'ssl', 'verify_peer', false);
        stream_context_set_option($context, 'ssl', 'verify_host', false);
        stream_context_set_option($context, 'ssl', 'allow_self_signed', true);
        stream_context_set_option($context, 'ssl', 'ciphers','TLSv1+HIGH:!aNull:@STRENGTH:!DH');
        stream_context_set_option($context, 'ssl', 'verify_peer_name',false);

        $socket = stream_socket_client($this->url .':'.$this->port , $errorn, $errostr, 60, STREAM_CLIENT_CONNECT, $context);


       // $socket = @fsockopen($this->url, $this->port , $errorn, $errostr, 10);
        $this->socket = $socket;


        $this->session_id = time();
        $this->send(false);

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
        <epp xmlns="urn:ietf:params:xml:ns:epp-1.0" xmlns:xsi="http://www.w3.org/2001/XMLSchemainstance" xsi:schemaLocation="urn:ietf:params:xml:ns:epp-1.0 epp-1.0.xsd">
            <command>
                <login>
                    <clID>'.$Registrar->login.'</clID>
                    <pw>'.$Registrar->password.'</pw>
                    <options>
                        <version>1.0</version>
                        <lang>en</lang>
                    </options>
                </login>
            <clTRID>'.$this->session_id.'</clTRID>
            </command>
        </epp>';



       $res = $this->send($xml);

        if($res['epp']['response']['result']['@attributes']['code'] == 1000){

        }

    }

    public function createPerson(DomainOwner $owner){
        return null;
    }

    public function changeContactPerson(DomainOrder $DomainOrder, DomainOwner $owner)
    {
        $names = explode(' ', $owner->fio);
        $last_name = $names[0];
        $first_name = $names[1];

        $phone = str_replace('+380', '+380.', $owner->mobile_phone);

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
       <epp xmlns="urn:ietf:params:xml:ns:epp-1.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="urn:ietf:params:xml:ns:epp-1.0 epp-1.0.xsd">
            <command>
            <create>
                <contact:create xmlns:contact="urn:ietf:params:xml:ns:contact-1.0" xsi:schemaLocation="urn:ietf:params:xml:ns:contact-1.0 contact-1.0.xsd">
                    <contact:id>AUTO</contact:id>
                    <contact:postalInfo type="loc">
                        <contact:name>'.Tools::transliteration($first_name).' '.Tools::transliteration($last_name).'</contact:name>
                        <contact:org>'.Tools::transliteration($owner->organization_name, 1).'</contact:org>
                        <contact:addr>
                            <contact:street>'.Tools::transliteration($owner->address, 1).'</contact:street>
                            <contact:street></contact:street>
                            <contact:city>'.Tools::transliteration($owner->city, 1).'</contact:city>
                            <contact:sp>'.Tools::transliteration($owner->region, 1).'</contact:sp>
                            <contact:pc>'.$owner->zip_code.'</contact:pc>
                            <contact:cc>'.$owner->country.'</contact:cc>
                        </contact:addr>
                    </contact:postalInfo>
                    <contact:voice>'.$phone.'</contact:voice>
                    <contact:email>'.$owner->email.'</contact:email>
                    <contact:authInfo>
                        <contact:pw>348937123</contact:pw>
                    </contact:authInfo>
                    <contact:disclose flag="0">
                        <contact:voice />
                        <contact:email />
                    </contact:disclose>
                </contact:create>
            </create>
                <clTRID>'.$this->session_id.'</clTRID>
            </command>
        </epp>
        ';

        $res = $this->send($xml);
        Logger::log(json_encode($res));

       if($res['epp']['response']['result']['@attributes']['code'] == 1000) {
           $contact = ($res['epp']['response']['resData']['contact:creData']['contact:id']);

           $xml2 = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
            <epp xmlns="urn:ietf:params:xml:ns:epp-1.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="urn:ietf:params:xml:ns:epp-1.0 epp-1.0.xsd">
             <command>
             <update>
             <domain:update xmlns:domain="urn:ietf:params:xml:ns:domain-1.0" xsi:schemaLocation="urn:ietf:params:xml:ns:domain-1.0 domain-1.0.xsd">
                 <domain:name>' . $DomainOrder->domain . '</domain:name>
                 <domain:add>
                    <domain:contact type="tech">' . $contact . '</domain:contact>
                    <domain:contact type="admin">' . $contact . '</domain:contact>
                    <domain:contact type="billing">' . $contact . '</domain:contact>
                 </domain:add>
                 <domain:rem>
                    <domain:contact type="tech">' . $DomainOrder->nic_hdl . '</domain:contact>
                    <domain:contact type="admin">' . $DomainOrder->nic_hdl . '</domain:contact>
                    <domain:contact type="billing">' . $DomainOrder->nic_hdl . '</domain:contact>
                 </domain:rem>
                 <domain:chg>
                    <domain:registrant>' . $contact . '</domain:registrant>
                  </domain:chg>
             </domain:update>
             </update>
             <clTRID>'.$this->session_id.'</clTRID>
             </command>
            </epp>';

            Logger::log($xml2);
           $res2 = $this->send($xml2);
           Logger::log(json_encode($res2));
           if ($res2['epp']['response']['result']['@attributes']['code'] == 1000 || $res2['epp']['response']['result']['@attributes']['code'] == 1001) {
               return $contact;
           }
       }

        return DomainAPI::ANSWER_CONTACT_CREATE_FAIL;
    }

    public function createContactPerson(DomainOwner $owner, $c_id=null){
        $names = explode(' ', $owner->fio);
        $last_name = $names[0];
        $first_name = $names[1];

        $phone = str_replace('+380', '+380.', $owner->mobile_phone);
        
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
        <epp xmlns="urn:ietf:params:xml:ns:epp-1.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="urn:ietf:params:xml:ns:epp-1.0 epp-1.0.xsd">
            <command>
            <create>
                <contact:create xmlns:contact="urn:ietf:params:xml:ns:contact-1.0" xsi:schemaLocation="urn:ietf:params:xml:ns:contact-1.0 contact-1.0.xsd">
                    <contact:id>AUTO</contact:id>
                    <contact:postalInfo type="loc">
                        <contact:name>'.Tools::transliteration($first_name).' '.Tools::transliteration($last_name).'</contact:name>
                        <contact:org>'.Tools::transliteration($owner->organization_name, 1).'</contact:org>
                        <contact:addr>
                            <contact:street>'.Tools::transliteration($owner->address, 1).'</contact:street>
                            <contact:street></contact:street>
                            <contact:city>'.Tools::transliteration($owner->city, 1).'</contact:city>
                            <contact:sp>'.Tools::transliteration($owner->region, 1).'</contact:sp>
                            <contact:pc>'.$owner->zip_code.'</contact:pc>
                            <contact:cc>'.$owner->country.'</contact:cc>
                        </contact:addr>
                    </contact:postalInfo>
                    <contact:voice>'.$phone.'</contact:voice>
                    <contact:email>'.$owner->email.'</contact:email>
                    <contact:authInfo>
                        <contact:pw>348937123</contact:pw>
                    </contact:authInfo>
                    <contact:disclose flag="0">
                        <contact:voice />
                        <contact:email />
                    </contact:disclose>
                </contact:create>
            </create>
                <clTRID>'.$this->session_id.'</clTRID>
            </command>
        </epp>
        ';


        $res = $this->send($xml);
        Logger::log(json_encode($res));

        if($res['epp']['response']['result']['@attributes']['code'] == 1000) {
            return ($res['epp']['response']['resData']['contact:creData']['contact:id']);
        }



        return DomainAPI::ANSWER_CONTACT_CREATE_FAIL;
    }
    public function reqPool(){}
    public function prolongDomain(DomainOrder $DomainOrder,DomainOwner $owner){

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<epp xmlns="urn:ietf:params:xml:ns:epp-1.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
xsi:schemaLocation="urn:ietf:params:xml:ns:epp-1.0
epp-1.0.xsd">
<command>
<renew>
<domain:renew xmlns:domain="urn:ietf:params:xml:ns:domain-1.0">
<domain:name>'.$DomainOrder->domain.'</domain:name>
<domain:curExpDate>'.$DomainOrder->date_end.'</domain:curExpDate>
<domain:period unit="y">'.$DomainOrder->period.'</domain:period>
</domain:renew>
</renew>
<clTRID>'.$this->session_id.'</clTRID>
</command>
</epp>';
        $res = $this->send($xml);

        if($res['epp']['response']['result']['@attributes']['code'] == 1000){
            return DomainAPI::ANSWER_DOMAIN_PROLONG_SUCCESS;
        }
        return DomainAPI::ANSWER_DOMAIN_PROLONG_FAIL;
    }

    public function checkDomainAvailable( $domain){

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
                 <epp xmlns="urn:ietf:params:xml:ns:epp-1.0">
                   <command>
                     <check>
                       <domain:check xmlns:domain="urn:ietf:params:xml:ns:domain-1.0">
                         <domain:name>'.$domain.'</domain:name>
                       </domain:check>
                     </check>
                     <clTRID>'.$this->session_id.'</clTRID>
                   </command>
                 </epp>
        ';
        $res = $this->send($xml);
        if($res['epp']['response']['resData']['domain:chkData']['domain:cd']['domain:name']['@attributes']['avail']){
            return DomainAPI::ANSWER_DOMAIN_AVAILABLE;
        }
        return DomainAPI::ANSWER_DOMAIN_UNAVAILABLE;
    }

    public function registerDomain( DomainOrder $DomainOrder, DomainOwner $Owner){

        $ns1 = $DomainOrder->dns1; $ns1ip = $DomainOrder->ip1;
        $ns2 = $DomainOrder->dns2; $ns2ip = $DomainOrder->ip2;
        $ns3 = $DomainOrder->dns3; $ns3ip = $DomainOrder->ip3;
        $ns4 = $DomainOrder->dns4; $ns4ip = $DomainOrder->ip4;



        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<epp xmlns="urn:ietf:params:xml:ns:epp-1.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="urn:ietf:params:xml:ns:epp-1.0 epp-1.0.xsd">
 <command>
 <create>
 <domain:create xmlns:domain="urn:ietf:params:xml:ns:domain-1.0" xsi:schemaLocation="urn:ietf:params:xml:ns:domain-1.0 domain-1.0.xsd">
 <domain:name>'.$DomainOrder->domain.'</domain:name>
 <domain:ns>
 <domain:hostObj>'.$ns1.'</domain:hostObj>
 <domain:hostObj>'.$ns2.'</domain:hostObj>
 </domain:ns>
 <domain:registrant>'.$DomainOrder->nic_hdl.'</domain:registrant>
 <domain:contact
 type="admin">'.$DomainOrder->nic_hdl.'</domain:contact>
 <domain:contact
 type="tech">'.$DomainOrder->nic_hdl.'</domain:contact>
 <domain:contact
 type="billing">'.$DomainOrder->nic_hdl.'</domain:contact>
 <domain:authInfo>
 <domain:pw>'.$DomainOrder->auth_code.'</domain:pw>
 </domain:authInfo>
 </domain:create>
 </create>
 <clTRID>'.$this->session_id.'</clTRID>
 </command>
</epp>';


        $res = $this->send($xml);

        Logger::log('Ukrnames reg domain query: '.json_encode($xml));

        if($res['epp']['response']['result']['@attributes']['code'] == 1000){
            Logger::log('Ukrnames reg domain result: ANSWER_DOMAIN_REG_SUCCESS');
            return DomainAPI::ANSWER_DOMAIN_REG_SUCCESS;
        }
        Logger::log('Ukrnames reg domain result: ANSWER_DOMAIN_REG_FAIL');
        return DomainAPI::ANSWER_DOMAIN_REG_FAIL;
    }

    public function changeNS(DomainOrder $DomainOrder, $old_ns_array){

        $ns1 = $DomainOrder->dns1; $ns1ip = $DomainOrder->ip1;
        $ns2 = $DomainOrder->dns2; $ns2ip = $DomainOrder->ip2;
        $ns3 = $DomainOrder->dns3; $ns3ip = $DomainOrder->ip3;
        $ns4 = $DomainOrder->dns4; $ns4ip = $DomainOrder->ip4;



        $ons1 =''; $ons1ip = '';
        $ons2=''; $ons2ip = '';
        $ons3=''; $ons3ip = '';
        $ons4=''; $ons4ip = '';

        $old_dns = array_keys($old_ns_array);
        Logger::log(json_encode($old_dns));
        $index = array_search($ns1, $old_dns);
        if($index !== false){
            $ns1= null;
            unset($old_dns[$index]);
        }

        $index = array_search($ns2, $old_dns);
        if($index !== false){
            $ns2= null;
            unset($old_dns[$index]);
        }

        $index = array_search($ns3, $old_dns);
        if($index !== false){
            $ns3= null;
            unset($old_dns[$index]);
        }

        $index = array_search($ns4, $old_dns);
        if($index !== false){
            $ns4= null;
            unset($old_dns[$index]);
        }

        Logger::log(json_encode($old_dns));
        $i = 0;
        foreach($old_dns as $key=> $name){
            if($i==0){
                $ons1 = $name;
            }elseif($i==1){
                $ons2 = $name;
            }elseif($i==2){
                $ons3 = $name;
            }elseif($i==3){
                $ons4 = $name;
            }
            $i++;
        }

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<epp
 xmlns="urn:ietf:params:xml:ns:epp-1.0"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 xsi:schemaLocation="urn:ietf:params:xml:ns:epp-1.0 epp-1.0.xsd">
 <command>
 <update>
 <domain:update
 xmlns:domain="urn:ietf:params:xml:ns:domain-1.0"
 xsi:schemaLocation="urn:ietf:params:xml:ns:domain-1.0 domain-1.0.xsd">
 <domain:name>'.$DomainOrder->domain.'</domain:name>
 <domain:add>
 <domain:ns>
 '.($ns1 ? '<domain:hostObj>'.$ns1.'</domain:hostObj>' : '').'
 '.($ns2 ? '<domain:hostObj>'.$ns2.'</domain:hostObj>' : '').'
 '.($ns3 ? '<domain:hostObj>'.$ns3.'</domain:hostObj>' : '').'
 '.($ns4 ? '<domain:hostObj>'.$ns4.'</domain:hostObj>' : '').'
 </domain:ns>
 </domain:add>
 <domain:rem>
 <domain:ns>
 '.($ons1 ? '<domain:hostObj>'.$ons1.'</domain:hostObj>' : '').'
 '.($ons2 ? '<domain:hostObj>'.$ons2.'</domain:hostObj>' : '').'
 '.($ons3 ? '<domain:hostObj>'.$ons3.'</domain:hostObj>' : '').'
 '.($ons4 ? '<domain:hostObj>'.$ons4.'</domain:hostObj>' : '').'
 </domain:ns>
 </domain:rem>
 </domain:update>
 </update>
 <clTRID>'.$this->session_id.'</clTRID>
 </command>
</epp>';
        Logger::log('Ukrnames change NS query: '. json_encode($xml));
        $res = $this->send($xml);
        Logger::log('Ukrnames change NS result: '. json_encode($res));

        if($res['epp']['response']['result']['@attributes']['code'] == 1000 || $res['epp']['response']['result']['@attributes']['code'] == 1001){
            return DomainAPI::ANSWER_DOMAIN_CHANGE_NS_SUCCESS;
        }

        return DomainAPI::ANSWER_DOMAIN_CHANGE_NS_FAIL;
    }

    public function getErrorCode(){
        return $this->error;
    }


    private function send($xml)
    {
        if($xml) {
            if (!@fwrite($this->socket, pack('N', (strlen($xml) + 4)) . $xml)) {
                return false;
            }
        }
        if (@feof($this->socket)) {
            return false;
        }

        $hdr = @fread($this->socket, 4);

        if (empty($hdr)) {
            return false;
        } else {
            $unpacked = unpack('N', $hdr);
            $answer = @fread($this->socket, ($unpacked[1] - 4));


            Xml2Array::loadXML($answer);
            return Xml2Array::getArray();
        }
    }


}