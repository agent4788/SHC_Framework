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
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class FileCreate extends AbstractEvent {

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
        if (!isset($this->data['file'])) {

            throw new \Exception('Eine Datei muss angegeben werden', 1580);
        }

        //pruefen ob Warteintervall angegeben und noch nicht abgelaufen
        if(isset($this->data['interval'])) {

            if($this->time instanceof DateTime){

                $date = clone $this->time;
                $date->add(new \DateInterval('PT'. $this->data['interval'] .'S'));
                if($date->isFuture()) {

                    //noch in der Sperrzeit fuer weitere Events
                    return false;
                }
            }
        }

        //pruefen ob der Ereigniszustand erfuellt ist
        if(isset($this->state['file'])) {

            //Status bereits bekannt
            if(file_exists($this->data['file']) && $this->state['file'] == false) {

                //Datei wurde erstellt
                $this->state['file'] = true;
            } else {

                //keine veraenderungen -> Status speichern
                $this->state['file'] = file_exists($this->data['file']);
                return false;
            }
        } else {

            //Status unbekannt -> Status speichern
            $this->state['file'] = file_exists($this->data['file']);
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