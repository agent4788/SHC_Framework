<?php

namespace SHC\Timer;

//Imports
use SHC\Core\SHC;
use RWF\XML\XmlFileManager;
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

    protected function __construct() {

        $this->loadData();
    }

    public function loadData() {

        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_SWITCHPOINTS);

        //Daten einlesen
        foreach ($xml->switchPoint as $switchPoint) {

            //Schaltpunkt konfigurieren
            $sp = new SwitchPoint();
            $sp->setId((int) $switchPoint->id);
            $sp->setName((string) $switchPoint->name);
            $sp->enable(((int) $switchPoint->enabled == 1 ? true : false));
            $sp->setCommand((int) $switchPoint->command);
            $sp->setYear(explode(',', (string) $switchPoint->year));
            $sp->setMonth(explode(',', (string) $switchPoint->month));
            $sp->setWeek(explode(',', (string) $switchPoint->week));
            $sp->setDay(explode(',', (string) $switchPoint->day));
            $sp->setHour(explode(',', (string) $switchPoint->hour));
            $sp->setMinute(explode(',', (string) $switchPoint->minute));
            $sp->setLastExecute(DateTime::createFromDatabaseDateTime((string) $switchPoint->lastExecute));

            //Bedingungen anhaengen
            foreach (explode(',', (string) $switchPoint->conditions) as $conditionId) {

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
     * setzt die letzte ausfuehrung auf das uebergebene Datum
     * 
     * @param  Integre $id ID
     * @param  \RWF\Date\DateTime $time
     * @return Boolean
     * @throws \RWF\Xml\Exception\XmlException
     */
    public function editExecutionTime($id, DateTime $time) {

        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_SWITCHPOINTS, true);

        //Server Suchen
        foreach ($xml->switchPoint as $switchPoint) {

            if ((int) $switchPoint->id == $id) {

                $switchPoint->lastExecute = $time->getDatabaseDateTime();
                $xml->save();
                return true;
            }
        }
        return false;
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
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addSwitchPoint($name, $enabled, $command, array $conditions, array $year, array $month, array $week, array $day, array $hour, array $minute) {

        //Ausnahme wenn Name der Bedingung schon belegt
        if (!$this->isSwitchPointNameAvailable($name)) {

            throw new \Exception('Der Name des Schaltpunktes ist schon vergeben', 1503);
        }

        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_SWITCHPOINTS, true);

        //Autoincrement
        $nextId = (int) $xml->nextAutoIncrementId;
        $xml->nextAutoIncrementId = $nextId + 1;

        //Datensatz erstellen
        $switchPoint = $xml->addChild('switchPoint');
        $switchPoint->addChild('id', $nextId);
        $switchPoint->addChild('name', $name);
        $switchPoint->addChild('enabled', ($enabled == true ? 1 : 0));
        $switchPoint->addChild('command', $command);
        $switchPoint->addChild('conditions', implode(',', $conditions));
        $switchPoint->addChild('year', implode(',', $year));
        $switchPoint->addChild('month', implode(',', $month));
        $switchPoint->addChild('week', implode(',', $week));
        $switchPoint->addChild('day', implode(',', $day));
        $switchPoint->addChild('hour', implode(',', $hour));
        $switchPoint->addChild('minute', implode(',', $minute));
        $switchPoint->addChild('lasteExecute', '2000-01-01 00:00:00');

        //Daten Speichern
        $xml->save();
        return true;
    }

    /**
     * bearbeitet einen Schaltpunkt
     * 
     * @param  Integre $id         ID
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
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editSwitchPoint($id, $name = null, $enabled = null, $command = null, array $conditions = null, array $year = null, array $month = null, array $week = null, array $day = null, array $hour = null, array $minute = null) {

        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_SWITCHPOINTS, true);

        //Server Suchen
        foreach ($xml->switchPoint as $switchPoint) {

            if ((int) $switchPoint->id == $id) {

                //Name
                if ($name !== null) {

                    //Ausnahme wenn Name der Bedingung schon belegt
                    if (!$this->isSwitchPointNameAvailable($name)) {

                        throw new \Exception('Der Name des Schaltpunktes ist schon vergeben', 1503);
                    }

                    $switchPoint->name = $name;
                }

                //Aktiv
                if ($enabled !== null) {

                    $switchPoint->$enabled = ($enabled == true ? 1 : 0);
                }
                
                //Befehl
                if($command !== null) {
                    
                    $switchPoint->command = $command;
                }
                
                //Bedingungen
                if($conditions !== null) {
                    
                    $switchPoint->conditions = implode(',', $conditions);
                }
                
                //Jahr
                if($year !== null) {
                    
                    $switchPoint->year = implode(',', $year);
                }
                
                //Monat
                if($month !== null) {
                    
                    $switchPoint->month = implode(',', $month);
                }
                
                //Kalenderwoche
                if($week !== null) {
                    
                    $switchPoint->week = implode(',', $week);
                }
                
                //Tag
                if($day !== null) {
                    
                    $switchPoint->day = implode(',', $day);
                }
                
                //Stunde
                if($hour !== null) {
                    
                    $switchPoint->hour = implode(',', $hour);
                }
                
                //Minute
                if($minute !== null) {
                    
                    $switchPoint->minute = implode(',', $minute);
                }
                
                //Daten Speichern
                $xml->save();
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
     * @throws \RWF\Xml\Exception\XmlException
     */
    public function removeSwitchPoint() {

        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_SWITCHPOINTS, true);

        //Bedingung suchen
        for ($i = 0; $i < count($xml->switchPoint); $i++) {

            if ((int) $xml->switchPoint[$i]->id == $id) {

                //Raum loeschen
                unset($xml->switchPoint[$i]);

                //Daten Speichern
                $xml->save();
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
