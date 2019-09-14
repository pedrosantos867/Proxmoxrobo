<?php

namespace sms;



class EmptySMS implements ISMS
{


    public function __construct()
    {

    }

    public function sendSMS($to, $text)
    {
        return true;
    }



} 