<?php
/**
 * Created by PhpStorm.
 * User: Viktor
 * Date: 27.06.15
 * Time: 20:17
 */

namespace admin;


use email\Email;
use model\Currency;
use model\Languages;
use model\ModuleHook;
use model\Page;
use stdClass;
use System\Config;
use System\Db\Schema\Schema;
use System\Db\Schema\Table;
use System\FileUpload;
use System\ImageTools;
use System\LanguageDictionary;
use System\Module;
use System\Path;
use System\Router;
use System\Tools;
use System\Upload;
use System\Validation;
use System\View\View;
use update\Update;

class SettingsController extends FrontController
{

    public function actionSendTextMessageAjax()
    {
        $config = Config::factory();
        $email = new Email();

        $email->to = $config->site_email;
        $email->msg = 'Test message from HopeBilling';

        if ($email->send()) {
            $this->returnAjaxAnswer(1, 'Тестовое письмо отправлено');
        }

        $this->returnAjaxAnswer(0, 'Не удалось отправить письмо');
    }

    public function actionIndexAjax()
    {
        $gview = $this->getView('settings.php');
        $this->layout->import('content', $gview);


        $view = $this->getView('setting/global.php');
        $gview->import('content', $view);

    }
    public function actionNotifications(){
        $gview = $this->getView('settings.php');
        $this->layout->import('content', $gview);
        $view = $this->getView('setting/notifications.php');
        $gview->import('content', $view);

        if(Tools::rPOST()){
            if(!HB_DEMO_MODE) {
                $email_notifications = (Tools::rPOST('email_notifications'));
                $sms_notifications = (Tools::rPOST('sms_notifications'));
                $client_notifications = (Tools::rPOST('notifications'));

                $this->config->email_notifications = ($email_notifications);
                $this->config->sms_notifications = ($sms_notifications);
                $this->config->client_notifications = ($client_notifications);

                $this->config->save();
            }
            else $this->layout->demo_mode = true;
        }

        $view->email_notifications  = $this->config->email_notifications;
        $view->sms_notifications    = $this->config->sms_notifications;
        $view->notifications    = $this->config->client_notifications;
    }

    public function actionNotificationsSetting(){
        $gview = $this->getView('settings.php');
        $this->layout->import('content', $gview);

        if(Tools::rPOST()){
            if(!HB_DEMO_MODE) {
                $this->config->enable_client_sms_notification_control = Tools::rPOST('enable_client_sms_notification_control', 0);
                $this->config->enable_client_email_notification_control = Tools::rPOST('enable_client_email_notification_control', 0);
                $this->config->save();
            }
            else $this->layout->demo_mode = true;
        }

        $view = $this->getView('setting/notifications_setting.php');
        $gview->import('content', $view);

    }
    
    public function actionAddCurrency()
    {
        $this->actionEditCurrency();
    }

    public function actionCurrencyRefreshStateAjax()
    {
        $config                   = new Config;
        $config->currency_refrash = Tools::rPOST('state');
        $config->save();
        unset($config);
    }
    
    public function actionEditCurrency()
    {
        $id_currency = Router::getParam('id_currency', 0);

        $gview = $this->getView('settings.php');
        $this->layout->import('content', $gview);


        $view = $this->getView('setting/currency.php');
        $gview->import('content', $view);

        $this->layout->import('content', $gview);
//echo $id_currency;
        $currency = new Currency($id_currency);
        //  print_r($currency);
        if (Tools::rPOST()) {
            if(!HB_DEMO_MODE) {
                $currency->name = Tools::rPOST('name');
                $currency->short_name = Tools::rPOST('short_name');
                $currency->symbol = Tools::rPOST('symbol');
                $currency->iso = strtoupper(Tools::rPOST('iso'));
                $currency->coefficient = Tools::rPOST('coefficient');
                $currency->save();
            }
            else $this->layout->demo_mode = true;
        }

        $view->cur = $currency;
    }

    public function actionCurrenciesSetting()
    {
        $gview = $this->getView('settings.php');
        $this->layout->import('content', $gview);


        $view = $this->getView('setting/currencies_setting.php');
        $gview->import('content', $view);


        if (Tools::rPOST('currency_default')) {

            if(!HB_DEMO_MODE) {
                $this->config->currency_default = Tools::rPOST('currency_default');
                $this->config->currency_server = Tools::rPOST('currency_server');
                $this->config->save();

                $currency = new Currency(Tools::rPOST('currency_default'));
                $currency->coefficient = 1;
                $currency->save();
            }
            else $this->layout->demo_mode = true;
        }


        $view->currencies = Currency::getInstance()->getRows();


    }

    public function actionSmsGateway()
    {
        $gview = $this->getView('settings.php');
        $this->layout->import('content', $gview);
        $view = $this->getView('setting/sms-gateway.php');
        $gview->import('content', $view);
        $config = new Config();
        if (Tools::rPOST('sms-gateway')) {
            if(!HB_DEMO_MODE) {
                $config->sms_gateway = Tools::rPOST('sms-gateway');
                $config->save();
            }
            else $this->layout->demo_mode = true;
        }
        $view->cfg = $config;
    }

    public function actionLanguages()
    {
        $gview = $this->getView('settings.php');
        $this->layout->import('content', $gview);


        $view = $this->getView('setting/language/list.php');
        $gview->import('content', $view);

        $view->languages = Languages::factory()->getRows();

    }

    public function actionRemoveLanguage(){
        if(!HB_DEMO_MODE) {
            $language = new Languages(Tools::rGET('id_lang'));
            @unlink(Path::getRoot('storage/i18n/flags/' . $language->iso_code . '.png'));

            $language->remove();
            Tools::redirect('admin/settings/languages/');
        }
        else $this->layout->demo_mode = true;
    }

    public function actionAddLanguage(){
        $language = new Languages(Tools::rGET('id_lang'));
        if(Tools::rPOST()){
            if(!HB_DEMO_MODE) {
                $language->name = Tools::rPOST('name');
                $language->iso_code = Tools::rPOST('iso_code');
                $language->save();

                /*
                $icou = new Upload(('storage/i18n/flags/'));
                $icou->setFileArray($_FILES['ico']);
    
                $icou->setMaxFileSize(0.01);
                $icou->setFilename($language->iso_code.'.png');
                $icou->upload();
                */
                if (($_FILES['ico']['tmp_name']) && $language->iso_code) {
                    try {
                        $it = new ImageTools($_FILES['ico']['tmp_name']);
                        $it->resize(24, 24);
                        $it->save(Path::getRoot('storage/i18n/flags/' . $language->iso_code . '.png'), IMAGETYPE_PNG);
                    } catch (\Exception $e) {

                    }
                }

                Tools::redirect('admin/settings/languages/');
            }
            else $this->layout->demo_mode = true;
        }

        $gview = $this->getView('settings.php');
        $this->layout->import('content', $gview);


        $view = $this->getView('setting/language/edit.php');
        $gview->import('content', $view);

        $view->language = $language;
    }

    public function actionTranslateManager(){

        $id_lang = Tools::rGET('id_lang');
        $Language = new Languages($id_lang);

        $gview = $this->getView('settings.php');
        $this->layout->import('content', $gview);


        $view = $this->getView('setting/language/translate_manager.php');
        $gview->import('content', $view);

        if(Tools::rGET('type') == 2){
            $dir = _BASE_DIR_TEMPLATE_ .'admin'.'/default';
        } else if(Tools::rGET('type') == 3) {
            $dir = _BASE_DIR_TEMPLATE_ .'install';
        } else if(Tools::rGET('type') == 4) {
            $dir = _BASE_DIR_TEMPLATE_ .'email/default/front';
        } else if(Tools::rGET('type') == 5) {
            $dir = _BASE_DIR_TEMPLATE_ .'email/default/admin';
        } else if(strpos(Tools::rGET('type'), 'm') !== false){
            $module_id = str_replace('m', '', Tools::rGET('type'));
            $ModuleObject = new \model\Module($module_id);
            if(strpos(Tools::rGET('type'), 'f') !== false)
            { $dir = _BASE_DIR_APP_.'modules/'.$ModuleObject->system_name.'/template/front/default';}
            else if(strpos(Tools::rGET('type'), 'a') !== false)
            {$dir = _BASE_DIR_APP_.'modules/'.$ModuleObject->system_name.'/template/admin/default';}
        } else {
            $dir = _BASE_DIR_TEMPLATE_ .'front'.'/default';
        }



        $view->modules = \model\Module::factory()->getRows();

        $LD = new LanguageDictionary($Language->iso_code, $dir);

        if(Tools::rPOST('copy_from')){
            if(!HB_DEMO_MODE){
                $Languagef = new Languages(Tools::rPOST('copy_from'));
                $LDf = new LanguageDictionary($Languagef->iso_code, $dir);
                $allf = $LDf->getAll();

                $all = array();
                foreach($allf as $file => $translates){
                    // $translates_clean = array();
                    //  foreach($translates as $key => $v){
                    //     $translates_clean[$key] = '';
                    //  }

                    $all[$file] = $translates;

                }

                $LD->setArray($all);
            }
            else $this->layout->demo_mode = true;
        }



        $view->language = $Language;

        $view->languages = $Language->getRows();


        if(Tools::rPOST('translate')){
            if(!HB_DEMO_MODE){
                $LD->setArray(Tools::rPOST('translate', array()));
            }
            else $this->layout->demo_mode = true;
        }
        $translate = $LD->getAll();
        $view->dir = $dir;
       // $LD->setArray($translate);
        $view->translates = $translate;
    }

    public function actionLanguageSettings(){
        if(Tools::rPOST()){
            if(!HB_DEMO_MODE){
                $this->config->admin_default_lang = Tools::rPOST('admin_default_lang');
                $this->config->front_default_lang = Tools::rPOST('front_default_lang');
                $this->config->enable_lang_switcher_for_admin = Tools::rPOST('enable_lang_switcher_for_admin', 0);
                $this->config->enable_lang_switcher_for_client = Tools::rPOST('enable_lang_switcher_for_client', 0);

                $this->config->save();
            }
            else $this->layout->demo_mode = true;
        }


        $gview = $this->getView('settings.php');
        $this->layout->import('content', $gview);

        $view = $this->getView('setting/language/settings.php');
        $gview->import('content', $view);

        $view->languages = Languages::factory()->getRows();


    }

    public function actionSocialAuthSettings(){
        $qview = $this->getView('settings.php');
        $this->layout->import('content', $qview);

        $view = $this->getView('setting/social-auth.php');
        $qview->import('content', $view);
        
        $socialAuthConfig = new Config('social-auth');

        if (!$socialAuthConfig->networks) {
            $socialAuthConfig->networks = array();
        }


        $view->socialConfig = $socialAuthConfig;
        
        if(Tools::rPOST('set_networks')) {
            if(!HB_DEMO_MODE){
                $enableNetworks = Tools::rPOST('networks', array());
                $socialAuthConfig->networks = $enableNetworks;
                $socialAuthConfig->save();
            }
            else $this->layout->demo_mode = true;
        }
    }


    public function actionGatewaySetting()
    {
        $gview = $this->getView('settings.php');
        $this->layout->import('content', $gview);
        $smsconfig = new Config('sms-gateway');

        $view = $this->getView('setting/sms-gateway_setting.php');
        $gview->import('content', $view);

        $view->system    = Router::getParam('system');
        $view->smsconfig = $smsconfig;

        switch (Router::getParam('system')) {
            case "turbosms":
                if (Tools::rPOST('login') && Tools::rPOST('password')) {
                    if(!HB_DEMO_MODE){
                        $smsconfig->turbosms           = new \stdClass();
                        $smsconfig->turbosms->sender    = Tools::rPOST('sender');
                        $smsconfig->turbosms->login    = Tools::rPOST('login');
                        $smsconfig->turbosms->password = Tools::rPOST('password');
                        $smsconfig->save();
                    }
                    else $this->layout->demo_mode = true;
                }
                break;
            case "smsc":
                if (Tools::rPOST('login') && Tools::rPOST('password')) {
                    if(!HB_DEMO_MODE){
                        $smsconfig->smsc           = new \stdClass();
                        $smsconfig->smsc->sender    = Tools::rPOST('sender');
                        $smsconfig->smsc->login    = Tools::rPOST('login');
                        $smsconfig->smsc->password = Tools::rPOST('password');
                        $smsconfig->save();
                    }
                    else $this->layout->demo_mode = true;
                }
                break;

        }
        $view->smsconfig = $smsconfig;
    }

    public function actionCurrenciesAjax()
    {
        $this->actionCurrencies(true);
    }

    public function actionCurrencies($ajax = false)
    {
        $view_currencies = $this->getView('setting/currencies.php');
        $view            = $this->getView('settings.php');
        $this->layout->import('content', $view);
        $view->import('content', $view_currencies);

        $view_currencies->currencies = Currency::getInstance()->getRows();
    }

    public function actionRemoveCurrencyAjax()
    {
        $currency = new Currency(Router::getParam('id_currency'));
        if ($currency->id != $this->config->currency_default) {
            $currency->remove();

            $this->returnAjaxAnswer(1, 'Валюта успешно удалена');
        } else {
            $this->returnAjaxAnswer(0, 'Нельзя удалить основную валюту');

        }
    }

    public function actionRemoveCurrency()
    {
        $currency = new Currency(Router::getParam('id_currency'));
        $currency->remove();
        Tools::redirect('/admin/settings/currencies');
    }

    public function actionRefreshCurrency()
    {
            Currency::updateCurses();
            Tools::redirect('/admin/settings/currencies');

    }

    public function actionUpdate()
    {

        if (Tools::rPOST('update')) {
            if (Update::getUpdate()) {
                Tools::redirect('/update.php');
            }
        }

        $gview = $this->getView('settings.php');
        $this->layout->import('content', $gview);
        $view = $this->getView('setting/update.php');
        $gview->import('content', $view);


        $update     = new Update();
        if(Update::is_writable_r(_DOCUMENT_ROOT_)){
            $view->canUpdate = true;
        } else {
            $view->canUpdate = false;
        }

        $view->info = $update->checkUpdates(Tools::rGET('beta', 0));

    }



    public function actionLicenseInfo()
    {
        $gview = $this->getView('settings.php');
        $this->layout->import('content', $gview);

        $view = $this->getView('setting/license.php');
        $gview->import('content', $view);

    }

    public function actionRecaptcha()
    {
        $gview = $this->getView('settings.php');
        $this->layout->import('content', $gview);

        $view = $this->getView('setting/recaptcha.php');
        $gview->import('content', $view);

        if (Tools::rPOST('recaptcha_sitekey')) {
            if(!HB_DEMO_MODE){
                $this->config->recaptcha_sitekey    = Tools::rPOST('recaptcha_sitekey');
                $this->config->recaptcha_secret     = Tools::rPOST('recaptcha_secret');
                $this->config->save();
            }
            else $this->layout->demo_mode = true;

        }
        $view->cfg = $this->config;
    }

    public function actionIndex()
    {
        $gview = $this->getView('settings.php');
        $this->layout->import('content', $gview);
        $view = $this->getView('setting/global.php');
        $gview->import('content', $view);

        $view->pages = Page::factory()->getRows();

        $admin_templates = scandir(_BASE_DIR_TEMPLATE_.'admin');

        unset($admin_templates[0]);
        unset($admin_templates[1]);

        $view->admin_templates = $admin_templates;

        $front_templates = scandir(_BASE_DIR_TEMPLATE_.'front');

        unset($front_templates[0]);
        unset($front_templates[1]);

        $view->front_templates = $front_templates;

        $email_templates = scandir(_BASE_DIR_TEMPLATE_ . 'email');

        unset($email_templates[0]);
        unset($email_templates[1]);

        $view->email_templates = $email_templates;


        if (Tools::rPOST()) {

            if(!HB_DEMO_MODE){
                $this->config->sitename            = Tools::rPOST('sitename');
                $this->config->site_email          = Tools::rPOST('site_email');
                $this->config->site_sms          = Tools::rPOST('site_sms');
                $this->config->notification_email          = Tools::rPOST('notification_email');

                if(is_dir(_BASE_DIR_TEMPLATE_.'front/'.Tools::rPOST('front_template').'/')){
                    $this->config->front_template            = Tools::rPOST('front_template');
                }
                if(is_dir(_BASE_DIR_TEMPLATE_.'admin/'.Tools::rPOST('admin_template').'/')){
                    $this->config->admin_template            = Tools::rPOST('admin_template');
                }
                if (is_dir(_BASE_DIR_TEMPLATE_ . 'email/' . Tools::rPOST('email_template') . '/')) {
                    $this->config->email_template = Tools::rPOST('email_template');
                }

                $this->config->enabled_sms_login = Validation::onlyBoolInt(Tools::rPOST('enabled_sms_login', 0));
                $this->config->enabled_sms_confirm = Validation::onlyBoolInt(Tools::rPOST('enabled_sms_confirm', 0));
                $this->config->enabled_captcha = Validation::onlyBoolInt(Tools::rPOST('enabled_captcha', 0));
                $this->config->only_ssl = Validation::onlyBoolInt(Tools::rPOST('only_ssl', 0));
                $this->config->refprogram_enable = Validation::onlyNumber(Tools::rPOST('refprogram_enable', 0));
                $this->config->refprogram_percent = Validation::onlyNumber(Tools::rPOST('refprogram_percent'));
                $this->config->hosting_rules = Validation::onlyNumber(Tools::rPOST('hosting_rules', 0));
                $this->config->email_method = Validation::onlyFromArray((Tools::rPOST('email_method', 'mail')), array('smtp', 'mail'));
                $this->config->smtp_protocol = Validation::onlyFromArray((Tools::rPOST('smtp_protocol', '')), array(0, 1));
                $this->config->smtp_server = Validation::onlyString(Tools::rPOST('smtp_server', ''));
                $this->config->smtp_email = Validation::onlyString(Tools::rPOST('smtp_email', ''));
                $this->config->smtp_port = Validation::onlyNumber(Tools::rPOST('smtp_port', 465));
                $this->config->smtp_username = Validation::onlyString(Tools::rPOST('smtp_username', ''));
                $this->config->smtp_password = Validation::onlyString(Tools::rPOST('smtp_password', ''));


                $this->config->ns1 = Tools::rPOST('ns1');
                $this->config->ns2 = Tools::rPOST('ns2');
                $this->config->ns3 = Tools::rPOST('ns3');
                $this->config->ns4 = Tools::rPOST('ns4');

                //hosting component
                $this->config->enable_component_hosting  = Tools::rPOST('enable_component_hosting');

                $this->config->enable_component_domain  = Tools::rPOST('enable_component_domain');
                $this->config->enable_component_vps  = Tools::rPOST('enable_component_vps');
                $this->config->enable_social_auth  = Tools::rPOST('enable_social_auth');

                $this->config->save();
            }
            else $this->layout->demo_mode = true;

        }


    }



}