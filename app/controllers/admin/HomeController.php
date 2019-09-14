<?php

namespace admin;

use DateTime;
use model\Client;
use model\HostingAccount;
use model\Bill;
use model\HostingServer;
use model\Ticket;
use System\Config;
use System\Cookie;
use System\Tools;
use System\View\View;
use update\Update;

class HomeController extends FrontController
{
    public function actionIndex()
    {
        $this->layout->import('content', $v = $this->getView('home.php'));
        $config = new Config('widgets');
        $period = Tools::rPOST('period', $config->period);
        $from = '';
        $to = '';
$v->period = $period;
        if($period == 'day'){
            $from = date('Y-m-d');
            $to = $from;
        }

        else if($period == 'year'){
            $from = date('Y-m-d', time()-Cookie::ONE_YEAR);
            $to = date('Y-m-d');
        }
        else {
            $from = date('Y-m-d', time()-Cookie::ONE_MONAT);
            $to = date('Y-m-d');
        }

        $HostingAccount = new HostingAccount();
        $HostingAccount
            ->select('date')
            ->select(array('field'=>'date', 'function' => 'DATE_FORMAT(%field%, "%d-%m-%Y")', 'as' =>'showdate'))
            ->select(array('field' => 'id', 'function' => 'COUNT', 'as' => 'c'))
            ->groupBy(array('field' => 'date', 'function'=>'DATE_FORMAT(%field%, "%Y%m%d")'))
            ->where('date', '>', $from)
            ->where('date', '<', $to)
        ;

        $data1  = $HostingAccount->getRows();
        $chart1 = array();
        foreach ($data1 as $d1) {
            $chart1[$d1->showdate] = $d1->c;
        }
        $v->chart1 = ($chart1);

        $chart2 = array();
        $Client = new Client();
        $data2  = $Client
            ->select('date')
            ->select(array('field'=>'date', 'function' => 'DATE_FORMAT(%field%, "%d-%m-%Y")', 'as' =>'showdate'))
            ->select(array('field' => 'id', 'function' => 'COUNT', 'as' => 'c'))
            ->groupBy(array('field' => 'date', 'function'=>'DATE_FORMAT(%field%, "%Y%m%d")'))
            ->where('date', '>', $from)
            ->where('date', '<', $to)
            ->getRows();

        foreach ($data2 as $d2) {
            $chart2[$d2->showdate] = $d2->c;
        }
        $v->chart2 = ($chart2);

        $HostingAccount->select('active');
        $HostingAccount->select(array('field' => 'id', 'function' => 'COUNT', 'as' => 'c'));
        $HostingAccount->groupBy('active');
        $data3 = $HostingAccount->getRows();

        $chart3 = array();
        foreach ($data3 as $d3) {
            $chart3[$d3->active] = $d3->c;
        }
        $v->chart3 = ($chart3);


        $port                 = 8083;
        $waitTimeoutInSeconds = 1;
        $chart4               = array();
        foreach (HostingServer::getInstance()->getRows() as $s) {
            $host = $s->ip;

            $starttime = microtime(true);
            if ($fp = @fsockopen($host, $port, $errCode, $errStr, $waitTimeoutInSeconds)) {
                $chart4[$s->name] = microtime(true) - $starttime;

            } else {
                $chart4[$s->name] = 0;
            }
            if ($fp) {
                fclose($fp);
            }

        }
        $v->chart4 = $chart4;


        $Ticket = new Ticket();
        $Ticket->select(array('field' => 'date', 'function' => "DATE_FORMAT(%field%, '%Y-%m-%d')", 'as' => 'date1'));
        $Ticket->select(array('field' => 'id', 'function' => 'COUNT', 'as' => 'c'));
        $Ticket->groupBy('date')
            ->where('date', '>', $from)
            ->where('date', '<', $to)
        ;

        $data1  = $Ticket->getRows();
        $chart5 = array();
        foreach ($data1 as $d1) {
            $chart5[$d1->date1] = $d1->c;
        }
        $v->chart5 = ($chart5);


        $Bill = new Bill();
        $Bill->select(array('field' => 'date', 'function' => "DATE_FORMAT(%field%, '%Y-%m-%d')", 'as' => 'date1'));
        $Bill->select(array('field' => 'id', 'function' => 'COUNT', 'as' => 'c'));
        $Bill->groupBy('date')
            ->where('date', '>', $from)
            ->where('date', '<', $to)
        ;
        $data1  = $Bill->getRows();
        $chart6 = array();
        foreach ($data1 as $d1) {
            $chart6[$d1->date1] = $d1->c;
        }
        $v->chart6 = ($chart6);


        $version           = Update::checkUpdates();
        $v->update_aviable = 0;
        if(isset($version->next)) {
            $v->update_aviable = $version->next > $this->config->app_version ? 1 : 0;

            $v->new_version = $version->next;
        }

        $license_key = @file_get_contents('key.lic');
        $info       = @file_get_contents('http://service.hopebilling.com/licenser.php?info=' . $license_key);
        $info       = json_decode($info);
        $v->license_end_days = false;
        if(isset($info->lifetime)){
            $d = new DateTime($info->lifetime);
            $days = $d->diff( new DateTime() )->format("%a");
            //echo $days;
            if( $days < 5){
                $v->license_end_days    = $days;
            }
        }
       // print_r($info);

        $v->positions =$config->positions;
        if($config->period != $period){
            $config->period = $period;
            $config->save();
        }
    }

    public function actionSetPositionsAjax(){
        $config = new Config('widgets');
        $config->positions = Tools::rPOST('data');
        $config->save();

    }

}