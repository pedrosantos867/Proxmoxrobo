<?php

namespace modules\paysera\classes\paysera;
use payment\PaymentAPI;
use System\Exception;
use System\Logger;
use System\Tools;

class PaySeraAPI extends  PaymentAPI
{
    public function setFormAction(){
        $this->_form_action = 'https://www.paysera.com/pay/';
    }



    public function getFormValues()
    {
        $fields              = $this->getFields();
        $fields['sign'] = $this->getSign($fields);

        return $fields;
    }

    public function getFields()
    {
        $data = array(
            'accepturl'    =>  $this->getSuccessUrl(),
            'amount'       =>  $this->getAmount()*100,
            'callbackurl'  =>  $this->getStatusUrl(),
            'cancelurl'    =>  $this->getFailUrl(),
            'currency'     => $this->getCurrency(),
            'orderid'      => $this->getId(),
            'paytext'      => $this->getDescription(),
            'projectid'    => $this->_shop['projectid'],
            'version'      => '1.6',
            'test'         => $this->_shop['test']
        );


        $fields['data'] = strtr(base64_encode(http_build_query($data, null, '&')), array('+' => '-', '/' => '_'));
        return $fields;
    }

    public function getSign($fields)
    {

        unset($fields['sign']); //удаляем из данных строку подписи

        $sign = md5($fields['data'] . $this->_shop['sign_password']);
        return $sign; // возвращаем результат

    }

    final protected function _checkSignature(array $source)
    {
        return md5($source['data'].$this->_shop['sign_password']) === $source['ss1'];
    }



    public function getPayment()
    {


        $source = $_GET;


        if (!$source || empty($source) || !isset($source['data'])) {
            throw new Exception('Source not exist');
        }


        if ($this->_checkSignature($source))
            $this->verified = true;
        else {
            throw new Exception('Signature does not match the data');
        }
        $params = array();
        if(Tools::rGET('data')) {
            parse_str(base64_decode(strtr($source['data'], array('-' => '+', '_' => '/'))), $params);
        }
        //print_r($params);
        if($params['projectid'] != $this->_shop['projectid']){
            throw new Exception('projectid is not equal');
        }


        $this->_id          = $params['orderid'];
        $this->_amount      = $params['amount']/100;
        $this->_currency    = $params['currency'];
        $this->_description = $params['paytext'];
        $this->_state       = $params['status'];

        return $this;

    }

}