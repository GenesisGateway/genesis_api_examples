<?php

require 'vendor/autoload.php';

use Genesis;

Configuration::setDebug();
Configuration::setEnvironment('');
Configuration::setToken('');
Configuration::setUsername('');
Configuration::setPassword('');

$genesisRequest = new \Genesis\API\Request\Reconcile\Transaction();

$genesisRequest->setCardNumber('0123456789123456');

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