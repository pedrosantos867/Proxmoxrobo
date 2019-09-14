<?php

namespace System;


use email\Email;
use model\Bill;
use model\Client;
use model\DomainOrder;
use model\HostingAccount;
use model\HostingPlan;
use model\HostingServer;
use model\Service;
use model\ServiceOrder;
use model\Ticket;
use sms\SMS;
use System\View\View;

class Notifier
{
    public static function NewBill(Client $clientObject, Bill $billObject)
    {
        $billObject = new Bill($billObject->id);

        if ($clientObject->isNotifyEnabled('new_bill')) {
            $email          = new Email();
            $email->to = $clientObject->email;
            $vemail = $email->getView('front/bill/new.php', $clientObject->getDefaultLang());
            $vemail->bill   = $billObject;
            $vemail->client = $clientObject;
            $vemail->site_name = Config::factory()->sitename;
            $vemail->site_email = Config::factory()->site_email;
            $email->msg     = $vemail->fetch();

            $data = array('bill' => $billObject, 'email' => &$email);
            Module::extendMethod('newBill', $data);

            $email->send();
        }



        if ($clientObject->isNotifyEnabled('sms_new_bill')) {
            $view = new View('sms/front/bill/new.php');
            SMS::getGateway()->sendSMS($clientObject->phone, $view->fetch());
        }

    }

    public static function SuspendHostingOrder(Client $clientObject, HostingAccount $Account)
    {

        if ($clientObject->isNotifyEnabled('suspend_hosting_order')) {
            $email = new Email();
            $email->to = $clientObject->email;
            $vemail = $email->getView('front/hosting_account/suspend.php', $clientObject->getDefaultLang());
            $vemail->account = $Account;
            $vemail->client = $clientObject;
            $vemail->site_name = Config::factory()->sitename;
            $vemail->site_email = Config::factory()->site_email;
            $email->msg = $vemail->fetch();
            $email->send();
        }


        if ($clientObject->isNotifyEnabled('sms_suspend_hosting_order')) {
            $view = new View('sms/front/hosting/order/suspend.php');
            SMS::getGateway()->sendSMS($clientObject->phone, $view->fetch());
        }

    }

    public static function UnSuspendHostingOrder(Client $clientObject, HostingAccount $Account)
    {

        if ($clientObject->isNotifyEnabled('unsuspend_hosting_order')) {
            $email = new Email();
            $email->to = $clientObject->email;
            $vemail = $email->getView('front/hosting_account/unsuspend.php', $clientObject->getDefaultLang());
            $vemail->account = $Account;
            $vemail->client = $clientObject;
            $email->msg = $vemail->fetch();
            $vemail->site_name = Config::factory()->sitename;
            $vemail->site_email = Config::factory()->site_email;
            $email->send();
        }

        if ($clientObject->isNotifyEnabled('sms_unsuspend_hosting_order')) {
            $view = new View('sms/front/hosting/order/unsuspend.php');
            SMS::getGateway()->sendSMS($clientObject->phone, $view->fetch());
        }


    }

    public static function NewTicket(Client $clientObject, Ticket $Ticket)
    {
        if ($clientObject->isNotifyEnabled('new_ticket')) {
            $Email          = new Email();
            $Email->to = $clientObject->email;
            $eview = $Email->getView('front/ticket/new.php', $clientObject->getDefaultLang());
            $eview->ticket  = $Ticket;
            $eview->site_name = Config::factory()->sitename;
            $eview->site_email = Config::factory()->site_email;
            $eview->client = $clientObject;
            $Email->msg     = $eview->fetch();
            $Email->send();
        }




        $config = new Config();

        if ($clientObject->isNotifyEnabled('sms_new_ticket')) {
            $view = new View('sms/front/ticket/new.php');
            SMS::getGateway()->sendSMS($clientObject->phone, $view->fetch());
        }

        if(isset($config->email_notifications['new_ticket'])){
            $Email          = new Email();
            $Email->to      = $config->site_email;
            $eview = $Email->getView('admin/ticket/new.php', $clientObject->getDefaultLang());
            $eview->ticket  = $Ticket;
            $eview->site_name = Config::factory()->sitename;
            $eview->site_email = Config::factory()->site_email;
            $eview->client = $clientObject;
            $Email->msg     = $eview->fetch();
            $Email->send();
        }
        if(isset($config->sms_notifications['new_ticket'])){

            $view = new View('sms/admin/ticket/new.php');
            SMS::getGateway()->sendSMS($config->site_sms, $view->fetch());
        }

    }

    public static function TicketAnswer(Client $clientObject, Ticket $Ticket)
    {
        if ($clientObject->isNotifyEnabled('ticket_answer')) {

            $Email          = new Email();
            $Email->to = $clientObject->email;

            $eview = $Email->getView('front/ticket/new_answer.php', $clientObject->getDefaultLang());
            $eview->ticket  = $Ticket;

            $eview->client = $clientObject;
            $eview->site_name = Config::factory()->sitename;
            $eview->site_email = Config::factory()->site_email;
            $Email->msg     = $eview->fetch();
            $Email->send();
        }

        $config = new Config();
        if(isset($config->email_notifications['ticket_answer'])){
            $Email          = new Email();
            $Email->to      = $config->site_email;
            $eview = $Email->getView('admin/ticket/new_answer.php', $clientObject->getDefaultLang());
            $eview->ticket  = $Ticket;
            $eview->client = $clientObject;
            $eview->site_name = Config::factory()->sitename;
            $eview->site_email = Config::factory()->site_email;
            $Email->msg     = $eview->fetch();
            $Email->send();
        }

        if(isset($config->sms_notifications['ticket_answer'])){
            $view = new View('sms/admin/ticket/answer.php');
            SMS::getGateway()->sendSMS($config->site_sms, $view->fetch());
        }


        if ($clientObject->isNotifyEnabled('sms_ticket_answer')) {
            $view = new View('sms/front/ticket/answer.php');
            SMS::getGateway()->sendSMS($clientObject->phone, $view->fetch());
        }


    }

    public static function EndHostingOrder(Client $clientObject, HostingAccount $Account, $days)
    {
        if ($clientObject->isNotifyEnabled('end_hosting_order')) {
            $email          = new Email();
            $email->to = $clientObject->email;

            $vemail = $email->getView('front/hosting_account/end.php', $clientObject->getDefaultLang());
            $vemail->client = $clientObject;
            $vemail->account = $Account;
            $vemail->days   = $days;
            $vemail->site_name = Config::factory()->sitename;
            $vemail->site_email = Config::factory()->site_email;

            $email->msg     = $vemail->fetch();
            $email->send();
        }

        if ($clientObject->isNotifyEnabled('sms_end_hosting_order')) {
            $view = new View('sms/front/hosting/order/end.php');
            SMS::getGateway()->sendSMS($clientObject->phone, $view->fetch());
        }
    }

    public static function NewMessageToServiceOrder(Client $clientObject, ServiceOrder $ServiceOrder, Service $Service)
    {
        if ($clientObject->isNotifyEnabled('info_service_order')) {
            $email = new Email();
            $email->to = $clientObject->email;

            $vemail = $email->getView('front/service/order/new_message.php', $clientObject->getDefaultLang());
            $vemail->client = $clientObject;
            $vemail->order = $ServiceOrder;
            $vemail->site_name = Config::factory()->sitename;
            $vemail->site_email = Config::factory()->site_email;

            $email->msg = $vemail->fetch();
            $email->send();
        }

        if ($clientObject->isNotifyEnabled('sms_info_service_order')) {
            $view = new View('sms/front/service/order/new_message.php');
            $view->order_id = $ServiceOrder->id;
            $view->order_name = $Service->name;
            SMS::getGateway()->sendSMS($clientObject->phone, $view->fetch());
        }
    }

    public static function ChangeMessageToServiceOrder(Client $clientObject, ServiceOrder $ServiceOrder, Service $Service)
    {
        if ($clientObject->isNotifyEnabled('info_service_order')) {
            $email = new Email();
            $email->to = $clientObject->email;

            $vemail = $email->getView('front/service/order/change_message.php', $clientObject->getDefaultLang());
            $vemail->client = $clientObject;
            $vemail->order = $ServiceOrder;
            $vemail->site_name = Config::factory()->sitename;
            $vemail->site_email = Config::factory()->site_email;

            $email->msg = $vemail->fetch();
            $email->send();
        }

        if ($clientObject->isNotifyEnabled('sms_info_service_order')) {
            $view = new View('sms/front/service/order/change_message.php');
            $view->order_id = $ServiceOrder->id;
            $view->order_name = $Service->name;
            SMS::getGateway()->sendSMS($clientObject->phone, $view->fetch());
        }
    }

    public static function NewRegistration(Client $clientObject, $pass)
    {
        $Email      = new Email();
        $eview = $Email->getView('front/client/reg.php', $clientObject->getDefaultLang());
        $eview->client = $clientObject;
        $eview->password = $pass;

        $eview->site_name = Config::factory()->sitename;
        $eview->site_email = Config::factory()->site_email;

        $eview->company = Config::factory()->sitename;
        $eview->setDataToHelper('force_lang', $clientObject->getDefaultLang());


        $Email->msg      = $eview->fetch();
        $Email->to = $clientObject->email;
        $Email->send();
    }

    public static function PaidBill(Client $clientObject, Bill $Bill)
    {
        $Email          = new Email();
        $Email->to = $clientObject->email;
        $eview = $Email->getView('front/bill/pay.php', $clientObject->getDefaultLang());
        $eview->bill    = $Bill;
        $eview->client = $clientObject;
        $eview->site_name = Config::factory()->sitename;
        $eview->site_email = Config::factory()->site_email;
        $Email->msg     = $eview->fetch();

        $data = array('email' => $Email, 'billObject' => $Bill);
        \System\Module::extendMethod('paidBill', $data);

        $Email->send();
    }

    public static function PaidBills(Client $clientObject, Bill $Bill)
    {
        $Email = new Email();
        $Email->to = $clientObject->email;
        $eview = $Email->getView('front/bill/multi_pay.php', $clientObject->getDefaultLang());
        $eview->bill = $Bill;
        $eview->client = $clientObject;
        $eview->site_name = Config::factory()->sitename;
        $eview->site_email = Config::factory()->site_email;
        $Email->msg = $eview->fetch();
        $Email->send();
    }

    public static function NewHostingOrder(Client $clientObject, HostingAccount $Order, HostingServer $Server = null, HostingPlan $Plan = null)
    {

        if (!$Server) {
            $Server = new HostingServer($Order->server_id);
        }

        if (!$Plan) {
            $Plan = new HostingPlan($Order->plan_id);
        }

        //Send email notify to client
        $email = new Email();
        $email->to = $clientObject->email;
        $vemail = $email->getView('front/hosting_account/new.php', $clientObject->getDefaultLang());
        $vemail->link = $Server->ip ? $Server->ip : $Server->host;
        $vemail->login = $Order->login;
        $vemail->pass = Tools::rPOST('pass');

        /*Deprecated*/
        $vemail->client_name = $clientObject->name;

        $vemail->client = $clientObject;
        $vemail->domain = Tools::rPOST('domain');
        $vemail->plan = $Plan->name;
        $vemail->site_name = Config::factory()->sitename;
        $vemail->site_email = Config::factory()->site_email;
        $email->msg = $vemail->fetch();
        $email->send();

        $config = new Config();

        //Send email notify to server admin
        if (isset($config->email_notifications['new_order'])) {
            $email = new Email();
            $email->to = $config->site_email;
            $vemail = $email->getView('admin/hosting/order/new.php');
            $vemail->order = $Order;
            $vemail->server = $Server;
            $vemail->client = $clientObject;
            $email->msg = $vemail->fetch();
            $email->send();
        }

        //Send sms notify to server admin
        if (isset($config->sms_notifications['new_order'])) {
            $view = new View('sms/admin/hosting/order/new.php');
            SMS::getGateway()->sendSMS($config->site_sms, $view->fetch());
        }
    }

    public static function RemindPassword(Client $clientObject, $code)
    {
        $email = new Email();
        $email->to = $clientObject->email;

        $eview = $email->getView('front/client/reminder.php', $clientObject->getDefaultLang());
        $eview->code = $code->code;
        $eview->client = $clientObject;
        $eview->site_name = Config::factory()->sitename;
        $eview->site_email = Config::factory()->site_email;
        $email->msg = $eview->fetch();

        $email->send();
    }

    public static function EndServiceOrder(Client $clientObject, Service $serviceObject, $days)
    {
        $email = new Email();
        $email->to = $clientObject->email;
        $vemail = $email->getView('front/service/end.php', $clientObject->getDefaultLang());
        $vemail->client = $clientObject;
        $vemail->service = $serviceObject;
        $vemail->days = $days;
        $vemail->site_name = Config::factory()->sitename;
        $vemail->site_email = Config::factory()->site_email;
        $email->msg = $vemail->fetch();
        $email->send();
    }

    public static function TicketChangePriority(Ticket $ticketObject)
    {
        $client = new Client($ticketObject->client_id);
        $Email = new Email();
        $Email->to = $client->email;
        $eview = $Email->getView('front/ticket/change_priority.php', $client->getDefaultLang());
        $eview->ticket = $ticketObject;
        $eview->client = $client;
        $eview->site_name = Config::factory()->sitename;
        $eview->site_email = Config::factory()->site_email;
        $Email->msg = $eview->fetch();
        $Email->send();
    }

    public static function TicketChangeStatus($ticket)
    {
        $client = new Client($ticket->client_id);
        $Email = new Email();
        $Email->to = $client->email;
        $eview = $Email->getView('front/ticket/change_status.php', $client->getDefaultLang());
        $eview->ticket = $ticket;
        $eview->client = $client;
        $eview->site_name = Config::factory()->sitename;
        $eview->site_email = Config::factory()->site_email;

        $Email->msg = $eview->fetch();
        $Email->send();
    }

    public static function RemindPasswordNew(Client $clientObject, $password)
    {
        $Email = new Email();
        $Email->to = $clientObject->email;
        $eview = $Email->getView('front/client/reminder_password.php', $clientObject->getDefaultLang());
        $eview->client     = $clientObject;
        $eview->password   = $password;

        $eview->site_name = Config::factory()->sitename;
        $eview->site_email = Config::factory()->site_email;

        $Email->msg         = $eview->fetch();
        $Email->send();
    }

    public static function AdminRemindPasswordNew($employeeObject, $password)
    {
        $email = new Email();
        $email->to = $employeeObject->email;
        $eview = $email->getView('admin/employee/reminder_password.php');
        $eview->password = $password;
        $eview->site_name = Config::factory()->sitename;
        $eview->site_email = Config::factory()->site_email;

        $email->msg = $eview->fetch();
        $email->send();
    }

    public static function AdminRemindPassword($employeeObject, $code)
    {
        $email = new Email();
        $email->to = $employeeObject->email;
        $eview = $email->getView('admin/employee/reminder.php');
        $eview->code = $code;

        $eview->site_name = Config::factory()->sitename;
        $eview->site_email = Config::factory()->site_email;
        $email->msg = $eview->fetch();
        $email->send();
    }

    public static function NewDomainOrder(DomainOrder $domainOrderObject)
    {
        $config = Config::factory();

        //Send email notify about new order to server admin
        if (isset($config->email_notifications['new_order'])) {

            $email = new Email();
            $email->to = $config->site_email;
            $vemail = $email->getView('admin/domain/order/new.php');
            $vemail->client = $config->client;
            $vemail->site_name = Config::factory()->sitename;
            $vemail->site_email = Config::factory()->site_email;
            $email->msg = $vemail->fetch();
            $email->send();

        }

        //Send sms notify to server admin
        if (isset($config->sms_notifications['new_order'])) {
            $view = new View('sms/admin/domain/order/new.php');
            SMS::getGateway()->sendSMS($config->site_sms, $view->fetch());
        }
    }

    public static function NewServiceOrder(Client $clientObject, ServiceOrder $serviceOrderObject, Service $serviceObject = null)
    {
        if (!$serviceObject) {
            $serviceObject = new Service($serviceOrderObject->service_id);
        }

        $config = Config::factory();

        //Send email notify about new order to server admin
        if (isset($config->email_notifications['new_order'])) {
            $email = new Email();
            $email->to = $config->site_email;
            $vemail = $email->getView('admin/service/order/new.php');
            $vemail->Service = $serviceObject;
            $vemail->ServiceOrder = $serviceOrderObject;
            $vemail->client = $clientObject;
            $vemail->site_name = Config::factory()->sitename;
            $vemail->site_email = Config::factory()->site_email;
            $email->msg = $vemail->fetch();
            $email->send();
        }

        //Send sms notify to server admin
        if (isset($config->sms_notifications['new_order'])) {
            $view = new View('sms/admin/service/order/new.php');
            $view->Service = $serviceObject;
            SMS::getGateway()->sendSMS($config->site_sms, $view->fetch());
        }
    }
}