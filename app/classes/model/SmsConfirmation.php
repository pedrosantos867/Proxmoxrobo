<?php

namespace model;

use System\ObjectModel;

class SmsConfirmation extends ObjectModel
{
    const TYPE_REG = 0;
    protected static $table = 'sms_confirmations';
}