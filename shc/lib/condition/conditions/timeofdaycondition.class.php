<?php

namespace SHC\Condition\Conditions;

//Imports
use SHC\Condition\AbstractCondition;
use RWF\Date\DateTime;

/**
 * Bedingung Uhrzeit
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class TimeOfDayCondition extends AbstractCondition {

    /**
     * gibt an ob die Bedingung erfuellt ist
     * 
     * @return Boolean
     */
    public function isSatisfies() {

        //wenn deaktiviert immer True
        if (!$this->isEnabled()) {

            return true;
        }

        //noetige Parameter pruefen
        if (!isset($this->data['start']) || !isset($this->data['end'])) {

            throw new \Exception('Start und End Zeit müssen angegeben werden', 1580);
        }

        //Aktuelle Zeit
        $now = DateTime::now();

        //Uhrzeit Start
        $start = new DateTime('now');
        $n = explode(':', $this->data['start']);
        $start->setTime($n[0], $n[1], 0);

        //Uhrzeit Ende
        $end = new DateTime('now');
        $m = explode(':', $this->data['end']);
        $end->setTime($m[0], $m[1], 1);

        if($start > $end && $start > $now) {
            
            $start->sub(new \DateInterval('P1D'));
        } elseif($start > $end && $start < $now) {
            
            $end->add(new \DateInterval('P1D'));
        }
        
        //Zeitbereich
        $period = new \DatePeriod($start, new \DateInterval('PT1M'), $end);

        //alle Uhrzeiten durchlaufen und pruefen ob die Zeit enthalten ist
        $minute = $now->format('H:i');
        foreach ($period as $date) {

            if ($date->format('H:i') == $minute) {

                return true;
            }
        }
        return false;
    }

}
