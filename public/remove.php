<?php

use Bank2Loyalty\Models\Requests\PostRemove;
use Bank2Loyalty\Security\HashValidator;
use Example\Storage\ConsumerStorage;
use Example\HashPassword;

/**
 * This is an example implementation of the 'Remove URL'.
 * The endpoint will be called after the consumer wants to remove his account.
 * We'll delete the consumer data.
 *
 * @link https://developer.bank2loyalty.com/#233-removeurl
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

// Create mapper and map JSON to a PostRemove request class
$mapper = new JsonMapper();
/** @var PostRemove $request */
$request = $mapper->map($json, new PostRemove());

// Just a simple JSON file
$storage = new ConsumerStorage();
$consumer = $storage->getConsumer($request->getConsumerId());

// Remove consumer if found
if ($consumer !== null) {
    $storage->deleteConsumer($request->getConsumerId());
}

http_response_code(204);
