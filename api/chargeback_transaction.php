<?php
require '../lib/vendor/autoload.php';

use \Genesis\Genesis as Genesis;
use \Genesis\GenesisConfig as GenesisConfig;

GenesisConfig::loadSettings('../config/default.ini');

$genesis = new Genesis('FraudRelated\Chargeback\Transaction');

$genesis
	->request()
        ->setArn($_POST['arn'])
        ->setOriginalTransactionUniqueId($_POST['original_transaction_unique_id']);

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