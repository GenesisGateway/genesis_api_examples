<?php
require '../../lib/php/vendor/autoload.php';

use \Genesis\Genesis as Genesis;
use \Genesis\GenesisConfig as GenesisConfig;

GenesisConfig::loadSettings('../../config/default.ini');

$genesis = new Genesis('Financial\Recurring\RecurringSale');

$genesis
	->request()
        ->setTransactionId($_POST['transaction_id'])
        ->setUsage($_POST['usage'])
        ->setRemoteIp($_POST['remote_ip'])
        ->setReferenceId($_POST['reference_id'])
		->setCurrency($_POST['currency'])
        ->setAmount($_POST['amount']);

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