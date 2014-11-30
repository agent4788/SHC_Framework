<?php

namespace SHC\Event\Events;

//Imports
use SHC\Event\AbstractEvent;
use RWF\Date\DateTime;
use SHC\Switchable\Readable;
use SHC\Switchable\SwitchableEditor;

/**
 * Ereignis Eingang Statuswechsel von Low auf High
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class InputHigh extends AbstractEvent {

    /**
     * gibt an ob das Ereigniss erfuellt ist
     *
     * @return Boolean
     */
    public function isSatisfies() {

        //Pruefen ob Ereignis aktiv
        if($this->enabled == false) {

            return false;
        }

        //noetige Parameter pruefen
        if (!isset($this->data['inputs'])) {

            throw new \Exception('Eine Liste mit Eingängen muss angegeben werden', 1580);
        }
        $this->data['inputs'] = explode(',', $this->data['inputs']);

        //pruefen ob Warteintervall angegeben und noch nicht abgelaufen
        if(isset($this->data['interval'])) {

            $date = DateTime::now();
            $date->sub(new \DateInterval('PT'. $this->data['interval'] .'S'));

            if($this->time instanceof DateTime && $this->time > $date) {

                //noch in der Sperrzeit fuer weitere Events
                return false;
            }
        }

        //pruefen ob der Ereigniszustand erfuellt ist
        $success = false;
        $readables = SwitchableEditor::getInstance()->listElements(SwitchableEditor::SORT_NOTHING);
        foreach($readables as $readable) {


            if (in_array($readable->getId(), $this->data['inputs']) && $readable instanceof Readable) {

                if (isset($this->state[$readable->getId()])) {

                    if ($this->state[$readable->getId()] != $readable->getState() && $readable->getState() == Readable::STATE_ON) {

                        //Eingang bekannt
                        $this->state[$readable->getId()] = $readable->getState();
                        $success = true;
                    } else {

                        //Eingang bekannt
                        $this->state[$readable->getId()] = $readable->getState();
                    }
                } else {

                    //Eingang unbekannt
                    $this->state[$readable->getId()] = $readable->getState();
                }
            }
        }

        //kein Zustandswechsel erfolgt
        if($success === false) {

            return false;
        }

        //Bedingungen pruefen
        foreach ($this->conditions as $condition) {

            /* @var $condition \SHC\Condition\Condition */
            if(!$condition->isSatisfies()) {

                //eine Bedingung trifft nicht zu
                return false;
            }
        }

        //Ereignis zur ausfuehrung bereit
        $this->time = DateTime::now();
        return true;
    }

}