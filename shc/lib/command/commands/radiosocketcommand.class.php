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
     * Anzahl der Sendevorgaenge
     *
     * @var Integer
     */
    protected $continuous = 1;
    
    /**
     * @param String  $protocol   Protokoll
     * @param String  $systemCode Systemcode
     * @param String  $deviceCode Geraetecode
     * @param Integer $command    Kommando
     * @param Integer $continuous Sendevorgaenge
     */
    public function __construct($protocol, $systemCode, $deviceCode, $command, $continuous) {
        
        $this->protocol = $protocol;
        $this->systemCode = $systemCode;
        $this->deviceCode = $deviceCode;
        $this->command = $command;
        $this->continuous = $continuous;
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
     * @return string
     */
    public function getDeviceCode() {
        
        return $this->deviceCode;
    }

    /**
     * setzt die Anzahl der Sendevorgaenge
     *
     * @param  Integer $continuous ANzahl wie of der Sendebefehl ausgefuehrt werden soll
     * @return \SHC\Command\Commands\RadioSocketCommand
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
     * gibt ein Array mit den zu sendenen Daten zurueck
     * 
     * @return Array
     */
    public function getCommandData() {
        
        return array(
            'type' => 'radiosocket',
            'protocol' => $this->protocol,
            'systemCode' => $this->systemCode,
            'deviceCode' => $this->deviceCode,
            'command' => $this->command,
            'continuous' => $this->continuous
        );
    }

}
