<?php

namespace SHC\Event\Events;

//Imports
use SHC\Event\AbstractEvent;
use RWF\Date\DateTime;

/**
 * Ereignis Sonnenuntergang
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class Sunset extends AbstractEvent {

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

        //Daten verbereiten
        $now = DateTime::now();
        $sunsetDate = DateTime::now()->getSunset();
        if(!isset($this->state['lastExecute'])) {

            $this->state['lastExecute'] = DateTime::createFromDatabaseDateTime('2000-01-01 00:00:00');
        }

        //pruefen ob der Ereigniszustand erfuellt ist
        $success = false;
        if($now->format('YmdHi') == $sunsetDate->format('YmdHi') && $this->state['lastExecute']->format('YmdHi') != $now->format('YmdHi')) {

            $success = true;
            $this->state['lastExecute'] = $now;
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