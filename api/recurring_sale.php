<?php

require '../lib/vendor/autoload.php';

use \Genesis\Base as Genesis;
use \Genesis\Configuration as Config;

Config::loadSettings('/Users/petermanchev/Documents/Workspace/git/github/ldap/genesis_php/legacy/settings.ini');

$genesis = new Genesis('Financial\Recurring\RecurringSale');

$genesis->request()->setTransactionId($_POST['transaction_id']);
$genesis->request()->setUsage($_POST['usage']);
$genesis->request()->setRemoteIp($_POST['remote_ip']);
$genesis->request()->setReferenceId($_POST['reference_id']);
$genesis->request()->setAmount($_POST['amount']);
$genesis->request()->setCurrency($_POST['currency']);

$output = null;

try
{
    $genesis->sendRequest();
    $output['request']  = $genesis->request()->getDocument();
    $output['response'] = $genesis->response()->getResponseRaw();
}
catch (\Exception $e)
{
    $output['response'] = $e->getMessage();
}

echo json_encode($output);
exit(0);