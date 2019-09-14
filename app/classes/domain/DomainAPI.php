<?php

namespace domain;

use domain\whois\Whois;
use model\DomainRegistrar;
use System\Exception;
use System\Module;

class DomainAPI
{

    const ANSWER_SYSTEM_ERROR = 0;



    protected $objectPanel;

    const REGISTRANT_NIC_RU         = 1;
    const REGISTRANT_R01            = 2;
    const REGISTRANT_REG_RU         = 3;
    const REGISTRANT_UKRNAMES       = 4;
    const REGISTRANT_2DOMAINS       = 5;
    const REGISTRANT_DRSUA          = 6;
    const REGISTRANT_SLIMHOSTUA     = 7;

    const ANSWER_DOMAIN_UNAVAILABLE = -1;
    const ANSWER_DOMAIN_AVAILABLE = 1;

    const ANSWER_CONTACT_CREATE_FAIL = -2;
    const ANSWER_CONTACT_CREATE_SUCCESS = 2;

    const ANSWER_CONTACT_CREATE_ERROR_POSTAL_ADDRESS = -21;
    const ANSWER_CONTACT_CREATE_ERROR_FIO = -22;
    const ANSWER_CONTACT_CREATE_ERROR_PASSPORT = -23;
    const ANSWER_CONTACT_CREATE_ERROR_BDATE = -24;


    const ANSWER_DOMAIN_REG_FAIL = -3;
    const ANSWER_DOMAIN_REG_SUCCESS = 3;
    const ANSWER_DOMAIN_REG_SUCCESS_PENDING = 33;

    const ANSWER_CONTACT_EXIST = 4;
    const ANSWER_CONTACT_NOT_EXIST = -4;


    const ANSWER_DOMAIN_PROLONG_SUCCESS = 5;
    const ANSWER_DOMAIN_PROLONG_FAIL = -5;

    const ANSWER_DOMAIN_CHANGE_NS_FAIL = -6;
    const ANSWER_DOMAIN_CHANGE_NS_SUCCESS = 6;


    const ANSWER_POOL_ACTION_SUCCESS = 1001;

    public static function getRegistrar($registrant_id)
    {
        $Registrar = new DomainRegistrar($registrant_id);

        switch ($Registrar->type) {
            case self::REGISTRANT_R01:
                return new R1API($Registrar);
                break;
            case self::REGISTRANT_NIC_RU:
                return new NICRUAPI($Registrar);
                break;
            case self::REGISTRANT_REG_RU:
                return new REGRUAPI($Registrar);
                break;

            case  self::REGISTRANT_2DOMAINS:
                return new R2domainsAPI($Registrar);
                break;
            case  self::REGISTRANT_DRSUA:
                return new DRSAPI($Registrar);
                break;
        }

        $data = array('Registrar' => $Registrar, 'Class' => null);

        Module::extendMethod('getRegistrar', $data);
        if($data['Class']){
            return $data['Class'];
        }

        throw new Exception('Registrar doesnt exist');
    }

    public function checkDomainAvailable($domain){
        $check = new Whois($domain);

        if ($check->isAvailable()) {
            return self::ANSWER_DOMAIN_AVAILABLE;
        } else {
            return self::ANSWER_DOMAIN_UNAVAILABLE;
        }

    }

    public function checkDomainsAvailable($domains){


        $result = [];

        foreach ($domains as $domain) {
            $result[$domain] = $this->checkDomainAvailable($domain);
        }

        return $result;
    }
}