<?php

namespace SHC\Sensor;

//Imports
use SHC\Room\Room;

/**
 * Standard Sensor
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
abstract class AbstractSensor implements Sensor {

    /**
     * ID
     * 
     * @var String 
     */
    protected $id = 0;

    /**
     * Name
     * 
     * @var String 
     */
    protected $name = '';

    /**
     * Raum
     * 
     * @var \SHC\Room\Room 
     */
    protected $room = 0;

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
     * Datenaufzeichnung
     * 
     * @var Boolean 
     */
    protected $dataRecording = false;

    /**
     * Zeit des aktuellsten Wertes
     * 
     * @var \RWF\Date\DateTime 
     */
    protected $time = null;
    
    /**
     * letzte 5 Werte
     * 
     * @var Array 
     */
    protected $oldValues = array(0 => array(), 1 => array(), 2 => array(), 3 => array(), 4 => array());
    
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
     * @return \SHC\Sensor\Sensor
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
     * @return \SHC\Sensor\Sensor
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

        return $this->name;
    }

    /**
     * setzt die Raum ID
     * 
     * @param  \SHC\Room\Room $room Raum
     * @return \SHC\Sensor\Sensor
     */
    public function setRoom(Room $room) {

        $this->room = $room;
        return $this;
    }

    /**
     * gibt den Raum zurueck
     * 
     * @return \SHC\Room\Room
     */
    public function getRoom() {

        return $this->room;
    }

    /**
     * setzt die Sortierungs ID
     * 
     * @param  Integer $orderId Sortierungs ID
     * @return \SHC\Sensor\Sensor
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
     * @return \SHC\Sensor\Sensor
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
     * aktiviert/deaktiviert das aufzeichnen der Sensordaten
     * 
     * @param  Boolean $enabled aktiviert/deaktiviert
     * @return \SHC\Sensor\Sensor
     */
    public function enableDataRecording($enabled) {

        $this->dataRecording = $enabled;
        return $this;
    }

    /**
     * gibt an ob die Daten des Sensors aufgezeichnet werden sollen
     * 
     * @return Boolean
     */
    public function isDataRecordingEnabled() {

        return $this->dataRecording;
    }

    /**
     * gibt den Zeitstempel des letzten Sensorwertes zurueck
     * 
     * @return \RWF\Date\DateTime
     */
    public function getTime() {

        return $this->time;
    }
    
    /**
     * gibt die letzten 5 Sensorwerte zurueck
     * 
     * @return Array
     */
    public function getOldValues() {
        
        return $this->oldValues;
    }
    
    /**
     * gibt an ob die Sensorwerte vereandert wurden
     * 
     * @return Boolean
     */
    public function isModified() {
        
        return $this->isModified;
    }

}
