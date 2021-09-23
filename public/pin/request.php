<?php

use Bank2Loyalty\Models\Scripting\Script;
use Bank2Loyalty\Models\Scripting\ScriptStep;
use Bank2Loyalty\Models\Scripting\Steps\ShowEnterPin;
use Bank2Loyalty\Security\HashValidator;
use Example\Examples\Response;
use Example\HashPassword;

/**
 * This is an example implementation of the 'Request URL'.
 * We'll instruct the reader to ask the consumer for a PIN code.
 * Note: This requires an additional layer of security by using RSA encryption.
 *
 * @link https://developer.bank2loyalty.com/#231-requesturl
 * @link https://developer.bank2loyalty.com/#47-showenterpin-model
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

// Create a step to ask the consumer for a PIN code
$script = (new Script)
    ->addStep((new ScriptStep)
        ->setShowEnterPin((new ShowEnterPin)
            ->setTimeOutInSeconds(15)
            ->setInstructionText('Please enter your PIN.')
            ->setPinLength(4)
            ->setPublicCertificate('PUBLICEY') // Note: Add your public certificate here
        )
    );

Response::json($script);
