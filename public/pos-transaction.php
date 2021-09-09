<?php

use Bank2Loyalty\Models\Requests\PostPosTransaction;
use Bank2Loyalty\Security\HashValidator;
use Example\Storage\ConsumerStorage;
use Example\HashPassword;

/**
 * This is an example implementation of the 'Pos transaction URL'.
 * The endpoint will be called after a finished transaction at the cash register.
 *
 * @link https://developer.bank2loyalty.com/#235-postransactionurl
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

// Create mapper and map JSON to a PostPosTransaction request class
$mapper = new JsonMapper();
/** @var PostPosTransaction $request */
$request = $mapper->map($json, new PostPosTransaction());

// Just a simple JSON file
$storage = new ConsumerStorage();

// Retrieve consumer if present
$consumer = $storage->getConsumer($request->getConsumerId());

// Do something with the consumer or store scanned data for analyse
http_response_code(204);
