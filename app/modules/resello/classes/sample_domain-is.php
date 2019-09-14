<?php
/**
 * HostControl REST API PHP Sample
 * This requires curl-support for php
 *
 * POST domain-is
 *
 * Please be aware that this is not production code.
 * You have to take care of data-validation yourself
 * and present the result to the user.
 */

error_reporting(E_ALL);
ini_set('display_errors', 'on');

require_once 'hostcontrol_api_client/php/base.php';

$hostcontrol_api_url = "https://backoffice.hostcontrol.com/api/v1";
$hostcontrol_api_key = "get-your-api-key-from-the-reseller-area";

$hc_api_client = new HostControlAPIClient($hostcontrol_api_url, $hostcontrol_api_key);

$domain = "hostcontrol-sample.com";


try
{
    $result = $hc_api_client->domain->check($domain);
    echo "<h1>Success!</h1>";
    echo "<pre>";
    var_dump($result);
}
catch(HostControlAPIClientError $e)
{
    die("Exception during whois-check: " . $e->getMessage());
}