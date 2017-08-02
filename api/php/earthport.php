<?php
require '../../vendor/autoload.php';

use \Genesis\Genesis as Genesis;
use \Genesis\Config as GenesisConfig;

GenesisConfig::loadSettings('../../config/default.ini');

$genesis = new Genesis('Financial\Alternatives\Earthport');

/** @var \Genesis\API\Request\Financial\Alternatives\Earthport $earthportRequest */
$earthportRequest = $genesis->request();

$earthportRequest
    ->setTransactionId($_POST['transaction_id'])
    ->setUsage($_POST['usage'])
    ->setRemoteIp($_POST['remote_ip'])
    ->setCurrency($_POST['currency'])
    ->setAmount($_POST['amount'])
    ->setCustomerEmail($_POST['customer_email'])
    ->setCustomerPhone($_POST['customer_phone']);

$earthportRequest
    ->setAccountName($_POST['bank']['account_name'])
    ->setBankName($_POST['bank']['name'])
    ->setBic($_POST['bank']['bic'])
    ->setIban($_POST['bank']['iban'])
    ->setAccountNumber($_POST['bank']['account_number'])
    ->setAccountNumberSuffix($_POST['bank']['account_number_suffix'])
    ->setBankCode($_POST['bank']['code'])
    ->setBranchCode($_POST['bank']['branch_code'])
    ->setSortCode($_POST['bank']['sort_code'])
    ->setAbaRoutingNumber($_POST['bank']['aba_routing_number']);

$earthportRequest
    ->setBillingFirstName($_POST['billing_address']['first_name'])
    ->setBillingLastName($_POST['billing_address']['last_name'])
    ->setBillingAddress1($_POST['billing_address']['address1'])
    ->setBillingZipCode($_POST['billing_address']['zip_code'])
    ->setBillingCity($_POST['billing_address']['city'])
    ->setBillingCountry($_POST['billing_address']['country'])
    ->setBillingState($_POST['billing_address']['state']);

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
