<?php
/**
 * HostControl REST API PHP Sample
 * This requires curl-support for php
 *
 * POST customer
 *
 * This call creates a new customer in the HostControl Backoffice
 * The returnvalue of this call contains the customer ID. Please
 * save this id for future calls (such as registering domains)
 *
 * E-mail addresses have to be unique!
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
$customer_info = array(
    'name'              => 'Full Customer Name',
    'address'           => 'Customer Address 12345',
    'zipcode'           => '8000AA',
    'city'              => 'Zwolle',
    'state'             => 'Overijssel',
    'country'           => 'NL',
    'voice'             => '+31.381234567',
    'password'          => 'S3cur3PassWord',
    'email'             => 'john@doe.com',
    'registration_ip'   => '212.131.141.151',
);

try
{
    $hostcontrol_customer = $hc_api_client->customer->create($customer_info);
    $hostcontrol_customer_id = $hostcontrol_customer->id;

    /* TODO: Save $hostcontrol_customer_id for future calls! */
    echo "<h1>Success!</h1>";
    echo "<pre>";
    var_dump($hostcontrol_customer);
}
catch(HostControlAPIClientError $e)
{
    die("Could not create customer because: " . $e->getMessage());
}
