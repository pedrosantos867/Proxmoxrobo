<?php
namespace modules\cryptonator\classes\payment;

use payment\PaymentAPI;
use System\Exception;

class CryptonatorAPI extends PaymentAPI
{

    public function setFormAction(){
        $this->_form_action = 'https://api.cryptonator.com/api/merchant/v1/startpayment';
    }

    public function getFields()
    {
        $fields = array(
            'merchant_id'           => $this->_shop['merchant_id'],
            'item_name'             => $this->_shop['item_name'],
            'order_id'              => $this->getId(),
            'item_description'      => $this->getDescription(),
            'invoice_amount'        => $this->getAmount(),
            'invoice_currency'      => mb_strtolower($this->getCurrency()),
            'success_url'           => $this->getSuccessUrl(),
            'failed_url'            => $this->getFailUrl(),
            'language'              => $this->getLocale()
        );
		
        return $fields;
    }

    public function getSign($fields)
    {
        return '';
    }

    private function getHash($fields)
    {
        return sha1(implode('&', $fields) . '&' . $this->_shop['secret']);
    }

    final protected function _checkSignature(array $source)
    {
        $hash = $source['secret_hash'];
        unset($source['secret_hash']);
		
		if ($source['merchant_id']      != $this->_shop['merchant_id'] ||
			$source['order_id']         != $this->getId() ||
            $source['invoice_currency'] != mb_strtolower($this->getCurrency()) ||
            $source['invoice_amount']   != $this->getAmount())
			return false;
		
        return $this->getHash($source) === $hash;
    }

    public function getPayment()
    {
        $source = $_REQUEST;

        switch ($source['invoice_status'])
        {
            default:
            case 'unpaid':
            case 'confirming':
            case 'mispaid':
                break;

            case 'cancelled':
                // TODO
                break;

            case 'paid':
                if ($this->_checkSignature($source))
                    $this->verified = true;
                else
                    throw new Exception('Signature does not match the data');
                break;
        }
		
        $this->_id          = $source['merchant_id'];
        $this->_amount      = $source['invoice_amount'];
        $this->_currency    = $source['invoice_currency'];
		
        return $this;
    }
}