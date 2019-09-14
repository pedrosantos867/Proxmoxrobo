<?php

ini_set('display_errors', 'On');
return array(
    'pay/([0-9]{1,})' => 'front|tinkoff|pay|id_bill=$1',
    'success' => 'front|tinkoff|success',
    'callback' => 'front|tinkoffStatus|status',
    'cancel' => 'front|tinkoff|cancel'
);
?>


