<?php

namespace SHC\Event;

//Imports
use SHC\Condition\Condition;
use RWF\Date\DateTime;
use SHC\Switchable\Switchable;

/**
 * Ereignis Schnittstelle
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
interface Event {

    /**
     * Status eingeschalten
     *
     * @var Integer
     */
    const STATE_OFF = 0;

    /**
     * Status Ausgeschalten
     *
     * @var Integer
     */
    const STATE_ON = 1;

    /**
     * setzt die ID
     * 
     * @param   Integer $id ID
     * @return \SHC\Condition\Condition
     */
    public function setId($id);
    
    /**
     * gibt die ID zurueck
     * 
     * @return Integer
     */
    public function getId();
    
    /**
     * setzt den Namen
     * 
     * @param  String $name Name
     * @return \SHC\Condition\Condition
     */
    public function setName($name);
    
    /**
     * gibt den Namen zurueck
     * 
     * @return String
     */
    public function getName();
    
    /**
     * setzt die Zeit der letzten ausfuehrung
     * 
     * @param  \RWF\Date\DateTime $time
     * @return \SHC\Event\Event
     */
    public function setTime(DateTime $time);
    
    /**
     * gibt die Zeit der letzten ausfuehrung zurueck
     * 
     * @return \RWF\Date\DateTime
     */
    public function getTime();
    
    /**
     * setzt die Daten fuer das Ereignis
     * 
     * @param  Array $data Daten
     * @return \SHC\Condition\Condition
     */
    public function setData(array $data);
    
    /**
     * gibt die Datendas Ereignis zurueck
     * 
     * @return Array 
     */
    public function getData();

    /**
     * gibt den Objektstatus zurueck
     *
     * @return Array
     */
    public function getState();

    /**
     * setzt den Objektstatus
     *
     * @param array $state
     * @return \SHC\Event\Event
     */
    public function setState(array $state);
    
    /**
     * Aktiviert/Deaktiviert das Ereignis
     * 
     * @param  Boolean $enabled Aktiviert
     * @return \SHC\Condition\Condition
     */
    public function enable($enabled);

    /**
     * gibt an ob das Ereignis Aktiviert ist
     * 
     * @return Boolean 
     */
    public function isEnabled();
    
    /**
     * fuegt eine Bedingung hinzu
     * 
     * @param  \SHC\Condition\Condition $condition
     * @return \SHC\Event\Event
     */
    public function addCondition(Condition $condition);

    /**
     * loecht eine Bedingung
     * 
     * @param  \SHC\Condition\Condition $condition
     * @return \SHC\Event\Event
     */
    public function removeCondition(Condition $condition);

    /**
     * loescht alle Bedingungen
     * 
     * @return \SHC\Event\Event
     */
    public function removeAllConditions();

    /**
     * gibt eine Liste mit allen Bedingungen zurueck
     *
     * @return Array
     */
    public function listConditions();

    /**
     * fuegt ein neues schaltbares Element hinzu
     *
     * @param  \SHC\Switchable\Switchable $switchable schaltbares Element
     * @param  Integer                    $command    Kommando
     * @return \SHC\Event\Event
     */
    public function addSwitchable(Switchable $switchable, $command);

    /**
     * loecht eine Bedingung
     *
     * @param  \SHC\Switchable\Switchable $switchable schaltbares Element
     * @return \SHC\Event\Event
     */
    public function removeSwitchable(Switchable $switchable);

    /**
     * loescht alle Bedingungen
     *
     * @return \SHC\Event\Event
     */
    public function removeAllSwitchables();

    /**
     * gibt eine Liste mit allen Elementen des Ereignisses zurueck
     *
     * @return Array
     */
    public function listSwitchables();

    /**
     * gibt an ob das Ereigniss erfuellt ist
     * 
     * @return Boolean
     */
    public function isSatisfies();
    
    /**
     * fuehr die Aktionen aus
     */
    public function execute();

    /**
     * prueft on das Ereignis gerade zutrifft und fuehrt wenn es Zutrifft die zugeordneten Befehle aus
     */
    public function run();

    /**
     * gibt den Typnamen zurueck
     *
     * @return string
     */
    public function getTypeName();
}
