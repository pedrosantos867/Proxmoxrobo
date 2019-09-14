<?php

namespace front;

use model\Client;
use System\Db\Db;
use System\ObjectModel;
use System\Tools;
use System\View\View;

class PartnerController extends FrontController
{

    public function process()
    {

        if (!$this->config->refprogram_enable) {
          Tools::display404Error();
        }
        parent::process();
    }
    public function actionIndex()
    {
        $view = $this->getView('partner.php');
        $view->plink     = _SITE_URL_ . '/' . 'reg/ref' . $this->client->id;
        $c              = new Client();
        $referals       = $c->where('ref_id', $this->client->id)->getRows();
        $view->referals = $referals;

        $this->layout->import('content', $view);
    }

    public function actionGetMoney()
    {
        $c        = new Client();
        $referals = $c->where('ref_id', $this->client->id)->getRows();
        $summ     = 0;

        Db::getInstance()->beginTransaction();
        foreach ($referals as $ref) {
            $client = new Client($ref);
            $summ += $client->ref_rev * $this->config->refprogram_percent/100;
            $client->ref_rev = 0;
            $client->save();
        }

        $this->client->balance += $summ;
        $this->client->save();
        Db::getInstance()->commit();

        Tools::redirect('/partner');
    }
}