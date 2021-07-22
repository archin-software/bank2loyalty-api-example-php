<?php

namespace Example\Examples;

use Bank2Loyalty\Models\Enums\FrameMode;
use Bank2Loyalty\Models\Enums\MessageMode;
use Bank2Loyalty\Models\Scripting\Script;
use Bank2Loyalty\Models\Scripting\ScriptAction;
use Bank2Loyalty\Models\Scripting\ScriptActionResult;
use Bank2Loyalty\Models\Scripting\ScriptStep;
use Bank2Loyalty\Models\Scripting\Steps\ShowCard;
use Bank2Loyalty\Models\Scripting\Steps\ShowMessage;
use Bank2Loyalty\Models\Scripting\Steps\ShowYesNoQuestion;

class HappyFlower
{
    public const FULL_CARD_STAMP_AMOUNT = 8;

    public static function notSaving(): Script
    {
        return (new Script)
            ->addStep((new ScriptStep)
                ->setShowCard((new ShowCard)
                    ->setTimeOutInSeconds(7)
                    ->setImageTextCenter('You are not saving for a large tulip bouquet.')
                    ->setImageFrameMode(FrameMode::Red)
                    ->setButtonText('Start saving')
                    ->setButtonAction((new ScriptAction)
                        ->setNextScriptStep(1)
                    )
                )
            )
            ->addStep((new ScriptStep)
                ->setShowYesNoQuestion((new ShowYesNoQuestion)
                    ->setTimeOutInSeconds(10)
                    ->setQuestion('Would you like to start saving for a large tulip bouquet?')
                    ->setYesAction((new ScriptAction)
                        ->setScriptActionResult((new ScriptActionResult)
                            ->setKeyString('tulipBouquet')
                            ->setValueString('on')
                        )
                    )
                    ->setNoAction((new ScriptAction)
                        ->setScriptActionResult((new ScriptActionResult)
                            ->setKeyString('tulipBouquet')
                            ->setValueString('off')
                        )
                    )
                )
            );
    }

    public static function newUser(): Script
    {
        return (new Script)
            ->addStep((new ScriptStep)
                ->setShowYesNoQuestion((new ShowYesNoQuestion)
                    ->setTimeOutInSeconds(15)
                    ->setQuestion('Would you like to start saving stamps for a large tulip bouquet?')
                    ->setYesAction((new ScriptAction)
                        ->setScriptActionResult((new ScriptActionResult)
                            ->setKeyString('tulipBouquet')
                            ->setValueString('on')
                        )
                    )
                    ->setNoAction((new ScriptAction)
                        ->setScriptActionResult((new ScriptActionResult)
                            ->setKeyString('tulipBouquet')
                            ->setValueString('off')
                        )
                    )
                )
            );
    }

    public static function fullCard(): Script
    {
        return (new Script)
            ->addStep((new ScriptStep)
                ->setShowCard((new ShowCard)
                    ->setImageKey('full-card')
                    ->setTimeOutInSeconds(10)
                    ->setImageTextCenter('You\'ve got a full card!')
                    ->setImageFrameMode(FrameMode::Off)
                    ->setButtonText('Take bouquet now')
                    ->setButtonAction((new ScriptAction)
                        ->setScriptActionResult((new ScriptActionResult)
                            ->setKeyString('fullCard')
                            ->setValueString('redeemed')
                        )
                    )
                )
            );
    }

    public static function showSavedStamps(int $stampCount): Script
    {
        $imageKey = null;

        switch ($stampCount) {
            case 1:
                $imageKey = '1-stamp';
                break;
            case 2:
                $imageKey = '2-stamps';
                break;
            case 3:
                $imageKey = '3-stamps';
                break;
            case 4:
                $imageKey = '4-stamps';
                break;
            case 5:
                $imageKey = '5-stamps';
                break;
            case 6:
                $imageKey = '6-stamps';
                break;
            case 7:
                $imageKey = '7-stamps';
                break;
            case 8:
                $imageKey = 'full-card';
                break;
        }

        return (new Script)
            ->addStep((new ScriptStep)
                ->setShowCard((new ShowCard)
                    ->setImageKey($imageKey)
                    ->setTimeOutInSeconds(5)
                    ->setImageFrameMode(FrameMode::Off)
                )
            );
    }

    public static function switchedOn(): Script
    {
        return self::showSavedStamps(1);
    }

    public static function switchedOff(): Script
    {
        return (new Script)
            ->addStep((new ScriptStep)
                ->setShowMessage((new ShowMessage)
                    ->setTimeOutInSeconds(5)
                    ->setMessageMode(MessageMode::Approved)
                    ->setTextToShow('You have disabled saving for a large tulip bouquet.')
                )
            );
    }

    public static function confirmFullCardExchange(): Script
    {
        return (new Script)
            ->addStep((new ScriptStep)
                ->setShowMessage((new ShowMessage)
                    ->setTimeOutInSeconds(5)
                    ->setMessageMode(MessageMode::Celebrate)
                    ->setTextToShow('An employee nearby will give you your large tulip bouquet.')
                    ->setSendToPos('EANcode[fullsavingscard]')
                )
            );
    }

    public static function error(): Script
    {
        return (new Script)
            ->addStep((new ScriptStep)
                ->setShowMessage((new ShowMessage)
                    ->setTimeOutInSeconds(7)
                    ->setMessageMode(MessageMode::Error)
                    ->setTextToShow('Sorry, something went wrong while processing your request!')
                )
            );
    }
}
