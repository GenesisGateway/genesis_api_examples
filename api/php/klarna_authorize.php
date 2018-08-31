<?php
require '../../vendor/autoload.php';

use \Genesis\Genesis as Genesis;
use \Genesis\Config as GenesisConfig;
use \Genesis\API\Request\Financial\Alternatives\Klarna\Items;
use \Genesis\API\Request\Financial\Alternatives\Klarna\Item;

GenesisConfig::loadSettings('../../config/default.ini');

$genesis = new Genesis('Financial\Alternatives\Klarna\Authorize');

/**
 * @var \Genesis\API\Request\Financial\Alternatives\Klarna\Authorize $request
 */
$request = $genesis->request();

// Params
$request
    ->setTransactionId($_POST['transaction_id'])
    ->setUsage($_POST['usage'])
    ->setRemoteIp($_POST['remote_ip'])
    ->setCurrency($_POST['currency'])
    ->setCustomerEmail($_POST['customer_email'])
    ->setCustomerPhone($_POST['customer_phone'])
    ->setReturnSuccessUrl($_POST['return_success_url'])
    ->setReturnFailureUrl($_POST['return_failure_url']);
// Billing
$request
    ->setBillingFirstName($_POST['billing_address']['first_name'])
    ->setBillingLastName($_POST['billing_address']['last_name'])
    ->setBillingAddress1($_POST['billing_address']['address1'])
    ->setBillingZipCode($_POST['billing_address']['zip_code'])
    ->setBillingCity($_POST['billing_address']['city'])
    ->setBillingCountry($_POST['billing_address']['country']);
// Shipping
$request
    ->setShippingFirstName($_POST['shipping_address']['first_name'])
    ->setShippingLastName($_POST['shipping_address']['last_name'])
    ->setShippingAddress1($_POST['shipping_address']['address1'])
    ->setShippingZipCode($_POST['shipping_address']['zip_code'])
    ->setShippingCity($_POST['shipping_address']['city'])
    ->setShippingCountry($_POST['shipping_address']['country']);

$output = null;

try {
    // Items
    $items = new Items($_POST['currency']);
    foreach ($_POST['items'] as $item) {
        $item = new Item(
            $item['name'],
            $item['item_type'],
            $item['quantity'],
            $item['unit_price'],
            $item['tax_rate'],
            $item['total_discount_amount'],
            $item['reference'],
            $item['image_url'],
            $item['product_url'],
            $item['quantity_unit']
        );
        $items->addItem($item);
    }
    $request->setItems($items);

    $genesis->execute();
    $output['request']  = $genesis->request()->getDocument();
    $output['response'] = $genesis->response()->getResponseRaw();
} catch (\Exception $e) {
    $output['response'] = $e->getMessage();
}

echo json_encode($output);

exit(0);
