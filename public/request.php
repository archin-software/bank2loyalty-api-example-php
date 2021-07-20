<?php

use Bank2Loyalty\Models\Requests\PostRequest;
use Bank2Loyalty\Security\HashValidator;
use Bank2Loyalty\Storage\ConsumerStorage;
use Example\Examples\MokkenActie;
use Example\Examples\Response;
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
/** @var PostRequest $request */
$request = $mapper->map($json, new PostRequest());

$storage = new ConsumerStorage();

try {
    // Retrieve consumer if present
    $consumer = $storage->getConsumer($request->getConsumerId());

    if ($consumer === null) {
        Response::json(MokkenActie::newUser());
    }

    // Notify if not saving, allow to start saving
    if (!$consumer['isSaving']) {
        Response::json(MokkenActie::notSaving());
    }

    // Increase stamp amount if saving
    $consumer['totalStamps'] += 1;

    $storage->addOrUpdateConsumer($request->getConsumerId(), $consumer);

    // Check if card is full
    if ($consumer['totalStamps'] >= MokkenActie::FULL_CARD_STAMP_AMOUNT) {
        Response::json(MokkenActie::fullCard());
    }

    Response::json(MokkenActie::showSavedStamps($consumer['totalStamps']));
} catch (Exception $e) {
    error_log($e);

    Response::json(MokkenActie::error());
}
