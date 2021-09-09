<?php

use Bank2Loyalty\Models\Requests\PostGetBalance;
use Bank2Loyalty\Security\HashValidator;
use Example\HashPassword;
use Example\Storage\ConsumerStorage;

error_reporting(E_ALL);
require '../vendor/autoload.php';

$payload = file_get_contents('php://input');

if (!HashValidator::validate($payload, HashPassword::getInstance()->getHashPassword())) {
    http_response_code(401);
    exit;
}

$json = json_decode($payload);

$mapper = new JsonMapper();
/** @var PostGetBalance $request */
$request = $mapper->map($json, new PostGetBalance());

$storage = new ConsumerStorage();

try {
    // Retrieve consumer if present
    $consumer = $storage->getConsumer($request->getConsumerId());

    if ($consumer !== null) {
        // Return balance
        exit(sprintf('%s stamp(s)', $consumer['totalStamps']));
    }
} catch (Exception $e) {
    error_log($e);
}

// Something went wrong; return empty balance
echo '-';
