<?php
return array(
    'pay/([0-9]{1,})' => 'front|paypal|pay|id_bill=$1',
    'return' => 'front|paypal|return',
    'cancel' => 'front|paypal|cancel'
);