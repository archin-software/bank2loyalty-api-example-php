<?php

use Bank2Loyalty\Models\Enums\MessageMode;
use Bank2Loyalty\Models\Requests\PostScriptResult;
use Bank2Loyalty\Models\Scripting\Script;
use Bank2Loyalty\Models\Scripting\ScriptStep;
use Bank2Loyalty\Models\Scripting\Steps\ShowMessage;
use Bank2Loyalty\Security\HashValidator;
use Example\Examples\Response;
use Example\HashPassword;

/**
 * This is an example implementation of the 'Script result URL'.
 * This endpoint will be called with the entered PIN code of the consumer.
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

// Create mapper and map JSON to a PostScriptResult request class
$mapper = new JsonMapper();
/** @var PostScriptResult $request */
$request = $mapper->map($json, new PostScriptResult());

try {
    // Check if we've some actions to process
    if (count($request->getScriptActionResults()->getActionResults()) > 0) {
        foreach ($request->getScriptActionResults()->getActionResults() as $actionResult) {
            if ($actionResult->getKeyString() === 'pin') {
                // This will be our PIN code
                $encryptedPinCode = base64_decode($actionResult->getValueString());

                // This an example to decrypt the PIN code
                $pinCode = null;
                openssl_private_decrypt($encryptedPinCode, $pinCode, 'YOURPRIVATEKEY'); // Note: Add your private certificate here

                // Just return a message to the consumer
                $script = (new Script)
                    ->addStep((new ScriptStep)
                        ->setShowMessage((new ShowMessage())
                            ->setTimeOutInSeconds(5)
                            ->setTextToShow('Thanks for your PIN code!')
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

