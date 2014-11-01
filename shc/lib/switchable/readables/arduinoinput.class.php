<?php

namespace SHC\Switchable\Readables;

//Imports
use SHC\Switchable\AbstractReadable;

/**
 * Basisklasse eines Lesbaren Elements
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ArduinoInput extends AbstractReadable {
    
    /**
     * Geraete ID
     * 
     * @var String 
     */
    protected $deviceId = '';

    /**
     * Pin Nummer
     * 
     * @var Integer 
     */
    protected $pinNumber = 0;

    /**
     * @param String  $deviceId  Gereate ID
     * @param Integer $pinNumber Pin Nummer
     */
    public function __construct($deviceId = '', $pinNumber = 0) {
        
        $this->deviceId = $deviceId;
        $this->pinNumber = $pinNumber;
    }
    
    /**
     * setzt die Geraete ID
     * 
     * @param  String $deviceId Geraete ID
     * @return \SHC\Switchable\Switchables\ArduinoOutput
     */
    public function setDeviceId($deviceId) {
        
        $this->deviceId = $deviceId;
        return $this;
    }
    
    /**
     * gibt die Geraete ID zurueck
     * 
     * @return String
     */
    public function getDeviceId() {
        
        return $this->deviceId;
    }

    /**
     * setzt die Pin Nummer
     * 
     * @param Integer $pinNumber Pin Nummer
     * @return \SHC\Switchable\Switchables\ArduinoOutput
     */
    public function setPinNumber($pinNumber) {
        
        $this->pinNumber = $pinNumber;
        return $this;
    }
    
    /**
     * gibt die Pin Nummer zurueck
     * 
     * @return Integer
     */
    public function getPinNumber() {
        
        return $this->pinNumber;
    }

    /**
     * liest en aktuellen Status des Einganges ein
     */
    public function readState() {

    }

    /**
     * gibt den aktuellen geschaltenen Zustand zurueck
     * 
     * @return Integer
     */
    public function getState() {
        
        return $this->state;
    }
}
