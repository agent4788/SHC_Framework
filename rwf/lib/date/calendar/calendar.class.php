<?php

namespace RWF\Date\Calendar;

//Imports
use RWF\Date\DateTime;

/**
 * Kalender Funktionen
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
abstract class Calendar {

    /**
     * gibt die Anzahl der Tage im Monat zurueck
     * 
     * @param  Integer $month Monat
     * @param  Integer $year  Jahr
     * @return Integer
     */
    public static function countDaysInMonth($month, $year) {

        return cal_days_in_month(CAL_GREGORIAN, $month, $year);
    }

    /**
     * gibt ein Array mit den Tagen im Monat zurueck
     * 
     * @param  Integer $month     Monat
     * @param  Integer $year      Jahr
     * @param  Boolean $twoDigits Tag immer Zweistellig
     * @return Array
     */
    public static function listDaysInMonth($month, $year, $twoDigits = false) {

        $count = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $list = array();
        for ($i = 1; $i <= $count; $i++) {

            if ($twoDigits == true && $i < 10) {

                $list[] = '0' . $i;
            } else {

                $list[] = $i . '';
            }
        }

        return $list;
    }

    /**
     * gibt das Datum des Ostersonntags zurueck
     * 
     * @param  Integer $year Jahr
     * @return \DateTime
     */
    public static function getEasterDate($year) {

        $date = new DateTime();
        $date->setDate($year, 3, 21);
        $days = easter_days($year);
        $date->add(new \DateInterval('P' . $days . 'D'));
        return $date;
    }

}
