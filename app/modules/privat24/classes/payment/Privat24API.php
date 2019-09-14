<?php
namespace modules\privat24\classes\payment;


use payment\PaymentAPI;
use System\Exception;
use System\Logger;

class Privat24API extends  PaymentAPI
{

    public function setFormAction(){
        $this->_form_action = 'https://api.privatbank.ua/p24api/ishop';
    }



    public function getFormValues()
    {
        $fields              = $this->getFields();
        $fields['signature'] = $this->getSign($fields);

        return $fields;
    }

    public function getFields()
    {
        $fields = array(
            'merchant' => $this->_shop['merchant'],
            'amt'      => $this->getAmountAsString(),
            'order'    => $this->getId(),
            'details'  => $this->getDescription(),
            'pay_way'  => 'privat24',
        );

        $return_url = $this->getSuccessUrl();
        $status_url = $this->getStatusUrl();
        $curr       = $this->getCurrency();


        $fields['ext_details'] = (string)$this->getBaggage();

        if ($return_url) {
            $fields['return_url'] = (string)$return_url;
        }

        if ($status_url) {
            $fields['server_url'] = (string)$status_url;
        }

        if ($curr)
            $fields['ccy'] = (string)$curr;

        return $fields;
    }

    public function getSign($fields)
    {

        unset($fields['signature']); //удаляем из данных строку подписи

        $fields_sort  = array('amt', 'ccy', 'details', 'ext_details', 'pay_way', 'order', 'merchant');
        $sign_paymnet = array();

        foreach ($fields_sort as $f) {
            if (isset($fields[$f])) {
                $sign_paymnet[] = $f . '=' . $fields[$f];
            }
        }

        $str_payment = implode('&', $sign_paymnet);
        $sign = sha1(md5($str_payment . $this->_shop['secret_key']));
        return $sign; // возвращаем результат

    }

    final protected function _checkSignature(array $source)
    {
        Logger::log('Privat24: sign = '. $source['signature'].'    '.sha1(md5($source['payment'] . $this->_shop['secret_key'])));

        return $source['signature'] === sha1(md5($source['payment'] . $this->_shop['secret_key']));
    }

    public function getPayment()
    {
        $source = $_POST;


        if (!$source || empty($source)) {
            throw new Exception('Source not exist');
        }

        if ($this->_checkSignature($source))
            $this->verified = true;
        else {
            throw new Exception('Signature does not match the data');
        }

        $payment = $source['payment'];
        $sign    = $source['signature'];

        $arr_payment = explode('&', $payment);
        $res         = array();

        foreach ($arr_payment as $p) {
            $r          = explode('=', $p);
            $res[$r[0]] = $r[1];
        }


        $received_id = strtoupper($res['merchant']);
        $shop_id     = strtoupper($this->_shop['merchant']);

        if ($received_id !== $shop_id) {
            throw new Exception('Received shop id does not match current shop id');
        }

        $this->_id = $res['order'];
        $this->_amount = $res['amt'];
        $this->_currency = $res['ccy'];
        $this->_description = $res['details'];
        $this->setBaggage('ext_details');
        $this->_state = $res['state'];

        return $this;

    }

}