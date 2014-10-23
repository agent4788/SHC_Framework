<?php

namespace SHC\Timer;

//Imports
use RWF\Date\DateTime;

/**
 * Schaltpunkt
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SwitchPoint {

    /**
     * Einschaltbefehl
     * 
     * @var Integer
     */
    const SWITCH_ON = 1;

    /**
     * Ausschaltbefehl
     * 
     * @var Integer
     */
    const SWITCH_OFF = 2;

    /**
     * Umschaltbefehl
     * 
     * @var Integer
     */
    const SWITCH_TOGGLE = 4;

    /**
     * ID
     * 
     * @var Integer 
     */
    protected $id = 0;

    /**
     * Name
     * 
     * @var String 
     */
    protected $name = '';

    /**
     * aktiviert/deaktiviert
     * 
     * @var Boolean 
     */
    protected $enabled = true;

    /**
     * Bedingungen
     * 
     * @var Array 
     */
    protected $conditions = array();

    /**
     * Befehl
     * 
     * @var Integer
     */
    protected $command = '';

    /**
     * Jahr
     * 
     * @var Array
     */
    protected $year = array('*');

    /**
     * Monat
     * 
     * @var Array
     */
    protected $month = array('*');

    /**
     * Tag
     * 
     * @var Array
     */
    protected $day = array('*');

    /**
     * Kalenderwoche
     * 
     * @var Array
     */
    protected $week = array('*');

    /**
     * Stunde
     * 
     * @var Array
     */
    protected $hour = array('*');

    /**
     * Minute
     * 
     * @var Array
     */
    protected $minute = array('*');

    /**
     * Gibt den Zeitstempel der letzten ausfuehrung zurueck
     * 
     * @var \RWF\Date\DateTime
     */
    protected $lastExecute = null;

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

    public function __construct() {
        
        $this->lastExecute = DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-01 00:00:00');
    }
    
    /**
     * setzt die ID
     * 
     * @param  Integer $id ID
     * @return \SHC\Timer\SwitchPoint
     */
    public function setId($id) {

        $this->id = $id;
        return $this;
    }

    /**
     * gibt die ID zurueck
     * 
     * @return Integer
     */
    public function getId() {

        return $this->id;
    }

    /**
     * setzt den Namen
     * 
     * @param  String $name Name
     * @return \SHC\Timer\SwitchPoint
     */
    public function setName($name) {

        $this->name = $name;
        return $this;
    }

    /**
     * gibt den Namen zurueck
     * 
     * @return String
     */
    public function getName() {

        return $this->name;
    }

    /**
     * Aktiviert/Deaktiviert den Schaltpunkt
     * 
     * @param  Boolean $enabled Aktiviert
     * @return \SHC\Timer\SwitchPoint
     */
    public function enable($enabled) {

        if ($enabled == true) {

            $this->enabled = true;
        } else {

            $this->enabled = false;
        }
        return $this;
    }

    /**
     * gibt an ob den Schaltpunkt Aktiviert ist
     * 
     * @return Boolean 
     */
    public function isEnabled() {

        return $this->enabled;
    }

    /**
     * fuegt eine Bedingung hinzu
     * 
     * @param  \SHC\Condition\Condition $condition
     * @return \SHC\Timer\SwitchPoint
     */
    public function addCondition(Condition $condition) {

        $this->conditions[] = $condition;
        return $this;
    }

    /**
     * loecht eine Bedingung
     * 
     * @param  \SHC\Condition\Condition $condition
     * @return \SHC\Timer\SwitchPoint
     */
    public function removeCondition(Condition $condition) {

        $this->conditions = array_diff($this->conditions, array($condition));
        return $this;
    }

    /**
     * loescht alle Bedingungen
     * 
     * @return \SHC\Timer\SwitchPoint
     */
    public function removeAllConditions() {

        $this->conditions = array();
        return $this;
    }

    /**
     * gibt eine Liste mit allen Bedingungen zurueck
     *
     * @return Array
     */
    public function listConditions() {

        return $this->conditions;
    }

    /**
     * setzt das Jahr
     * 
     * @param  Array $year Liste mit den Jahren
     * @return \SHC\Timer\SwitchPoint
     */
    public function setYear(array $year) {
        
        $this->year = $year;
        return $this;
    }

    /**
     * gibt eine Liste mit den Jahren in denen der Schaltpunkt ausgefuehrt werden soll zurueck
     * 
     * @return Array
     */
    public function getYear() {
        
        return $this->year;
    }

    /**
     * setzt den Monat
     * 
     * @param  Array $month Liste mit den Monaten
     * @return \SHC\Timer\SwitchPoint
     */
    public function setMonth(array $month) {
        
        $this->month = $month;
        return $this;
    }

    /**
     * gibt eine Liste mit den Monaten in denen der Schaltpunkt ausgefuehrt werden soll zurueck
     * 
     * @return Array
     */
    public function getMonth() {
        
        return $this->month;
    }

    /**
     * setzt den Tag
     * 
     * @param  Array $day Liste mit den Tagen
     * @return \SHC\Timer\SwitchPoint
     */
    public function setDay(array $day) {
        
        $this->day = $day;
        return $this;
    }

    /**
     * gibt eine Liste mit den Tage in denen der Schaltpunkt ausgefuehrt werden soll zurueck
     * 
     * @return Array
     */
    public function getDay() {
        
        return $this->day;
    }

    /**
     * setzt die Kalenderwoche
     * 
     * @param  Array $week Liste mit den Kalenderwochen
     * @return \SHC\Timer\SwitchPoint
     */
    public function setWeek(array $week) {
        
        $this->week = $week;
        return $this;
    }

    /**
     * gibt eine Liste mit den kalenderwochen in denen der Schaltpunkt ausgefuehrt werden soll zurueck
     * 
     * @return Array
     */
    public function getWeek() {
        
        return $this->week;
    }

    /**
     * setzt die Stunde
     * 
     * @param  Array $hour Liste mit den Stunden
     * @return \SHC\Timer\SwitchPoint
     */
    public function setHour(array $hour) {
        
        $this->hour = $hour;
        return $this;
    }

    /**
     * gibt eine Liste mit den Stunden in denen der Schaltpunkt ausgefuehrt werden soll zurueck
     * 
     * @return Array
     */
    public function getHour() {
        
        return $this->hour;
    }

    /**
     * setzt die Minute
     * 
     * @param  Array $minute Liste mit den Minuten
     * @return \SHC\Timer\SwitchPoint
     */
    public function setMinute(array $minute) {
        
        $this->minute = $minute;
        return $this;
    }

    /**
     * gibt eine Liste mit den Minuten in denen der Schaltpunkt ausgefuehrt werden soll zurueck
     * 
     * @return Array
     */
    public function getMinute() {
        
        return $this->minute;
    }

    /**
     * setzt den Zeitstempel der letzten ausfuehrung
     * 
     * @param  \RWF\Date\DateTime $lastExecute letzte ausfuehrung
     * @return \SHC\Timer\SwitchPoint
     */
    public function setLastExecute(DateTime $lastExecute) {
        
        $this->lastExecute = $lastExecute;
        return $this;
    }

    /**
     * gibt die Zeit der letzten ausfuehrung zurueck
     *
     * @return \RWF\Date\DateTime
     */
    public function getLastExecute() {
        
        return $this->lastExecute;
    }

    /**
     * setzt den Befehl
     * 
     * @param  Integer $command Befehl
     * @return \SHC\Timer\SwitchPoint
     */
    public function setCommand($command) {
        
        $this->command = $command;
        return $this;
    }
    
    /**
     * gibt den Befehl zurueck
     * 
     * @return Integer
     */
    public function getCommand() {
        
        return $this->command;
    }

    /**
     * gibt an ob der Schaltpunkt erfuellt ist
     * 
     * @return Boolean
     */
    public function isSatisfies() {

        $daysOfWeek = array(
            0 => 'mon',
            1 => 'tue',
            2 => 'wed',
            3 => 'thu',
            4 => 'fri',
            5 => 'sat',
            6 => 'sun'
        );

        //Pruefen ob Schaltpunkt aktiv
        if($this->enabled == false) {
            
            return false;
        }
        
        //aktuelle Zeit
        $now = DateTime::now();
        
        //Minute pruefen
        if($this->minute[0] != '*' && !in_array($now->getMinute(), $this->minute)) {
            
            return false;
        }
        
        //Stunde pruefen
        if($this->hour[0] != '*' && !in_array($now->getHour(), $this->hour)) {
            
            return false;
        }
        
        //Tag pruefen
        if(($this->day[0] != '*' && (!in_array($daysOfWeek[$now->getDayOfWeek()], $this->day) || !in_array($now->getDay(), $this->day)))) {

            return false;
        }
        
        //Kalenderwoche pruefen
        if ($this->week[0] != '*' && !in_array($now->getWeekOfYear(), $this->week)) {

            return false;
        }

        //Month pruefen
        if ($this->month[0] != '*' && !in_array($now->getMonth(), $this->month)) {

            return false;
        }

        //Year pruefen
        if ($this->year[0] != '*' && !in_array($now->getYear(), $this->year)) {

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
        
        //Pruefen ob die letzte ausfuehrung nicht der aktuellen entspricht
        if($now->format('Y-m-d H:i') == $this->lastExecute->format('Y-m-d H:i')) {
            
            //Schaltpunkt wurde schon ausgefuehrt
            return false;
        }
        $this->lastExecute = $now;
        
        //alle Schaltpunkte und Bedingungen sind wahr
        SwitchPointEditor::getInstance()->editExecutionTime($this->getId(), DateTime::now());
        return true;
    }

}
