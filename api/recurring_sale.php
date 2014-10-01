<?php

require '../lib/vendor/autoload.php';

use \Genesis\Base as Genesis;
use \Genesis\Configuration as Config;

Config::loadSettings('/Users/petermanchev/Documents/Workspace/git/github/ldap/genesis_php/legacy/settings.ini');

Genesis::loadRequest('Financial\Recurring\RecurringSale');

Genesis::Request()->setTransactionId($_POST['transaction_id']);
Genesis::Request()->setUsage($_POST['usage']);
Genesis::Request()->setRemoteIp($_POST['remote_ip']);
Genesis::Request()->setReferenceId($_POST['reference_id']);
Genesis::Request()->setAmount($_POST['amount']);
Genesis::Request()->setCurrency($_POST['currency']);

$output = array(
    'request'   => null,
    'response'  => null,
);

try
{
    $output['request']  = Genesis::Request()->getDocument();
    Genesis::Request()->Send();
    $output['response'] = Genesis::Request()->getGenesisResponse();
}
catch (\Exception $e)
{
    $output['response'] = $e->getMessage();
}

echo json_encode($output);
exit(0);