<?php
/**
 * Created by PhpStorm.
 * User: Viktor
 * Date: 17.07.15
 * Time: 16:00
 */

namespace sms;


use SoapClient;
use System\Config;

class SMSC implements ISMS
{

    private $login = '';
    private $pass = '';
    protected $client = null;

    public function __construct()
    {
        $this->client = new SoapClient('https://smsc.ru/sys/soap.php?wsdl');
    }

    public function sendSMS($to, $text)
    {

        $smsconfig = new Config('sms-gateway');
        $params = array(
            'login'  => $smsconfig->smsc->login,
            'psw'    => $smsconfig->smsc->password,
            'phones' => $to,
            'mes'    => $text,
            'id'     => '',
            'sender' => $smsconfig->smsc->sender,
            'time'   => 0);


        $ret          = $this->client->send_sms(
            $params
        );
        $this->result = $ret;



        if (isset($this->result->sendresult->id) && $this->result->sendresult->id) {
            return true;
        }

        return false;

    }


} 