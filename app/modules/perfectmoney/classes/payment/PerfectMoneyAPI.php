<?php

namespace modules\perfectmoney\classes\payment;

use payment\PaymentAPI;
use System\Exception;

class PerfectMoneyAPI extends PaymentAPI
{

    public function setFormAction(){
        $this->_form_action = 'https://perfectmoney.is/api/step1.asp';
    }

    public function getFields()
    {
        $fields = array(
            'PAYEE_ACCOUNT'          => $this->_shop['payee_account'],
            'PAYEE_NAME'             => $this->_shop['payee_name'],
			'SUGGESTED_MEMO'         => $this->getDescription(),
			'PAYMENT_ID'             => $this->getId(),
			'PAYMENT_AMOUNT'         => $this->getAmount(),
			'PAYMENT_UNITS'          => $this->getCurrency(),
			'STATUS_URL'             => $this->getStatusUrl(),
            'PAYMENT_URL'            => $this->getSuccessUrl(),
			'PAYMENT_URL_METHOD'     => $this->getSuccessMethod(),
			'NOPAYMENT_URL'          => $this->getFailUrl(),
			'NOPAYMENT_URL_METHOD'   => $this->getFailMethod(),
			'INTERFACE_LANGUAGE'     => $this->getLocale()
        );
		
        return $fields;
    }

    public function getSign($fields)
    {
        return '';
    }

    final protected function _checkSignature(array $source)
    {
        $hash_values = array(
            $source['PAYMENT_ID'],
            $source['PAYEE_ACCOUNT'],
            $source['PAYMENT_AMOUNT'],
            $source['PAYMENT_UNITS'],
            $source['PAYMENT_BATCH_NUM'],
            $source['PAYER_ACCOUNT'],
            strtoupper(md5($this->_shop['alternate_passphrase'])),
            $source['TIMESTAMPGMT']
        );

        $our_key = strtoupper(md5(join(':', $hash_values)));
        $their_key = $source['V2_HASH'];

        if($our_key != $their_key ||
            $source['PAYMENT_AMOUNT'] != $this->getAmount() ||
            $source['PAYMENT_UNITS']  != $this->getCurrency() ||
            $source['PAYEE_ACCOUNT']  != $this->_shop['payee_account'])
        {
            return false;
        }

        return true;
    }

    public function getPayment()
    {
        $source = $_REQUEST;

        if ($this->_checkSignature($source))
            $this->verified = true;
        else
            throw new Exception('Signature does not match the data');

        $this->_id          = $source['PAYMENT_ID'];
        $this->_amount      = $source['PAYMENT_AMOUNT'];
		
        return $this;
    }
}