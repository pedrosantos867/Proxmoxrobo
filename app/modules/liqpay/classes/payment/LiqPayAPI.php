<?php

namespace modules\liqpay\classes\payment;

use payment\PaymentAPI;
use System\Exception;

class LiqPayAPI extends PaymentAPI
{

    public function setFormAction(){
        $this->_form_action = 'https://www.liqpay.com/api/3/checkout';
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
            'data'         => base64_encode(json_encode(array(
                'version' => 3,
                'public_key' => $this->_shop['public_key'],
                'action' => 'pay',
                'amount' => $this->getAmount(),
                'currency' => $this->getCurrency(),
                'description' => $this->getDescription(),
                'order_id'  => $this->getId(),
                'server_url' => $this->getStatusUrl(),
                'result_url' => $this->getSuccessUrl()
            ))),
        );

        return $fields;
    }

    public function getSign($fields)
    {
        $sign = base64_encode( sha1( $this->_shop['private_key'] . $fields['data'] . $this->_shop['private_key'], 1 ) );
        return $sign;
    }

    final protected function _checkSignature(array $source)
    {
        $sign = base64_encode( sha1( $this->_shop['private_key'] . $source['data'] . $this->_shop['private_key'] , 1 ));
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
        $this->_state       = $data['status'];


        return $this;

    }
}