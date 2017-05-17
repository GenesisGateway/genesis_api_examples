<?php
require '../../vendor/autoload.php';

use \Genesis\Genesis as Genesis;
use \Genesis\Config as GenesisConfig;

GenesisConfig::loadSettings('../../config/default.ini');

$genesis = new Genesis('Financial\OnlineBankingPayments\Citadel\Payout');

/** @var \Genesis\API\Request\Financial\OnlineBankingPayments\Citadel\Payout $citadelPayoutRequest */
$citadelPayoutRequest = $genesis->request();

$citadelPayoutRequest
    ->setTransactionId($_POST['transaction_id'])
    ->setUsage($_POST['usage'])
    ->setRemoteIp($_POST['remote_ip'])
    ->setCurrency($_POST['currency'])
    ->setAmount($_POST['amount'])
    ->setCustomerEmail($_POST['customer_email'])
    ->setCustomerPhone($_POST['customer_phone'])
    ->setHolderName($_POST['holder_name'])
    ->setIban($_POST['iban'])
    ->setSwiftCode($_POST['swift_code'])
    ->setAccountNumber($_POST['account_number'])
    ->setBankName($_POST['bank_name'])
    ->setBankCity($_POST['bank_city'])
    ->setBankCode($_POST['bank_code'])
    ->setBranchCode($_POST['branch_code'])
    ->setBranchCheckDigit($_POST['branch_check_digit']);

$citadelPayoutRequest
    ->setBillingFirstName($_POST['billing_address']['first_name'])
    ->setBillingLastName($_POST['billing_address']['last_name'])
    ->setBillingAddress1($_POST['billing_address']['address1'])
    ->setBillingZipCode($_POST['billing_address']['zip_code'])
    ->setBillingCity($_POST['billing_address']['city'])
    ->setBillingCountry($_POST['billing_address']['country']);

$citadelPayoutRequest
    ->setShippingFirstName($_POST['shipping_address']['first_name'])
    ->setShippingLastName($_POST['shipping_address']['last_name'])
    ->setShippingAddress1($_POST['shipping_address']['address1'])
    ->setShippingZipCode($_POST['shipping_address']['zip_code'])
    ->setShippingCity($_POST['shipping_address']['city'])
    ->setShippingCountry($_POST['shipping_address']['country']);

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
