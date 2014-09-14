<?php

namespace RWF\Request\Commands;

//Imports
use RWF\Request\AbstractCommand;

/**
 * Anfrage auf der Kommandozeile
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
abstract class CliCommand extends AbstractCommand {

    /**
     * kurzer Kommandozeilen Parameter
     * 
     * @var String 
     */
    protected $shortParam = '';
    
    /**
     * voller Kommandozeilen Parameter
     * 
     * @var String 
     */
    protected $fullParam = '';

    /**
     * gibt den kurzen Kommandozeilen Parameter zurueck
     * 
     * @return String
     */
    public function getShortParam() {
        
        return $this->shortParam;
    } 
    
    /**
     * gibt den vollen Kommandozeilen Parameter zurueck
     * 
     * @return String
     */
    public function getFullParam() {
        
        return $this->fullParam;
    }
    
    /**
     * fuehrt das Kommando aus
     */
    public function executeCommand() {
        
        global $argv;
        
        //Hilfe anfordern
        if (in_array('-h', $argv) || in_array('--help', $argv)) {
        
            $this->writeHelp();
            return;
        }
        
        //Konfiguration anfordern
        if (in_array('-c', $argv) || in_array('--config', $argv)) {
        
            $this->config();
            return;
        }
        
        $this->executeCliCommand();
    }
    
    /**
     * gibt die Hilfe zu der Kommandozeilen Funktion auf die Kommandozeile aus
     */
    protected abstract function writeHelp();
    
    /**
     * konfiguriert das CLI Kommando
     */
    protected abstract function config();
    
    /**
     * fuehrt das CLI Kommando aus
     */
    protected abstract function executeCliCommand();

}
