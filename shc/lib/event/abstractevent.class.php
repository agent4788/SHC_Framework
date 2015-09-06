<?php

namespace SHC\Event;

//Imports
use RWF\Core\RWF;
use SHC\Command\CommandSheduler;
use SHC\Condition\Condition;
use RWF\Date\DateTime;
use SHC\Core\SHC;
use SHC\Event\Events\FileCreate;
use SHC\Event\Events\FileDelete;
use SHC\Event\Events\HumidityClimbOver;
use SHC\Event\Events\HumidityFallsBelow;
use SHC\Event\Events\InputHigh;
use SHC\Event\Events\InputLow;
use SHC\Event\Events\LightIntensityClimbOver;
use SHC\Event\Events\LightIntensityFallsBelow;
use SHC\Event\Events\MoistureClimbOver;
use SHC\Event\Events\MoistureFallsBelow;
use SHC\Event\Events\Sunrise;
use SHC\Event\Events\Sunset;
use SHC\Event\Events\TemperatureClimbOver;
use SHC\Event\Events\TemperatureFallsBelow;
use SHC\Event\Events\UserComesHome;
use SHC\Event\Events\UserLeavesHome;
use SHC\Switchable\Switchable;

/**
 * Standard Ereignis
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
abstract class AbstractEvent implements Event {
    
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
     * Zeit der letzten ausfuehrung
     * 
     * @var \RWF\Date\DateTime 
     */
    protected $time = null;
    
    /**
     * Daten
     * 
     * @var Array 
     */
    protected $data = array();
    
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
     * liste aller Elemente die beim eintreten des Ereignisses geschalten werden sollen
     *
     * @var Array
     */
    protected $switchables = array();

    /**
     * Status
     *
     * @var Array
     */
    protected $state = array();

    /**
     * Zeitpunkt ver letzten Ausfuehrung
     *
     * @var \RWF\Date\DateTime
     */
    protected $lastExecute = null;
    
    /**
     * @param Integer            $id          ID
     * @param String             $name        Name
     * @param Array              $data        Daten
     * @param Boolean            $enabled     Aktiv
     * @param \RWF\Date\DateTime $lastExecute letzte Ausfuehrung
     */
    public function __construct($id, $name, array $data = array(), $enabled = true, DateTime $lastExecute = null) {
        
        $this->id = $id;
        $this->name = $name;
        $this->data = $data;
        $this->enable($enabled);
        $this->lastExecute = $lastExecute;
    }
    
    /**
     * setzt die ID
     * 
     * @param   Integer $id ID
     * @return \SHC\Event\Event
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
     * @return \SHC\Event\Event
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
     * setzt die Zeit der letzten ausfuehrung
     * 
     * @param  \RWF\Date\DateTime $time
     * @return \SHC\Event\Event
     */
    public function setTime(DateTime $time) {
        
        $this->time = $time;
        return $this;
    }
    
    /**
     * gibt die Zeit der letzten ausfuehrung zurueck
     * 
     * @return \RWF\Date\DateTime
     */
    public function getTime() {
        
        return $this->time;
    }
    
    /**
     * setzt die Daten fuer das Ereignis
     * 
     * @param  Array $data Daten
     * @return \SHC\Event\Event
     */
    public function setData(array $data) {
        
        $this->data = $data;
        return $this;
    }
    
    /**
     * gibt die Daten das Ereignis zurueck
     * 
     * @return Array 
     */
    public function getData() {
        
        return $this->data;
    }

    /**
     * gibt den Objektstatus zurueck
     *
     * @return Array
     */
    public function getState() {

        return $this->state;
    }

    /**
     * setzt den Objektstatus
     *
     * @param array $state
     * @return \SHC\Event\Event
     */
    public function setState(array $state) {

        $this->state = $state;
        return $this;
    }

    /**
     * Aktiviert/Deaktiviert das Ereignis
     * 
     * @param  Boolean $enabled Aktiviert
     * @return \SHC\Event\Event
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
     * gibt an ob das Ereignis Aktiviert ist
     * 
     * @return Boolean 
     */
    public function isEnabled() {

        return $this->enabled;
    }

    /**
     * setzt den Zeitpunkt der letzten Ausfuehrung
     *
     * @param \RWF\Date\DateTime $lastExecute
     */
    public function setLastExecute(DateTime $lastExecute) {

        $this->lastExecute = $lastExecute;
    }

    /**
     * gibt den Zeitpunkt der letzten Ausfuehrung zuruck
     *
     * @return \RWF\Date\DateTime
     */
    public function getLastExecute() {

        return $this->lastExecute;
    }
    
    /**
     * fuegt eine Bedingung hinzu
     * 
     * @param  \SHC\Condition\Condition $condition
     * @return \SHC\Event\Event
     */
    public function addCondition(Condition $condition) {

        $this->conditions[] = $condition;
        return $this;
    }

    /**
     * loecht eine Bedingung
     * 
     * @param  \SHC\Condition\Condition $condition
     * @return \SHC\Event\Event
     */
    public function removeCondition(Condition $condition) {

        $this->conditions = array_diff($this->conditions, array($condition));
        return $this;
    }

    /**
     * loescht alle Bedingungen
     * 
     * @return \SHC\Event\Event
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
     * fuegt ein neues schaltbares Element hinzu
     *
     * @param  \SHC\Switchable\Switchable $switchable schaltbares Element
     * @param  Integer                    $command    Kommando
     * @return \SHC\Event\Event
     */
    public function addSwitchable(Switchable $switchable, $command) {

        $this->switchables[] = array('object' => $switchable, 'command' => $command);
        return $this;
    }

    /**
     * loecht eine Bedingung
     *
     * @param  \SHC\Switchable\Switchable $switchable schaltbares Element
     * @return \SHC\Event\Event
     */
    public function removeSwitchable(Switchable $switchable) {

        foreach ($this->switchables as $index => $switchableObject) {

            if ($switchableObject['object'] == $switchable) {

                unset($this->switchables[$index]);
            }
        }
        return $this;
    }

    /**
     * loescht alle Bedingungen
     *
     * @return \SHC\Event\Event
     */
    public function removeAllSwitchables() {

        $this->switchables = array();
        return $this;
    }

    /**
     * gibt eine Liste mit allen Elementen des Ereignisses zurueck
     *
     * @return Array
     */
    public function listSwitchables() {

        return $this->switchables;
    }

    /**
     * fuehrt die Aktionen aus
     */
    public function execute() {

        foreach ($this->switchables as $switchable) {

            /* @var $object \SHC\Switchable\Switchable */
            $object = $switchable['object'];
            $command = $switchable['command'];

            if ($command == self::STATE_ON) {

                $object->switchOn();
            } else {

                $object->switchOff();
            }
        }

        //Befehle senden
        try {
            CommandSheduler::getInstance()->sendCommands();
            $this->lastExecute = DateTime::now();
            EventEditor::getInstance()->updateLastExecute($this->getId(), $this->getLastExecute());
        } catch(\Exception $e) {
            RWF::getResponse()->writeLnColored('Fehler beim Senden : '. $e->getMessage() .' - '. $e->getCode(), 'red');
        }
    }

    /**
     * prueft on das Ereignis gerade zutrifft und fuehrt wenn es Zutrifft die zugeordneten Befehle aus
     */
    public function run() {

        if($this->isEnabled() && $this->isSatisfies()) {

            $this->execute();
        }
    }

    /**
     * gibt den Typnamen zurueck
     *
     * @return string
     */
    public function getTypeName() {

        if($this instanceof HumidityClimbOver) {

            $type = SHC::getLanguage()->get('acp.eventsManagement.events.HumidityClimbOver');
        } elseif($this instanceof HumidityFallsBelow) {

            $type = SHC::getLanguage()->get('acp.eventsManagement.events.HumidityFallsBelow');
        } elseif($this instanceof HumidityFallsBelow) {

            $type = SHC::getLanguage()->get('acp.eventsManagement.events.HumidityFallsBelow');
        } elseif($this instanceof InputHigh) {

            $type = SHC::getLanguage()->get('acp.eventsManagement.events.InputHigh');
        } elseif($this instanceof InputLow) {

            $type = SHC::getLanguage()->get('acp.eventsManagement.events.InputLow');
        } elseif($this instanceof LightIntensityClimbOver) {

            $type = SHC::getLanguage()->get('acp.eventsManagement.events.LightIntensityClimbOver');
        } elseif($this instanceof LightIntensityFallsBelow) {

            $type = SHC::getLanguage()->get('acp.eventsManagement.events.LightIntensityFallBelow');
        } elseif($this instanceof MoistureClimbOver) {

            $type = SHC::getLanguage()->get('acp.eventsManagement.events.MoistureClimbOver');
        } elseif($this instanceof MoistureFallsBelow) {

            $type = SHC::getLanguage()->get('acp.eventsManagement.events.MoistureFallsBelow');
        } elseif($this instanceof TemperatureClimbOver) {

            $type = SHC::getLanguage()->get('acp.eventsManagement.events.TemperatureClimbOver');
        } elseif($this instanceof TemperatureFallsBelow) {

            $type = SHC::getLanguage()->get('acp.eventsManagement.events.TemperatureFallsBelow');
        } elseif($this instanceof UserComesHome) {

            $type = SHC::getLanguage()->get('acp.eventsManagement.events.UserComesHome');
        } elseif($this instanceof UserLeavesHome) {

            $type = SHC::getLanguage()->get('acp.eventsManagement.events.UserLeavesHome');
        } elseif($this instanceof Sunrise) {

            $type = SHC::getLanguage()->get('acp.eventsManagement.events.Sunrise');
        } elseif($this instanceof Sunset) {

            $type = SHC::getLanguage()->get('acp.eventsManagement.events.Sunset');
        } elseif($this instanceof FileCreate) {

            $type = SHC::getLanguage()->get('acp.eventsManagement.events.FileCreate');
        } elseif($this instanceof FileDelete) {

            $type = SHC::getLanguage()->get('acp.eventsManagement.events.FileDelete');
        } else {

            $type = 'unknown';
        }
        return $type;
    }
}
