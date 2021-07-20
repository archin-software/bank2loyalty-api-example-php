<?php

use Bank2Loyalty\Models\Requests\PostPosTransaction;
use Bank2Loyalty\Security\HashValidator;
use Bank2Loyalty\Storage\ConsumerStorage;
use Example\HashPassword;

error_reporting(E_ALL);
require '../vendor/autoload.php';

$payload = file_get_contents('php://input');

if (!HashValidator::validate($payload, HashPassword::getInstance()->getHashPassword())) {
    http_response_code(401);
    exit;
}

$json = json_decode($payload);

$mapper = new JsonMapper();
/** @var PostPosTransaction $request */
$request = $mapper->map($json, new PostPosTransaction());

$storage = new ConsumerStorage();
$consumer = $storage->getConsumer($request->getConsumerId());

if ($consumer === null) {
    // New consumer
    $storage->addOrUpdateConsumer($request->getConsumerId(), []);
} else {
    // Existing consumer
}

http_response_code(204);
