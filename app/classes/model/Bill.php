<?php

namespace model;

use domain\DomainAPI;
use hosting\HostingAPI;
use System\Cookie;

use System\Tools;
use vps\VPSAPI;

class Bill extends \System\ObjectModel
{
    protected static $table = 'bills';

    const TYPE_ORDER            = 0;
    const TYPE_CHANGE_PLAN      = 1;

    const TYPE_BALANCE          = 2;

    const TYPE_INC              = 3;

    const TYPE_DOMAIN_ORDER     = 4;
    const TYPE_DOMAIN_PROLONG   = 5;

    const TYPE_SERVICE_ORDER    = 6;


    const TYPE_VPS              = 7;


    public function pay()
    {

        $bill  = $this;
        $bills = array();

        if ($bill->type == Bill::TYPE_INC) {
            $bs = explode('|', $bill->inc);
            foreach ($bs as $bl) {
                $bills[] = new Bill($bl);
            }
        } else {
            $bills[] = $bill;
        }

        foreach ($bills as $bill) {
            if($bill->is_paid == 1){
                continue;
            }
            //  $bill = new Bill($bill);
            $bill->is_paid = 1;
            $bill->save();

            if ($bill->type == self::TYPE_ORDER) {
                $ha = new HostingAccount($bill->hosting_account_id);
                if ($ha->paid_to == '0000-00-00' || $ha->paid_to < date('Y-m-d')) {
                    $d = date('Y-m-d');
                } else {
                    $d = $ha->paid_to;
                }

                $dt = new \DateTime(($d));
                $dt->add(new \DateInterval('P' . $bill->pay_period . 'M'));
                $ha->paid_to = $dt->format('Y-m-d');
                $ha->active  = 1;
                $ha->save();

                $client = new Client($ha->client_id);
                $client->rev += $bill->total;
                $client->ref_rev += $bill->total;
                $client->save();


                HostingAPI::selectServer(new HostingServer($ha->server_id))->unsuspendUser($ha->login);


            } elseif ($bill->type == self::TYPE_CHANGE_PLAN) {
                $ha                  = new HostingAccount($bill->hosting_account_id);
                $ha->plan_id = $bill->hosting_plan_id2;
                if ($ha->save()) {
                    $plan = new HostingPlan($ha->plan_id);
                    HostingAPI::selectServer(new HostingServer($ha->server_id))->changePlan($ha->login, $plan->panel_name);
                }
            } elseif ($bill->type == self::TYPE_BALANCE) {
                $client = new Client($bill->client_id);
                if ($client->isLoadedObject()) {
                    $client->balance += $bill->total;
                    if ($client->save()) {

                    } else {

                    }
                }
            } elseif ($bill->type == self::TYPE_DOMAIN_ORDER) {



                $DomainOrder = new DomainOrder($bill->domain_order_id);
                $DomainOwner = new DomainOwner($DomainOrder->owner_id);

                $DomainOrder->period    = $bill->pay_period;
                $DomainOrder->auth_code = Tools::generateCode(4).'H_1b'.Tools::generateCode(4);

                $client = new Client($DomainOrder->client_id);
                $client->rev += $bill->total;
                $client->ref_rev += $bill->total;
                $client->save();


                $r = DomainAPI::getRegistrar($DomainOrder->registrant_id)
                    ->registerDomain(
                        $DomainOrder,
                        $DomainOwner
                    );

                if($r == DomainAPI::ANSWER_DOMAIN_REG_SUCCESS) {
                    $DomainOrder->status = 1;
                } else if ($r == DomainAPI::ANSWER_DOMAIN_REG_SUCCESS_PENDING) {
                    $DomainOrder->status = 3;
                } else {
                    $DomainOrder->status = 2;
                }
                $DomainOrder->date_end = date('Y-m-d', (time() + ($bill->pay_period * Cookie::ONE_YEAR)));
                $DomainOrder->save();


            } elseif ($bill->type == self::TYPE_DOMAIN_PROLONG) {
                $DomainOrder = new DomainOrder($bill->domain_order_id);
                $DomainOwner = new DomainOwner($DomainOrder->owner_id);
                $DomainOrder->period = $bill->pay_period;

                $client = new Client($DomainOrder->client_id);
                $client->rev += $bill->total;
                $client->ref_rev += $bill->total;
                $client->save();

                $res = DomainAPI::getRegistrar($DomainOrder->registrant_id)->prolongDomain($DomainOrder, $DomainOwner);

                if($res == DomainAPI::ANSWER_DOMAIN_PROLONG_SUCCESS){
                    $DomainOrder->date_end = date('Y-m-d', (strtotime($DomainOrder->date_end) + ((Cookie::ONE_YEAR) * $DomainOrder->period)));
                    $DomainOrder->save();
                }
            } elseif ($bill->type == self::TYPE_SERVICE_ORDER) {
                $ServiceOrder = new ServiceOrder($bill->service_order_id);

                $client = new Client($ServiceOrder->client_id);
                $client->rev += $bill->total;
                $client->ref_rev += $bill->total;
                $client->save();

                if($ServiceOrder->isLoadedObject()){
                    $ServiceOrder->status = 1;

                    $ServiceOrder->paid_to = date('Y-m-d', (strtotime($ServiceOrder->paid_to) + ($this->pay_period * Cookie::ONE_MONAT)));
                    $ServiceOrder->save();
                    $ServiceOrder->sendEvent('prolong');
                }
            } elseif ($bill->type == self::TYPE_VPS) {
                $VpsOrder = new VpsOrder($bill->hosting_account_id);



                $client             = new Client($VpsOrder->client_id);
                $client->rev        += $bill->total;
                $client->ref_rev    += $bill->total;
                $client->save();

                if($VpsOrder->isLoadedObject()){

                    if ($VpsOrder->paid_to == '0000-00-00' || $VpsOrder->paid_to < date('Y-m-d')) {
                        $d = date('Y-m-d');
                    } else {
                        $d = $VpsOrder->paid_to;
                    }

                    $dt = new \DateTime(($d));
                    $dt->add(new \DateInterval('P' . $bill->pay_period . 'M'));
                    $VpsOrder->paid_to = $dt->format('Y-m-d');
                    $VpsOrder->active = 1;
                    $VpsOrder->save();

                    VPSAPI::selectServer(new VpsServer($VpsOrder->server_id))->unsuspendVM($VpsOrder->node, $VpsOrder->vmid, $VpsOrder->username, $VpsOrder->type);
                }
            }

            \System\Module::extendMethod('payBill', $bill);
        }

        return true;
    }


}