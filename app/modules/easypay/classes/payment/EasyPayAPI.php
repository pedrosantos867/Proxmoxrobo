<?php

namespace modules\easypay\classes\payment;

use payment\PaymentAPI;
use System\Exception;

class EasyPayAPI extends PaymentAPI
{

    public function setFormAction(){
        $this->_form_action = 'https://easypay.ua/merchant/2_0/order';
    }

    public function getFormValues()
    {
        $fields            = $this->getFields();
        $fields['signature']       = $this->getSign($fields);

        return $fields;
    }

    public function getFields()
    {
        $fields = array(
            'merchant_id'  => $this->_shop['merchant_id'],
            'order_id'     => $this->getId(),
            'lang'         => $this->getLocale(),
            'desc'         => $this->getDescription(),
            'url_success'  => $this->getSuccessUrl(),
            'amount'       => $this->getAmount()
        );

        return $fields;
    }

    public function getSign($fields)
    {
        $sign = base64_encode( sha1( $this->_shop['secret_key'] . $this->_shop['merchant_id']. $fields['order_id'] . $fields['amount'] . $fields['desc'] . $fields['url_success'] . $fields['lang'], 1 ) );
        return $sign;
    }

    final protected function _checkSignature(array $source)
    {
        $sign = '';//base64_encode( sha1( $this->_shop['private_key'] . $source['data'] . $this->_shop['private_key'] , 1 ));
        return $source['signature'] === $sign;
    }

    public function getPayment()
    {
        $source = $_REQUEST;

        if ($this->_checkSignature($source))
            $this->verified = true;
        else
            throw new Exception('Signature does not match the data');

        $data = json_decode(base64_decode($source['data']), 1);

        if($data['status'] != 'success'){
            throw new Exception('Status not success');
        }


        $this->_id          = $data['order_id'];
        $this->_amount      = $data['amount'];



        return $this;

    }
}