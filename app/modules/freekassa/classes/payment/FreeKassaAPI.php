<?php

namespace modules\freekassa\classes\payment;

use payment\PaymentAPI;
use System\Exception;
use System\Logger;

class FreeKassaAPI extends PaymentAPI
{

    public function setFormAction(){
        $this->_form_action = 'http://www.free-kassa.ru/merchant/cash.php';
    }

    public function getFormValues()
    {
        $fields            = $this->getFields();
        $fields['s']       = $this->getSign($fields);

        return $fields;
    }

    public function getFields()
    {
        $fields = array(
            'm'         => $this->_shop['shop_id'],
            'oa'        => $this->getAmountAsString(),
            'o'         => $this->getId()
        );

        $locale = $this->getLocale();
        if ($locale)
            $fields['lang'] = $locale;
/*
        if($this->getCurrency() == 'usd'){
            $curr = 2;
        } elseif($this->getCurrency() == 'rub'){
            $curr = 1;
        } elseif($this->getCurrency() == 'eur'){
            $curr = 3;
        } else {
            $curr = 133;
        }

        $fields['i'] = $curr;
*/
        return $fields;
    }

    public function getSign($fields)
    {
        $sign = md5($fields['m'].':'.$fields['oa'].':'.$this->_shop['secret_key'].':'.$fields['o']);
        return $sign; // возвращаем результат
    }

    final protected function _checkSignature(array $source)
    {

        Logger::log('FreeKassa signature argument: '.$this->_shop['shop_id'].':'.$source['AMOUNT'].':'.$this->_shop['secret_key'].':'.$source['MERCHANT_ORDER_ID']);
        Logger::log(json_encode($source));
        $sign = md5($this->_shop['shop_id'].':'.$source['AMOUNT'].':'.$this->_shop['secret_key2'].':'.$source['MERCHANT_ORDER_ID']);
        if ($sign === $source['SIGN']) {
            return true;
        }
        return false;
    }

    public function getPayment()
    {
        $source = $_REQUEST;

        if (!$source || empty($source)) {
            throw new Exception('Source not exist');
        }

        if ($this->_checkSignature($source))
            $this->verified = true;
        else
            throw new Exception('Signature does not match the data');

        $this->_id          = $source['MERCHANT_ORDER_ID'];
        $this->_amount      = $source['AMOUNT'];


        return $this;

    }
}