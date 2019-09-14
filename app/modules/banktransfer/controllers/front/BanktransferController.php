<?php

namespace modules\banktransfer\controllers\front;

//include_once ('simple_html_dom.php');

use Dompdf\Dompdf;
use front\ModuleFrontController;
use model\Bill;
use model\Client;
use model\ClientInfo;
use model\Currency;

use System\Config;
use System\Path;
use System\Router;

class BanktransferController extends ModuleFrontController{
    public function actionPay(){
        $id_bill = Router::getParam('id_bill');
        $billObject = new Bill($id_bill);
        $this->checkAccess($billObject);

        $view = $this->getModuleView('bill/generate.php');
        $view->id_bill = $id_bill;
        $view->bankconfig = new Config('banktransfer.module');
        $config = new Config('banktransfer.module');

        $this->layout->import('content', $view);

    }

    public function actionGetPdfInvoice(){
        $id_bill = Router::getParam('id_bill');
        $view = $this->getModuleView('bill/pdf.php');
        $view->id_bill = $id_bill;
        $Bill = new Bill($id_bill);
        $this->checkAccess($Bill);

        $config = new Config('banktransfer.module');
        $html = $this->parseHtml($Bill, $config->invoice);
        $view->pdf = $html;
        include_once (__DIR__.'/../../dompdf/autoload.inc.php');

        $html = $view->fetch();
        $html = str_replace('Times New Roman', 'times', $html);
        $html = str_replace('&nbsp;', ' ', $html);
        $html = htmlentities($html, null, 'utf-8');
        $html = str_replace("&nbsp;", "", $html);
        $html = html_entity_decode($html);
        //  echo $html;

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);

        // $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('invoice_' . $id_bill . '');
    }

    public function actionGetPdfAct()
    {
        $id_bill = Router::getParam('id_bill');
        $view = $this->getModuleView('bill/pdf.php');
        $view->id_bill = $id_bill;
        $Bill = new Bill($id_bill);
        $this->checkAccess($Bill);


        $config = new Config('banktransfer.module');
        $html = $this->parseHtml($Bill, $config->act);
        $view->pdf = $html;
        include_once(__DIR__ . '/../../dompdf/autoload.inc.php');

        $html = $view->fetch();
        $html = str_replace('Times New Roman', 'times', $html);
        $html = str_replace('&nbsp;', ' ', $html);
        $html = htmlentities($html, null, 'utf-8');
        $html = str_replace("&nbsp;", "", $html);
        $html = html_entity_decode($html);
        //  echo $html;

        $dompdf = new Dompdf( );
        $dompdf->loadHtml($html);

        // $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('act_' . $id_bill . '');
    }

    public function actionGetInvoiceAjax(){
        $id_bill = Router::getParam('id_bill');
        $view = $this->getModuleView('bill/html.php');
        $view->id_bill = $id_bill;
        $Bill = new Bill($id_bill);
        $this->checkAccess($Bill);

        $config = new Config('banktransfer.module');
        $html = $this->parseHtml($Bill, $config->invoice);
        $view->pdf = $html;


        echo json_encode(['result' =>1, 'content' => $view->fetch()]);

    }

    public function actionPrintpdf(){
        $id_bill = Router::getParam('id_bill');
        $view = $this->getModuleView('bill/pdf.php');
        $view->id_bill = $id_bill;
        $Bill = new Bill($id_bill);
        $this->checkAccess($Bill);

        $config = new Config('banktransfer.module');
        $html = $this->parseHtml($Bill,$config->invoice);



        $view->pdf = $html;
        $this->layout->import('content', $view);
    }

    public static function parseHtml($Bill, $content){
        $bills = array();

        if ($Bill->type == Bill::TYPE_INC) {
            $bs = explode('|', $Bill->inc);
            foreach ($bs as $bl) {
                $bills[] = new Bill($bl);
            }
        } else {
            $bills[] = $Bill;
        }
        $invoices = '';
        foreach ($bills as $bill) {
            $invoices[] = $bill->id;
        }
        $invoices = implode(',', $invoices);

        $config = new Config('banktransfer.module');



        /*
                $html = new simple_html_dom();
                $html->load($content);
                $tables = $html->find('table');
                $rows = $tables[0]->find('tr');
                $row = end($rows);
                $row_tr = $row->outertext;
                $row->outertext = '';

                foreach ($bills as $b) {
                    $new_tr = $row_tr;
                    $new_tr  = str_replace('{{id}}', $b->id, $new_tr  );
                    $new_tr  = str_replace('{{count}}', $b->pay_period, $new_tr  );
                    $new_tr  = str_replace('{{price}}', $b->total, $new_tr  );
                    $new_tr  = str_replace('{{sum}}', $b->total, $new_tr  );
                    $row->outertext .= $new_tr;
                }

        */



        $Currency = new Currency($config->currency);

        $Client = new Client($Bill->client_id);
        $html = $content;

        $html  = str_replace('{invoice}', $Bill->id, $html  );
        $html  = str_replace('{invoices}', $invoices, $html  );
        $html  = str_replace('{date}', date('d.m.Y', strtotime($Bill->date)), $html  );
        $html = str_replace('{sum}', number_format($Currency->getPrice($Bill->total), 2), $html);
        $html  = str_replace('{sum_str}', self::num2str($Currency->getPrice($Bill->total), $Currency->iso), $html  );

        /*price calculate*/
        $matches = null;
        preg_match_all('/{sum([0-9+-\/*\.]{0,})}/', $html, $matches  );
        $all_matches = isset($matches[0]) ? $matches[0] : array();
        $all_replace = ($matches[1]) ? $matches[1] : array();

        if(count($all_matches) === count($all_replace)) {
            foreach ($all_matches as $i => $m) {
                $expression = ($all_replace[$i]);
                $res = self::calc($Currency->getPrice($Bill->total).$expression);
                $html = str_replace($m, $res, $html);
            }
        }

        /*Date calculate*/
        $matches = null;
        preg_match_all('/{date([a-z0-9\s+]{0,})}/', $html, $matches  );
        $all_matches = isset($matches[0]) ? $matches[0] : array();
        $all_replace = ($matches[1]) ? $matches[1] : array();
        if(count($all_matches) === count($all_replace)) {
            foreach ($all_matches as $i => $m) {
                $Date = new \DateTime($Bill->date);
                $add = ($all_replace[$i]);
                $date = $Date->add(\DateInterval::createFromDateString($add))->format('d.m.Y');

                $html = str_replace($m, $date, $html);
            }
        }



        $location_address = $Client->organization_location_address ? $Client->organization_location_address : $Client->organization_address;


        $html  = str_replace('{customer.name}', $Client->name, $html  );
        $html  = str_replace('{customer.organization_name}', $Client->organization_name, $html  );
        $html = str_replace('{customer.organization_chief}', $Client->organization_chief ? $Client->organization_chief : $Client->name, $html);

        $html  = str_replace('{customer.phone}', $Client->phone, $html  );
        $html  = str_replace('{customer.email}', $Client->email, $html  );

        $html  = str_replace('{customer.address}', $Client->organization_address, $html  );
        $html  = str_replace('{customer.location_address}', $location_address, $html  );


        $html  = str_replace('{customer.organization_number}', $Client->organization_number, $html  );
        $html  = str_replace('{customer.organization_ipn}', $Client->organization_ipn, $html  );

        return $html;
    }

    private static function calc($expression){
        $ma = $expression;
        $p = 1;
        if(preg_match('/(.+)(?:\s*)([\+\-\*\/])(?:\s*)(.+)/', $ma, $matches) !== FALSE){
            $operator = $matches[2];

            if(!is_numeric($matches[1]) || !is_numeric($matches[3])){
                return 0 ;
            }
            switch($operator){
                case '+':
                    $p = $matches[1] + $matches[3];
                    break;
                case '-':
                    $p = $matches[1] - $matches[3];
                    break;
                case '*':

                    $p = $matches[1] * $matches[3];

                    break;
                case '/':
                    $p = $matches[1] / $matches[3];
                    break;
            }


        }
        return $p;
    }

    /**
     * Возвращает сумму прописью
     * @author runcore
     * @uses morph(...)
     */
    private static function num2str($num, $currency = 'RUB') {

        $currency_data = array();
        $currency = strtolower($currency);
        if(file_exists(Path::getRoot('app/modules/banktransfer/currencies/'.$currency.'.php'))){
            $currency_data = include(Path::getRoot('app/modules/banktransfer/currencies/' . strtolower($currency) . '.php'));
        } else {
            $currency_data =  include (Path::getRoot('app/modules/banktransfer/currencies/rub.php'));
        }

        $nul = $currency_data['nul'];
        $ten = $currency_data['ten'];
        $a20 = $currency_data['a20'];
        $tens = $currency_data['tens'];
        $hundred = $currency_data['hundred'];

        $unit = $currency_data['unit'];



        //
        list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
        $out = array();
        if (intval($rub)>0) {
            foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
                if (!intval($v)) continue;
                $uk = sizeof($unit)-$uk-1; // unit key
                $gender = $unit[$uk][3];
                list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
                // mega-logic
                $out[] = $hundred[$i1]; # 1xx-9xx
                if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
                else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
                // units without rub & kop
                if ($uk>1) $out[]= self::morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
            } //foreach
        }
        else $out[] = $nul;
        $out[] = self::morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
        $out[] = $kop.' '.self::morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
        return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));



    }
    /**
     * Склоняем словоформу
     * @ author runcore
     */
    static function morph($n, $f1, $f2, $f5) {
        $n = abs(intval($n)) % 100;
        if ($n>10 && $n<20) return $f5;
        $n = $n % 10;
        if ($n>1 && $n<5) return $f2;
        if ($n==1) return $f1;
        return $f5;
    }

}