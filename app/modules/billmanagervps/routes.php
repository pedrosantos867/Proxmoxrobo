<?php
return array(
    'orders'                    => 'front|order|list',
    'order/new'                 => 'front|order|new',
    'order/prolong'                 => 'front|order|prolong',
    'order/info'                 => 'front|order|info',
    'order/plan/([0-9]{0,10})'  => 'front|order|plan|$1',


    'admin|plans'                => 'admin|plan|list',
    'admin|plans/edit'           => 'admin|plan|edit',
    'admin|plans/remove'         => 'admin|plan|remove',
    'admin|orders'               => 'admin|orders|list',
    'admin|order/info'           => 'admin|orders|info',
    'admin|order/remove' => 'admin|orders|remove',
    'cron' => 'front|cron|cron'
);