<?php

namespace SHC\Command\CLI;

//Imports
use RWF\Request\Commands\CliCommand;
use SHC\Sheduler\Sheduler;

/**
 * Schaltserver
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ShedulerDeamonCli extends CliCommand {

    /**
     * kurzer Kommandozeilen Parameter
     * 
     * @var String 
     */
    protected $shortParam = '-sh';

    /**
     * voller Kommandozeilen Parameter
     * 
     * @var String 
     */
    protected $fullParam = '--sheduler';

    /**
     * Debug Modus aktiv
     * 
     * @var Boolean 
     */
    protected $debug = false;

    /**
     * gibt die Hilfe zu der Kommandozeilen Funktion auf die Kommandozeile aus
     */
    public function writeHelp() {
        
    }

    /**
     * konfiguriert das CLI Kommando
     */
    protected function config() {
        
    }

    /**
     * fuehrt das CLI Kommando aus
     */
    protected function executeCliCommand() {

        //Sheduler Initialisieren
        $sheduler = new Sheduler();

        //Aufgaben zyklisch ausfuehren
        while (true) {
            
            $sheduler->executeTasks();

            //Ruhezeut bis zum naechsten Durchlauf
            sleep(1);
        }
    }

}
