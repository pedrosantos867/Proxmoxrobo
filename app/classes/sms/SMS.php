<?php

namespace sms;

use System\Config;

class SMS
{

    public function __construct()
    {

    }

    public static function getGateway()
    {
        $config = new Config();
        switch ($config->sms_gateway) {
            case 'turbosms':
                return new TurboSMS();
                break;
            case 'smsc':
                return new SMSC();
                break;
        }

        return new EmptySMS();
    }

}