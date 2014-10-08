<?php
error_reporting(0);

require '../lib/vendor/autoload.php';

use \Genesis\Base as Genesis;
use \Genesis\Configuration as Config;

Config::loadSettings('/Users/petermanchev/Documents/Workspace/git/github/ldap/genesis_php/legacy/settings.ini');

Genesis::loadRequest('Financial\Authorize');

$genesis->Config->loadSettings();

$genesis->Request
	->setTransaction()
	->setTest()
	->setData()
	->setArae();

// Params
Genesis::Request()->setTransactionId($_POST['transaction_id']);
Genesis::Request()->setUsage($_POST['usage']);
Genesis::Request()->setGaming($_POST['gaming']);
Genesis::Request()->setMoto($_POST['moto']);
Genesis::Request()->setRemoteIp($_POST['remote_ip']);
Genesis::Request()->setAmount($_POST['amount']);
Genesis::Request()->setCurrency($_POST['currency']);
Genesis::Request()->setCardHolder($_POST['card_holder']);
Genesis::Request()->setCardNumber($_POST['card_number']);
Genesis::Request()->setExpirationMonth($_POST['expiration_month']);
Genesis::Request()->setExpirationYear($_POST['expiration_year']);
Genesis::Request()->setCvv($_POST['cvv']);
Genesis::Request()->setCustomerEmail($_POST['customer_email']);
Genesis::Request()->setCustomerPhone($_POST['customer_phone']);
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
// Descriptor
Genesis::Request()->setDynamicMerchantName($_POST['dynamic_descriptor']['merchant_name']);
Genesis::Request()->setDynamicMerchantCity($_POST['dynamic_descriptor']['merchant_city']);

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