<?php

namespace SHC\Sheduler\Tasks;

//Imports
use SHC\Command\CommandSheduler;
use SHC\Condition\ConditionEditor;
use SHC\Core\SHC;
use SHC\Sheduler\AbstractTask;
use SHC\Switchable\SwitchableEditor;
use SHC\Switchable\Switchable;
use SHC\Switchable\Switchables\Countdown;
use RWF\Date\DateTime;
use SHC\Timer\SwitchPointEditor;

/**
 * Timer Task prueft regelmäßig ob Schaltpunkte zur ausfuehrung bereit stehen
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class TimerTask extends AbstractTask {

    /**
     * Prioriteat
     * 
     * @var Integer 
     */
    protected $priority = 70;

    /**
     * Wartezeit zwischen 2 durchläufen
     * 
     * @var String 
     */
    protected $interval = 'PT5S';

    /**
     * fuehrt die Aufgabe aus
     * falls ein Intervall angegeben ist wird automatisch die Ausfuerung in den vogegebenen Zeitabstaenden verzoegert
     */
    public function executeTask() {

        //Intervall festlegen
        switch(SHC::getSetting('shc.shedulerDaemon.performanceProfile')) {

            case 1:

                //fast
                $this->interval = 'PT1S';
                break;
            case 2:

                //default
                $this->interval = 'PT5S';
                break;
            case 3:

                //slow
                $this->interval = 'PT15S';
                break;
        }

        //Liste mit den Schaltbaren Elementen holen
        ConditionEditor::getInstance()->loadData();
        SwitchPointEditor::getInstance()->loadData();
        SwitchableEditor::getInstance()->loadData();
        $switchables = SwitchableEditor::getInstance()->listElements();

        //alle Elemente durchlaufen und Pruefen ob ausfuehrbar
        foreach ($switchables as $switchable) {

            if($switchable instanceof Switchable && $switchable->isEnabled()) {

                //Pruefen ob Schaltpunkte ausfuehrbar sind
                $switchable->execute();

                //Countdown pruefen ob abgelaufen
                if($switchable instanceof Countdown) {

                    $switchOffTime = $switchable->getSwitchOffTime();
                    if($switchOffTime != new DateTime('2000-01-01 00:00:00') && ($switchOffTime->isPast() || $switchOffTime == DateTime::now())) {

                        $switchable->switchOff();
                        try {
                            CommandSheduler::getInstance()->sendCommands();
                        } catch(\Exception $e) {

                        }
                    }
                }
            }
        }

        //Status speichern
        SwitchPointEditor::getInstance()->updateSwitchPoints();
        SwitchableEditor::getInstance()->updateState();
    }

}
