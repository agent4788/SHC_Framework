<?php

namespace RWF\Date;

//Imports
use RWF\Core\RWF;

/**
 * Sprachbezogene Datumsklasse
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */

class LanguageDateTime extends DateTime {
    
    /**
     * gibt das DateTime Objekt des gestrigen Tages zurueck
     *
     * @param  DateTimeZone $timezone Zeitzone
     * @return \RWF\Date\LanguageDateTime
     */
    public static function previousDay(DateTimeZone $timezone = null) {

        return new LanguageDateTime(self::YESTERDAY, $timezone);
    }

    /**
     * gibt das DateTime Objekt des aktuellen Tages zurueck
     *
     * @param  DateTimeZone $timezone Zeitzone
     * @return \RWF\Date\LanguageDateTime
     */
    public static function today(DateTimeZone $timezone = null) {

        return new LanguageDateTime(self::TODAY, $timezone);
    }

    /**
     * gibt das DateTime Objekt des aktuellen Tages und Zeit zurueck
     *
     * @param  DateTimeZone $timezone Zeitzone
     * @return \RWF\Date\LanguageDateTime
     */
    public static function now(DateTimeZone $timezone = null) {

        return new LanguageDateTime(self::NOW, $timezone);
    }

    /**
     * gibt das DateTime Objekt des morgigen Tages zurueck
     *
     * @param  DateTimeZone $timezone Zeitzone
     * @return \RWF\Date\LanguageDateTime
     */
    public static function nextDay(DateTimeZone $timezone = null) {

        return new LanguageDateTime(self::TOMORROW, $timezone);
    }

    /**
     * erzeugt ein DateTimeExtendet Objekt aus einer Formatiertern Datums/Zeitangabe
     * 
     * @param String $format Format
     * @param String $time   Datums/Zeitangabe
     * @param Object $object [optional]
     * @return \RWF\Date\LanguageDateTime
     */
    public static function createFromFormat($format, $time, $object = null) {

        //Zeitzone
        if ($object === null && defined('DATETIME_TIMEZONE')) {

            $object = new \DateTimeZone(DATETIME_TIMEZONE);
        } elseif ($object === null) {

            $object = new \DateTimeZone('Europe/London');
        }
        
        $date = parent::createFromFormat($format, $time, $object);
        $dateEx = new LanguageDateTime();
        $dateEx->setDate($date->format('Y'), $date->format('m'), $date->format('d'));
        $dateEx->setTime($date->format('H'), $date->format('i'), $date->format('s'));
        $dateEx->setTimezone($date->getTimezone());
        return $dateEx;
    }
    
    /**
     * gibt ein DateTime Objekt mit dem Datum zurueck
     * 
     * @param  String $date Datum aus der Datenbank
     * @return \RWF\Date\LanguageDateTime
     */
    public static function createFromDatabaseDate($date) {
        
        return self::createFromFormat('Y-m-d', $date);
    }
    
    /**
     * gibt ein DateTime Objekt mit der Zeit zurueck
     * 
     * @param  String $time Zeit aus der Datenbank
     * @return \RWF\Date\LanguageDateTime
     */
    public static function createFromDatabaseTime($time) {
        
        return self::createFromFormat('H:i:s', $time);
    }
    
    /**
     * gibt ein DateTime Objekt mit dem Datum und der Zeit zurueck
     * 
     * @param  String $datetime Datum und Zeit aus der Datenbank
     * @return \RWF\Date\LanguageDateTime
     */
    public static function createFromDatabaseDateTime($datetime) {
        
        return self::createFromFormat('Y-m-d H:i:s', $datetime);
    }
    
    /**
     * erstellt ein Datumsobjekt aus einem anderen
     * 
     * @param  DateTime $date Datumsobjekt
     * @return \RWF\Date\LanguageDateTime
     */
    public static function createFormObject(\DateTime $date) {
        
        $dateEx = new LanguageDateTime();
        $dateEx->setDate($date->format('Y'), $date->format('m'), $date->format('d'));
        $dateEx->setTime($date->format('H'), $date->format('i'), $date->format('s'));
        $dateEx->setTimezone($date->getTimezone());
        return $dateEx;
    }
    
    /**
     * formatiert ein Datum zur Anzeige
     * 
     * @param  String  $format       Datumsformat
     * @param  Boolean $withDayNames Tagesnamen anzeigen
     * @return String
     */
    public function showDate($format = '', $withDayNames = true) {
        
        //Standard Format falls nicht uebergeben
        if($format == '') {
            
            $format = RWF::getSetting('rwf.date.defaultDateFormat');
        }
        
        //Datum Formatieren
        if($withDayNames == true) {
            
            $diff = $this->DiffDaysFromToday();
            
            if($diff > 14) {
                
                return $this->format($format);
            } elseif($diff > 1 && $diff <= 14) {
                
                return RWF::getLanguage()->get('global.date.nextDays', abs($diff));
            } elseif($diff == 1) {
                
                return RWF::getLanguage()->val('global.date.tomorrow');
            } elseif($diff == 0) {
                
                return RWF::getLanguage()->val('global.date.totay');
            } elseif($diff == -1) {
                
                return RWF::getLanguage()->val('global.date.tomorrow');
            } elseif($diff < -1 && $diff >= -14) {
                
                return RWF::getLanguage()->get('global.date.yesterday', abs($diff));
            } else {
                
                return $this->format($format);
            }
        } else {
            
            return $this->format($format);
        }
    }
    
    /**
     * formatiert eine Zeit zur Aazeige
     * 
     * @param  String $format Zeitformat
     * @return String
     */
    public function showTime($format = '') {
        
        //Standard Format falls nicht uebergeben
        if($format == '') {
            
            $format = RWF::getSetting('rwf.date.defaultTimeFormat');
        }
        
        return $this->format($format);
    }
    
    /**
     * formatiert ein Datum und eine Zeit zur Anzeige
     * 
     * @param  String  $dateFormat       Datumsformat
     * @param  String  $timeFormat       Zeitformat
     * @param  Boolean $withDayNames     Tagesnamen verwenden
     * @param  Boolean $timeOnlyThisDays Uhrzeit nur fuer die aktuellen 3 Tage anzeigen
     * @return String
     */
    public function showDateTime($dateFormat = '', $timeFormat = '', $withDayNames = true, $timeOnlyThisDays = true) {
        
        if($timeOnlyThisDays == true) {
            
            $diff = $this->DiffDaysFromToday();
            if($diff >= -1 && $diff <= 1 && $withDayNames == true) {
                
                return $this->showDate($dateFormat, $withDayNames) .' '. $this->showTime($timeFormat);
            } else {
                
                return $this->showDate($dateFormat, $withDayNames);
            }
        }
        return $this->showDate($dateFormat, $withDayNames) .' '. $this->showTime($timeFormat);
    }
}

