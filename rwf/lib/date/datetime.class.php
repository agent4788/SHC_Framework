<?php

namespace RWF\Date;

//Imports
use RWF\Core\RWF;

/**
 * Datums und Zeitfunktionen
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DateTime extends \DateTime {
    
    /**
     * aktuelle Zeit
     * 
     * @var String
     */

    const NOW = 'now';

    /**
     * Datum Morgen
     * 
     * @var String
     */
    const TOMORROW = 'tomorrow';

    /**
     * Datum Heute
     *
     * @var String
     */
    const TODAY = 'today';

    /**
     * Datum Gestern
     *
     * @var String
     */
    const YESTERDAY = 'yesterday';

    /**
     * gibt das DateTime Objekt des gestrigen Tages zurueck
     *
     * @param  \DateTimeZone $timezone Zeitzone
     * @return \RWF\Date\DateTime
     */
    public static function previousDay(\DateTimeZone $timezone = null) {

        return new DateTime(self::YESTERDAY, $timezone);
    }

    /**
     * gibt das DateTime Objekt des aktuellen Tages zurueck
     *
     * @param  \DateTimeZone $timezone Zeitzone
     * @return \RWF\Date\DateTime
     */
    public static function today(\DateTimeZone $timezone = null) {

        return new DateTime(self::TODAY, $timezone);
    }

    /**
     * gibt das DateTime Objekt des aktuellen Tages und Zeit zurueck
     *
     * @param  \DateTimeZone $timezone Zeitzone
     * @return \RWF\Date\DateTime
     */
    public static function now(\DateTimeZone $timezone = null) {

        return new DateTime(self::NOW, $timezone);
    }

    /**
     * gibt das DateTime Objekt des morgigen Tages zurueck
     *
     * @param  \DateTimeZone $timezone Zeitzone
     * @return \RWF\Date\DateTime
     */
    public static function nextDay(\DateTimeZone $timezone = null) {

        return new DateTime(self::TOMORROW, $timezone);
    }

    /**
     * erzeugt ein DateTime Objekt aus einer Formatiertern Datums/Zeitangabe
     * 
     * @param String $format Format
     * @param String $time   Datums/Zeitangabe
     * @param Object $object [optional]
     * @return \RWF\Date\DateTime
     */
    public static function createFromFormat($format, $time, $object = null) {

        //Zeitzone
        if ($object === null && RWF::getSetting('rwf.date.Timezone') != '') {

            $object = new \DateTimeZone(RWF::getSetting('rwf.date.Timezone'));
        } elseif ($object === null) {

            $object = new \DateTimeZone('Europe/London');
        }
        
        $date = parent::createFromFormat($format, $time, $object);
        $dateEx = new DateTime();
        $dateEx->setDate($date->format('Y'), $date->format('m'), $date->format('d'));
        $dateEx->setTime($date->format('H'), $date->format('i'), $date->format('s'));
        $dateEx->setTimezone($date->getTimezone());
        return $dateEx;
    }
    
    /**
     * gibt ein DateTime Objekt mit dem Datum zurueck
     * 
     * @param  String $date Datum aus der Datenbank
     * @return \RWF\Date\DateTime
     */
    public static function createFromDatabaseDate($date) {
        
        return self::createFromFormat('Y-m-d', $date);
    }
    
    /**
     * gibt ein DateTime Objekt mit der Zeit zurueck
     * 
     * @param  String $time Zeit aus der Datenbank
     * @return \RWF\Date\DateTime
     */
    public static function createFromDatabaseTime($time) {
        
        return self::createFromFormat('H:i:s', $time);
    }
    
    /**
     * gibt ein DateTime Objekt mit dem Datum und der Zeit zurueck
     * 
     * @param  String $datetime Datum und Zeit aus der Datenbank
     * @return \RWF\Date\DateTime
     */
    public static function createFromDatabaseDateTime($datetime) {
        
        return self::createFromFormat('Y-m-d H:i:s', $datetime);
    }
    
    /**
     * erstellt ein Datumsobjekt aus einem anderen
     * 
     * @param  \DateTime $date Datumsobjekt
     * @return \RWF\Date\DateTime
     */
    public static function createFormObject(\DateTime $date) {
        
        $dateEx = new DateTime();
        $dateEx->setDate($date->format('Y'), $date->format('m'), $date->format('d'));
        $dateEx->setTime($date->format('H'), $date->format('i'), $date->format('s'));
        $dateEx->setTimezone($date->getTimezone());
        return $dateEx;
    }

    /**
     * @param String        $time                  Zeitstring
     * @param \DateTimeZone $timezone              Zeitzone
     */
    public function __construct($time = null, \DateTimeZone $timezone = null) {

        //Zeitzone
        if ($timezone === null && RWF::getSetting('rwf.date.Timezone') != '') {

            $timezone = new \DateTimeZone(RWF::getSetting('rwf.date.Timezone'));
        } elseif ($timezone === null) {

            $timezone = new \DateTimeZone('Europe/London');
        }

        parent::__construct($time, $timezone);
    }

    /**
     * gibt das Formatierte Datum/Zeit zurueck
     * 
     * @param  String $format Format
     * @return String
     * @see \DateTime::format()
     */
    public function format($format) {

        return parent::format($format);
    }
    
    /**
     * gibt das Datum im Datenbankformat zurueck
     * 
     * @return String
     */
    public function getDatabaseDate() {
        
        return parent::format('Y-m-d');
    }
    
    /**
     * gibt die Zeit im Datenbankformat zurueck
     * 
     * @return String
     */
    public function getDatabaseTime() {
        
        return parent::format('H:i:s');
    }
    
    /**
     * gibt das Datum und die Zeit im Datenbankformat zurueck
     * 
     * @return String
     */
    public function getDatabaseDateTime() {
        
        return parent::format('Y-m-d H:i:s');
    }

    /**
     * gibt das Jahr vierstellig zurueck
     * 
     * @return String
     */
    public function getYear() {

        return $this->format('Y');
    }

    /**
     * gibt den Monat zweistellig zurueck
     *
     * @return String
     */
    public function getMonth() {

        return $this->format('m');
    }

    /**
     * gibt den Tage zweistellig zurueck
     *
     * @return String
     */
    public function getDay() {

        return $this->format('d');
    }

    /**
     * gibt die Stunde zweistellig zurueck
     *
     * @return String
     */
    public function getHour() {

        return $this->format('H');
    }

    /**
     * gibt die Minute zweistellig zurueck
     *
     * @return String
     */
    public function getMinute() {

        return $this->format('i');
    }

    /**
     * gibt die Sekunde zweistellig zurueck
     *
     * @return String
     */
    public function getSecond() {

        return $this->format('s');
    }

    /**
     * gibt die Nummer des Wochentags zurueck (0 = Montag -> 6 Sonntag)
     * 
     * @return String
     */
    public function getDayOfWeek() {

        $day = $this->format('w');
        $days = array(1 => '0', 2 => '1', 3 => '2', 4 => '3', 5 => '4', 6 => '5', 0 => '6');
        return $days[$day];
    }

    /**
     * gibt die Tagesnummer zurueck (0 - 365)
     *
     * @return String
     */
    public function getDayOfYear() {

        return $this->format('z');
    }

    /**
     * gibt die Wochennummer zurueck
     * 
     * @return String
     */
    public function getWeekOfYear() {

        return $this->format('W');
    }

    /**
     * gibt das Quartal des Jahres zurueck
     * 
     * @return String
     */
    public function getQuarterOfYear() {

        $month = intval($this->format('n'));
        if ($month <= 3) {

            return 1;
        } elseif ($month >= 4 && $month <= 6) {

            return 2;
        } elseif ($month >= 7 && $month <= 9) {

            return 3;
        }

        return 4;
    }

    /**
     * gibt an ob das Jahr ein Schaltjahr ist
     * 
     * @return Boolean
     */
    public function isLeapYear() {

        $year = $this->format('Y');
        if (($year % 400) == 0 || (($year % 4) == 0 && ($year % 100) != 0)) {

            return true;
        }
        return false;
    }

    /**
     * gibt an ob das Datum in der Vergangenheit liegt
     * 
     * @return Boolean
     */
    public function isPast() {

        $date = new DateTime(self::NOW, $this->getTimezone());
        if ($this < $date) {

            return true;
        }

        return false;
    }

    /**
     * gibt an ob das Datum in der Zukunft liegt
     *
     * @return Boolean
     */
    public function isFuture() {

        $date = new DateTime(self::NOW, $this->getTimezone());
        if ($this > $date) {

            return true;
        }

        return false;
    }
    
    /**
     * prueft ob das Datumsobjekt der aktuelle Tag ist
     * 
     * @return Boolean
     */
    public function isToday() {
        
        $diff = self::today()->diff($this);
        if($diff->format('%R%a') == 0) {
            
            return true;
        }
        return false;
    }
    
    /**
     * prueft ob das Datumsobjekt der naechste Tag ist
     * 
     * @return Boolean
     */
    public function isTomorrow() {
        
        $diff = self::today()->diff($this);
        if($diff->format('%R%a') == 1) {
            
            return true;
        }
        return false;
    }
    
    /**
     * prueft ob das Datumsobjekt der vorherige Tag ist
     * 
     * @return Boolean
     */
    public function isYesterday() {
        
        $diff = self::today()->diff($this);
        if($diff->format('%R%a') == -1) {
            
            return true;
        }
        return false;
    }
    
    /**
     * gibt die Anzahl der Tage zuruck die zwischen heute und dem Datum des Objektes liegt
     * 
     * @return Integer
     */
    public function DiffDaysFromToday() {
        
        $diff = self::today()->diff($this);
        return (int) $diff->format('%R%a');
    }
    
    /**
     * gibt ein Datumsobjekt mit dem Datum/Uhrzeit des Sonnenaufgangs zurueck
     * 
     * @return \RWF\Date\DateTime
     */
    public function getSunrise() {
        
        $sunrise = date_sunrise($this->getTimestamp(), SUNFUNCS_RET_TIMESTAMP, RWF::getSetting('rwf.date.Latitude'), RWF::getSetting('rwf.date.Longitude'), 90.833333, ($this->format('I') == 1 ? 2 : 1));
        $date = new DateTime();
        $date->setTimestamp($sunrise);
        $offset = RWF::getSetting('rwf.date.sunriseOffset');
        if($offset != 0) {
            if($offset > 0) {
                $date->add(new \DateInterval('PT'. $offset .'M'));
            } elseif($offset <= 0) {
                $date->sub(new \DateInterval('PT'. abs($offset) .'M'));
            }
        }
        
        return $date;
    }
    
    /**
     * gibt ein Datumsobjekt mit dem Datum/Uhrzeit des Sonnenuntergang zurueck
     * 
     * @return \RWF\Date\DateTime
     */
    public function getSunset() {
        
        $sunset = date_sunset($this->getTimestamp(), SUNFUNCS_RET_TIMESTAMP, RWF::getSetting('rwf.date.Latitude'), RWF::getSetting('rwf.date.Longitude'), 90.833333, ($this->format('I') == 1 ? 2 : 1));
        $date = new DateTime();
        $date->setTimestamp($sunset);
        $offset = RWF::getSetting('rwf.date.sunsetOffset');
        if($offset != 0) {
            if($offset > 0) {
                $date->add(new \DateInterval('PT'. $offset .'M'));
            } elseif($offset <= 0) {
                $date->sub(new \DateInterval('PT'. abs($offset) .'M'));
            }
        }
        
        return $date;
    }

    /**
     * konvertiert das Objekt als String
     * 
     * @return String
     */
    public function __toString() {
        
        return $this->format('Y-m-d H:i:s');
    }
}
