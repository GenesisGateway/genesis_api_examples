<?php
require '../lib/vendor/autoload.php';

use \Genesis\Base as Genesis;
use \Genesis\Configuration as GenesisConfig;

GenesisConfig::loadSettings('../config/default.ini');

$genesis = new Genesis('FraudRelated\Blacklist');

$genesis->request()
            ->setCardNumber($_POST['card_number'])
            ->setTerminalToken($_POST['terminal_token']);

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