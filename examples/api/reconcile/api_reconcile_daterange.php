<?php

require 'vendor/autoload.php';

use \Genesis\Configuration as Config;

Config::setDebug();
Config::setEnvironment('');
Config::setToken('');
Config::setUsername('');
Config::setPassword('');

$genesisRequest = new \Genesis\API\Request\Reconcile\DateRange();

$genesisRequest->setStartDate('2014-01-01');
$genesisRequest->setEndDate('2014-01-31');

try
{
    $genesisRequest->generateXML();
    $genesisRequest->submitRequest();

    $genesisResponse = new \Genesis\API\Response();
    $genesisResponse->parseResponse($genesisRequest->getGenesisResponse());

    if (!$genesisResponse->checkResponseCode()) {
        echo $genesisResponse->getResponseObject()->technical_message . "\r\n";
        echo \Genesis\API\Errors::getErrorDescription($genesisResponse->getResponseObject()->code) . "\r\n";
        throw new \Exception("\r\nInvalid Response from Genesis, something went wrong!\r\n");
    }
}
catch (\Exception $e)
{
    echo $e->getMessage();
}