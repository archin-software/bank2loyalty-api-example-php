<?php

use Bank2Loyalty\Models\Requests\PostGetBalance;
use Bank2Loyalty\Security\HashValidator;
use Example\HashPassword;
use Example\Storage\ConsumerStorage;

/**
 * This is an example implementation of the 'Get balance URL'.
 * We'll look up a consumer and return his balance if known.
 *
 * @link https://developer.bank2loyalty.com/#234-getbalanceurl
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

// Create mapper and map JSON to a PostGetBalance request class
$mapper = new JsonMapper();
/** @var PostGetBalance $request */
$request = $mapper->map($json, new PostGetBalance());

// Just a simple JSON file
$storage = new ConsumerStorage();

try {
    // Retrieve consumer if present
    $consumer = $storage->getConsumer($request->getBank2LoyaltyInfo()->getConsumerId());

    if ($consumer !== null) {
        // Return balance
        exit(sprintf('%s stamp(s)', $consumer['totalStamps']));
    }
} catch (Exception $e) {
    error_log($e);
}

// Something went wrong; return empty balance
echo '-';
