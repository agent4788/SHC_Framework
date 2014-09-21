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
class RpiGpioInput extends AbstractReadable {

    /**
     * ID des Schaltservers
     * 
     * @var Integer 
     */
    protected $switchServer = 0;
    
    /**
     * Pin Nummer
     * 
     * @var Integer 
     */
    protected $pinNumber = 0;
    
    /**
     * @param Integer $switchServer Schaltserver
     * @param Integer $pinNumber    GPIO Pin Nummer
     */
    public function __construct($switchServer = 0, $pinNumber = 0) {
        
        $this->switchServer = $switchServer;
        $this->pinNumber = $pinNumber;
    }
    
    /**
     * setzt den Schaltserver
     * 
     * @param  Integer $switchServer ID des Schaltservers
     * @return \SHC\Switchable\Switchables\RpiGpioOutput
     */
    public function setSwitchServer($switchServer) {
        
        $this->switchServer = $switchServer;
        return $this;
    }
    
    /**
     * gibt die ID des Schaltservers zurueck
     * 
     * @return Integer
     */
    public function getSwitchServer() {
        
        return $this->switchServer;
    }

    /**
     * setzt die Pin Nummer
     * 
     * @param  Integer $pinNumber Pin Nummer
     * @return \SHC\Switchable\Switchables\RpiGpioOutput
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
     * gibt den aktuellen geschaltenen Zustand zurueck
     * 
     * @return Integer
     */
    public function getState() {
        
        //noch nicht Implementiert
    }

}
