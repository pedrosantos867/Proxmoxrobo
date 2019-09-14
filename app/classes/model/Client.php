<?php


namespace model;


use email\Email;
use System\Config;
use System\Crypt;
use System\ObjectModel;
use System\Tools;
use System\Validation;
use System\View\View;

class Client extends ObjectModel
{
    protected static $table = 'clients';

    public function validationFields()
    {


        if($this->where('username', $this->username)->getRow()){
            $this->validateErrors[] = 'username';
        }

        if (!Validation::isFullName($this->name)) {
            $this->validateErrors[] = 'name';
        }
        if (!Validation::isUserName($this->username)) {
            $this->validateErrors[] = 'username';
        }
        if (!Validation::isPasswd($this->password)) {
            $this->validateErrors[] = 'password';
        }

        if (empty($errors)) {
            return true;
        }

        return false;

    }

    public function save($id_lang = 0)
    {
        if(!$this->id) {
            $this->notifications = json_encode(Config::factory()->client_notifications);
        }

        return parent::save($id_lang);

    }

    public function getDocs()
    {
        if (file_exists(_BASE_DIR_STORAGE_ . 'docs/' . $this->id . '/')) {
            $all = scandir(_BASE_DIR_STORAGE_ . 'docs/' . $this->id . '/');
            unset($all[0]);
            unset($all[1]);
            return $all;
        }

        return [];
    }

    public function getDefaultLang(){
        $config = Config::factory();

     return $config->enable_lang_switcher_for_client ? $this->default_lang : $config->front_default_lang;

    }

    public function isNotifyEnabled($notify){

        $config = new Config();
           $notifications = json_decode($this->notifications, true);

        if(!$config->enable_client_sms_notification_control && strpos($notify, 'sms_') === 0){

            if(isset($config->client_notifications[$notify]) && $config->client_notifications[$notify]){
                return true;
            }

            return false;
        }

        if(!$config->enable_client_email_notification_control && strpos($notify, 'sms_') !== 0){

            if(isset($config->client_notifications[$notify]) && $config->client_notifications[$notify]){
                return true;
            }

            return false;
        }

        if(isset($notifications[$notify]) && $notifications[$notify]){
           return true;
        }

        return false;
    }
    public function login()
    {
        $client = $this;
        $hash    = Tools::passCrypt(uniqid());
        $user_id = $client->id;
        $browser = Tools::getBrowser();
        $us             = new ClientSession();
        $us->hash       = $hash;
        $us->client_id  = $user_id;
        $us->ip = 'SOCIAL LOGIN';
        $us->browser = $browser['name'];
        $us->os = Tools::getOS();


        $us->save();

        $crypt = new Crypt();
        setcookie('user', $crypt->encrypt($user_id), time() + 3600 * 35 * 36 * 36, '/');
        setcookie('hash', ($hash), time() + 3600 * 35 * 36 * 36, '/');
        return true;
    }
    public function remove()
    {

        $HostingAccount = new HostingAccount();
        $HostingAccount->where('client_id', $this->id)->removeRows();

        $Bill = new Bill();
        $Bill->where('client_id', $this->id)->removeRows();

        $Ticket = new Ticket();
        $Ticket->where('client_id', $this->id)->removeRows();

        $TicketAnswer = new TicketAnswer();
        $TicketAnswer->where('client_id', $this->id)->removeRows();

        $DomainOwner = new DomainOwner();
        $DomainOwner->where('client_id', $this->id)->removeRows();

        $DomainOrder = new DomainOrder();
        $DomainOrder->where('client_id', $this->id)->removeRows();

        $ClientSession = new ClientSession();
        $ClientSession->where('client_id', $this->id)->removeRows();

        $ServiceOrder = new ServiceOrder();
        $ServiceOrder->where('client_id', $this->id)->removeRows();

        $ClientSocialAccount = new ClientSocialAccount();
        $ClientSocialAccount->where('client_id', $this->id)->removeRows();
        
        return parent::remove();
    }
} 