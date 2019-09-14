<?php

namespace modules\erip\classes\payment\erip;

use payment\PaymentAPI;
use System\Exception;
use System\Logger;

class EripAPI extends PaymentAPI
{


    public function setFormAction(){
        $this->_form_action = 'https://api.bepaid.by/beyag/payments';
    }



    public function getFormValues()
    {
        $fields            = array();

        return $fields;
    }



    public function getFields()
    {

    }

    public function sendPaymentRequest($data)
    {
        $data = array(
            'request' => array(
                'amount' => $this->_amount*100,
                'currency' => $this->getCurrency(),
                'description' => $this->getDescription(),
                'email' => $data['email'],
                'ip' => '127.0.0.1',
                'order_id' => $this->getId(),
                'notification_url' => $this->getStatusUrl(),
                'customer' => array(),
                'payment_method' => array(
                    'type' => 'erip',
                    'account_number' => $this->getId(),
                    'service_no' => $this->_shop['service_code'],
                    'service_info' => array( 'Оплата по счету ' . $this->getId() ),
                    'receipt' => array( 'Благодарим за оплату!' ),
                ),
            )
        );

        //echo '<pre>'; print_r($data); echo '</pre>';
        //echo '<pre>'; print_r($this->_shop); echo '</pre>';

        $data_string = json_encode($data);

        $ch = curl_init('https://api.bepaid.by/beyag/payments');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Basic '. base64_encode($this->_shop['shop_id'].":".$this->_shop['key']),
                'Content-Length: ' . strlen($data_string))
        );

        $result = curl_exec($ch);

        $data = json_decode($result,1);

       return $data;
    }

    public function getSign($fields)
    {
        $sign = '';
        return $sign; // возвращаем результат
    }

    final protected function _checkSignature(array $source)
    {

        Logger::log('Erip: '.json_encode($source));

        if($source['transaction']['payment']['status'] == 'successful'){
            return true;
        }

        return false;
    }

    public function getPayment()
    {


        $source = json_decode(file_get_contents('php://input'), 1);


        if (!$source || empty($source)) {
            throw new Exception('Source not exist');
        }

        if($_SERVER['PHP_AUTH_USER']  !=  $this->_shop['shop_id']){
            throw new Exception('Auth user not valid');
        }

        if($_SERVER['PHP_AUTH_PW']  !=  $this->_shop['key']){
            throw new Exception('Auth pw not valid');
        }

        if ($this->_checkSignature($source))
            $this->verified = true;
        else
            throw new Exception('Signature does not match the data');

        Logger::log('Erip _id: '.$source['transaction']['tracking_id']);
        Logger::log('Erip _amount: '.$source['transaction']['amount']);
        $this->_id          = $source['transaction']['tracking_id'];
        $this->_amount      = $source['transaction']['amount'];


        return $this;

    }

    public function getAmount()
    {
        return $this->_amount/100;
    }

    public function getAmountAsString($decimals = 2)
    {
        return number_format($this->_amount/100, $decimals, '.', '');
    }
}