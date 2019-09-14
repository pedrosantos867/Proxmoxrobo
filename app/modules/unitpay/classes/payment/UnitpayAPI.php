<?php
namespace modules\unitpay\classes\payment;


use payment\PaymentAPI;
use System\Exception;

class UnitpayAPI extends PaymentAPI
{
    public function setFormAction(){
        $this->_form_action = 'https://unitpay.ru/pay/'.$this->_shop['public_key'];
    }

    public function getFields(){
        {
            $fields = array(
                'account'   => $this->getId(),
                'sum'       => $this->getAmountAsString(),
                'desc'      => $this->getDescription(),
            );
            $curr            = $this->getCurrency();
            if ($curr)
                $fields['currency'] = (string)$curr;

            return $fields;
        }
    }

    public function getSign($fields)
    {
        $hashStr = $fields['account'].'{up}'.$fields['currency'].'{up}'.$fields['desc'].'{up}'.$fields['sum'].'{up}'.$this->_shop['secret_key'];

        return hash('sha256', $hashStr);
    }

    final protected function _checkSignature(array $source)
    {
        $params = $source['params'];

        $sign = $params['signature'];

        ksort($params);
        unset($params['sign']);
        unset($params['signature']);
        array_push($params, $this->_shop['secret_key']);
        array_unshift($params, 'pay');

        return hash('sha256', join('{up}', $params)) === $sign;

    }

    public function getPayment()
    {
        $source = $_GET;


        if (!$source || empty($source)) {
            throw new Exception('Source not exist');
        }

        if ($this->_checkSignature($source))
            $this->verified = true;




        $params = $source['params'];

        $this->_id          = $params['account'];
        $this->_amount      = $params['orderSum'];
        $this->_currency    = $params['orderCurrency'];
        $this->_description = '';
        $this->_state       = $source['method'] == 'pay' ? 1 : 0;


        return $this;

    }
}