<?php
/**
 * HostControl REST API PHP Sample
 * This requires curl-support for php
 *
 * POST order
 *
 * Create a new order with a domain for a specified customer
 *
 * Please be aware that this is not production code.
 *
 * You have to take care of data-validation yourself
 * and present the result to the user.
 */

error_reporting(E_ALL);
ini_set('display_errors', 'on');

require_once 'hostcontrol_api_client/php/base.php';

$hostcontrol_api_url = "https://backoffice.hostcontrol.com/api/v1";
$hostcontrol_api_key = "get-your-api-key-from-the-reseller-area";

$hc_api_client = new HostControlAPIClient($hostcontrol_api_url, $hostcontrol_api_key);

// Collect some customer information
$customer_id = 2826;
$domain = "my-new-domain-from-the-api.com";

try
{
    $hostcontrol_order = $hc_api_client->order->order_domain($customer_id, $domain);

    echo "<h1>Success!</h1>";
    echo "<pre>";
    var_dump($hostcontrol_order);
}
catch(HostControlAPIClientError $e)
{
    die("Could not create order because: " . $e->getMessage());
}
