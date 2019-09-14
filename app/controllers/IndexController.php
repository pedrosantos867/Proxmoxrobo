<?php

use System\View\View;
use System\Config;
use System\Tools;



class IndexController
{


    protected $carcase;
    protected $config;
    protected $params;

    protected $request;

    protected $dcurrency;
    protected $isAjaxQuery = false;



    public function __construct()
    {

        $this->request = $_SERVER['REQUEST_URI'];
        $this->config = new Config();
    }

    public function run($action = 'actionIndex')
    {

        if (Tools::rGET('ajax') || Tools::rPOST('ajax')) {
            $this->isAjaxQuery = true;
        }


        $this->carcase = new View();


        $this->init();

        $this->carcase->glob('config', $this->config);


        $this->dcurrency = new \model\Currency($this->config->currency_default);
        $this->carcase->glob('dcurrency', $this->dcurrency);

        $this->process();

        if ($this->isAjaxQuery) {
            unset($_POST['ajax']);
            unset($_GET['ajax']);
            $action .= 'Ajax';
            if (Tools::rPOST('action')) {
                $action = 'action' . ucfirst(Tools::rPOST('action')) . 'Ajax';
                unset($_POST['action']);
            }
            if (method_exists($this, $action)) {
                $this->$action();
            } else {
                throw new \Exception('Action ' . $action . ' not found');
            }

            if ($this->carcase->isLoaded('content')) {
                $this->carcase->g('ajax', 1);
                echo $this->carcase->fetch();
            }
            exit();
        }


        if (method_exists($this, $action)) {
            $this->$action();
        } else {
            throw new \Exception('Action ' . $action . ' not found');
        }


        $this->carcase->display();
    }



    public function init()
    {
    }

    public function process()
    {
    }

    public function actionIndex()
    {
    }


}