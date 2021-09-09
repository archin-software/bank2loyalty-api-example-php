<?php

use Example\Examples\HappyFlower;
use Example\Examples\Response;
use Bank2Loyalty\Models\Requests\PostScriptResult;
use Bank2Loyalty\Security\HashValidator;
use Example\Storage\ConsumerStorage;
use Example\HashPassword;

/**
 * This is an example implementation of the 'Script result URL'.
 * This endpoint will be called after interaction between the reader and the consumer.
 * We'll check the response of the consumer and act accordingly.
 *
 * @link https://developer.bank2loyalty.com/#232-scriptresulturl
 */

error_reporting(E_ALL);
require '../vendor/autoload.php';

// Get body (as string)
$payload = file_get_contents('php://input');

// Check if the hash in the HTTP header matches our own calculate hash, based on the hash password
if (!HashValidator::validate($payload, HashPassword::getInstance()->getHashPassword())) {
    http_response_code(401);
    exit;
}

// Decode JSON body
$json = json_decode($payload);

// Create mapper and map JSON to a PostScriptResult request class
$mapper = new JsonMapper();
/** @var PostScriptResult $request */
$request = $mapper->map($json, new PostScriptResult());

// Just a simple JSON file
$storage = new ConsumerStorage();

try {
    // Check if we've some actions to process
    if (count($request->getScriptActionResults()->getActionResults()) > 0) {
        foreach ($request->getScriptActionResults()->getActionResults() as $actionResult) {
            if ($actionResult->getKeyString() === 'tulipBouquet') {
                // Start or stop saving
                if ($actionResult->getValueString() === 'on') {
                    // Consumer wants to start saving
                    // Just a random card number
                    $cardNumber = uniqid();

                    // Add or update the consumer in our storage
                    $storage->addOrUpdateConsumer($request->getConsumerId(), [
                        'isSaving' => true,
                        'totalStamps' => 1,
                        'cardNumber' => $cardNumber,
                    ]);

                    // Show message and the first stamp to the consumer
                    Response::json(HappyFlower::switchedOn($cardNumber));
                } elseif ($actionResult->getValueString() === 'off') {
                    // Consumer wants to stop saving
                    $storage->addOrUpdateConsumer($request->getConsumerId(), [
                        'isSaving' => false,
                        'totalStamps' => 1,
                    ]);

                    // Show message
                    Response::json(HappyFlower::switchedOff());
                }
            } elseif ($actionResult->getKeyString() === 'fullCard' && $actionResult->getValueString() === 'redeemed') {
                // Consumer has a full card
                // Reset the total stamps in our storage
                $storage->addOrUpdateConsumer($request->getConsumerId(), [
                    'isSaving' => true,
                    'totalStamps' => 0,
                ]);

                // Show message about exchanging the full card
                Response::json(HappyFlower::confirmFullCardExchange());
            }
        }
    }
} catch (Exception $e) {
    error_log($e);

    // Present a general error message
    Response::json(HappyFlower::error());
}

