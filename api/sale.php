<?php
error_reporting(0);

require '../lib/vendor/autoload.php';

use \Genesis\Base as Genesis;
use \Genesis\Configuration as Config;

Config::loadSettings('/Users/petermanchev/Documents/Workspace/git/github/ldap/genesis_php/legacy/settings.ini');

$genesis = new Genesis('Financial\Sale');

// Params
$genesis->request()->setTransactionId($_POST['transaction_id']);
$genesis->request()->setUsage($_POST['usage']);
$genesis->request()->setGaming($_POST['gaming']);
$genesis->request()->setMoto($_POST['moto']);
$genesis->request()->setRemoteIp($_POST['remote_ip']);
$genesis->request()->setAmount($_POST['amount']);
$genesis->request()->setCurrency($_POST['currency']);
$genesis->request()->setCardHolder($_POST['card_holder']);
$genesis->request()->setCardNumber($_POST['card_number']);
$genesis->request()->setExpirationMonth($_POST['expiration_month']);
$genesis->request()->setExpirationYear($_POST['expiration_year']);
$genesis->request()->setCvv($_POST['cvv']);
$genesis->request()->setCustomerEmail($_POST['customer_email']);
$genesis->request()->setCustomerPhone($_POST['customer_phone']);
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
// Descriptor
$genesis->request()->setDynamicMerchantName($_POST['dynamic_descriptor']['merchant_name']);
$genesis->request()->setDynamicMerchantCity($_POST['dynamic_descriptor']['merchant_city']);

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