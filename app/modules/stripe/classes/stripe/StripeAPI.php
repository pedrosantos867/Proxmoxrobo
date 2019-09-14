<?php

namespace modules\stripe\classes\stripe;

use payment\StripeAPI;
use System\Exception;
use System\Logger;
use System\Tools;

class Stripe extends PaymentAPI
{

    public function setFormAction()
    {
        return null;

    }


    public function getFormValues()
    {
        return null;
    }

    public function getFields()
    {
        return null;
    }

    public function getSign($fields)
    {
        return null;

    }

    final protected function _checkSignature(array $source)
    {
        return null;
    }


    public function createPayment(array $options)
    {


    }

    public function getPayment()
    {

    }


}