<?php

namespace SHC\Switchable\Switchables;

//Imports
use SHC\Switchable\AbstractSwitchable;

/**
 * Arduino Ausgang (per 433MHz)
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ArduinoOutput extends AbstractSwitchable {

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
     * schaltet das Objekt ein
     * 
     * @return Boolean
     */
    public function switchOn() {

        //nicht implementiert
        $this->stateModified = true;
    }

    /**
     * schaltet das Objekt aus
     * 
     * @return Boolean
     */
    public function switchOff() {

        //nicht implementiert
        $this->stateModified = true;
    }

}
