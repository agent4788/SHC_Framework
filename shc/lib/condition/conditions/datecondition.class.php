<?php

namespace SHC\Condition\Conditions;

//Imports
use SHC\Condition\AbstractCondition;
use RWF\Date\DateTime;

/**
 * Bedingung Datum
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DateCondition extends AbstractCondition {

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

            throw new \Exception('Start und End Datum müssen angegeben werden', 1580);
        }

        //Aktuelle Zeit
        $now = DateTime::now();

        //Start/Endzeit
        $start = new DateTime($now->getYear() . '-' . $this->data['start']);
        $end = new DateTime($now->getYear() . '-' . $this->data['end']);

        if ($start < $end) {

            //Start Datum vor End Datum
            $datePeriod = new \DatePeriod($start, new \DateInterval('P1D'), $end);
        } if ($end < $start) {

            //Start Datum nach End Datum
            if ($start > $end && $start > $now) {

                $start->sub(new \DateInterval('P1Y'));
            } elseif ($start > $end && $start < $now) {

                $end->add(new \DateInterval('P1Y'));
            }

            $datePeriod = new \DatePeriod($start, new \DateInterval('P1D'), $end);
        }

        //Daten pruefen
        $dateNow = $now->format('Y-m-d');
        foreach ($datePeriod as $date) {

            if ($date->format('Y-m-d') == $dateNow) {

                return true;
            }
        }
        return false;
    }

}
