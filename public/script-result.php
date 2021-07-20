<?php

use Example\Examples\MokkenActie;
use Example\Examples\Response;
use Bank2Loyalty\Models\Requests\PostScriptResult;
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
/** @var PostScriptResult $request */
$request = $mapper->map($json, new PostScriptResult());

$storage = new ConsumerStorage();

try {
    if (count($request->getScriptActionResults()->getActionResults()) > 0) {
        foreach ($request->getScriptActionResults()->getActionResults() as $actionResult) {
            if ($actionResult->getKeyString() === 'mokkenactie') {
                // Start or stop saving
                if ($actionResult->getValueString() === 'aan') {
                    $storage->addOrUpdateConsumer($request->getConsumerId(), [
                        'isSaving' => true,
                        'totalStamps' => 1,
                    ]);

                    Response::json(MokkenActie::switchedOn());
                } elseif ($actionResult->getValueString() === 'uit') {
                    $storage->addOrUpdateConsumer($request->getConsumerId(), [
                        'isSaving' => false,
                        'totalStamps' => 1,
                    ]);

                    Response::json(MokkenActie::switchedOff());
                }
            } elseif ($actionResult->getKeyString() === 'vollekaart' && $actionResult->getValueString() === 'ingewisseld') {
                // Full card
                $storage->addOrUpdateConsumer($request->getConsumerId(), [
                    'isSaving' => true,
                    'totalStamps' => 0,
                ]);

                Response::json(MokkenActie::confirmFullCardExchange());
            }
        }
    }
} catch (Exception $e) {
    error_log($e);

    Response::json(MokkenActie::error());
}

