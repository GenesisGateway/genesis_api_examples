<?php

require 'vendor/autoload.php';

use \Genesis\API\Errors as Errors;
use \Genesis\Configuration as Config;

Config::setDebug();
Config::setEnvironment('');
Config::setToken('');
Config::setUsername('');
Config::setPassword('');

$genesisRequest = new \Genesis\API\Request\Financial\Sale3D();

$genesisRequest->setTransactionId('4035747662996176904');
$genesisRequest->setAmount(1);
$genesisRequest->setCurrency('USD');
$genesisRequest->setUsage('40208 Concert Tickets');
$genesisRequest->setDescription('Genesis PHP Client Example Request');
$genesisRequest->setCustomerEmail('john.doe@mywebsite.com');
$genesisRequest->setCustomerPhone('+44-001-002-003');
$genesisRequest->setNotificationUrl('https://www.mywebsite.com/notify');
$genesisRequest->setReturnSuccessUrl('https://www.mywebsite.com/redir/success');
$genesisRequest->setReturnFailureUrl('https://www.mywebsite.com/redir/failure');
$genesisRequest->setReturnCancelUrl('https://www.mywebsite.com/redir/cancel');
$genesisRequest->setBillingFirstName('John');
$genesisRequest->setBillingLastName('Doe');
$genesisRequest->setBillingAddress1('10 Downing Street');
$genesisRequest->setBillingZipCode('SW1A 2AA');
$genesisRequest->setBillingCity('London');
$genesisRequest->setBillingCountry('UK');
$genesisRequest->addTransactionType('sale');
$genesisRequest->addTransactionType('sale3d');

try
{
    $genesisRequest->generateXML();
    $genesisRequest->submitRequest();

    $genesisResponse = new \Genesis\API\Response();
    $genesisResponse->parseResponse($genesisRequest->getGenesisResponse());

    if (!$genesisResponse->checkResponseCode()) {
        echo $genesisResponse->getResponseObject()->technical_message . "\r\n";
        echo Errors::getErrorDescription($genesisResponse->getResponseObject()->code) . "\r\n";
        throw new \Exception("\r\nInvalid Response from Genesis, something went wrong!\r\n");
    }
}
catch (\Exception $e)
{
    echo $e->getMessage();
}