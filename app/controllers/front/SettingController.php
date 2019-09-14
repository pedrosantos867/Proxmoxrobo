<?php
namespace front;

use model\ClientSession;
use model\ClientSocialAccount;
use model\DomainOrder;
use model\DomainOwner;
use model\Languages;
use model\Ticket;
use System\Config;
use System\Cookie;
use System\Router;
use System\Tools;
use System\Upload;
use System\Validation;
use System\View\View;

class SettingController extends FrontController
{
    public function actionIndex()
    {
        $view = $this->getView('setting.php');
        $this->layout->import('content', $view);
        $messages = array();

        $socialAuthConfig = new Config('social-auth');

        if (Tools::rPOST()) {
            if (!HB_DEMO_MODE){

                $valid =    Validation::isFullName(Tools::rPOST('name')) &&
                            Validation::isEmail(Tools::rPOST('email'))   &&
                            Validation::isPhone(Tools::rPOST('phone'));

                if (!$valid) Tools::reload();

                $this->client->name = Tools::rPOST('name');
                $this->client->email = Tools::rPOST('email');
                $this->client->phone = Tools::rPOST('phone');


                $this->client->type = Tools::rPOST('type');

                if(Tools::rPOST('type')==1) {

                    $organization_name = Tools::rPOST('organization_name');
                   
                    $this->client->country = Tools::rPOST('country');
                    $this->client->organization_name = ($organization_name);
                    $this->client->organization_address = Tools::rPOST('organization_address');
                    $this->client->organization_located_address = Tools::rPOST('organization_located_address');
                    $this->client->organization_number = Tools::rPOST('organization_number');
                    $this->client->organization_ipn = Tools::rPOST('organization_ipn');
                    $this->client->organization_chief = Tools::rPOST('organization_chief');
                } else {
                    $this->client->country = '';
                    $this->client->organization_name = '';
                    $this->client->organization_address = '';
                    $this->client->organization_located_address = '';
                    $this->client->organization_number = '';
                    $this->client->organization_ipn = '';
                }

                if ($this->config->enable_lang_switcher_for_client) {
                    $this->client->default_lang = Tools::rPOST('default_lang');
                }

                $this->client->save();


                if (isset($_FILES['docs'])) {
                    if (!is_dir(_BASE_DIR_STORAGE_ . 'docs/')) {
                        mkdir(_BASE_DIR_STORAGE_ . 'docs');
                    }

                    if (!is_dir(_BASE_DIR_STORAGE_ . 'docs/' . $this->client->id . '/')) {
                        mkdir(_BASE_DIR_STORAGE_ . 'docs/' . $this->client->id . '/');
                    }


                    for ($i = 0; $i < count($_FILES['docs']['tmp_name']); $i++) {
                        $icou = new Upload(('storage/docs/' . $this->client->id . '/'));
                        $icou->setFileArray(
                            array(
                                'name' => $_FILES['docs']['name'][$i],
                                'type' => $_FILES['docs']['type'][$i],
                                'tmp_name' => $_FILES['docs']['tmp_name'][$i],
                                'size' => $_FILES['docs']['size'][$i],
                                'error' => $_FILES['docs']['error'][$i]
                            )
                        );
                        $ext = $icou->getExtension($_FILES['docs']['name'][$i]);
                        $icou->setFilename(uniqid('f') . '.' . $ext);
                        $icou->setMaxFileSize(5);
                        $icou->setAllowedMimeTypes(array("image/jpeg", "image/png", "image/gif", "image/x-ms-bmp", "application/zip", "text/plain", "application/msword"));
                        $r = $icou->upload();

                    }
                }
                $messages[] = 'ok';
                $view->messages = $messages;
            }
            else $this->layout->demo_mode = true;
        }

        $ClientSocialAccounts = new ClientSocialAccount();
        $clientSocialAccounts = $ClientSocialAccounts->where('client_id', $this->client->id)->getRows();

        $docs = $this->client->getDocs();

        $view->docs = $docs;
        $view->languages = Languages::factory()->getRows();
        $view->social_accounts = $clientSocialAccounts;
        $view->page = 'main';


        $view->socialAuthInfo = ($socialAuthConfig);
    }

    public function actionRemoveSocialAccount()
    {
        $id = Tools::rGET('id');
        $ClientSocialAccount = new ClientSocialAccount($id);
        $ClientSocialAccount->remove();
        Tools::redirect('/setting');
    }

    public function actionRemoveDocs()
    {
        $doc = Tools::rGET('doc');
        @unlink(_BASE_DIR_STORAGE_ . 'docs/' . $this->client->id . '/' . $doc);
        Tools::redirect('setting');
    }
    public function actionSetCurrency()
    {
        //  echo Router::getParam('back');
        Cookie::set('currency', Router::getParam('id_currency'));
        Tools::redirect(Tools::rGET('back'));
    }

    public function actionNotifications()
    {
        if (Tools::rPOST()) {

            $notifications = Tools::rPOST('notifications', []);

            if (!$this->config->enable_client_sms_notification_control) {
                if ($notifications) {
                    foreach ($notifications as $name => &$val) {
                        if (strpos($name, 'sms_') === 0) {
                            unset($notifications[$name]);
                        }
                    }
                }
            }
            if (!$this->config->enable_client_email_notification_control) {
                foreach ($this->config->client_notifications as $name => $val) {
                    if (strpos($name, 'sms_') === 0) {
                        $notifications[$name] = $val;
                    }
                }
            }

            $this->client->notifications = json_encode($notifications);
            $this->client->save();
        }

        $notifications = json_decode($this->client->notifications, true);

        if (!$this->config->enable_client_sms_notification_control) {
            if ($notifications) {
                foreach ($notifications as $name => $val) {

                    if (strpos($name, 'sms_') === 0) {
                        unset($notifications[$name]);
                    }
                }
            }
            if ($this->config->client_notifications) {
                foreach ($this->config->client_notifications as $name => $val) {
                    if (strpos($name, 'sms_') === 0) {
                        $notifications[$name] = $val;
                    }
                }
            }
        }

        if (!$this->config->enable_client_email_notification_control) {
            if ($notifications) {
                foreach ($notifications as $name => $val) {

                    if (strpos($name, 'sms_') !== 0) {
                        unset($notifications[$name]);
                    }
                }
            }

            if ($this->config->client_notifications) {
                foreach ($this->config->client_notifications as $name => $val) {
                    if (strpos($name, 'sms_') !== 0) {
                        $notifications[$name] = $val;
                    }
                }
            }
        }

        $this->layout->import('content', $v = $this->getView('setting.php'));
        $v->notifications = $notifications;

        $v->page = 'notifications';
    }

    public function actionSafety()
    {
        $messages = array();
        $this->layout->import('content', $v = $this->getView('setting.php'));
        $v->page = 'safety';

        $old_password = Tools::rPOST('old_password');

        if ((Tools::passCrypt($old_password) === ($this->client->password))) {
            if(!HB_DEMO_MODE) {

                $valid = Validation::isPasswd(Tools::rPOST('new_password')) &&
                         Validation::isPasswd(Tools::rPOST('new2_password'));

                if (!$valid) Tools::reload();

                $new_password = Tools::rPOST('new_password');
                $new2_password = Tools::rPOST('new2_password');
                if ($new_password === $new2_password) {
                    $this->client->password = Tools::passCrypt($new_password);
                    if ($this->client->save()) {
                        $messages[] = 'ok';
                        $v->messages = $messages;
                    }

                }
            }
            else $this->layout->demo_mode = true;
        }

        $Session = new ClientSession();
        if (Tools::rPOST('remove_sessions') == 1) {

            $Session->where('client_id', $this->client->id)->where('hash', '!=', Cookie::get('hash'))->removeRows();

        }

        $v->sessions = $Session->where('client_id', $this->client->id)->getRows();
    }



    public function actionValidateAjax()
    {
        $field = Tools::rPOST('field');
        $val = Tools::rPOST('value');
        if ($field == 'old_password') {
            if (Tools::passCrypt($val) === $this->client->password) {
                echo json_encode(['result' => 1]);
            } else {
                echo json_encode(['result' => 0]);
            }
        }


    }


}