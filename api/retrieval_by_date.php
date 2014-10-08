<?php
require '../lib/vendor/autoload.php';

use \Genesis\Base as Genesis;
use \Genesis\Configuration as Config;

Config::loadSettings('/Users/petermanchev/Documents/Workspace/git/github/ldap/genesis_php/legacy/settings.ini');

$genesis = new Genesis('Retrieval\DateRange');

$genesis->request()->setStartDate($_POST['start_date']);
$genesis->request()->setEndDate($_POST['end_date']);
$genesis->request()->setPage($_POST['page']);

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