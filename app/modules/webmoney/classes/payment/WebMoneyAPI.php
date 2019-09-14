<?php
namespace modules\webmoney\classes\payment;


use payment\PaymentAPI;
use System\Exception;


class WebMoneyAPI extends PaymentAPI
{
    public function setFormAction(){
        $this->_form_action = 'https://merchant.webmoney.ru/lmi/payment.asp';
    }

    public function getFields(){
            $fields = array(
                'LMI_PAYEE_PURSE'          => $this->_shop['purse'],
                'LMI_PAYMENT_AMOUNT'       => $this->getAmountAsString(),
                'LMI_PAYMENT_NO'           => $this->getId(),
                'LMI_PAYMENT_DESC_BASE64'  => base64_encode($this->getDescription()),
                'LMI_RESULT_URL'           => $this->getStatusUrl(),
                'LMI_SUCCESS_URL'          => $this->getSuccessUrl(),
                'LMI_SUCCESS_METHOD'       => $this->getSuccessMethod(),
                'LMI_FAIL_URL'             => $this->getFailUrl(),
                'LMI_FAIL_METHOD'          => $this->getFailMethod()
            );
            return $fields;
    }

    public function getFormValues()
    {
        $fields              = $this->getFields();
        $fields['LMI_PAYMENTFORM_SIGN']      = $this->getSign($fields);

        return $fields;
    }

    public function getSign($fields)
    {
        return hash('sha256', $fields['LMI_PAYEE_PURSE'].';'.$fields['LMI_PAYMENT_AMOUNT'].';'.$fields['LMI_PAYMENT_NO'].';'.$this->_shop['secret_keyx20'].';');
    }

    final protected function _checkSignature(array $source)
    {
        $hash = $source['LMI_HASH'];
        $fields = $source;

        return $hash === strtoupper(hash('sha256', $fields['LMI_PAYEE_PURSE'].$fields['LMI_PAYMENT_AMOUNT'].$fields['LMI_PAYMENT_NO'].$fields['LMI_MODE'].$fields['LMI_SYS_INVS_NO'].$fields['LMI_SYS_TRANS_NO'].$fields['LMI_SYS_TRANS_DATE'].$this->_shop['secret_key'].$fields['LMI_PAYER_PURSE'].$fields['LMI_PAYER_WM']));

    }

    public function getPayment()
    {

        $params = $_POST;

        if(isset($params['LMI_PREREQUEST']) && $params['LMI_PREREQUEST'] == 1){
            exit('YES');
        }

        if (!$params || empty($params)) {
            throw new Exception('Source not exist');
        }

        if($params['LMI_PAYEE_PURSE'] != $this->_shop['purse']){
            throw  new Exception('Purse not valid'.$this->_shop['purse'].' =! '.$_POST['LMI_PAYEE_PURSE']);
        }

        if ($this->_checkSignature($params))
            $this->verified = true;
        else
            throw new Exception('Signature not valid');


        $this->_id      = $params['LMI_PAYMENT_NO'];
        $this->_amount  = $params['LMI_PAYMENT_AMOUNT'];

        $this->_description = '';
        $this->_state = 1;

        return $this;

    }
}