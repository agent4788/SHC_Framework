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
     * Feiertage
     *
     * @var Integer
     */
    //Neujahr
    const NEW_YEARS_DAY = 1;
    //Heilige Drei Koenige
    const EPIPHANY = 2;
    //Gruendonnerstag
    const MAUNDY_THURSDAY = 4;
    //Karfreitag
    const GOOD_FRIDAY = 8;
    //Ostersonntag
    const EASTER_DAY = 16;
    //Ostermontag
    const EASTER_MONDAY = 32;
    //Tag der Arbeit
    const DAY_OF_WORK = 64;
    //Christi Himmelfahrt
    const ASCENSION_DAY = 128;
    //Pfingstsonntag
    const WHIT_SUN = 256;
    //Pfingstmontag
    const WHIT_MONDAY = 512;
    //Fronleichnam
    const CORPUS_CHRISTI = 1024;
    //Tag der Deutschen Einheit
    const GERMAN_UNIFICATION_DAY = 2048;
    //Reformationstag
    const REFOMATION_DAY = 4096;
    //Allerheiligen
    const ALL_SAINTS_DAY = 8192;
    //Buss- und Bettag
    const DAY_OF_REPENTANCE = 16384;
    //Heiligabend
    const CHRISTMAS_DAY = 32768;
    //1. Weihnachtstag
    const XMAS_DAY = 65536;
    //2. Weihnachtstag
    const BOXING_DAY = 131072;
    //Silvester
    const NEW_YEARS_EVE = 262144;
    //Mariae Himmelfahrt
    const ASSUMPTION = 524288;

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
     * @var \RWF\Date\DateTime
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
     * @return \RWF\Date\DateTime
     */
    public function getNewYearsDay() {

        $date = new DateTime(null, $this->timeZone);
        $date->setDate($this->year, 1, 1);
        return $date;
    }

    /**
     * gibt das Datum von Heilige Drei Koenige zurueck
     *
     * @return \RWF\Date\DateTime
     */
    public function getEpiphany() {

        $date = new DateTime(null, $this->timeZone);
        $date->setDate($this->year, 1, 6);
        return $date;
    }

    /**
     * gibt das Datum von Gruendonnerstag zurueck
     *
     * @return \RWF\Date\DateTime
     */
    public function getMaundyThursday() {

        $date = clone $this->easterDate;
        $date->sub(new \DateInterval('P3D'));
        return $date;
    }

    /**
     * gibt das Datum von Karfreitag zurueck
     *
     * @return \RWF\Date\DateTime
     */
    public function getGoodFriday() {

        $date = clone $this->easterDate;
        $date->sub(new \DateInterval('P2D'));
        return $date;
    }

    /**
     * gibt das Datum von Ostersonntag zurueck
     *
     * @return \RWF\Date\DateTime
     */
    public function getEasterDay() {

        return clone $this->easterDate;
    }

    /**
     * gibt das Datum von Ostermontag zurueck
     *
     * @return \RWF\Date\DateTime
     */
    public function getEasterMonday() {

        $date = clone $this->easterDate;
        $date->add(new \DateInterval('P1D'));
        return $date;
    }

    /**
     * gibt das Datum von Tag der Arbeit zurueck
     *
     * @return \RWF\Date\DateTime
     */
    public function getDayOfWork() {

        $date = new DateTime(null, $this->timeZone);
        $date->setDate($this->year, 5, 1);
        return $date;
    }

    /**
     * gibt das Datum von Christi Himmelfahrt zurueck
     *
     * @return \RWF\Date\DateTime
     */
    public function getAscensionDay() {

        $date = clone $this->easterDate;
        $date->add(new \DateInterval('P39D'));
        return $date;
    }

    /**
     * gibt das Datum von Pfingstsonntag zurueck
     *
     * @return \RWF\Date\DateTime
     */
    public function getWhitsun() {

        $date = clone $this->easterDate;
        $date->add(new \DateInterval('P49D'));
        return $date;
    }

    /**
     * gibt das Datum von Pfingstmontag zurueck
     *
     * @return \RWF\Date\DateTime
     */
    public function getWhitMonday() {

        $date = clone $this->easterDate;
        $date->add(new \DateInterval('P50D'));
        return $date;
    }

    /**
     * gibt das Datum von Fronleichnam zurueck
     *
     * @return \RWF\Date\DateTime
     */
    public function getCorpusChristi() {

        $date = clone $this->easterDate;
        $date->add(new \DateInterval('P60D'));
        return $date;
    }

    /**
     * gibt das Datum von Mariae Himmelfahrt zurueck
     *
     * @return \RWF\Date\DateTime
     */
    public function getAssumption() {

        $date = new DateTime(null, $this->timeZone);
        $date->setDate($this->year, 8, 15);
        return $date;
    }

    /**
     * gibt das Datum von Tag der Deutschen Einheit zurueck
     *
     * @return \RWF\Date\DateTime
     */
    public function getGermanUnificationDay() {

        $date = new DateTime(null, $this->timeZone);
        $date->setDate($this->year, 10, 3);
        return $date;
    }

    /**
     * gibt das Datum von Reformationstag zurueck
     *
     * @return \RWF\Date\DateTime
     */
    public function getReformationDay() {

        $date = new DateTime(null, $this->timeZone);
        $date->setDate($this->year, 10, 31);
        return $date;
    }

    /**
     * gibt das Datum von Allerheiligen zurueck
     *
     * @return \RWF\Date\DateTime
     */
    public function getAllSaintsDay() {

        $date = new DateTime(null, $this->timeZone);
        $date->setDate($this->year, 11, 1);
        return $date;
    }

    /**
     * gibt das Datum von Buss- und Bettag zurueck
     *
     * @return \RWF\Date\DateTime
     */
    public function getDayOfRepentance() {

        $nov23 = new DateTime(null, $this->timeZone);
        $nov23->setDate($this->year, 11, 23);

        $days = array(3 => 7, 2 => 6, 1 => 5, 0 => 4, 6 => 3, 5 => 2, 4 => 1);

        $date = clone $nov23;
        $date->sub(new \DateInterval('P' . $days[$nov23->format('w')] . 'D'));

        var_dump($date->format('r'));
    }

    /**
     * gibt das Datum von Heiligabend zurueck
     *
     * @return \RWF\Date\DateTime
     */
    public function getChristmasDay() {

        $date = new DateTime(null, $this->timeZone);
        $date->setDate($this->year, 12, 24);
        return $date;
    }

    /**
     * gibt das Datum von 1. Weihnachtstag zurueck
     *
     * @return \RWF\Date\DateTime
     */
    public function getXmasDay() {

        $date = new DateTime(null, $this->timeZone);
        $date->setDate($this->year, 12, 25);
        return $date;
    }

    /**
     * gibt das Datum von 2. Weihnachtstag zurueck
     *
     * @return \RWF\Date\DateTime
     */
    public function getBoxingDay() {

        $date = new DateTime(null, $this->timeZone);
        $date->setDate($this->year, 12, 26);
        return $date;
    }

    /**
     * gibt das Datum von Silvester zurueck
     *
     * @return \RWF\Date\DateTime
     */
    public function getNewYearsEve() {

        $date = new DateTime(null, $this->timeZone);
        $date->setDate($this->year, 12, 31);
        return $date;
    }

}
