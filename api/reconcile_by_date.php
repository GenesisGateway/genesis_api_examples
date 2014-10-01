<?php
require '../lib/vendor/autoload.php';

use \Genesis\Base as Genesis;
use \Genesis\Configuration as Config;

Config::loadSettings('/Users/petermanchev/Documents/Workspace/git/github/ldap/genesis_php/legacy/settings.ini');


Genesis::loadRequest('Reconcile\DateRange');

Genesis::Request()->setStartDate($_POST['start_date']);
Genesis::Request()->setEndDate($_POST['end_date']);
Genesis::Request()->setPage($_POST['page']);

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