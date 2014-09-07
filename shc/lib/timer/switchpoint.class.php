<?php

namespace SHC\Timer;

//Imports


/**
 * Schaltpunkt
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SwitchPoint {
    
    /**
     * Einschaltbefehl
     * 
     * @var Integer
     */
    const SWITCH_ON = 1;
    
    /**
     * Ausschaltbefehl
     * 
     * @var Integer
     */
    const SWITCH_OFF = 2;
    
    /**
     * Umschaltbefehl
     * 
     * @var Integer
     */
    const SWITCH_TOGGLE = 4;
    
    /**
     * gibt den Befehl zurueck
     * 
     * @return Integer
     */
    public function getCommand() {
        
        
    }
    
    /**
     * gibt an ob der Schaltpunkt erfuellt ist
     * 
     * @return Boolean
     */
    public function isSatisfies() {
        
        
    }
}
