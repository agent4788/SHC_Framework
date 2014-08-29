<?php

namespace RWF\Date\Calendar\Holidays;

//Imports
use RWF\Date\DateTime;
use RWF\Date\Calendar\Calendar;

/**
 * Datums und Zeitfunktionen
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class GermanHolidays {

    /**
     * Jahr
     * 
     * @var Integer
     */
    protected $year = 2000;

    /**
     * Zeitzone
     * 
     * @var \DateTimeZone
     */
    protected $timeZone = null;

    /**
     * Datum des Ostersonntag
     * 
     * @var hf\date\DateTime
     */
    protected $easterDate = null;

    /**
     * @param Integer $year Jahr
     */
    public function __construct($year = null) {

        //Jahr
        if ($year !== null) {

            $this->setYear($year);
        }

        //Zeitzone
        $this->timeZone = new \DateTimeZone('Europe/Berlin');

        //Osterdatum
        $this->easterDate = Calendar::getEasterDate($this->year);
    }

    /**
     * setzt das Jahr
     * 
     * @param Integer $year Jahr
     */
    public function setYear($year) {

        $this->year = $year;
    }

    /**
     * gibt das Jahr zurueck
     * 
     * @return Integer
     */
    public function getYear() {

        return $this->year;
    }

    /**
     * gibt das Datum von Neujahr zurueck
     * 
     * @return \hf\date\DateTime
     */
    public function getNewYearsDay() {

        $date = new DateTime(null, $this->timeZone);
        $date->setDate($this->year, 1, 1);
        return $date;
    }

    /**
     * gibt das Datum von Heilige Drei Könige zurueck
     *
     * @return \hf\date\DateTime
     */
    public function getEpiphany() {

        $date = new DateTime(null, $this->timeZone);
        $date->setDate($this->year, 1, 6);
        return $date;
    }

    /**
     * gibt das Datum von Gruendonnerstag zurueck
     *
     * @return \hf\date\DateTime
     */
    public function getMaundyThursday() {

        $date = clone $this->easterDate;
        $date->sub(new \DateInterval('P3D'));
        return $date;
    }

    /**
     * gibt das Datum von Karfreitag zurueck
     *
     * @return \hf\date\DateTime
     */
    public function getGoodFriday() {

        $date = clone $this->easterDate;
        $date->sub(new \DateInterval('P2D'));
        return $date;
    }

    /**
     * gibt das Datum von Ostersonntag zurueck
     *
     * @return \hf\date\DateTime
     */
    public function getEasterDay() {

        return clone $this->easterDate;
    }

    /**
     * gibt das Datum von Ostermontag zurueck
     *
     * @return \hf\date\DateTime
     */
    public function getEasterMonday() {

        $date = clone $this->easterDate;
        $date->add(new \DateInterval('P1D'));
        return $date;
    }

    /**
     * gibt das Datum von Tag der Arbeit zurueck
     *
     * @return \hf\date\DateTime
     */
    public function getDayOfWork() {

        $date = new DateTime(null, $this->timeZone);
        $date->setDate($this->year, 5, 1);
        return $date;
    }

    /**
     * gibt das Datum von Christi Himmelfahrt zurueck
     *
     * @return \hf\date\DateTime
     */
    public function getAscensionDay() {

        $date = clone $this->easterDate;
        $date->add(new \DateInterval('P39D'));
        return $date;
    }

    /**
     * gibt das Datum von Pfingstsonntag zurueck
     *
     * @return \hf\date\DateTime
     */
    public function getWhitsun() {

        $date = clone $this->easterDate;
        $date->add(new \DateInterval('P49D'));
        return $date;
    }

    /**
     * gibt das Datum von Pfingstmontag zurueck
     *
     * @return \hf\date\DateTime
     */
    public function getWhitMonday() {

        $date = clone $this->easterDate;
        $date->add(new \DateInterval('P50D'));
        return $date;
    }

    /**
     * gibt das Datum von Fronleichnam zurueck
     *
     * @return \hf\date\DateTime
     */
    public function getCorpusChristi() {

        $date = clone $this->easterDate;
        $date->add(new \DateInterval('P60D'));
        return $date;
    }

    /**
     * gibt das Datum von Tag der Deutschen Einheit zurueck
     *
     * @return \hf\date\DateTime
     */
    public function getGermanUnificationDay() {

        $date = new DateTime(null, $this->timeZone);
        $date->setDate($this->year, 10, 3);
        return $date;
    }

    /**
     * gibt das Datum von Reformationstag zurueck
     *
     * @return \hf\date\DateTime
     */
    public function getReformationDay() {

        $date = new DateTime(null, $this->timeZone);
        $date->setDate($this->year, 10, 31);
        return $date;
    }

    /**
     * gibt das Datum von Allerheiligen zurueck
     *
     * @return \hf\date\DateTime
     */
    public function getAllSaintsDay() {

        $date = new DateTime(null, $this->timeZone);
        $date->setDate($this->year, 11, 1);
        return $date;
    }

    /**
     * gibt das Datum von Buss- und Bettag zurueck
     *
     * @return \hf\date\DateTime
     */
    public function getDayOfRepentance() {

        $nov23 = new DateTime(null, $this->timeZone);
        $nov23->setDate($this->year, 11, 23);
        $day = $nov23->format('w');

        $days = array(3 => 7, 2 => 6, 1 => 5, 0 => 4, 6 => 3, 5 => 2, 4 => 1);

        $date = clone $nov23;
        $date->sub(new \DateInterval('P' . $days[$nov23->format('w')] . 'D'));

        var_dump($date->format('r'));
    }

    /**
     * gibt das Datum von Heiligabend zurueck
     *
     * @return \hf\date\DateTime
     */
    public function getChristmasDay() {

        $date = new DateTime(null, $this->timeZone);
        $date->setDate($this->year, 12, 24);
        return $date;
    }

    /**
     * gibt das Datum von 1. Weihnachtstag zurueck
     *
     * @return \hf\date\DateTime
     */
    public function getXmasDay() {

        $date = new DateTime(null, $this->timeZone);
        $date->setDate($this->year, 12, 25);
        return $date;
    }

    /**
     * gibt das Datum von 2. Weihnachtstag zurueck
     *
     * @return \hf\date\DateTime
     */
    public function getBoxingDay() {

        $date = new DateTime(null, $this->timeZone);
        $date->setDate($this->year, 12, 26);
        return $date;
    }

    /**
     * gibt das Datum von Silvester zurueck
     *
     * @return \hf\date\DateTime
     */
    public function getNewYearsEve() {

        $date = new DateTime(null, $this->timeZone);
        $date->setDate($this->year, 12, 31);
        return $date;
    }

}
