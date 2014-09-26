<?php

require '../../../lib/vendor/autoload.php';

use \Genesis\Base as Genesis;
use \Genesis\Configuration as Config;

Config::loadSettings('/Users/petermanchev/Documents/Workspace/git/github/ldap/genesis_php/legacy/settings.ini');

Genesis::loadRequest('Financial\Capture');

Genesis::Request()->setTransactionId($_POST['transaction_id']);
Genesis::Request()->setUsage($_POST['usage']);
Genesis::Request()->setRemoteIp($_POST['remote_ip']);
Genesis::Request()->setReferenceId($_POST['reference_id']);
Genesis::Request()->setAmount($_POST['amount']);
Genesis::Request()->setCurrency($_POST['currency']);

$out = array(
    'request'   => null,
    'response'  => null,
);

try
{
    $out['request']  = Genesis::Request()->getDocument();
    Genesis::Request()->Send();
    $out['response'] = Genesis::Request()->getGenesisResponse();
}
catch (\Exception $e)
{
    $out['response'] = $e->getMessage();
}

echo json_encode($out);
exit(0);