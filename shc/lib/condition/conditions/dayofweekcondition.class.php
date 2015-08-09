<?php

namespace SHC\Condition\Conditions;

//Imports
use SHC\Condition\AbstractCondition;
use RWF\Date\DateTime;

/**
 * Bedingung Wochentag
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DayOfWeekCondition extends AbstractCondition {

    /**
     * Wochentage
     * 
     * @var Array 
     */
    protected $daysOfWeek = array(
        0 => 'mon',
        1 => 'tue',
        2 => 'wed',
        3 => 'thu',
        4 => 'fri',
        5 => 'sat',
        6 => 'sun'
    );

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

            throw new \Exception('Start und End Wochentag müssen angegeben werden', 1580);
        }

        //Aktuelle Zeit
        $now = DateTime::now();
        $currentDay = $now->getDayOfWeek();

        //ersten Tag der Woche bestimmen
        $startDateOfWeek = new DateTime(DateTime::NOW);
        $startDateOfWeek->sub(new \DateInterval('P' . $currentDay . 'D'));

        //Start und Ende vom Datumsbereich
        $start = array_search($this->data['start'], $this->daysOfWeek);
        $end = array_search($this->data['end'], $this->daysOfWeek);

        if ($start < $end) {

            //Datumsbereich ermittel wenn der letzt Tag nach dem ersten liegt
            $dateStart = clone $startDateOfWeek;
            $dateStart->add(new \DateInterval('P' . ($start) . 'D'));

            $dateEnd = clone $startDateOfWeek;
            $dateEnd->add(new \DateInterval('P' . ($end + 1) . 'D'));

            $datePeriod = new \DatePeriod($dateStart, new \DateInterval('P1D'), $dateEnd);
        } elseif ($start > $end) {

            //Datumsbereich ermittel wenn der letzt Tag vor dem ersten liegt
            $dateStart = clone $startDateOfWeek;
            $dateStart->sub(new \DateInterval('P' . (7 - $start) . 'D'));

            $dateEnd = clone $startDateOfWeek;
            $dateEnd->add(new \DateInterval('P' . ($end + 1) . 'D'));

            $datePeriod = new \DatePeriod($dateStart, new \DateInterval('P1D'), $dateEnd);
        }

        //Pruefen ob der aktuelle Wochentag im Datumsbereich liegt
        $dateNow = $now->format('Y-m-d');
        foreach ($datePeriod as $date) {

            if ($date->format('Y-m-d') == $dateNow) {

                return true;
            }
        }
        return false;
    }

}
