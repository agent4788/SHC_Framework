<?php

namespace SHC\Switchable\Switchables;

//Imports
use SHC\Switchable\AbstractSwitchable;
use SHC\Command\CommandSheduler;
use SHC\Command\Commands\RadioSocketCommand;

/**
 * Funksteckdose
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class RadioSocket extends AbstractSwitchable {
    
    /**
     * Protokol
     * 
     * @var String 
     */
    protected $protocol = '';
    
    /**
     * Systemcode
     * 
     * @var String 
     */
    protected $systemCode = '';
    
    /**
     * Geraetecode
     * 
     * @var String 
     */
    protected $deviceCode = '';

    /**
     * Anzahl der Sendevorgaenge
     *
     * @var Integer
     */
    protected $continuous = 1;
    
    /**
     * @param String  $protocol   Protokoll
     * @param String  $systemCode System Code
     * @param String  $deviceCode Geraete Code
     * @param Integer $continuous Sendevorgaenge
     */
    public function __construct($protocol = '', $systemCode = '',  $deviceCode = '', $continuous = 1) {
        
        $this->protocol = $protocol;
        $this->systemCode = $systemCode;
        $this->deviceCode = $deviceCode;
        $this->continuous = $continuous;
    }
    
    /**
     * setzt das Protokoll
     * 
     * @param  String $protocol Sendeprotokoll
     * @return \SHC\Switchable\Switchables\RadioSocket
     */
    public function setProtocol($protocol) {
        
        $this->protocol = $protocol;
        return $this;
    }
    
    /**
     * gibt das Protokoll zurueck
     * 
     * @return String
     */
    public function getProtocol() {
        
        return $this->protocol;
    }
    
    /**
     * setzt den Systemcode
     * 
     * @param  String $systemCode Systemcode
     * @return \SHC\Switchable\Switchables\RadioSocket
     */
    public function setSystemCode($systemCode) {
        
        $this->systemCode = $systemCode;
        return $this;
    }

    /**
     * gibt den Systemcode zurueck
     * 
     * @return String
     */
    public function getSystemCode() {
        
        return $this->systemCode;
    }

    /**
     * etzt den Geraetecode
     * 
     * @param  String $deviceCode Geraetecode
     * @return \SHC\Switchable\Switchables\RadioSocket
     */
    public function setDeviceCode($deviceCode) {
        
        $this->deviceCode = $deviceCode;
        return $this;
    }
    
    /**
     * gibt den Geraetecode zurueck
     * 
     * @return String
     */
    public function getDeviceCode() {
        
        return $this->deviceCode;
    }

    /**
     * setzt die Anzahl der Sendevorgaenge
     *
     * @param  Integer $continuous ANzahl wie of der Sendebefehl ausgefuehrt werden soll
     * @return \SHC\Switchable\Switchables\RadioSocket
     */
    public function setContinuous($continuous) {

        $this->continuous = $continuous;
        return $this;
    }

    /**
     * gibt die Anzahl zurueck wieoft ein Steckdosenbefehl gesendet werden soll
     *
     * @return Integer
     */
    public function getContinuous() {

        return $this->continuous;
    }
    
    /**
     * schaltet das Objekt ein
     * 
     * @return Boolean
     */
    public function switchOn() {
        
        CommandSheduler::getInstance()->addCommand(new RadioSocketCommand($this->protocol, $this->systemCode, $this->deviceCode, RadioSocketCommand::SWITCH_ON, $this->continuous));
        $this->state = self::STATE_ON;
        $this->stateModified = true;
    }
    
    /**
     * schaltet das Objekt aus
     * 
     * @return Boolean
     */
    public function switchOff() {
        
        CommandSheduler::getInstance()->addCommand(new RadioSocketCommand($this->protocol, $this->systemCode, $this->deviceCode, RadioSocketCommand::SWITCH_OFF, $this->continuous));
        $this->state = self::STATE_OFF;
        $this->stateModified = true;
    }

}
