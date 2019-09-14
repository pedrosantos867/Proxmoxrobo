<?php
return array(
    'pay/([0-9]{1,})' => 'front|banktransfer|pay|id_bill=$1',
    'printpdf/([0-9]{1,})' => 'front|banktransfer|printpdf|id_bill=$1',
    'savepdf/([0-9]{1,})' => 'front|banktransfer|GetPdfInvoice|id_bill=$1',
    'saveactpdf/([0-9]{1,})' => 'front|banktransfer|GetPdfAct|id_bill=$1'
);