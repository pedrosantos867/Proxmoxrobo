<?php
/**
 * Created by PhpStorm.
 * User: Viktor
 * Date: 17.07.15
 * Time: 14:42
 */

namespace sms;


use SoapClient;
use System\Config;

class TurboSMS implements ISMS
{

    protected $client = null;

    public function __construct()
    {
        $this->client = new SoapClient('http://turbosms.in.ua/api/wsdl.html');
        $smsconfig = new Config('sms-gateway');


        if ($smsconfig->turbosms) {
            if (isset($smsconfig->turbosms->login) && isset($smsconfig->turbosms->password)) {
                $auth = Array(
                    'login' => $smsconfig->turbosms->login,
                    'password' => $smsconfig->turbosms->password
                );
            }
            else {
                $auth = Array(
                    'login' => '',
                    'password' => ''
                );
            }
            // Авторизируемся на сервере
            $result = $this->client->Auth($auth);
            // echo '1'.$result->AuthResult . PHP_EOL;
        }
    }

    public function sendSMS($to, $text)
    {
        $smsconfig = new Config('sms-gateway');
        $sms    = Array(
            'sender'      => $smsconfig->turbosms->sender,
            'destination' => $to,
            'text'        => $text
        );

        $result = $this->client->SendSMS($sms);
        $result = $result->SendSMSResult->ResultArray;


        if (isset($result[0])) {
            return true;
        }

        return false;
    }



}