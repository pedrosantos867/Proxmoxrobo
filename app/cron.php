#!/usr/bin/php -q
<?php

if (function_exists('php_sapi_name') && php_sapi_name() != 'cli') {
    echo 'cli not defined';
    die();
}

require(dirname(__FILE__) . '/../core/setting/setting.php');
$cron = \System\ControllerFactory::getController('CronController');
$cron->run(true);