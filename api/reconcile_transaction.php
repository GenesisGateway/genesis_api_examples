<?php
require '../lib/vendor/autoload.php';

use \Genesis\Genesis as Genesis;
use \Genesis\GenesisConfig as GenesisConfig;

GenesisConfig::loadSettings('../config/default.ini');

$genesis = new Genesis('Reconcile\Transaction');

$genesis
	->request()
		->setUniqueId($_POST['unique_id']);

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