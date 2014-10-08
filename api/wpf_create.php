<?php
error_reporting(0);

require '../lib/vendor/autoload.php';

use \Genesis\Base as Genesis;
use \Genesis\Configuration as Config;

Config::loadSettings('/Users/petermanchev/Documents/Workspace/git/github/ldap/genesis_php/legacy/settings.ini');

$genesis = new Genesis('WPF\Create');

// Params
$genesis->request()->setTransactionId($_POST['transaction_id']);
$genesis->request()->setAmount($_POST['amount']);
$genesis->request()->setCurrency($_POST['currency']);
$genesis->request()->setUsage($_POST['usage']);
$genesis->request()->setDescription($_POST['description']);
$genesis->request()->setCustomerEmail($_POST['customer_email']);
$genesis->request()->setCustomerPhone($_POST['customer_phone']);
// URLs
$genesis->request()->setNotificationUrl($_POST['notification_url']);
$genesis->request()->setReturnSuccessUrl($_POST['return_success_url']);
$genesis->request()->setReturnFailureUrl($_POST['return_failure_url']);
$genesis->request()->setReturnCancelUrl($_POST['return_cancel_url']);
// Billing
$genesis->request()->setBillingFirstName($_POST['billing_address']['first_name']);
$genesis->request()->setBillingLastName($_POST['billing_address']['last_name']);
$genesis->request()->setBillingAddress1($_POST['billing_address']['address1']);
$genesis->request()->setBillingZipCode($_POST['billing_address']['zip_code']);
$genesis->request()->setBillingCity($_POST['billing_address']['city']);
$genesis->request()->setBillingCountry($_POST['billing_address']['country']);
// Shipping
$genesis->request()->setShippingFirstName($_POST['shipping_address']['first_name']);
$genesis->request()->setShippingLastName($_POST['shipping_address']['last_name']);
$genesis->request()->setShippingAddress1($_POST['shipping_address']['address1']);
$genesis->request()->setShippingZipCode($_POST['shipping_address']['zip_code']);
$genesis->request()->setShippingCity($_POST['shipping_address']['city']);
$genesis->request()->setShippingCountry($_POST['shipping_address']['country']);
// Risk
$genesis->request()->setRiskSsn($_POST['risk_params']['ssn']);
$genesis->request()->setRiskMacAddress($_POST['risk_params']['mac_address']);
$genesis->request()->setRiskSessionId($_POST['risk_params']['session_id']);
$genesis->request()->setRiskUserId($_POST['risk_params']['user_id']);
$genesis->request()->setRiskUserLevel($_POST['risk_params']['user_level']);
$genesis->request()->setRiskEmail($_POST['risk_params']['email']);
$genesis->request()->setRiskPhone($_POST['risk_params']['phone']);
$genesis->request()->setRiskRemoteIp($_POST['risk_params']['remote_ip']);
$genesis->request()->setRiskSerialNumber($_POST['risk_params']['serial_number']);

// Transaction Type
foreach ($_POST['transaction_type'] as $transaction_type) {
    $genesis->request()->addTransactionType($transaction_type);
}

$output = null;

try
{
    $genesis->sendRequest();
    $output['request']  = $genesis->request()->getDocument();
    $output['response'] = $genesis->response()->getResponseRaw();
}
catch (\Exception $e)
{
    $output['response'] = $e->getMessage();
}

echo json_encode($output);
exit(0);