<?php

namespace SHC\Switchable\Switchables;

//Imports
use SHC\Switchable\AbstractSwitchable;
use SHC\WakeOnLan\WakeOnLan as WakeOnLanTransmitter;

/**
 * Wake On Lan Geraet
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class WakeOnLan extends AbstractSwitchable {
    
    /**
     * MAC Adresse
     * 
     * @var String 
     */
    protected $mac = '';
    
    /**
     * IP Adresse
     * 
     * @var String 
     */
    protected $ipAddress = '';
    
    /**
     * @param String $mac
     * @param String $ipAddess
     */
    public function __construct($mac = '', $ipAddess = '') {
        
        $this->mac = $mac;
        $this->ipAddress = $ipAddess;
    }
    
    /**
     * setzt die MAC Adresse
     * 
     * @param  String $mac MAC Adresse
     * @return \SHC\Switchable\Switchables\WakeOnLan
     */
    public function setMac($mac) {
        
        $this->mac = $mac;
        return $this;
    }
    
    /**
     * gibt die MAC Adresse zurueck
     * 
     * @return String
     */
    public function getMac() {
        
        return $this->mac;
    }

    /**
     * setzt die IP Adresse
     * 
     * @param  String $ipAddress IP Adresse
     * @return \SHC\Switchable\Switchables\WakeOnLan
     */
    public function setIpAddress($ipAddress) {
        
        $this->ipAddress = $ipAddress;
        return $this;
    }
    
    /**
     * gibt die IP Adresse zurueck
     * 
     * @return String
     */
    public function getIpAddress() {
        
        return $this->ipAddress;
    }
    
    /**
     * schaltet das Objekt ein
     * 
     * @return Boolean
     */
    public function switchOn() {

        WakeOnLanTransmitter::wakeUp($this->mac, $this->ipAddress);
        $this->state = self::STATE_ON;
        $this->stateModified = true;
    }
    
    /**
     * schaltet das Objekt aus
     * 
     * @return Boolean
     */
    public function switchOff() {
        
        //nicht moeglich bei WOL
    }

}
