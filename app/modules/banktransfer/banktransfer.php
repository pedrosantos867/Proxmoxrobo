<?php

namespace modules\banktransfer;

use email\Email;
use Dompdf\Dompdf;
use model\Bill;
use model\Currency;
use modules\banktransfer\controllers\front\BanktransferController;
use stdClass;
use System\Config;
use System\Module;
use System\Router;
use System\Tools;

class banktransfer extends Module{
    public $name = 'Генерация счета на оплату';
    public $author = '<a target="_blank" href="http://hopebilling.com">hopebilling.com</a>';
    public $category = 3; // for payments systems


    public function install(){

        $pconfig                                = new Config('banktransfer.module');
        $pconfig->desc                          = '';
        $pconfig->invoice                       = '';
        $pconfig->act_on = 0;
        $pconfig->act = '';
        $pconfig->currency                      = 0;
        $pconfig->save();

        $this->registerHook('displayPaymentMethods');
        $this->registerHook('newBill');
        $this->registerHook('paidBill');

    }

    public function uninstall(){

        $pconfig                                 = new Config('banktransfer.module');
        $pconfig->remove();

        return parent::uninstall();
    }

    public function displayPaymentMethods(&$view){
        $pconfig = new Config('banktransfer.module');
        $id_bill = Router::getParam(0);

        $Bill = new Bill($id_bill);
        $view = $this->getModuleView('bill/pay.php');
        $dcurrency = new Currency($this->config->currency_default);

        $currency = new Currency($pconfig->currency);

        if(!$currency->isLoadedObject()){
            $currency = $dcurrency;
        }

        $view->id_bill = $id_bill;

    }

    public function newBill(&$data){
        $pconfig = new Config('banktransfer.module');
        $email = $data['email'];
        $bill = $data['bill'];

        //$email->addAttachment('invoice.pdf', 'pdf', 'sdfsdfsdfsdf');

        $id_bill = $bill->id;
        $view = $this->getModuleView('bill/pdf.php');
        $view->id_bill = $id_bill;
        $content = $pconfig->invoice;
        $html = BanktransferController::parseHtml($bill,$content);


        $view->pdf = $html;
        include_once (__DIR__.'/dompdf/autoload.inc.php');

        $invoice = $view->fetch();


        $invoice = str_replace('Times New Roman', 'times', $invoice);
        $invoice = str_replace('&nbsp;', ' ', $invoice);

        $dompdf = new Dompdf( );
        $dompdf->loadHtml($invoice);
        $dompdf->render();
        $pdf = $dompdf->output();

        $email->addAttachment('invoice_' . $id_bill . '.pdf', base64_encode($pdf));


    }

    public function paidBill(&$data){
        $pconfig = new Config('banktransfer.module');
        $email = $data['email'];
        $bill = $data['billObject'];

        $id_bill = $bill->id;

        include_once (__DIR__.'/dompdf/autoload.inc.php');


        if($pconfig->act_on) {
            $view = $this->getModuleView('bill/pdf.php');
            $view->id_bill = $id_bill;

            $content = $pconfig->act;
            $html = BanktransferController::parseHtml($bill, $content);

            $view->pdf = $html;

            $act = $view->fetch();
            $act = str_replace('Times New Roman', 'times', $act);
            $act = str_replace('&nbsp;', ' ', $act);

            $dompdf = new Dompdf();
            $dompdf->loadHtml($act);
            $dompdf->render();
            $pdf = $dompdf->output();

            $email->addAttachment('act_' . $id_bill . '.pdf', base64_encode($pdf));
        }
    }

    public function actionSetting(){
        $pconfig = new Config('banktransfer.module');


        if (Tools::rPOST()) {
            $pconfig->desc      = Tools::rPOST('desc');
            $pconfig->invoice   = Tools::rPOST('invoice');
            $pconfig->act_on    = Tools::rPOST('on-act', 0);
            $pconfig->act       = Tools::rPOST('act');
            $pconfig->currency  = Tools::rPOST('currency');
            $pconfig->save();
        }

        $view  = $this->getModuleView('setting.php', 'admin');
        $view->pconfig= $pconfig;
        $view->currencies = Currency::factory()->getRows();
        return $view;
    }


}