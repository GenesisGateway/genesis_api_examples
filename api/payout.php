<?php
require '../lib/vendor/autoload.php';

use \Genesis\Base as Genesis;
use \Genesis\Configuration as GenesisConfig;

GenesisConfig::loadSettings('../config/default.ini');

$genesis = new Genesis('Financial\Payout');

$genesis->request()
            ->setTransactionId($_POST['transaction_id'])
            ->setUsage($_POST['usage'])
            ->setRemoteIp($_POST['remote_ip'])
			->setCurrency($_POST['currency'])
			->setAmount($_POST['amount'])
			->setCardHolder($_POST['card_holder'])
			->setCardNumber($_POST['card_number'])
			->setExpirationMonth($_POST['expiration_month'])
			->setExpirationYear($_POST['expiration_year'])
			->setCvv($_POST['cvv'])
			->setCustomerEmail($_POST['customer_email'])
			->setCustomerPhone($_POST['customer_phone']);
// Billing
$genesis->request()
	        ->setBillingFirstName($_POST['billing_address']['first_name'])
	        ->setBillingLastName($_POST['billing_address']['last_name'])
	        ->setBillingAddress1($_POST['billing_address']['address1'])
	        ->setBillingZipCode($_POST['billing_address']['zip_code'])
	        ->setBillingCity($_POST['billing_address']['city'])
	        ->setBillingCountry($_POST['billing_address']['country']);
// Shipping
$genesis->request()
	        ->setShippingFirstName($_POST['shipping_address']['first_name'])
	        ->setShippingLastName($_POST['shipping_address']['last_name'])
	        ->setShippingAddress1($_POST['shipping_address']['address1'])
	        ->setShippingZipCode($_POST['shipping_address']['zip_code'])
	        ->setShippingCity($_POST['shipping_address']['city'])
	        ->setShippingCountry($_POST['shipping_address']['country']);

$output = null;

try {
    $genesis->sendRequest();
    $output['request']  = $genesis->request()->getDocument();
    $output['response'] = $genesis->response()->getResponseRaw();
}
catch (\Exception $e) {
    $output['response'] = $e->getMessage();
}

echo json_encode($output);
exit(0);