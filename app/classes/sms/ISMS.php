<?php

namespace sms;

interface ISMS
{
    public function sendSMS($to, $text);

}