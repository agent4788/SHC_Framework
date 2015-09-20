<?php

namespace SHC\Timer;

//Imports
use RWF\Core\RWF;
use RWF\Date\DateTime;
use RWF\Util\String;
use SHC\Condition\Condition;

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
     * gibt an ob der Schaltpunkt ausgefuehrt wurde
     *
     * @var Boolean
     */
    protected $executed = false;

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
     * @param  \RWF\Date\DateTime $lastExecute   letzte ausfuehrung
     * @param  Boolean            $resetExecuted den Ausgefuehrt Status zuruecksetzen
     * @return \SHC\Timer\SwitchPoint
     */
    public function setLastExecute(DateTime $lastExecute, $resetExecuted = false) {
        
        $this->lastExecute = $lastExecute;
        if($resetExecuted == true) {

            $this->executed = false;
        }
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
     * gibt an ob der Schaltpunkt ausgefuehrt wurde
     *
     * @return Boolean
     */
    public function isExecuted() {

        return $this->executed;
    }

    /**
     * gibt ein HTML Fragment fuer ein Tooltip zurueck
     *
     * @return String
     */
    public function fetchTooltip() {

        $html = '';

        //Befehl
        $html .= '<div class="tootlip_row">';
        $html .= '<span class="tooltip_strong">' . RWF::getLanguage()->get('acp.switchpointsManagment.tooltip.command') . '</span>:';
        $html .= '<span>';
        $html .= ($this->command == 1 ? RWF::getLanguage()->get('global.on') : RWF::getLanguage()->get('global.off'));
        $html .= '</span>';
        $html .= '</div>';

        //Bedingungen
        $html .= '<div class="tootlip_row">';
        $html .= '<span class="tooltip_strong">' . RWF::getLanguage()->get('acp.switchpointsManagment.tooltip.condition') . '</span>:';
        $html .= '<span>';
        $comma = ' ';
        if (count($this->conditions)) {

            foreach ($this->conditions as $condition) {

                /* @var $condition \SHC\Condition\Condition */
                $html .= $comma . String::encodeHTML($condition->getName());
                $comma = ', </br>';
            }
        } else {

            $html .= RWF::getLanguage()->get('acp.switchpointsManagment.tooltip.condition.none');
        }
        $html .= '</span>';
        $html .= '</div>';

        //Jahr
        $html .= '<div class="tootlip_row">';
        $html .= '<span class="tooltip_strong">' . RWF::getLanguage()->get('acp.switchpointsManagment.tooltip.year') . '</span>:';
        $html .= '<span>';
        if (isset($this->year[0]) && $this->year[0] != '*') {

            $html .= ' '. implode(', ', $this->year);
        } else {

            $html .= RWF::getLanguage()->get('acp.switchpointsManagment.tooltip.year.every');
        }
        $html .= '</span>';
        $html .= '</div>';

        //Monat
        $html .= '<div class="tootlip_row">';
        $html .= '<span class="tooltip_strong">' . RWF::getLanguage()->get('acp.switchpointsManagment.tooltip.month') . '</span>:';
        $html .= '<span>';
        if (isset($this->month[0]) && $this->month[0] != '*') {

            $str = implode(', ', $this->month);
            $str = str_replace(
                array(
                    1,
                    2,
                    3,
                    4,
                    5,
                    6,
                    7,
                    8,
                    9,
                    10,
                    11,
                    12
                ), array(
                    RWF::getLanguage()->get('global.date.month.1.short'),
                    RWF::getLanguage()->get('global.date.month.2.short'),
                    RWF::getLanguage()->get('global.date.month.3.short'),
                    RWF::getLanguage()->get('global.date.month.4.short'),
                    RWF::getLanguage()->get('global.date.month.5.short'),
                    RWF::getLanguage()->get('global.date.month.6.short'),
                    RWF::getLanguage()->get('global.date.month.7.short'),
                    RWF::getLanguage()->get('global.date.month.8.short'),
                    RWF::getLanguage()->get('global.date.month.9.short'),
                    RWF::getLanguage()->get('global.date.month.10.short'),
                    RWF::getLanguage()->get('global.date.month.11.short'),
                    RWF::getLanguage()->get('global.date.month.12.short')
                ), $str
            );
            $html .= ' '. $str;
        } else {

            $html .= RWF::getLanguage()->get('acp.switchpointsManagment.tooltip.month.every');
        }
        $html .= '</span>';
        $html .= '</div>';

        //Tag
        $html .= '<div class="tootlip_row">';
        $html .= '<span class="tooltip_strong">' . RWF::getLanguage()->get('acp.switchpointsManagment.tooltip.day') . '</span>:';
        $html .= '<span>';
        if (isset($this->day[0]) && $this->day[0] != '*') {

            $str = implode(', ', $this->day);
            $str = str_replace(
                array(
                    'mon',
                    'tue',
                    'wed',
                    'thu',
                    'fri',
                    'sat',
                    'sun'
                ), array(
                    RWF::getLanguage()->get('global.date.weekDay.mon.short'),
                    RWF::getLanguage()->get('global.date.weekDay.tue.short'),
                    RWF::getLanguage()->get('global.date.weekDay.wed.short'),
                    RWF::getLanguage()->get('global.date.weekDay.thu.short'),
                    RWF::getLanguage()->get('global.date.weekDay.fri.short'),
                    RWF::getLanguage()->get('global.date.weekDay.sat.short'),
                    RWF::getLanguage()->get('global.date.weekDay.sun.short')
                ), $str
            );
            $html .= ' '. $str;
        } else {

            $html .= RWF::getLanguage()->get('acp.switchpointsManagment.tooltip.day.every');
        }
        $html .= '</span>';
        $html .= '</div>';

        //Stunde
        $html .= '<div class="tootlip_row">';
        $html .= '<span class="tooltip_strong">' . RWF::getLanguage()->get('acp.switchpointsManagment.tooltip.hour') . '</span>:';
        $html .= '<span>';
        if (isset($this->hour[0]) && $this->hour[0] != '*') {

            $html .= ' '. implode(', ', $this->hour);
        } else {

            $html .= RWF::getLanguage()->get('acp.switchpointsManagment.tooltip.hour.every');
        }
        $html .= '</span>';
        $html .= '</div>';

        //Minute
        $html .= '<div class="tootlip_row">';
        $html .= '<span class="tooltip_strong">' . RWF::getLanguage()->get('acp.switchpointsManagment.tooltip.minute') . '</span>:';
        $html .= '<span>';
        if (isset($this->minute[0]) && $this->minute[0] != '*') {

            $html .= ' '. implode(', ', $this->minute);
        } else {

            $html .= RWF::getLanguage()->get('acp.switchpointsManagment.tooltip.minute.every');
        }
        $html .= '</span>';
        $html .= '</div>';

        return $html;
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
        if(($this->day[0] != '*' && !in_array($daysOfWeek[$now->getDayOfWeek()], $this->day) && !in_array($now->getDay(), $this->day))) {

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

        //alle Schaltpunkte und Bedingungen sind wahr
        $this->executed = true;
        return true;
    }

}
