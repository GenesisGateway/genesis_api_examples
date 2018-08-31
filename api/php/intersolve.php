<?php
require '../../vendor/autoload.php';

use \Genesis\Genesis as Genesis;
use \Genesis\Config as GenesisConfig;

GenesisConfig::loadSettings('../../config/default.ini');

$genesis = new Genesis('Financial\GiftCards\Intersolve');

/**
 * @var \Genesis\API\Request\Financial\GiftCards\Intersolve $intersolveRequest
 */
$intersolveRequest = $genesis->request();

// Params
$intersolveRequest
    ->setTransactionId($_POST['transaction_id'])
    ->setUsage($_POST['usage'])
    ->setRemoteIp($_POST['remote_ip'])
    ->setCurrency($_POST['currency'])
    ->setAmount($_POST['amount'])
    ->setCardNumber($_POST['card_number'])
    ->setCvv($_POST['cvv'])
    ->setCustomerEmail($_POST['customer_email'])
    ->setCustomerPhone($_POST['customer_phone'])
 // Billing
    ->setBillingFirstName($_POST['billing_address']['first_name'])
    ->setBillingLastName($_POST['billing_address']['last_name'])
    ->setBillingAddress1($_POST['billing_address']['address1'])
    ->setBillingZipCode($_POST['billing_address']['zip_code'])
    ->setBillingCity($_POST['billing_address']['city'])
    ->setBillingCountry($_POST['billing_address']['country'])
// Shipping
    ->setShippingFirstName($_POST['shipping_address']['first_name'])
    ->setShippingLastName($_POST['shipping_address']['last_name'])
    ->setShippingAddress1($_POST['shipping_address']['address1'])
    ->setShippingZipCode($_POST['shipping_address']['zip_code'])
    ->setShippingCity($_POST['shipping_address']['city'])
    ->setShippingCountry($_POST['shipping_address']['country'])
// Descriptor
    ->setDynamicMerchantName($_POST['dynamic_descriptor']['merchant_name'])
    ->setDynamicMerchantCity($_POST['dynamic_descriptor']['merchant_city'])
// Reference id for split payments
    ->setReferenceId($_POST['reference_id']);

$output = null;

try {
    $genesis->execute();
    $output['request']  = $genesis->request()->getDocument();
    $output['response'] = $genesis->response()->getResponseRaw();
} catch (\Exception $e) {
    $output['response'] = $e->getMessage();
}

echo json_encode($output);

exit(0);
