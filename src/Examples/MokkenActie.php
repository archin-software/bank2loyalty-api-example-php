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

class MokkenActie
{
    public const FULL_CARD_STAMP_AMOUNT = 4;

    public static function notSaving(): Script
    {
        return (new Script)
            ->addStep((new ScriptStep)
                ->setShowCard((new ShowCard)
                    ->setTimeOutInSeconds(7)
                    ->setImageTextCenter('U spaart niet voor "Rooyse" mokken')
                    ->setImageFrameMode(FrameMode::Red)
                    ->setButtonText('Starten met sparen')
                    ->setButtonAction((new ScriptAction)
                        ->setNextScriptStep(1)
                    )
                )
            )
            ->addStep((new ScriptStep)
                ->setShowYesNoQuestion((new ShowYesNoQuestion)
                    ->setTimeOutInSeconds(10)
                    ->setQuestion('Wilt u starten met zegels sparen voor "Rooyse" mokken?')
                    ->setYesAction((new ScriptAction)
                        ->setScriptActionResult((new ScriptActionResult)
                            ->setKeyString('mokkenactie')
                            ->setValueString('aan')
                        )
                    )
                    ->setNoAction((new ScriptAction)
                        ->setScriptActionResult((new ScriptActionResult)
                            ->setKeyString('mokkenactie')
                            ->setValueString('uit')
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
                    ->setQuestion('Wilt u zegeltjes sparen voor "Rooyse" mokken?')
                    ->setYesAction((new ScriptAction)
                        ->setScriptActionResult((new ScriptActionResult)
                            ->setKeyString('mokkenactie')
                            ->setValueString('aan')
                        )
                    )
                    ->setNoAction((new ScriptAction)
                        ->setScriptActionResult((new ScriptActionResult)
                            ->setKeyString('mokkenactie')
                            ->setValueString('uit')
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
                    ->setImageKey('4-zegels')
                    ->setTimeOutInSeconds(10)
                    ->setImageTextCenter('U heeft een volle spaarkaart!')
                    ->setImageFrameMode(FrameMode::Off)
                    ->setButtonText('Mok nu meenemen')
                    ->setButtonAction((new ScriptAction)
                        ->setScriptActionResult((new ScriptActionResult)
                            ->setKeyString('vollekaart')
                            ->setValueString('ingewisseld')
                        )
                    )
                )
            );
    }

    public static function showSavedStamps(int $zegelCount): Script
    {
        $imageKey = null;

        switch ($zegelCount) {
            case 1:
                $imageKey = '1-zegel';
                break;
            case 2:
                $imageKey = '2-zegels';
                break;
            case 3:
                $imageKey = '3-zegels';
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
                    ->setTextToShow('U heeft sparen voor "Rooyse" mokken UITGESCHAKELD!')
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
                    ->setTextToShow('U krijgt uw "Rooyse" mok van de cassiere')
                    ->setSendToPos('EANcode[vollespaarkaart]')
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
                    ->setTextToShow('Sorry, er is iets mis gegaan bij de verwerking van uw verzoek!')
                )
            );
    }
}
