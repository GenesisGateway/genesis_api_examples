<?php

require 'vendor/autoload.php';

use \Genesis\Configuration as Config;

Config::setDebug();
Config::setEnvironment('');
Config::setToken('');
Config::setUsername('');
Config::setPassword('');

$genesisRequest = new \Genesis\API\Request\Reconcile\Transaction();

$genesisRequest->setUniqueId('4035747662996176904');

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