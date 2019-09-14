<?php

namespace api;

use ApiController;
use domain\DomainAPI;
use model\Domain;
use model\DomainOrder;
use System\Exception;
use System\Tools;

class DomainController extends ApiController {

    public function createProfile()
    {

    }

    public function registerDomain()
    {

    }

    public function actionGetDomains(){
        $domainObject = new Domain();
        $from = 0;
        $count = 10;

        if(isset($this->data->count)){
            $count = $this->data->count;
        }
        if(isset($this->data->from)){
            $from = $this->data->from;
        }

        $domainObject->limit($from, $count);

        $this->returnAnswer(1, $domainObject->getRows());

    }
    public function actionCheckDomain(){

        if(isset($this->data->domain)) {
            $domains = explode(',', $this->data->domain);
        } else if(isset($this->data->domains) && is_array($this->data->domains)){
            $domains = $this->data->domains;
    } else {
            $this->returnAnswer(0, ['message' => 'Required data have been missed']);
        }
        $domains = str_replace('http://', '', $domains);
        $domains = str_replace('https://', '', $domains);

        foreach ($domains as $domain) {
            $Domain = new Domain();
            if (!strpos($domain, '.')) {
                foreach ($Domain->getRows() as $d) {
                    $domains[] = $domain . '.' . $d->name;
                }

            }
        }
        $domains_by_registrar = array();
        foreach ($domains as $domain) {
            $domain = trim($domain);



            $res = array();
            //$domain = mb_strtolower($domain);

            preg_match('/\.(.*)/', $domain, $res);

            if(!isset($res[1])){
                continue;
            }

            $domain_zone = $res[1];


            $domain_registrant = $Domain->where('name', $domain_zone)->getRow();
            if(!isset($domain_registrant->registrant_id)){
                $domains_by_registrar[0][] = $domain;
                continue;
            }
            $domains_by_registrar[$domain_registrant->registrant_id][] = $domain;
        }

        foreach ($domains_by_registrar as $domain_registrant_id => $domains){


            if($domain_registrant_id){
                try {
                    $rapi = DomainAPI::getRegistrar($domain_registrant_id);
                } catch (Exception $e) {

                }

                try {

                    $check_domains = $rapi->checkDomainsAvailable($domains) ;

                    //print_r($check_domains);
                    foreach ($check_domains as $domain => $check_domain){

                        $domain = trim($domain);
                        $res = array();

                        preg_match('/\.(.*)/', $domain, $res);

                        if(!isset($res[1])){
                            continue;
                        }

                        $domain_zone = $res[1];

                        $Domain = new Domain();
                        $domain_registrant = $Domain->where('name', $domain_zone)->getRow();

                        if ($check_domain == DomainAPI::ANSWER_DOMAIN_AVAILABLE) {
                            if (!DomainOrder::factory()->where('domain', $domain)->where('status', '!=', -1)->getRow()) {
                                $domains_res['available'][$domain] = $domain_registrant;
                            } else {
                                $domains_res['orders'][$domain] = $domain_registrant;
                            }
                        } else {
                            if (!DomainOrder::factory()->where('domain', $domain)->getRow()) {
                                $domains_res['no_available'][$domain] = $domain_registrant;
                            } else {
                                $domains_res['orders'][$domain] = $domain_registrant;
                            }

                        }
                    }
                } catch(Exception $e){

                }
            } else{
                foreach ($domains as $domain) {
                    $domains_res['no_available'][$domain] = 0;
                }
            }
        }

        $this->returnAnswer(1, $domains_res);
    }
}