<?php

use Bank2Loyalty\Models\Requests\PostGetBalance;
use Bank2Loyalty\Security\HashValidator;
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
/** @var PostGetBalance $request */
$request = $mapper->map($json, new PostGetBalance());

// Just return a fake balance for now
echo sprintf('%s punten', rand(50, 100));
