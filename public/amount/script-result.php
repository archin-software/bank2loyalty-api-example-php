<?php

use Bank2Loyalty\Models\Enums\MessageMode;
use Bank2Loyalty\Models\Requests\PostScriptResultV3;
use Bank2Loyalty\Models\Scripting\Script;
use Bank2Loyalty\Models\Scripting\ScriptStep;
use Bank2Loyalty\Models\Scripting\Steps\ShowMessage;
use Bank2Loyalty\Security\HashValidator;
use Example\Examples\Response;
use Example\HashPassword;

/**
 * This is an example implementation of the 'Script result URL'.
 * This endpoint will be called with the entered amount of the consumer.
 *
 * @link https://developer.bank2loyalty.com/#232-scriptresulturl
 */

error_reporting(E_ALL);
require '../../vendor/autoload.php';

// Get body (as string)
$payload = file_get_contents('php://input');

// Check if the hash in the HTTP header matches our own calculate hash, based on the hash password
if (!HashValidator::validate($payload, HashPassword::getInstance()->getHashPassword())) {
    http_response_code(401);
    exit;
}

// Decode JSON body
$json = json_decode($payload);

// Create mapper and map JSON to a PostScriptResultV3 request class
$mapper = new JsonMapper();
/** @var PostScriptResultV3 $request */
$request = $mapper->map($json, new PostScriptResultV3());

try {
    // Check if we've some actions to process
    if (count($request->getScriptActionResults()->getActionResults()) > 0) {
        foreach ($request->getScriptActionResults()->getActionResults() as $actionResult) {
            if ($actionResult->getKeyString() === 'amount') {
                // This will be our amount
                // Just return a message to the consumer to show the amount
                // In real life, this is the place to handle the entered amount
                $script = (new Script)
                    ->addStep((new ScriptStep)
                        ->setShowMessage((new ShowMessage())
                            ->setTimeOutInSeconds(5)
                            ->setTextToShow(sprintf('Thanks for your amount: %s', $actionResult->getValueString()))
                            ->setMessageMode(MessageMode::Celebrate)
                        )
                    );

                Response::json($script);
            }
        }
    }
} catch (Exception $e) {
    error_log($e);
}

