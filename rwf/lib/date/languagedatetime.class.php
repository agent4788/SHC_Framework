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
     * @param  \DateTimeZone $timezone Zeitzone
     * @return \RWF\Date\LanguageDateTime
     */
    public static function previousDay(\DateTimeZone $timezone = null) {

        return new LanguageDateTime(self::YESTERDAY, $timezone);
    }

    /**
     * gibt das DateTime Objekt des aktuellen Tages zurueck
     *
     * @param  \DateTimeZone $timezone Zeitzone
     * @return \RWF\Date\LanguageDateTime
     */
    public static function today(\DateTimeZone $timezone = null) {

        return new LanguageDateTime(self::TODAY, $timezone);
    }

    /**
     * gibt das DateTime Objekt des aktuellen Tages und Zeit zurueck
     *
     * @param  \DateTimeZone $timezone Zeitzone
     * @return \RWF\Date\LanguageDateTime
     */
    public static function now(\DateTimeZone $timezone = null) {

        return new LanguageDateTime(self::NOW, $timezone);
    }

    /**
     * gibt das DateTime Objekt des morgigen Tages zurueck
     *
     * @param  \DateTimeZone $timezone Zeitzone
     * @return \RWF\Date\LanguageDateTime
     */
    public static function nextDay(\DateTimeZone $timezone = null) {

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

            $timeDiff = self::now()->diff($this);

            if($timeDiff->invert == 0 && $timeDiff->d > 14) {

                return $this->format($format);
            } elseif($timeDiff->invert == 0 && $timeDiff->d > 1 && $timeDiff->d <= 14) {

                return RWF::getLanguage()->get('global.date.nextDays', $timeDiff->d);
            } elseif($timeDiff->invert == 0 && $timeDiff->d == 1) {

                return RWF::getLanguage()->val('global.date.tomorrow');
            } elseif($timeDiff->d == 0) {

                if($timeDiff->invert == 1 && $timeDiff->d < 1 && self::now()->format('d') != $this->format('d')) {

                    return RWF::getLanguage()->val('global.date.yesterday');
                } elseif($timeDiff->invert == 0 && $timeDiff->d < 1 && self::now()->format('d') != $this->format('d')) {

                    return RWF::getLanguage()->val('global.date.tomorrow');
                }
                return RWF::getLanguage()->val('global.date.totay');
            } elseif($timeDiff->invert == 1 && $timeDiff->d == 1) {

                return RWF::getLanguage()->val('global.date.yesterday');
            } elseif($timeDiff->invert == 1 && $timeDiff->d < -1 && $timeDiff->d >= 14) {

                return RWF::getLanguage()->get('global.date.yesterday', $timeDiff->d);
            } else {

                return $this->format($format);
            }
        } else {

            return $this->format($format);
        }
    }

    /**
     * formatiert ein Datum zur Anzeige
     *
     * @return String
     */
    public function showTimeline() {

        $format = RWF::getSetting('rwf.date.defaultDateFormat') .' '. RWF::getSetting('rwf.date.defaultTimeFormat');

        $timeDiff = self::now()->diff($this);
        if($timeDiff->invert == 0 && $timeDiff->d > 14) {

            return $this->format($format);
        } elseif($timeDiff->invert == 0 && $timeDiff->d > 1 && $timeDiff->d <= 14) {

            return RWF::getLanguage()->get('global.date.nextDays', $timeDiff->d);
        } elseif($timeDiff->invert == 0 && $timeDiff->d == 1) {

            return RWF::getLanguage()->val('global.date.tomorrow') .' '. $this->format(RWF::getSetting('rwf.date.defaultTimeFormat'));
        } elseif($timeDiff->d == 0) {

            //Zeitdifferenz ermitteln
            $time = '';
            if($timeDiff->invert == 1 && $timeDiff->h > 0) {

                //mehr als eine Stunde
                if($timeDiff->h == 1) {

                    $time = RWF::getLanguage()->get('global.date.oneHourAgo');
                } else {

                    $time = RWF::getLanguage()->get('global.date.viewHoursAgo', $timeDiff->h);
                }
            } elseif($timeDiff->invert == 1 && $timeDiff->i > 0) {

                //mehr als eine Minute
                if($timeDiff->i == 1) {

                    $time = RWF::getLanguage()->get('global.date.oneMinuteAgo');
                } else {

                    $time = RWF::getLanguage()->get('global.date.viewMinutesAgo', $timeDiff->i);
                }
            } elseif($timeDiff->invert == 1 && $timeDiff->s > 0) {

                //Sekunden
                if($timeDiff->s == 1) {

                    $time = RWF::getLanguage()->get('global.date.oneSecondAgo');
                } else {

                    $time = RWF::getLanguage()->get('global.date.viewSecondsAgo', $timeDiff->s);
                }
            } elseif($timeDiff->invert == 0 && $timeDiff->h > 0) {

                //mehr als eine Stunde
                if($timeDiff->h == 1) {

                    $time = RWF::getLanguage()->get('global.date.oneHour');
                } else {

                    $time = RWF::getLanguage()->get('global.date.viewHours', $timeDiff->h);
                }
            } elseif($timeDiff->invert == 0 && $timeDiff->i > 0) {

                //mehr als eine Minute
                if($timeDiff->i == 1) {

                    $time = RWF::getLanguage()->get('global.date.oneMinute');
                } else {

                    $time = RWF::getLanguage()->get('global.date.viewMinutes', $timeDiff->i);
                }
            } elseif($timeDiff->invert == 0 && $timeDiff->s > 0) {

                //Sekunden
                if($timeDiff->s == 0) {

                    $time = RWF::getLanguage()->get('global.date.oneSecond');
                } else {

                    $time = RWF::getLanguage()->get('global.date.viewSeconds', $timeDiff->s);
                }
            } elseif($timeDiff->s == 0 && $timeDiff->i == 0 && $timeDiff->h == 0) {

                $time = RWF::getLanguage()->get('global.date.now');
            }

            if($timeDiff->invert == 1 && $timeDiff->d < 1 && self::now()->format('d') != $this->format('d')) {

                return RWF::getLanguage()->val('global.date.yesterday') .' '. $this->format(RWF::getSetting('rwf.date.defaultTimeFormat'));
            } elseif($timeDiff->invert == 0 && $timeDiff->d < 1 && self::now()->format('d') != $this->format('d')) {

                return RWF::getLanguage()->val('global.date.tomorrow') .' '. $this->format(RWF::getSetting('rwf.date.defaultTimeFormat'));
            }
            return $time;
        } elseif($timeDiff->invert == 1 && $timeDiff->d == 1) {

            return RWF::getLanguage()->val('global.date.yesterday') .' '. $this->format(RWF::getSetting('rwf.date.defaultTimeFormat'));
        } elseif($timeDiff->invert == 1 && $timeDiff->d < -1 && $timeDiff->d >= 14) {

            return RWF::getLanguage()->get('global.date.previousDays', $timeDiff->d);
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
                
                return $this->showDate($dateFormat, $withDayNames) .' '. $this->showTime($timeFormat);
            }
        }
        return $this->showDate($dateFormat, $withDayNames) .' '. $this->showTime($timeFormat);
    }
}

