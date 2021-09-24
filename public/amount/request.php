<?php

use Bank2Loyalty\Models\Scripting\Script;
use Bank2Loyalty\Models\Scripting\ScriptStep;
use Bank2Loyalty\Models\Scripting\Steps\ShowEnterAmount;
use Bank2Loyalty\Security\HashValidator;
use Example\Examples\Response;
use Example\HashPassword;

/**
 * This is an example implementation of the 'Request URL'.
 * We'll instruct the reader to ask the consumer for an amount.
 *
 * @link https://developer.bank2loyalty.com/#231-requesturl
 * @link https://developer.bank2loyalty.com/#46-showenteramount-model
 */

error_reporting(E_ALL);
require '../../vendor/autoload.php';

// Get body (as string)
$payload = file_get_contents('php://input');

// Check if the hash in the HTTP header matches our own calculate hash, based on the hash password
if (!HashValidator::validate($payload, HashPassword::getInstance()->getHashPassword())) {
    http_response_code(401);
    exit;
}

// Create a step to ask the consumer for an amount
$script = (new Script)
    ->addStep((new ScriptStep)
        ->setShowEnterAmount((new ShowEnterAmount)
            ->setTimeOutInSeconds(15)
            ->setInstructionText('Please enter your amount.')
            ->setCurrencyName('EUR')
            ->setDecimalPlaces(2)
            ->setDecimalCharacter('.')
            ->setErrorMessageLanguage('en')
        )
    );

Response::json($script);
