<?php

namespace SHC\Command\Commands;

//Imports
use SHC\Command\AbstractCommand;

/**
 * Funksteckdose schalten
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class RadioSocketCommand extends AbstractCommand {
    
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
     * @param String  $protocol   Protokoll
     * @param String  $systemCode Systemcode
     * @param String  $deviceCode Geraetecode
     * @param Integer $command    Kommando
     */
    public function __construct($protocol, $systemCode, $deviceCode, $command) {
        
        $this->protocol = $protocol;
        $this->systemCode = $systemCode;
        $this->deviceCode = $deviceCode;
        $this->command = $command;
    }
    
    /**
     * setzt das Protokoll
     * 
     * @param  String $protocol Sendeprotokoll
     * @return \SHC\Command\Commands\RadioSocketCommand
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
     * @return \SHC\Command\Commands\RadioSocketCommand
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
     * @return \SHC\Command\Commands\RadioSocketCommand
     */
    public function setDeviceCode($deviceCode) {
        
        $this->deviceCode = $deviceCode;
        return $this;
    }
    
    /**
     * gibt den Geraetecode zurueck
     * 
     * @return Stzring
     */
    public function getDeviceCode() {
        
        return $this->deviceCode;
    }
    
    /**
     * gibt ein Array mit den zu sendenen Daten zurueck
     * 
     * @return Array
     */
    public function getCommandData() {
        
        return array(
            'protocol' => $this->protocol,
            'systemCode' => $this->systemCode,
            'deviceCode' => $this->deviceCode,
            'command' => $this->command
        );
    }

}
