<?php
require '../lib/vendor/autoload.php';

use \Genesis\Genesis as Genesis;
use \Genesis\GenesisConfig as GenesisConfig;

GenesisConfig::loadSettings('../config/default.ini');

$genesis = new Genesis('WPF\Create');

// Params
$genesis
	->request()
        ->setTransactionId($_POST['transaction_id'])
        ->setCurrency($_POST['currency'])
        ->setAmount($_POST['amount'])
        ->setUsage($_POST['usage'])
        ->setDescription($_POST['description'])
        ->setCustomerEmail($_POST['customer_email'])
        ->setCustomerPhone($_POST['customer_phone']);
// URLs
$genesis
	->request()
        ->setNotificationUrl($_POST['notification_url'])
        ->setReturnSuccessUrl($_POST['return_success_url'])
        ->setReturnFailureUrl($_POST['return_failure_url'])
        ->setReturnCancelUrl($_POST['return_cancel_url']);
// Billing
$genesis
	->request()
        ->setBillingFirstName($_POST['billing_address']['first_name'])
        ->setBillingLastName($_POST['billing_address']['last_name'])
        ->setBillingAddress1($_POST['billing_address']['address1'])
        ->setBillingZipCode($_POST['billing_address']['zip_code'])
        ->setBillingCity($_POST['billing_address']['city'])
        ->setBillingCountry($_POST['billing_address']['country']);
// Shipping
$genesis
	->request()
        ->setShippingFirstName($_POST['shipping_address']['first_name'])
        ->setShippingLastName($_POST['shipping_address']['last_name'])
        ->setShippingAddress1($_POST['shipping_address']['address1'])
        ->setShippingZipCode($_POST['shipping_address']['zip_code'])
        ->setShippingCity($_POST['shipping_address']['city'])
        ->setShippingCountry($_POST['shipping_address']['country']);
// Risk
$genesis
	->request()
        ->setRiskSsn($_POST['risk_params']['ssn'])
        ->setRiskMacAddress($_POST['risk_params']['mac_address'])
        ->setRiskSessionId($_POST['risk_params']['session_id'])
        ->setRiskUserId($_POST['risk_params']['user_id'])
        ->setRiskUserLevel($_POST['risk_params']['user_level'])
        ->setRiskEmail($_POST['risk_params']['email'])
        ->setRiskPhone($_POST['risk_params']['phone'])
        ->setRiskRemoteIp($_POST['risk_params']['remote_ip'])
        ->setRiskSerialNumber($_POST['risk_params']['serial_number']);

// Transaction Type
foreach ($_POST['transaction_type'] as $transaction_type) {
    $genesis->request()->addTransactionType($transaction_type);
}

$output = null;

try {
    $genesis->execute();
    $output['request']  = $genesis->request()->getDocument();
    $output['response'] = $genesis->response()->getResponseRaw();
}
catch (\Exception $e) {
    $output['response'] = $e->getMessage();
}

echo json_encode($output);
exit(0);