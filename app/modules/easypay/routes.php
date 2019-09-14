<?php
return array(
    'pay/([0-9]{1,})' => 'front|EasyPayStatus|pay|id_bill=$1',
    'status/([0-9]{1,})' => 'front|EasyPay|displayStatus|id_bill=$1'
);