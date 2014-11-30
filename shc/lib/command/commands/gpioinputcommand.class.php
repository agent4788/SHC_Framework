<?php

namespace SHC\Command\Commands;

//Imports
use SHC\Command\AbstractCommand;

/**
 * GPIO als Eingang abfragen
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class GpioInputCommand extends AbstractCommand {
    
    /**
     * Status "1"
     * 
     * @var Integer
     */
    const HIGH = 1;
    
    /**
     * Status "0"
     * 
     * @var Integer
     */
    const LOW = 0;
    
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
     * Status
     * 
     * @var Integer 
     */
    protected $state = 0;
    
    /**
     * @param Integer $switchServer ID des Schaltservers
     * @param Integer $pinNumber    Pin Nummer
     * @param Integer $command      Kommando
     */
    public function __construct($switchServer, $pinNumber) {
        
        $this->switchServer = $switchServer;
        $this->pinNumber = $pinNumber;
    }
    
    /**
     * setzt den Schaltserver
     * 
     * @param  Integer $switchServer ID des Schaltservers
     * @return \SHC\Command\Commands\GpioOutputCommand
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
     * @return \SHC\Command\Commands\GpioOutputCommand
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
     * setzt den Status
     * 
     * @param  Integer $state Status
     * @return \SHC\Command\Commands\GpioInputCommand
     */
    public function setState($state) {
        
        $this->state = $state;
        return $this;
    }
    
    /**
     * gibt den Status zurueck
     * 
     * @return Integer
     */
    public function getState() {
        
        return $this->state;
    }
    
    /**
     * gibt ein Array mit den zu sendenen Daten zurueck
     * 
     * @return Array
     */
    public function getCommandData() {
        
        return array(
            'type' => 'gpioinput',
            'switchServer' => $this->switchServer,
            'pinNumber' => $this->pinNumber
        );
    }
}
