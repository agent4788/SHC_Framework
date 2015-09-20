<?php

namespace SHC\Timer;

//Imports
use RWF\Util\String;
use SHC\Core\SHC;
use RWF\Date\DateTime;
use SHC\Condition\ConditionEditor;
use SHC\Condition\Condition;

/**
 * Schaltpunkt verwaltung
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SwitchPointEditor {

    /**
     * nach ID sortieren
     * 
     * @var String
     */
    const SORT_BY_ID = 'id';

    /**
     * nach Namen sortieren
     * 
     * @var String
     */
    const SORT_BY_NAME = 'name';

    /**
     * nicht sortieren
     * 
     * @var String
     */
    const SORT_NOTHING = 'unsorted';

    /**
     * Schaltpunkte
     * 
     * @var Array 
     */
    protected $switchPoints = array();

    /**
     * Singleton Instanz
     * 
     * @var \SHC\Timer\SwitchPointEditor
     */
    protected static $instance = null;

    /**
     * name der HashMap
     *
     * @var String
     */
    protected static $tableName = 'shc:switchpoints';

    protected function __construct() {

        $this->loadData();
    }

    public function loadData() {

        //alte daten loeschen
        $this->switchPoints = array();

        $switchpoints = SHC::getDatabase()->hGetAllArray(self::$tableName);
        foreach($switchpoints as $switchPoint) {

            $sp = new SwitchPoint();
            $sp->setId((int) $switchPoint['id']);
            $sp->setName((string) $switchPoint['name']);
            $sp->enable(((int) $switchPoint['enabled'] == 1 ? true : false));
            $sp->setCommand((int) $switchPoint['command']);
            $sp->setYear($switchPoint['year']);
            $sp->setMonth($switchPoint['month']);
            $sp->setWeek($switchPoint['week']);
            $sp->setDay($switchPoint['day']);
            $sp->setHour($switchPoint['hour']);
            $sp->setMinute($switchPoint['minute']);
            $sp->setLastExecute(DateTime::createFromDatabaseDateTime((string) $switchPoint['lastExecute']));

            //Bedingungen anhaengen
            foreach ($switchPoint['conditions'] as $conditionId) {

                $condition = ConditionEditor::getInstance()->getConditionByID($conditionId);
                if ($condition instanceof Condition) {

                    $sp->addCondition($condition);
                }
            }

            //Schaltpunkt speichern
            $this->switchPoints[$sp->getId()] = $sp;
        }
    }

    /**
     * gibt den Schaltpunkt mit der ID zurueck
     * 
     * @param  Integer $id ID
     * @return \SHC\Timer\SwitchPoint
     */
    public function getSwitchPointById($id) {

        if (isset($this->switchPoints[$id])) {

            return $this->switchPoints[$id];
        }
        return null;
    }

    /**
     * prueft ob der Name des Schaltpunktes schon verwendet wird
     * 
     * @param  String  $name Name
     * @return Boolean
     */
    public function isSwitchPointNameAvailable($name) {

        foreach ($this->switchPoints as $switchPoint) {

            /* @var $condition \SHC\Timer\SwitchPoint */
            if (String::toLower($switchPoint->getName()) == String::toLower($name)) {

                return false;
            }
        }
        return true;
    }

    /**
     * gibt eine Liste mit allen Schaltpunkten zurueck
     * 
     * @param  String $orderBy Art der Sortierung (
     *      id => nach ID sorieren, 
     *      name => nach Namen sortieren,
     *      unsorted => unsortiert
     *  )
     * @return Array
     */
    public function listSwitchPoints($orderBy = 'id') {

        if ($orderBy == 'id') {

            //nach ID sortieren
            $switchPoint = $this->switchPoints;
            ksort($switchPoint, SORT_NUMERIC);
            return $switchPoint;
        } elseif ($orderBy == 'name') {

            //nach Namen sortieren
            $switchPoint = $this->switchPoints;

            //Sortierfunktion
            $orderFunction = function($a, $b) {

                if ($a->getName() == $b->getName()) {

                    return 0;
                }

                if ($a->getName() < $b->getName()) {

                    return -1;
                }
                return 1;
            };
            usort($switchPoint, $orderFunction);
            return $switchPoint;
        }
        return $this->switchPoints;
    }

    /**
     * fuegt einem Schaltpunkt eine Bedingung hinzu
     *
     * @param  Integer $switchPointId  ID des Schaltpunktes
     * @param  Integer $conditionId    ID der Bedingung
     * @return Boolean
     */
    public function addConditionToSwitchPoint($switchPointId, $conditionId) {

        $db = SHC::getDatabase();
        //pruefen ob Datensatz existiert
        if($db->hExists(self::$tableName, $switchPointId)) {

            $switchPoint = $db->hGetArray(self::$tableName, $switchPointId);
            $switchPoint['conditions'][] = $conditionId;

            if($db->hSetArray(self::$tableName, $switchPointId, $switchPoint) == 0) {

                return true;
            }
        }
        return false;
    }

    /**
     * entfernt eine Bedingung aus einem Schaltpunkt
     *
     * @param  Integer $switchPointId  ID des Schaltpunktes
     * @param  Integer $conditionId    ID der Bedingung
     * @return Boolean
     */
    public function removeConditionFromSwitchPoint($switchPointId, $conditionId) {

        $db = SHC::getDatabase();
        //pruefen ob Datensatz existiert
        if($db->hExists(self::$tableName, $switchPointId)) {

            $switchPoint = $db->hGetArray(self::$tableName, $switchPointId);
            $switchPoint['conditions'] = array_diff($switchPoint['conditions'], array($conditionId));


            if($db->hSetArray(self::$tableName, $switchPointId, $switchPoint) == 0) {

                return true;
            }
        }
        return false;
    }

    /**
     * setzt die letzte ausfuehrung auf das uebergebene Datum
     * 
     * @param  int                $id   ID
     * @param  \RWF\Date\DateTime $time
     * @return Boolean
     */
    public function editExecutionTime($id, DateTime $time) {

        $db = SHC::getDatabase();
        //pruefen ob Datensatz existiert
        if($db->hExists(self::$tableName, $id)) {

            $switchPoint = $db->hGetArray(self::$tableName, $id);

            if(isset($switchPoint['id']) && $switchPoint['id'] == $id) {

                $switchPoint['lastExecute'] = $time->getDatabaseDateTime();

                if($db->hSetArray(self::$tableName, $id, $switchPoint) == 0) {

                    return true;
                }
            }
        }
        return false;
    }

    /**
     * speichert den Status der Schaltpunkte
     *
     * @return Boolean
     */
    public function updateSwitchPoints() {

        $db = SHC::getDatabase();

        //Schaltpunkte suchen
        foreach($this->switchPoints as $switchPoint) {

            /* @var $switchPoint \SHC\Timer\SwitchPoint */
            if($switchPoint->isExecuted() === true) {

                $switchPoint->setLastExecute(DateTime::now(), true);
                $switchPointData = $db->hGetArray(self::$tableName, $switchPoint->getId());

                if(isset($switchPointData['id']) && $switchPointData['id'] == $switchPoint->getId()) {

                    $switchPointData['lastExecute'] = $switchPoint->getLastExecute()->getDatabaseDateTime();

                    if($db->hSetArray(self::$tableName, $switchPoint->getId(), $switchPointData) != 0) {

                        return false;
                    }
                } else {

                    //Datensatz existiert nicht mehr
                    continue;
                }
            }
        }
        return true;
    }

    /**
     * erstellt einen neuen Schaltpunkt
     * 
     * @param  String  $name       Name
     * @param  Boolean $enabled    Aktiv
     * @param  Integer $command    Befehl
     * @param  Array   $conditions Liste der Bedingunen
     * @param  Array   $year       Liste mit den Jahren
     * @param  Array   $month      Liste mit den Monaten
     * @param  Array   $week       Liste mit den Kalenderwochen
     * @param  Array   $day        Liste mit den Tagen
     * @param  Array   $hour       Liste mit den Stunden
     * @param  Array   $minute     Liste mit den Minuten
     * @return Boolean
     */
    public function addSwitchPoint($name, $enabled, $command, array $conditions, array $year, array $month, array $week, array $day, array $hour, array $minute) {

        //Ausnahme wenn Name der Bedingung schon belegt
        if (!$this->isSwitchPointNameAvailable($name)) {

            throw new \Exception('Der Name des Schaltpunktes ist schon vergeben', 1503);
        }

        $db = SHC::getDatabase();
        $index = $db->autoIncrement(self::$tableName);

        $newSwitchPoint = array(
            'id' => $index,
            'name' => $name,
            'enabled' => ($enabled == true ? true : false),
            'command' => $command,
            'conditions' => $conditions,
            'year' => $year,
            'month' => $month,
            'week' => $week,
            'day' => $day,
            'hour' => $hour,
            'minute' => $minute,
            'lastExecute' => '2000-01-01 00:00:00'
        );
        if($db->hSetNxArray(self::$tableName, $index, $newSwitchPoint) == 0) {

            return false;
        }
        return true;
    }

    /**
     * bearbeitet einen Schaltpunkt
     * 
     * @param  int     $id         ID
     * @param  String  $name       Name
     * @param  Boolean $enabled    Aktiv
     * @param  Integer $command    Befehl
     * @param  Array   $conditions Liste der Bedingunen
     * @param  Array   $year       Liste mit den Jahren
     * @param  Array   $month      Liste mit den Monaten
     * @param  Array   $week       Liste mit den Kalenderwochen
     * @param  Array   $day        Liste mit den Tagen
     * @param  Array   $hour       Liste mit den Stunden
     * @param  Array   $minute     Liste mit den Minuten
     * @return Boolean
     */
    public function editSwitchPoint($id, $name = null, $enabled = null, $command = null, array $conditions = null, array $year = null, array $month = null, array $week = null, array $day = null, array $hour = null, array $minute = null) {

        $db = SHC::getDatabase();
        //pruefen ob Datensatz existiert
        if($db->hExists(self::$tableName, $id)) {

            $switchPoint = $db->hGetArray(self::$tableName, $id);

            //Name
            if ($name !== null) {

                //Ausnahme wenn Name der Bedingung schon belegt
                if ((string) $switchPoint['name'] != $name && !$this->isSwitchPointNameAvailable($name)) {

                    throw new \Exception('Der Name des Schaltpunktes ist schon vergeben', 1503);
                }

                $switchPoint['name'] = $name;
            }

            //Aktiv
            if ($enabled !== null) {

                $switchPoint['enabled'] = ($enabled == true ? true : false);
            }

            //Befehl
            if($command !== null) {

                $switchPoint['command'] = $command;
            }

            //Bedingungen
            if($conditions !== null) {

                $switchPoint['conditions'] = $conditions;
            }

            //Jahr
            if($year !== null) {

                $switchPoint['year'] = $year;
            }

            //Monat
            if($month !== null) {

                $switchPoint['month'] = $month;
            }

            //Kalenderwoche
            if($week !== null) {

                $switchPoint['week'] = $week;
            }

            //Tag
            if($day !== null) {

                $switchPoint['day'] = $day;
            }

            //Stunde
            if($hour !== null) {

                $switchPoint['hour'] = $hour;
            }

            //Minute
            if($minute !== null) {

                $switchPoint['minute'] = $minute;
            }

            if($db->hSetArray(self::$tableName, $id, $switchPoint) == 0) {

                return true;
            }
        }
        return false;
    }

    /**
     * loascht einen Schaltpunkt
     * 
     * @param  Integer $id ID
     * @return Boolean
     */
    public function removeSwitchPoint($id) {

        $db = SHC::getDatabase();
        //pruefen ob Datensatz existiert
        if($db->hExists(self::$tableName, $id)) {

            if($db->hDel(self::$tableName, $id)) {

                return true;
            }
        }
        return false;
    }

    /**
     * geschuetzt wegen Singleton
     */
    private function __clone() {
        
    }

    /**
     * gibt den Schaltpunkt Editor zurueck
     * 
     * @return \SHC\Timer\SwitchPointEditor
     */
    public static function getInstance() {

        if (self::$instance === null) {

            self::$instance = new SwitchPointEditor();
        }
        return self::$instance;
    }

}
