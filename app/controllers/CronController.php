<?php

ini_set('max_execution_time', 0);


use domain\DomainAPI;
use hosting\HostingAPI;
use model\Client;
use model\DomainOrder;
use model\DomainRegistrar;
use model\HostingAccount;
use model\Bill;
use model\HostingServer;
use model\ServiceOrder;
use model\VpsOrder;
use model\VpsServer;
use System\Config;
use System\Module;
use System\Notifier;
use vps\VPSAPI;

class CronController
{
    public function run($without_key = false)
    {
        $key = \System\Router::getParam('key') ? \System\Router::getParam('key') : \System\Tools::rGET('key');
        $config = Config::factory();

        if (!$without_key && $key != $config->uniq_key) {
            echo 'Please use key for running cron script.';
            \System\Logger::log('Error! Running cron without key.');
            exit();
        }

        if (\System\Tools::rGET('hourly')) {
            \System\Logger::log('Running hourly cron: checkPollRequests');

            $this->checkPollRequests();

            Module::extendMethod('hourlyCronUpdate');
        } else {
            \System\Logger::log('Running daily cron: checkAccounts');
            $this->checkAccounts();
            \System\Logger::log('Running daily cron: checkVpsAccounts');
            $this->checkVpsAccounts();
            \System\Logger::log('Running daily cron: checkCustomServices');
            $this->checkCustomServices();
            \System\Logger::log('Running daily cron: disableOldBills');
            $this->disableOldBills();
            \System\Logger::log('Running daily cron: createBills');
            $this->createBills();
            \System\Logger::log('Running daily cron: refreshCurrencies');
            $this->refreshCurrencies();
            \System\Logger::log('Running daily cron: refreshCurrencies');
            $this->removeOldDomainOrders();
            \System\Logger::log('Running daily cron: removeOldDomainOrders');

            Module::extendMethod('dailyCronUpdate');
        }
    }

    public function removeOldDomainOrders(){
        $DomainOrder = new DomainOrder();
        $DomainOrder->where('status', 0)->where('create_date', '<', date('Y-m-d', time()-259200))->removeRows();
    }

    public function createBills()
    {


    }

    public function refreshCurrencies()
    {

        $config = new Config;

        if ($config->currency_refrash) {
            \model\Currency::updateCurses();
        }

    }

    public function disableOldBills()
    {
        $hb = new \model\Bill();
        // echo date('Y-m-d H:i:s');
        $now = time();
        foreach ($hb->getRows() as $row) {
            $bill = new \model\Bill($row);

            $date = strtotime($bill->date);

            if ($bill->is_paid == 0 && $now - $date > 86400 * 2) {
                $bill->is_paid = -1;
                $bill->save();
            }

        }
    }

    public function checkVpsAccounts(){
        $VpsOrder = new VpsOrder();
        $vps_orders = $VpsOrder->getRows();

        foreach ($vps_orders as $vps_order) {
            $VpsOrder = new VpsOrder($vps_order);


            $time_now     = time();
            $now          = date('Y-m-d');
            $account_paid = $VpsOrder->paid_to;
            $account_paid = strtotime($account_paid);


            $client = new Client($VpsOrder->client_id);

            if ($VpsOrder->active && ($account_paid - $time_now < 86400 * 3) && ($account_paid - $time_now) >= 0) {

                $time = $account_paid - $time_now;
                $days = ceil($time / (60 * 60 * 24));




                $bill = new \model\Bill();
                $res  = $bill
                    ->where('type', Bill::TYPE_ORDER)
                    ->where('hosting_account_id', $VpsOrder->id)
                    ->where('client_id', $VpsOrder->client_id)
                    ->where('is_paid', 0)->getRow();

                if (!($res)) {


                    $bill       = new \model\Bill();

                    $plan = new \model\VpsPlan($VpsOrder->plan_id);
                    $bill->hosting_account_id = $VpsOrder->id;
                    $bill->is_paid            = 0;
                    $bill->client_id          = $VpsOrder->client_id;
                    $bill->type               = Bill::TYPE_VPS;
                    $bill->pay_period         = 1;
                    $bill->hosting_plan_id    = $plan->id;
                    $bill->price              = $plan->price;
                    $bill->total              = $plan->price;
                    $bill->date               = date('Y-m-d');

                    if ($bill->save()) {
                        Notifier::NewBill($client, $bill);
                    }

                }
            }
//echo time().' -- '.$account_paid. '<br>';

            if (time() >= $account_paid && $VpsOrder->active == 1) {
                $VpsOrder->active = 0;
                VPSAPI::selectServer(new VpsServer($VpsOrder->server_id))->suspendVM($VpsOrder->node,$VpsOrder->vmid, $VpsOrder->username, $VpsOrder->type);
                $VpsOrder->save();

            } else if ($VpsOrder->active == 0 && time() < $account_paid) {
                $VpsOrder->active = 1;
                VPSAPI::selectServer(new VpsServer($VpsOrder->server_id))->unsuspendVM($VpsOrder->node,$VpsOrder->vmid, $VpsOrder->username, $VpsOrder->type);
                $VpsOrder->save();


            }
        }
    }


    /**
     *
     */
    public function checkAccounts()
    {
        $ha       = new HostingAccount();
        $accounts = $ha->getRows();

        foreach ($accounts as $account) {
            $account      = new HostingAccount($account);
            $now          = date('Y-m-d');
            $account_paid = $account->paid_to;
            $client = new Client($account->client_id);
            //$client_notifications = json_decode(ClientNotify::factory()->where('client_id', $client->id)->getRow()->type, true);

            $time_now     = time();
            $account_paid = strtotime($account_paid);

            if ($account->active && ($account_paid - $time_now < 86400 * 3) && ($account_paid - $time_now) >= 0) {

                $time = $account_paid - $time_now;
                $days = ceil($time / (60 * 60 * 24));

                $bill = new \model\Bill();
                $res  = $bill
                    ->where('type', Bill::TYPE_ORDER)
                    ->where('hosting_account_id', $account->id)
                    ->where('client_id', $account->client_id)
                    ->where('is_paid', 0)->getRow();;



                if (!($res)) {

                    $bill       = new \model\Bill();
                    $plan = new \model\HostingPlan($account->plan_id);
                    $bill->hosting_account_id = $account->id;
                    $bill->is_paid            = 0;
                    $bill->client_id          = $account->client_id;
                    $bill->type               = Bill::TYPE_ORDER;
                    $bill->pay_period         = 1;
                    $bill->hosting_plan_id    = $plan->id;
                    $bill->price              = $plan->price;
                    $bill->total              = $plan->price;
                    $bill->date               = date('Y-m-d');

                    if ($bill->save()) {
                        Notifier::NewBill($client, $bill);


                    }

                }

                Notifier::EndHostingOrder($client, $account, $days);

            }
            //continue;


            if (time() >= $account_paid && $account->active == 1) {
                $account->active = 0;

                HostingAPI::selectServer(new HostingServer($account->server_id))->suspendUser($account->login);
                Notifier::SuspendHostingOrder($client, $account);

                $account->save();
            } else if ($account->active == 0 && time() < $account_paid) {
                $account->active = 1;

                HostingAPI::selectServer(new HostingServer($account->server_id))->unsuspendUser($account->login);
                Notifier::UnSuspendHostingOrder($client, $account);

                $account->save();
            }


        }


    }

    private function checkCustomServices()
    {
        $ServiceOrder = new ServiceOrder();
        $orders = $ServiceOrder->getRows();

        foreach ($orders as $order) {

            $order = new ServiceOrder($order);

            $time_now     = time();
            $now          = date('Y-m-d');
            $account_paid = $order->paid_to;
            $account_paid = strtotime($account_paid);


            $client = new Client($order->client_id);

            if ($order->status && ($account_paid - $time_now < 86400 * 3) && ($account_paid - $time_now) >= 0) {

                $time = $account_paid - $time_now;
                $days = ceil($time / (60 * 60 * 24));




                $bill = new \model\Bill();
                $res  = $bill
                    ->where('type', Bill::TYPE_SERVICE_ORDER)
                    ->where('service_order_id', $order->id)
                    ->where('client_id', $order->client_id)
                    ->where('is_paid', 0)->getRow();
                $service = new \model\Service($order->service_id);
                if (!($res)) {


                    $bill       = new \model\Bill();



                    if($service->type != 0){
                        continue;
                    }
                    $bill->service_order_id = $order->id;
                    $bill->is_paid            = 0;
                    $bill->client_id          = $order->client_id;
                    $bill->type               = Bill::TYPE_SERVICE_ORDER;

                    $bill->pay_period         = 1;

                    $bill->price              = $service->price;
                    $bill->total              = $service->price;
                    $bill->date               = date('Y-m-d');

                    if ($bill->save()) {
                        Notifier::NewBill($client, $bill);
                    }

                }



                if (1) {
                    Notifier::EndServiceOrder($client, $service, $days);
                }


            }


            if (time() >= $account_paid && $order->status == 1) {
                $order->status = 0;
                $order->save();
                $order->sendEvent('end');

            } else if ($order->status == 0 && time() < $account_paid) {
                $order->status = 1;
                $order->save();

            }
        }

    }


    private function checkPollRequests()
    {
        $Registrar = new DomainRegistrar();
        $registrars = $Registrar->getRows();

        foreach($registrars as $registrar){
           $RegistrarAPI = DomainAPI::getRegistrar($registrar);
           $RegistrarAPI->reqPool();
        }
    }
}