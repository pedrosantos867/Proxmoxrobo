<?php

namespace admin;

use model\Client;
use model\ClientSession;
use model\Currency;
use model\Employee;
use model\EmployeeSession;
use model\Languages;
use System\Cookie;
use System\Crypt;
use System\Module;
use System\Path;
use System\Router;
use System\Tools;
use \System\View\View;

class FrontController extends \GeneralController
{

    protected $employee = null;
    protected $auth = 1;
    protected $errors = array();
    protected $currency = null;

    protected $from = 0;
    protected $count = 10;
    protected $page = 0;

    protected $viewTheme = 'default';

    protected $layout;
    protected $lang;


    public function init()
    {
        Languages::init('admin_lang');

        $this->viewTheme = $this->config->admin_template;

        if ($this->isAjaxQuery) {
            $this->carcase = $this->getView('ajax.php');
            $this->layout = $this->getView('ajax.php');
        } else {
            //  $this->layout = $this->getView('layout.php');
            $this->carcase = $this->getView('carcase.php');
            $this->layout = $this->getView('layout.php');

        }
        $this->carcase->import('content', $this->layout);

        $layoutMenu = array('services' => []);
        Module::extendMethod('getAdminLayoutMenu', $layoutMenu);
        $this->layout->menu = $layoutMenu;

        if (isset($_COOKIE['employee']) && isset($_COOKIE['employee_hash'])) {
            $crypt   = new Crypt();
            $id_user = $crypt->decrypt($_COOKIE['employee']);
            $hash    = $_COOKIE['employee_hash'];

            $us = new EmployeeSession();

            $session = $us->where('employee_id', $id_user)->where('hash', $hash)->getRow();

            if ($session) {
                $this->employee = new Employee($session->employee_id);
            }

        }

        $this->lang = new Languages(Languages::get('admin_lang'));
        if(Cookie::get('admin_lang') === null){
            Languages::set("admin_lang", $this->config->admin_default_lang);
        }

        if(Tools::rGET('lang')!==false && Cookie::get('admin_lang') != Tools::rGET('lang') && $this->config->enable_lang_switcher_for_admin){
            Languages::set('admin_lang', Tools::rGET('lang'));
            $rurl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
            Tools::redirect($rurl.Tools::removeGetParam('lang'));
        }

        if(!$this->config->enable_lang_switcher_for_admin && Cookie::get('admin_lang') != $this->config->admin_default_lang){
            Languages::set('admin_lang', $this->config->admin_default_lang);
            Tools::reload();
        }

        $this->layout->languages = Languages::factory()->getRows();

        $this->currency = new Currency($this->config->currency_default);


        if (!$this->employee && $this->auth) {
            Tools::redirect('/admin/login');
        }



        return true;
    }

    private function checkAccess($object)
    {
        if(isset($object->client_id) && $this->client->id != $object->client_id){
            Tools::display403Error();
        }
    }

    public function pagination($all, $limit = 10)
    {

        $v = $this->getView('pagination.php');
        $v->hide = 0;

        if ($all <= $limit) {
            $v->hide = 1;
        }

        //echo
        $v->pages = ceil($all / $limit);

        // echo $page;

        $v->current = $this->page;


        $html = $v->fetch();
        //$v->glob()
        $this->carcase->glob('pagination', $html);

        // $this->ajaxView->glob('pagination', $html);

        return $html;
    }

    public function getView($path)
    {
        //fix if selected template not exist.
        if(!file_exists( Path::getRoot('/template/admin/' . $this->viewTheme . '/views/' . $path))){
            $this->viewTheme = 'default';
        }

        $view = new View('admin/' . $this->viewTheme, 'views/' . $path);
        return $view;
    }

    public function returnAjaxAnswer($result, $message = ''){
        echo json_encode(['result' => $result, 'message' => $message ? Languages::translate($message, _BASE_DIR_TEMPLATE_.'admin/default', 'popup-messages') : '']);
        exit();
    }

    public function process()
    {
        $this->page = $this->page ? $this->page : Tools::rPOST('page', 1);
        $this->from = ($this->page * $this->count) - $this->count;

        $this->carcase->content  = '';
        $this->layout->content = '';
        $this->carcase->glob('employee' , $this->employee);
        $this->carcase->glob('request' , $this->request);

        $this->carcase->glob('currency' , $this->currency);
        $this->carcase->glob('errors', $this->errors);
        $this->carcase->glob('lang', $this->lang);

        // $this->layout->import('content', $this->carcase);
        //$this->layout->display();
    }

    public function actionList()
    {

        $view = $this->getView('empty-ajax-block.php');
        $this->layout->import('content', $view);
    }
}