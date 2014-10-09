<?php

require '../lib/vendor/autoload.php';

use \Genesis\Base as Genesis;
use \Genesis\Configuration as GenesisConfig;

GenesisConfig::loadSettings('/Users/petermanchev/Documents/Workspace/git/github/ldap/genesis_php/legacy/settings.ini');

$genesis = new Genesis('Financial\Void');

$genesis->request()
                ->setTransactionId(($_POST['transaction_id']))
                ->setUsage($_POST['usage'])
                ->setRemoteIp($_POST['remote_ip'])
                ->setReferenceId($_POST['reference_id']);

$output = null;

try  {
    $genesis->sendRequest();
    $output['request']  = $genesis->request()->getDocument();
    $output['response'] = $genesis->response()->getResponseRaw();
}
catch (\Exception $e)  {
    $output['response'] = $e->getMessage();
}

echo json_encode($output);
exit(0);