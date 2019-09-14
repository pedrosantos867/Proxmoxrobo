<?php


use System\Tools;

class GeneralController extends IndexController
{

    public function run($action = 'actionIndex')
    {
        if ($this->config->only_ssl && _SITE_PROTOCOL_ != 'https://') {
            Tools::redirectToSSL($_SERVER['REQUEST_URI']);
        }

        if (!$this->config->is_install) {
            Tools::redirect('install');
        }

        parent::run($action);


    }



}