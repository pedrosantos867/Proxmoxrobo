<?php
/**
 * Created by PhpStorm.
 * User: Viktor
 * Date: 10.06.15
 * Time: 20:52
 */

namespace model;


use System\Config;
use System\Cookie;
use System\ObjectModel;

class Currency extends ObjectModel
{


    protected static $table = 'currencies';

    const SERVER_PRIVAT24 = 1;
    const SERVER_CBR = 2;
    const SERVER_NBU = 3;
    const SERVER_ECB = 4;

    public static function convert($total, $currency_id)
    {
        $currency = new self($currency_id);
        if($currency->isLoadedObject()){
            return $total * $currency->coefficient;
        }
    }

    public static function getDefault()
    {
        $config = Config::factory();
        return new self($config->currency_default);
    }


    public function getPrice($default_price)
    {
        return round($default_price * $this->coefficient, 2, PHP_ROUND_HALF_EVEN);
    }

    public function convertToDefault($summ)
    {
        return $summ / $this->coefficient;
    }

    public function displayPrice($summ)
    {
        if (strpos($this->short_name, '{0}') !== false){
                return str_replace('{0}', $this->getPrice($summ), $this->short_name);
        } else {
            return $this->getPrice($summ).' '. $this->short_name;
        }
    }

    public function displayName(){
        return str_replace('{0}', '', $this->short_name);
    }

    public static function updateCurses()
    {
        $config   = new Config();
        $currency = new Currency($config->currency_default);
        if ($currency->iso == 'UAH') {
            if ($config->currency_server == self::SERVER_PRIVAT24) {
                $curses = simplexml_load_string(file_get_contents('https://api.privatbank.ua/p24api/pubinfo?exchange&coursid=5'));
                foreach ($curses->row as $curs) {

                    $currency = new Currency();
                    $currency = new Currency($currency->where('iso', $curs->exchangerate->attributes()->ccy)->getRow());

                    if ($currency->isLoadedObject()) {
                        $currency->coefficient = 1 / floatval($curs->exchangerate->attributes()->buy);
                        $currency->save();
                    }
                    //echo $curs->attributes()->ccy . ' == ' . $curs->attributes()->unit * 10000 / $curs->attributes()->buy . ' || ';
                }
            } else if ($config->currency_server == self::SERVER_NBU) {
                $curses = simplexml_load_string(file_get_contents('https://bank.gov.ua/NBUStatService/v1/statdirectory/exchange'));
                foreach ($curses->currency as $curs) {
                    $currency = new Currency();
                    $currency = new Currency($currency->where('iso', $curs->cc)->getRow());

                    if ($currency->isLoadedObject()) {
                        $currency->coefficient = 1 / (floatval($curs->rate));
                        $currency->save();
                    }
                }


            }
        } else if ($currency->iso == 'RUR') {

            $curses = simplexml_load_string(file_get_contents('https://www.cbr.ru/scripts/XML_daily.asp'));
            foreach ($curses->Valute as $valute) {
                $currency = new Currency();
                $currency = new Currency($currency->where('iso', $valute->CharCode)->getRow());

                if ($currency->isLoadedObject()) {
                    $currency->coefficient = floatval(str_replace(',', '.', $valute->Value));
                    $currency->save();
                }
            }

        } else if ($currency->iso == 'EUR') {
            if ($config->currency_server == self::SERVER_ECB) {

                $curses = simplexml_load_string(file_get_contents('http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml'));

                foreach ($curses->Cube->Cube->Cube as $curs) {
                    $currency = new Currency();
                    $currency = new Currency($currency->where('iso', $curs->attributes()->currency)->getRow());

                    if ($currency->isLoadedObject()) {
                        $currency->coefficient = (floatval($curs->attributes()->rate));
                        $currency->save();
                    }
                }
            }

        } else {
            return false;
        }
        //   $curses = simplexml_load_string(file_get_contents('https://privat24.privatbank.ua/p24/accountorder?oper=prp&PUREXML&apicour&country=ua&full'));
        // print_r($curses);


        return true;
    }


} 