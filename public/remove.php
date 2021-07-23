<?php

use Bank2Loyalty\Models\Requests\PostRemove;
use Bank2Loyalty\Security\HashValidator;
use Example\Storage\ConsumerStorage;
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
/** @var PostRemove $request */
$request = $mapper->map($json, new PostRemove());

$storage = new ConsumerStorage();
$consumer = $storage->getConsumer($request->getConsumerId());

if ($consumer !== null) {
    $storage->deleteConsumer($request->getConsumerId());
}

http_response_code(204);
