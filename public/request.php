<?php

use Bank2Loyalty\Models\Requests\PostRequest;
use Bank2Loyalty\Security\HashValidator;
use Example\Examples\HappyFlower;
use Example\Examples\Response;
use Example\HashPassword;
use Example\Storage\ConsumerStorage;

/**
 * This is an example implementation of the 'Request URL'.
 * It's the starting point after a read at the reader.
 * We'll look up a consumer and check if he's saving or wants to save (in case of a new user).
 *
 * @link https://developer.bank2loyalty.com/#231-requesturl
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

// Create mapper and map JSON to a PostRequest request class
$mapper = new JsonMapper();
/** @var PostRequest $request */
$request = $mapper->map($json, new PostRequest());

// Just a simple JSON file
$storage = new ConsumerStorage();

try {
    // Retrieve consumer if present
    $consumer = $storage->getConsumer($request->getConsumerId());

    if ($consumer === null) {
        // Consumer isn't know, return 'new user' step
        Response::json(HappyFlower::newUser());
    }

    if (!$consumer['isSaving']) {
        // Consumer isn't saving, notify 'not saving' to allow start saving
        Response::json(HappyFlower::notSaving());
    }

    // Increase stamp amount if saving
    $consumer['totalStamps'] += 1;

    // Add or update the consumer in our storage
    $storage->addOrUpdateConsumer($request->getConsumerId(), $consumer);

    // Check if card is full
    if ($consumer['totalStamps'] >= HappyFlower::FULL_CARD_STAMP_AMOUNT) {
        Response::json(HappyFlower::fullCard());
    }

    // Show the saved stamps to the user
    Response::json(HappyFlower::showSavedStamps($consumer['totalStamps']));
} catch (Exception $e) {
    error_log($e);

    // Present a general error message
    Response::json(HappyFlower::error());
}
