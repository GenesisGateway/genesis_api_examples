<?php
require '../../vendor/autoload.php';

use \Genesis\Genesis as Genesis;
use \Genesis\Config as GenesisConfig;

GenesisConfig::loadSettings('../../config/default.ini');

$genesis = new Genesis('Financial\Cards\Sale3D');

/**
 * @var \Genesis\API\Request\Financial\Cards\Sale3D $saleThreeDRequest
 */
$saleThreeDRequest = $genesis->request();

// Params
$saleThreeDRequest
    ->setTransactionId($_POST['transaction_id'])
    ->setUsage($_POST['usage'])
    ->setGaming(isset($_POST['gaming']) ? $_POST['gaming'] : '')
    ->setMoto(isset($_POST['moto']) ? $_POST['moto'] : '')
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
$saleThreeDRequest
    ->setBillingFirstName($_POST['billing_address']['first_name'])
    ->setBillingLastName($_POST['billing_address']['last_name'])
    ->setBillingAddress1($_POST['billing_address']['address1'])
    ->setBillingZipCode($_POST['billing_address']['zip_code'])
    ->setBillingCity($_POST['billing_address']['city'])
    ->setBillingCountry($_POST['billing_address']['country']);
// Shipping
$saleThreeDRequest
    ->setShippingFirstName($_POST['shipping_address']['first_name'])
    ->setShippingLastName($_POST['shipping_address']['last_name'])
    ->setShippingAddress1($_POST['shipping_address']['address1'])
    ->setShippingZipCode($_POST['shipping_address']['zip_code'])
    ->setShippingCity($_POST['shipping_address']['city'])
    ->setShippingCountry($_POST['shipping_address']['country']);
// MPI Sync
$saleThreeDRequest
    ->setNotificationUrl($_POST['notification_url'])
    ->setReturnSuccessUrl($_POST['return_success_url'])
    ->setReturnFailureUrl($_POST['return_failure_url']);
// MPI Async
$saleThreeDRequest
    ->setMpiCavv($_POST['mpi_params']['cavv'])
    ->setMpiEci($_POST['mpi_params']['eci'])
    ->setMpiXid($_POST['mpi_params']['xid']);
// Risk
$saleThreeDRequest
    ->setRiskSsn($_POST['risk_params']['ssn'])
    ->setRiskMacAddress($_POST['risk_params']['mac_address'])
    ->setRiskSessionId($_POST['risk_params']['session_id'])
    ->setRiskUserId($_POST['risk_params']['user_id'])
    ->setRiskUserLevel($_POST['risk_params']['user_level'])
    ->setRiskEmail($_POST['risk_params']['email'])
    ->setRiskPhone($_POST['risk_params']['phone'])
    ->setRiskRemoteIp($_POST['risk_params']['remote_ip'])
    ->setRiskSerialNumber($_POST['risk_params']['serial_number']);
// Descriptor
$saleThreeDRequest
    ->setDynamicMerchantName($_POST['dynamic_descriptor']['merchant_name'])
    ->setDynamicMerchantCity($_POST['dynamic_descriptor']['merchant_city']);

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
