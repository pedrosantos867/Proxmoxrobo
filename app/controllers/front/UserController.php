<?php

namespace front;

use email\Email;
use model\Client;
use model\ClientSession;
use model\ClientReminderCode;
use model\ClientSocialAccount;
use model\Languages;
use model\SmsConfirmation;
use sms\SMS;
use System\Config;
use System\Cookie;
use System\Crypt;
use System\Notifier;
use System\Router;
use System\Tools;
use System\Validation;
use System\View\View;

class UserController extends FrontController
{
    protected $auth = 0;

    public function init()
    {
        $act = Router::getParam('action');

        if (parent::init() && $this->client && $act != 'logout' && $act != 'socialAuth') {
            Tools::redirect('/');
        }
    }

    public function actionReg()
    {
        $view                        = $this->getView('reg.php');
        $this->carcase->import('content', $view);
        
        $socialAuthConfig = new Config('social-auth');
        $view->socialAuthInfo = $socialAuthConfig;
        
        $view->error = '';
        if (Router::getParam('id_ref')) {
            setcookie('ref', Router::getParam('id_ref'), time() + 600000, '/');
        }
        if (Tools::rPOST()) {
            $recaptcha_verifed = true;
            if ($this->config->enabled_captcha) {
                $recaptcha_verifed = false;
                if ($curl = curl_init()) {
                    curl_setopt($curl, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, "secret=" . $this->config->recaptcha_secret . "&response=" . Tools::rPOST('g-recaptcha-response'));
                    $out = curl_exec($curl);
                    $res = json_decode($out);
                    if ($res->success == 1) {
                        $recaptcha_verifed = true;
                    }
                    curl_close($curl);
                    //   exit();
                }
            }

            $sc = new SmsConfirmation(Tools::rPOST('id_confirmation'));
            $phone_verifed     = true;
            if ($this->config->enabled_sms_confirm) {
                $phone_verifed = false;
                if ($sc->code == Tools::rPOST('code') && $sc->phone == Tools::rPOST('phone')) {
                    $phone_verifed = true;
                }
            }

            $Client = Client::factory()
                ->where('username', Tools::rPOST('username'))
                ->whereOr()
                ->where('email', Tools::rPOST('email'))
                ->whereOr()
                ->where('phone', Tools::rPOST('phone'))
                ->getRow();

            $valid = true;

            if (!Validation::isUserName(Tools::rPOST('username')) ||
                !Validation::isEmail(Tools::rPOST('email')) ||
                !Validation::isFullName(Tools::rPOST('name')) ||
                !Validation::isPhone(Tools::rPOST('phone')))
            {

                $valid = false;
            }

            if (!$Client && $phone_verifed && $recaptcha_verifed && $valid) {
                $user           = new Client();
                $user->username = Tools::rPOST('username');
                $user->ref_id   = (Router::getParam('id_ref') ? Router::getParam('id_ref') : (isset($_COOKIE['ref']) ? $_COOKIE['ref'] : 0));
                $user->email    = Tools::rPOST('email');
                $user->name     = Tools::rPOST('name');
                $user->phone    = Tools::rPOST('phone');
                $user->default_lang = $this->lang->id;

                if (Tools::rPOST('pass') == Tools::rPOST('pass1')) {
                    $user->password = Tools::passCrypt(Tools::rPOST('pass'));
                }
                if ($user->save()) {
                   Notifier::NewRegistration($user, Tools::rPOST('pass'));
                    $this->actionLogin();
                }
            } else {
                Tools::reload();
            }
        }
    }

    public function actionReminder()
    {
        $view = $this->getView('reminder.php');
        $this->carcase->import('content', $view);

        if (Tools::rPOST('username')) {

            $user = new Client();

            if (strpos(Tools::rPOST('username'), '@')) {
                $user = $user->where('email', Tools::rPOST('username'))->getRow();
            } else {
                $user = $user->where('username', Tools::rPOST('username'))->getRow();
            }

            $client = new Client($user);

            if($client->isLoadedObject()) {
                $code            = new ClientReminderCode();
                $code->client_id = $client->id;
                $code->code      = Tools::generateCode(6);
                $code->save();
                Notifier::RemindPassword($client, $code);
                Tools::redirect('login?send_code=1');
            }

        }

        if (Router::getParam('code')) {
            $code    = Router::getParam('code');
            $remcode = new ClientReminderCode();

            $remcode    = new ClientReminderCode($remcode->where('code', $code)->getRow());
            if ($remcode->client_id) {
                $client           = new Client($remcode->client_id);
                $pass             = Tools::generateCode(5);
                $client->password = Tools::passCrypt($pass);
                $client->save();

                Notifier::RemindPasswordNew($client, $pass);

                $remcode->remove();

                Tools::redirect('login?send_pass=1');

            }

        }
    }

    public function actionLogin()
    {
        $view = $this->getView('login.php');
        $this->carcase->import('content', $view);
        
        $socialAuthConfig = new Config('social-auth');
        $view->socialAuthInfo = $socialAuthConfig;
        if (Tools::rGET('back')) $view->back = Tools::rGET('back');
        $view->error = '';
        if (Tools::rPOST('username') && Tools::rPOST('pass')) {
            $user = new Client();
            $row  = $user->where('username', Tools::rPOST('username'))->getRow();
            if ($row && $row->password == Tools::passCrypt(Tools::rPOST('pass'))) {
                $phone_conf = true;
                if($this->config->enabled_sms_login){
                    $phone_conf = false;
                    $code = Tools::rPOST('code');
                    $SmsConf = new SmsConfirmation(Tools::rPOST('id_conf'));
                    if($SmsConf->code = $code && $SmsConf->phone == $row->phone && $SmsConf->type == 1){
                        $phone_conf = true;
                        $SmsConf->remove();
                    }

                }

                if($phone_conf) {
                    $hash = Tools::passCrypt(uniqid());
                    $user_id = $row->id;

                    $us = new ClientSession();
                    $us->hash = $hash;
                    $us->ip = $_SERVER['REMOTE_ADDR'];
                    $us->os = Tools::getOS();
                    $browser = Tools::getBrowser();
                    $us->browser = $browser['name'];
                    $us->client_id = $user_id;
                    $us->save();

                    $crypt = new Crypt();
                    setcookie('user', $crypt->encrypt($user_id), time() + 3600 * 35 * 36 * 36, '/');
                    setcookie('hash', ($hash), time() + 3600 * 35 * 36 * 36, '/');

                    if (Tools::rGET('back')) {
                        Tools::redirect(Tools::rGET('back'));
                    } else {
                        Tools::redirect('/');
                    }
                } else {
                    $view->error = 'phone_error';
                }
            } else {
                $view->error = 'login_error';
            }
        } else if (Tools::rPOST()) {
            $view->error = 'no_valid';
        }

    }

    public function actionSocialAuth(){
        if(!$this->config->enable_social_auth){
            Tools::redirect('/');
        }
        if(!Tools::rPOST('token')){
            Tools::redirect('login?error_code=3');
        }
        $token = Tools::rPOST('token');
        $s = file_get_contents('http://ulogin.ru/token.php?token=' . $token . '&host=' . $_SERVER['HTTP_HOST']);
        $user = json_decode($s, true);

        if(!is_array($user)){
            Tools::redirect('login?error_code=2');
        }

        $clientSocialAccount = new ClientSocialAccount();

        $row = $clientSocialAccount->where('identity', $user['identity'])->getRow();
        
        if($this->client) {
            
            if($row && $row->client_id){
                //error, clientSocialAccount already exist
                Tools::redirect('setting?error_code=1');
            }
           
            $clientSocialAccount->client_id = $this->client->id;
            $clientSocialAccount->identity = $user['identity'];
            $clientSocialAccount->network = $user['network'];
            $clientSocialAccount->save();
            Tools::redirect('/setting');
        }
        
        if($row && $row->client_id){
            $client = new Client($row->client_id);
            if($client->isLoadedObject()) {
                $client->login();
                Tools::redirect(Router::getParam('back'));
            }
        }
        else{
            $phone = $user['phone'];
            $phone = str_replace('(', '', $phone);
            $phone = str_replace(')', '', $phone);
            $phone = str_replace('-', '', $phone);
            $nickname = 'p' . str_replace('+', '', $phone);
            $password = Tools::generateCode();
            $nickname = isset($user['nickname']) ? $user['nickname'] : $nickname;

            $client = new Client();
            
            $isUserExist = $client->where('username', $nickname)->getRow();
            if($isUserExist){
                $nickname = uniqid();
            }

            if($client->where('email', $user['email'])->whereOr()->where('phone', $phone)->getRow()){
                Tools::redirect('login?error_code=1');
            }
            
            $client->username = $nickname;
            $client->name = $user['first_name'] . ' ' . $user['last_name'];
            $client->email = $user['email'];
            $client->phone = $phone;
            $client->password = Tools::passCrypt($password);

            $client->save();

            $clientSocialAccount->client_id = $client->id;
            $clientSocialAccount->identity = $user['identity'];
            $clientSocialAccount->network = $user['network'];
            $clientSocialAccount->save();

            Notifier::NewRegistration($client, $password);

            $client->login();
            Tools::redirect(Router::getParam('back'));
        }
    }

    public function actionLoginAjax(){
        if (Tools::rPOST('username') && Tools::rPOST('pass')) {
            $user = new Client();
            $row  = $user->where('username', Tools::rPOST('username'))->getRow();
            if ($row && $row->password == Tools::passCrypt(Tools::rPOST('pass'))) {
                $SmsConf = new SmsConfirmation();
                $SmsConf->code  = Tools::generateCode(4, "1234567890");
                $SmsConf->phone = $row->phone;
                $SmsConf->type  = 1;
                $SmsConf->save();

                SMS::getGateway()->sendSMS($row->phone, $SmsConf->code);
                echo json_encode(['ok' =>1, 'id' => $SmsConf->id]);

                exit();
            } else {
                echo json_encode(['ok' =>0]);
                exit();
            }
        }
        echo json_encode(['ok' =>0]);
        exit();
    }

    public function actionLogout()
    {
        $Session = new ClientSession();
        $Session->where('client_id', $this->client->id)->where('hash', Cookie::get('hash'))->removeRows();
        setcookie('user', '', time() - 3600 * 35 * 36 * 36, '/');
        setcookie('hash', '', time() - 3600 * 35 * 36 * 36, '/');
        Tools::redirect('/');
    }


    public function actionSendSmsCodeAjax()
    {
        $sc        = new SmsConfirmation();
        $sc->where('phone', Tools::rPOST('phone'))->where('type', SmsConfirmation::TYPE_REG);
        foreach ($sc->getRows() as $s) {
            $s = new SmsConfirmation($s);
            $s->remove();
        }

        $sc         = new SmsConfirmation();
        $sc->code   = Tools::generateCode(4, '1234567890');
        $sc->type   = SmsConfirmation::TYPE_REG;
        $sc->phone  = Tools::rPOST('phone');
        $sc->save();

        SMS::getGateway()->sendSMS(Tools::rPOST('phone'), 'Код подтверждения: ' . $sc->code);

        echo json_encode(array('code' => $sc->id));
    }

    public function actionValidateAjax()
    {
        if (Tools::rPOST('field') == 'code') {
            $code           = Tools::rPOST('value');
            $id_transaction = Tools::rPOST('data');
            $sc             = new SmsConfirmation($id_transaction);
            if ($sc->code == $code) {
                echo json_encode(array('result' => 1));
            } else {
                echo json_encode(array('result' => 0));
            }
        } elseif (Tools::rPOST('field') == 'username') {

            $user = new Client();
            $user->where('username', Tools::rPOST('value'));

            if ($user->getRow()) {
                echo json_encode(array('result' => 0));
            } else {
                echo json_encode(array('result' => 1));
            }
        } elseif (Tools::rPOST('field') == 'email') {

            $user = new Client();
            $user->where('email', Tools::rPOST('value'));

            if ($user->getRow()) {
                echo json_encode(array('result' => 0));
            } else {
                echo json_encode(array('result' => 1));
            }
        }elseif (Tools::rPOST('field') == 'phone') {
            $user = new Client();
            $user->where('phone', Tools::rPOST('value'));

            if ($user->getRow()) {
                echo json_encode(array('result' => 0));
            } else {
                echo json_encode(array('result' => 1));
            }
        }

    }

}