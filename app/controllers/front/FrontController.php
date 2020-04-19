<?php
namespace front;

use model\Bill;
use model\Client;
use model\ClientNotify;
use model\ClientSession;
use model\Currency;
use model\Languages;
use model\ModuleHook;
use model\ServiceCategories;
use model\Ticket;
use model\TicketAnswer;
use System\Cookie;
use System\Crypt;
use System\Module;
use System\Path;
use System\Router;
use System\Tools;
use System\View\Helper;
use \System\View\View;

class FrontController extends \GeneralController
{

    /**
     * @var Client
     * Client model object
     */
    protected $client = null;
    protected $currency = null;

    protected $auth = 1;

    protected $lang = 0;
    protected $page = 0;
    protected $from = 0;
    protected $count = 10;

    protected $layout;
    protected $helper;
    protected $viewTheme = 'default';

    protected  $client_notifications = array();
    protected  $site_email_notifications = array();
    protected  $site_sms_notifications = array();


    public function init()
    {

        Languages::init('front_lang');

        $this->viewTheme = $this->config->front_template;

        if (!$this->isAjaxQuery) {
            $this->carcase = $this->getView('carcase.php');
            $this->layout = $this->getView('layout.php');
        } else {
            $this->carcase = $this->getView('ajax.php');
            $this->layout = $this->getView('ajax.php');
        }

        Module::execHook('displayBeforeContent', $this->carcase);
        $this->carcase->import('content', $this->layout);
        Module::execHook('displayAfterContent', $this->carcase);

        $layoutMenu = array('orders' => [], 'create_orders' => []);
        Module::extendMethod('getFrontLayoutMenu', $layoutMenu);
        $this->layout->menu = $layoutMenu;

        if (isset($_COOKIE['user']) && isset($_COOKIE['hash'])) {
            $crypt   = new Crypt();
            $id_user = $crypt->decrypt($_COOKIE['user']);
            $hash    = $_COOKIE['hash'];

            $us      = new ClientSession();
            $session = $us->where('client_id', $id_user)->where('hash', $hash)->getRow();

            if (isset($session) && $session) {
                $this->client = new Client($session->client_id);

                if (!$this->client->isLoadedObject()) {
                    setcookie('user', '', time() - 3600 * 35 * 36 * 36, '/');
                    setcookie('hash', '', time() - 3600 * 35 * 36 * 36, '/');
                    Tools::redirect('/');
                }

                $this->client_notifications = json_decode($this->client->notifications, true);

            } else {
                setcookie('user', '', time() - 3600 * 35 * 36 * 36, '/');
                setcookie('hash', '', time() - 3600 * 35 * 36 * 36, '/');
                Tools::redirect('/');
            }

        }

        if (Cookie::get('currency')) {
            $currency = new Currency(Cookie::get('currency'));
            if ($currency->isLoadedObject()) {
                $this->currency = $currency;
            } else {
                $this->currency = new Currency($this->config->currency_default);
            }
        } else {
            $this->currency = new Currency($this->config->currency_default);
        }

        $this->lang = new Languages(Languages::get('front_lang'));
        if(!$this->lang->isLoadedObject()){
            $this->lang = new Languages($this->config->front_default_lang);
        }


        if(Cookie::get('front_lang') === null){
            Languages::set("front_lang", $this->config->front_default_lang);
            Cookie::set('lang_default_flag', 1, time()+3600*24*24*24);
        }

        if(Tools::rGET('lang') !== false && Cookie::get('front_lang') != Tools::rGET('lang') && $this->config->enable_lang_switcher_for_client){
            Languages::set('front_lang', Tools::rGET('lang'));

            $rurl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
            // print_r($_SERVER);

            Tools::redirect($rurl.Tools::removeGetParam('lang'));
        } elseif((!Cookie::get('front_lang') || Cookie::get('lang_default_flag') == 1) && $this->client && $this->client->default_lang){
            Languages::set('front_lang', $this->client->default_lang);
            Cookie::remove('lang_default_flag');
            Tools::reload();
        }
/*
        if(!$this->config->enable_lang_switcher_for_client && Cookie::get('front_lang') != $this->config->front_default_lang){
            Languages::set('front_lang', $this->config->front_default_lang);
            Cookie::set('lang_default_flag', 1, time()+3600*24*24*24);
            Tools::reload();
        }
*/

        $languages = Languages::factory()->getRows();
        $this->carcase->glob('languages', $languages);

        $this->site_email_notifications = $this->config->email_notifications;
        $this->site_sms_notifications = $this->config->sms_notifications;


        $this->checkAuth();


        return true;
    }

    public function checkAuth()
    {
        if (!$this->client && $this->auth) {
            if (isset($_SERVER['REQUEST_URI'])) {
                Tools::redirect('/login?back=' . $_SERVER['REQUEST_URI']);
            } else {
                Tools::redirect('/login');
            }
        }
    }

    protected function checkAccess($object)
    {

        if($object->client_id && $this->client->id != $object->client_id){
            Tools::display403Error();
            exit();
        }
    }

    public function getView($path)
    {
        //fix if selected template not exist.
        if(!file_exists( Path::getRoot('/template/front/' . $this->viewTheme . '/views/' . $path))){
            $this->viewTheme = 'default';
        }

        $view = new View('front/' . $this->viewTheme, 'views/' . $path);
        return $view;
    }



    public function process()
    {

        $this->page = $this->page ? $this->page : Tools::rPOST('page', 1);
        $this->from = ($this->page * $this->count) - $this->count;

        $this->carcase->content    = '';
        $this->carcase->glob('client', $this->client);

        $this->carcase->glob('currency', $this->currency);
        $currencies   =  Currency::getInstance()->getRows();



        if($this->client){
            $billObject = new Bill();
            $this->layout->count_bills = $billObject

                ->where('client_id', $this->client->id)
                ->where('is_paid', 0)
                ->where('type' ,'!=', Bill::TYPE_INC)
                ->getRowsCount();

            $ticketObject = new Ticket();
            $ticketAnswerObject = new TicketAnswer();
            $this->layout->count_new_ticket_messages =
                $ticketObject
                    ->select(TicketAnswer::factory(), '*')
                    ->join(TicketAnswer::factory(), 'id', 'ticket_id')
                    ->where('client_id', $this->client->id)
                    ->where(TicketAnswer::factory(), 'is_new', 1)
                    ->where(TicketAnswer::factory(), 'employee_id', '!=', 0)
                    ->getRowsCount();


            $this->layout->service_categories = ServiceCategories::factory()->getRows();
        }



        $this->carcase->glob('currencies', $currencies);
        $this->carcase->glob('request', $this->request);
        $this->carcase->glob('link', $this->request);
        $this->carcase->glob('lang', $this->lang);
    }

    public function pagination($all, $limit = 10)
    {

        $v = $this->getView('pagination.php');
        $v->hide = 0;

        if ($all <= $limit) {
            $v->hide = 1;
        }

        $v->pages = round($all / $limit, PHP_ROUND_HALF_EVEN);

        // echo $page;

        $v->current = $this->page;


        $html = $v->fetch();
        $this->carcase->glob('pagination', $html);

        return $html;
    }

    public function returnAjaxAnswer($result, $message = ''){
        echo json_encode(['result' => $result, 'message' => Languages::translate($message, Path::getRoot('template/front/'.$this->viewTheme.'/'), 'popup-messages')]);
        exit();
    }

    public function actionList()
    {

        $view = $this->getView('empty-ajax-block.php');
        $this->layout->import('content', $view);
    }

    public function actionCheckerAjax()
    {
        if ($this->client) {
            $billObject = new Bill();
            $count_bills = $billObject
                ->where('client_id', $this->client->id)
                ->where('is_paid', 0)
                ->where('type', '!=', Bill::TYPE_INC)
                ->getRowsCount();

            $ticketObject = new Ticket();

            $count_new_ticket_messages =
                $ticketObject
                    ->select(TicketAnswer::factory(), '*')
                    ->join(TicketAnswer::factory(), 'id', 'ticket_id')
                    ->where('client_id', $this->client->id)
                    ->where(TicketAnswer::factory(), 'is_new', 1)
                    ->where(TicketAnswer::factory(), 'employee_id', '!=', 0)
                    ->getRowsCount();


            echo json_encode(['bills' => $count_bills, 'messages' => $count_new_ticket_messages]);
            exit();
        }

        echo json_encode(['bills' => 0, 'messages' => 0]);

        exit();
    }

}