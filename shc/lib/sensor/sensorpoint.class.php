<?php

namespace SHC\Sensor;

//Imports
use RWF\Date\DateTime;

/**
 * Sensor Punkt
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SensorPoint {

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
     * nach Sortierungs ID sortieren
     * 
     * @var String
     */
    const SORT_BY_ORDER_ID = 'orderId';

    /**
     * nicht sortieren
     * 
     * @var String
     */
    const SORT_NOTHING = 'unsorted';

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
     * Sortierungs ID
     * 
     * @var Integer 
     */
    protected $orderId = 0;

    /**
     * Sichtbarkeit
     * 
     * @var Integer 
     */
    protected $visibility = 1;

    /**
     * Zeit des letzten Kontaktes
     * 
     * @var \RWF\Date\DateTime 
     */
    protected $time = null;

    /**
     * Versorgungsspannung
     * 
     * @var Float
     */
    protected $voltage = 0.0;

    /**
     * Grenze bei der eine Warnung erzeugt werden soll wenn sie unterschritten wird
     * 
     * @var Float 
     */
    protected $voltageWarnLevel = 0.0;

    /**
     * Liste mit den Sensoren
     * 
     * @var Array 
     */
    protected $sensors = array();

    /**
     * Sensorwerte geaendert
     * 
     * @var Boolean 
     */
    protected $isModified = false;

    /**
     * setzt die Sensor ID
     * 
     * @param  String $id Sensor ID
     * @return \SHC\Sensor\SensorPoint
     */
    public function setId($id) {

        $this->id = $id;
        return $this;
    }

    /**
     * gibt die Sensor ID zurueck
     * 
     * @return String
     */
    public function getId() {

        return $this->id;
    }

    /**
     * setzt den Namen des Sensors
     * 
     * @param  String $name Name des Sensors
     * @return \SHC\Sensor\SensorPoint
     */
    public function setName($name) {

        $this->name = $name;
        return $this;
    }

    /**
     * gibt den Namen des Sensors zurueck
     * 
     * @return String
     */
    public function getName() {

        if($this->name == '') {

            return 'SensorPoint-'. $this->getId();
        }
        return $this->name;
    }

    /**
     * setzt die Sortierungs ID
     * 
     * @param  Integer $orderId Sortierungs ID
     * @return \SHC\Sensor\SensorPoint
     */
    public function setOrderId($orderId) {

        $this->orderId = $orderId;
        return $this;
    }

    /**
     * gibt die Sortierungs ID zurueck
     * 
     * @return Integer
     */
    public function getOrderId() {

        return $this->orderId;
    }

    /**
     * setzt die Sichtbarkeit
     * 
     * @param  Boolean $visible
     * @return \SHC\Sensor\SensorPoint
     */
    public function visibility($visible) {

        if ($visible == true) {

            $this->visibility = true;
        } else {

            $this->visibility = false;
        }
        return $this;
    }

    /**
     * gibt die Sichtbarkeit des Sensors zurueck
     * 
     * @return Integer
     */
    public function isVisible() {

        return $this->visibility;
    }

    /**
     * setzt den Zeitstempel des letzten Kontaktes
     * 
     * @param  DateTime $time 
     * @return \SHC\Sensor\SensorPoint
     */
    public function setTime(DateTime $time) {

        $this->time = $time;
        return $this;
    }

    /**
     * gibt den Zeitstempel des letzten Kontaktes zurueck
     * 
     * @return \RWF\Date\DateTime
     */
    public function getTime() {

        return $this->time;
    }

    /**
     * setzt die Versorgungsspannung
     * 
     * @param Float   $voltage  Versorgungsspannung
     * @param Boolean $modified als geaendert markieren
     */
    public function setVoltage($voltage, $modified = true) {

        $this->voltage = $voltage;
        if ($modified == true) {
            
            $this->isModified = true;
        }
    }

    /**
     * gibt die Versorgungsspannung zurueck
     * 
     * @return Float
     */
    public function getVoltage() {

        return $this->voltage;
    }

    /**
     * setzt die Warngrenze des Sensorpunktes
     * 
     * @param  Float $level Grenze ab der eine Meldung erzeugt werden soll
     * @return \SHC\Sensor\SensorPoint
     */
    public function setWarnLevel($level) {

        $this->voltageWarnLevel = $level;
        return $this;
    }

    /**
     * gibt die Warngrenze zurueck
     * 
     * @return Float
     */
    public function getWarnLevel() {

        return $this->voltageWarnLevel;
    }

    /**
     * gibt an ob eine Warnung aktiv ist
     * 
     * @return Boolean
     */
    public function isWarningActive() {

        if ($this->voltage < $this->voltageWarnLevel) {

            return true;
        }
        return false;
    }

    /**
     * fuegt einen Sensor hinzu
     * 
     * @param  \SHC\Sensor\Sensor  $sensor   Sensor
     * @param  Boolean             $modified als geaendert markieren
     * @return \SHC\Sensor\SensorPoint
     */
    public function addSensor(Sensor $sensor, $modified = true) {

        $this->sensors[] = $sensor;
        if ($modified == true) {
            
            $this->isModified = true;
        }
        return $this;
    }

    /**
     * entfernt einen Sensor
     * 
     * @param  \SHC\Sensor\Sensor $sensor Sensor
     * @return \SHC\Sensor\SensorPoint
     */
    public function removeSensor(Sensor $sensor) {

        $this->sensors = array_diff($this->sensors, array($sensor));
        return $this;
    }

    /**
     * entfernt alle Sensoren
     * 
     * @return \SHC\Sensor\SensorPoint
     */
    public function removeAllSensors() {

        $this->sensors = array();
        return $this;
    }

    /**
     * gibt einen Sensor zurueck
     * 
     * @param  Integer $id Sensor ID
     * @return Sensor
     */
    public function getSensorById($id) {

        foreach ($this->sensors as $sensor) {

            /* @var $sensor \SHC\Sensor\Sensor */
            if ($id == $sensor->getId()) {

                return $sensor;
            }
        }

        return null;
    }

    /**
     * gibt eine Liste mit allen Sensoren des Sensorpunktes zurueck
     * 
     * @param  String $orderBy Art der Sortierung (
     *      id => nach ID sorieren, 
     *      name => nach Namen sortieren, 
     *      orderId => nach Sortierungs ID,
     *      unsorted => unsortiert
     *  )
     * @return Array
     */
    public function listSensors($orderBy = 'id') {

        if ($orderBy == 'id') {

            //nach ID sortieren
            $sensors = $this->sensors;
            ksort($sensors, SORT_NUMERIC);
            return $sensors;
        } elseif ($orderBy == 'orderId') {

            //nach Sortierungs ID sortieren
            $sensors = array();
            foreach ($this->sensors as $sensor) {

                /* @var $sensor \SHC\Sensor\Sensor */
                $sensors[$sensor->getOrderId()] = $sensor;
            }

            ksort($sensors, SORT_NUMERIC);
            return $sensors;
        } elseif ($orderBy == 'name') {

            //nach Namen sortieren
            $sensors = $this->sensors;

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
            usort($sensors, $orderFunction);
            return $sensors;
        }
        return $this->sensors;
    }

    /**
     * gibt an ob Sensoren des Sensorpunktes vereandert wurden
     * 
     * @return Boolean
     */
    public function isModified() {

        //Sensorpunkt wurder veraendert
        if ($this->isModified) {

            return true;
        }

        //Sensoren wurden veraendert
        foreach ($this->sensors as $sensor) {

            /* @var $sensor \SHC\Sensor\Sensor */
            if ($sensor->isModified()) {

                return true;
            }
        }
        return false;
    }

}
