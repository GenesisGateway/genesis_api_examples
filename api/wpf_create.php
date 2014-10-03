<?php
error_reporting(0);

require '../lib/vendor/autoload.php';

use \Genesis\Base as Genesis;
use \Genesis\Configuration as Config;

Config::loadSettings('/Users/petermanchev/Documents/Workspace/git/github/ldap/genesis_php/legacy/settings.ini');

Genesis::loadRequest('WPF\Create');

// Params
Genesis::Request()->setTransactionId($_POST['transaction_id']);
Genesis::Request()->setAmount($_POST['amount']);
Genesis::Request()->setCurrency($_POST['currency']);
Genesis::Request()->setUsage($_POST['usage']);
Genesis::Request()->setDescription($_POST['description']);
Genesis::Request()->setCustomerEmail($_POST['customer_email']);
Genesis::Request()->setCustomerPhone($_POST['customer_phone']);
// URLs
Genesis::Request()->setNotificationUrl($_POST['notification_url']);
Genesis::Request()->setReturnSuccessUrl($_POST['return_success_url']);
Genesis::Request()->setReturnFailureUrl($_POST['return_failure_url']);
Genesis::Request()->setReturnCancelUrl($_POST['return_cancel_url']);
// Billing
Genesis::Request()->setBillingFirstName($_POST['billing_address']['first_name']);
Genesis::Request()->setBillingLastName($_POST['billing_address']['last_name']);
Genesis::Request()->setBillingAddress1($_POST['billing_address']['address1']);
Genesis::Request()->setBillingZipCode($_POST['billing_address']['zip_code']);
Genesis::Request()->setBillingCity($_POST['billing_address']['city']);
Genesis::Request()->setBillingCountry($_POST['billing_address']['country']);
// Shipping
Genesis::Request()->setShippingFirstName($_POST['shipping_address']['first_name']);
Genesis::Request()->setShippingLastName($_POST['shipping_address']['last_name']);
Genesis::Request()->setShippingAddress1($_POST['shipping_address']['address1']);
Genesis::Request()->setShippingZipCode($_POST['shipping_address']['zip_code']);
Genesis::Request()->setShippingCity($_POST['shipping_address']['city']);
Genesis::Request()->setShippingCountry($_POST['shipping_address']['country']);
// Risk
Genesis::Request()->setRiskSsn($_POST['risk_params']['ssn']);
Genesis::Request()->setRiskMacAddress($_POST['risk_params']['mac_address']);
Genesis::Request()->setRiskSessionId($_POST['risk_params']['session_id']);
Genesis::Request()->setRiskUserId($_POST['risk_params']['user_id']);
Genesis::Request()->setRiskUserLevel($_POST['risk_params']['user_level']);
Genesis::Request()->setRiskEmail($_POST['risk_params']['email']);
Genesis::Request()->setRiskPhone($_POST['risk_params']['phone']);
Genesis::Request()->setRiskRemoteIp($_POST['risk_params']['remote_ip']);
Genesis::Request()->setRiskSerialNumber($_POST['risk_params']['serial_number']);

// Transaction Type
foreach ($_POST['transaction_type'] as $transaction_type) {
    Genesis::Request()->addTransactionType($transaction_type);
}

$output = array(
    'request'   => null,
    'response'  => null,
);

try
{
    $output['request']  = Genesis::Request()->getDocument();
    Genesis::Request()->Send();
    $output['response'] = Genesis::Request()->getGenesisResponse();
}
catch (\Exception $e)
{
    $output['response'] = $e->getMessage();
}

echo json_encode($output);
exit(0);