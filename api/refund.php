<?php

require '../lib/vendor/autoload.php';

use \Genesis\Base as Genesis;
use \Genesis\Configuration as GenesisConfig;

GenesisConfig::loadSettings('../config/default.ini');

$genesis = new Genesis('Financial\Refund');

$genesis->request()
            ->setTransactionId($_POST['transaction_id'])
            ->setUsage($_POST['usage'])
            ->setRemoteIp($_POST['remote_ip'])
            ->setReferenceId($_POST['reference_id'])
            ->setAmount($_POST['amount'])
            ->setCurrency($_POST['currency']);

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