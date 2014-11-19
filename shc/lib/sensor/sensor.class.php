<?php

namespace SHC\Sensor;

//Imports
use SHC\Room\Room;

/**
 * Sensor Schnittstelle
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
interface Sensor {
    
    /**
     * Der Sensor soll Angezeigt werden
     * 
     * @var Integer
     */
    const SHOW = 1;
    
    /**
     * Der Sensor soll Versteckt werden
     * 
     * @var Integer
     */
    const HIDE = 0;
    
    /**
     * setzt die Sensor ID
     * 
     * @param  String $id Sensor ID
     * @return \SHC\Sensor\Sensor
     */
    public function setId($id);
    
    /**
     * gibt die Sensor ID zurueck
     * 
     * @return String
     */
    public function getId();
    
    /**
     * setzt den Namen des Sensors
     * 
     * @param  String $name Name des Sensors
     * @return \SHC\Sensor\Sensor
     */
    public function setName($name);
    
    /**
     * gibt den Namen des Sensors zurueck
     * 
     * @return String
     */
    public function getName();
    
    /**
     * setzt die Raum ID
     * 
     * @param  \SHC\Room\Room $room Raum
     * @return \SHC\Sensor\Sensor
     */
    public function setRoom(Room $room);
    
    /**
     * gibt den Raum zurueck
     * 
     * @return \SHC\Room\Room
     */
    public function getRoom();
    
    /**
     * setzt die Sortierungs ID
     * 
     * @param  Integer $orderId Sortierungs ID
     * @return \SHC\Sensor\Sensor
     */
    public function setOrderId($orderId);
    
    /**
     * gibt die Sortierungs ID zurueck
     * 
     * @return Integer
     */
    public function getOrderId();
    
    /**
     * setzt die Sichtbarkeit
     * 
     * @param  Boolean $enabled
     * @return \SHC\Sensor\Sensor
     */
    public function visibility($enabled);
    
    /**
     * gibt die Sichtbarkeit des Sensors zurueck
     * 
     * @return Integer
     */
    public function isVisible();
    
    /**
     * aktiviert/deaktiviert das aufzeichnen der Sensordaten
     * 
     * @param  Boolean $enabled aktiviert/deaktiviert
     * @return \SHC\Sensor\Sensor
     */
    public function enableDataRecording($enabled);
    
    /**
     * gibt an ob die Daten des Sensors aufgezeichnet werden sollen
     * 
     * @return Boolean
     */
    public function isDataRecordingEnabled();
    
    /**
     * gibt den Zeitstempel des letzten Sensorwertes zurueck
     * 
     * @return \RWF\Date\DateTime
     */
    public function getTime();
    
    /**
     * gibt die letzten 5 Sensorwerte zurueck
     * 
     * @return Array
     */
    public function getOldValues();
    
    /**
     * gibt an ob die Sensorwerte vereandert wurden
     * 
     * @return Boolean
     */
    public function isModified();

}
