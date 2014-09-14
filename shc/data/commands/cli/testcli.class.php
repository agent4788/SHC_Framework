<?php

namespace SHC\Command\CLI;

//Imports
use RWF\Request\Commands\CliCommand;

/**
 * Test Kommandozeilen Anfrage
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class TestCli extends CliCommand {
    
     /**
     * kurzer Kommandozeilen Parameter
     * 
     * @var String 
     */
    protected $shortParam = '-t';
    
    /**
     * voller Kommandozeilen Parameter
     * 
     * @var String 
     */
    protected $fullParam = '--test';
    
    /**
     * gibt die Hilfe zu der Kommandozeilen Funktion auf die Kommandozeile aus
     */
    public function writeHelp() {
        
        $this->response->writeLn('test Hilfe');
    }
    
    /**
     * konfiguriert das CLI Kommando
     */
    protected function config() {
        
        $this->response->writeLn('test Config');
    }
    
    /**
     * fuehrt das CLI Kommando aus
     */
    protected function executeCliCommand() {
        
        $this->response->writeLn('ausführen');
    }
}
