<?php
require '../../vendor/autoload.php';

use \Genesis\Genesis as Genesis;
use \Genesis\Config as GenesisConfig;
use \Genesis\API\Request\Financial\Alternatives\Klarna\Items;
use \Genesis\API\Request\Financial\Alternatives\Klarna\Item;

GenesisConfig::loadSettings('../../config/default.ini');

$genesis = new Genesis('Financial\Alternatives\Klarna\Capture');

/**
 * @var \Genesis\API\Request\Financial\Alternatives\Klarna\Capture $request
 */
$request = $genesis->request();

// Params
$request
    ->setTransactionId($_POST['transaction_id'])
    ->setUsage($_POST['usage'])
    ->setRemoteIp($_POST['remote_ip'])
    ->setCurrency($_POST['currency'])
    ->setReferenceId($_POST['reference_id']);

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
