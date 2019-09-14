<?php

namespace payment;


use System\Exception;

abstract class PaymentAPI
{

    const METHOD_GET  = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_LINK = 'LINK';
    const METHOD_OFF  = 'OFF';


    const STATE_SUCCESS = 'success';
    const STATE_FAIL    = 'fail';


    const FEES_PAYER_SHOP  = 0;
    const FEES_PAYER_BUYER = 1;
    const FEES_PAYER_EQUAL = 2;


    protected $_shop;

    protected $_id;

    protected $_amount;

    protected $_description;

    protected $_baggage = false;

    protected $_success_url = false;
    protected $_fail_url = false;
    protected $_status_url = false;


    protected $_success_method  = PaymentAPI::METHOD_POST;
    protected $_fail_method     = PaymentAPI::METHOD_POST;
    protected $_status_method   = PaymentAPI::METHOD_POST;

    protected $_form_action;


    protected $_locale = 'ru';


    protected $_currency = false;

    protected $_state;

    public $verified = false;

    protected $_test = 0;

    public function __construct(array $shop)
    {
        $this->_shop = $shop;
    }

    abstract public function setFormAction();

    public function createPayment(array $options)
    {

        if (!isset($options['id'])) {
            throw new Exception('Payment id is required');
        }

        if (!isset($options['amount'])) {
            throw new Exception('Payment amount is required');
        }

        if (!isset($options['description'])) {
            throw new Exception('Payment description is required');
        }

        $this->_id          = (string)$options['id'];
        $this->_amount      = (float)$options['amount'];
        $this->_description = (string)$options['description'];


        if (!empty($options['baggage'])) {
            $this->setBaggage($options['baggage']);
        }

        if (isset($options['test_mode']) && !empty($options['test_mode'])) {
            $this->setTestMode((bool)$options['test_mode']);
        }

        if (!empty($options['success_url'])) {
            $this->setSuccessUrl($options['success_url']);
        }

        if (!empty($options['success_method'])) {
            $this->setSuccessMethod($options['success_method']);
        }

        if (!empty($options['fail_url'])) {
            $this->setFailUrl($options['fail_url']);
        }

        if (!empty($options['fail_method'])) {
            $this->setFailMethod($options['fail_method']);
        }

        if (!empty($options['status_url'])) {
            $this->setStatusUrl($options['status_url']);
        }

        if (!empty($options['status_method'])) {
            $this->setStatusMethod($options['status_method']);
        }


        $this->setFormAction();


        if (!empty($options['currency'])) {
            $this->setCurrency($options['currency']);
        }

        return $this;
    }

    abstract public function getFields();

    abstract public function getSign($fields);

    abstract protected function _checkSignature(array $source);

    abstract public function getPayment();


    public function getId()
    {
        return $this->_id;
    }
    public function getTestMode()
    {
        return (int)((bool)$this->_test);
    }
    public function getAmount()
    {
        return round($this->_amount, 2, PHP_ROUND_HALF_EVEN);

    }

    public function getAmountAsString($decimals = 2)
    {
        return number_format($this->_amount, $decimals, '.', '');
    }

    public static function convertAmountAsString($amount, $decimals=2){
        return number_format($amount, $decimals, '.', '');
    }

    public function getDescription()
    {
        return $this->_description;
    }


    public function setLocale($locale){

    }

    public function getLocale(){
        return $this->_locale;
    }

    public function getBaggage()
    {
        return $this->_baggage;
    }


    public function setBaggage($baggage)
    {
        if (!empty($baggage)) {
            $this->_baggage = (string)$baggage;
        }

        return $this;
    }

    public function getSuccessUrl()
    {
        return $this->_success_url;
    }

    public function setSuccessUrl($url)
    {
        if (!empty($url)) {
            $this->_success_url = (string)$url;
        }

        return $this;
    }

    public function getSuccessMethod()
    {
        return $this->_success_method;
    }

    public function setSuccessMethod($method)
    {
        if (empty($method)) {
            return $this;
        }

        $methods = array(
            PaymentAPI::METHOD_POST,
            PaymentAPI::METHOD_GET,
            PaymentAPI::METHOD_LINK
        );

        if (in_array($method, $methods)) {
            $this->_success_method = $method;
        }

        return $this;
    }

    public function getFailUrl()
    {
        return $this->_fail_url;
    }

    public function setFailUrl($url)
    {
        if (!empty($url)) {
            $this->_fail_url = (string)$url;
        }

        return $this;
    }

    public function getFailMethod()
    {
        return $this->_fail_method;
    }

    public function setFailMethod($method)
    {
        if (empty($method)) {
            return $this;
        }

        $methods = array(
            PaymentAPI::METHOD_POST,
            PaymentAPI::METHOD_GET,
            PaymentAPI::METHOD_LINK
        );

        if (in_array($method, $methods)) {
            $this->_fail_method = $method;
        }

        return $this;
    }

    public function getStatusUrl()
    {
        return $this->_status_url;
    }

    public function setStatusUrl($url)
    {
        if (!empty($url)) {
            $this->_status_url = (string)$url;
        }

        return $this;
    }


    public function getStatusMethod()
    {
        return $this->_status_method;
    }


    public function setStatusMethod($method)
    {
        if (empty($method))
            return $this;

        $methods = array(
            PaymentAPI::METHOD_POST,
            PaymentAPI::METHOD_GET,
            PaymentAPI::METHOD_OFF
        );

        if (in_array($method, $methods)) {
            $this->_status_method = $method;
        }

        return $this;
    }


    public function getFormValues()
    {
        $fields              = $this->getFields();
        $fields['sign']      = $this->getSign($fields);

        return $fields;
    }



    public function getFormAction()
    {
        return $this->_form_action;
    }


    public function setCurrency($currency)
    {
        $this->_currency = $currency;

        return $this;
    }

    public function getCurrency()
    {
        return $this->_currency;
    }

    private function setTestMode($test_mode)
    {
        $this->_test = $test_mode;
    }


}