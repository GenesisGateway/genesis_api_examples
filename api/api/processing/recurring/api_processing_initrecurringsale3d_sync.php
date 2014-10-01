<?php

require 'vendor/autoload.php';

use \Genesis\Configuration as Config;

Config::setDebug();
Config::setEnvironment('');
Config::setToken('');
Config::setUsername('');
Config::setPassword('');

$genesisRequest = new \Genesis\API\Request\Financial\Recurring\InitRecurringSale3D();

$genesisRequest->setTransactionId('4035747662996176904');
$genesisRequest->setAmount(1);
$genesisRequest->setCurrency('USD');
$genesisRequest->setRemoteIp('127.0.0.1');
$genesisRequest->setUsage('40208 Concert Tickets');
$genesisRequest->setDescription('Genesis PHP Client Example Request');
$genesisRequest->setCardHolder('John Doe');
$genesisRequest->setCardNumber('4200000000000000');
$genesisRequest->setCvv('0123');
$genesisRequest->setExpirationMonth('01');
$genesisRequest->setExpirationYear('2020');
$genesisRequest->setCustomerEmail('john.doe@mywebsite.com');
$genesisRequest->setCustomerPhone('+44-001-002-003');
$genesisRequest->setBillingFirstName('John');
$genesisRequest->setBillingLastName('Doe');
$genesisRequest->setBillingAddress1('10 Downing Street');
$genesisRequest->setBillingZipCode('SW1A 2AA');
$genesisRequest->setBillingCity('London');
$genesisRequest->setBillingCountry('UK');

$genesisRequest->setMpiCavv('CAVV');
$genesisRequest->setMpiEci('ECI');
$genesisRequest->setXid('XID');

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