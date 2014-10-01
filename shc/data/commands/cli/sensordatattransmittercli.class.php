<?php

namespace SHC\Command\CLI;

//Imports
use RWF\Request\Commands\CliCommand;
use SHC\Sensor\SensorDataTransmitter;
use RWF\Core\RWF;

/**
 * Sensordaten an den Empfaenger senden
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SensorDatatTransmitterCli extends CliCommand {
    
    /**
     * kurzer Kommandozeilen Parameter
     * 
     * @var String 
     */
    protected $shortParam = '-st';

    /**
     * voller Kommandozeilen Parameter
     * 
     * @var String 
     */
    protected $fullParam = '--sensortransmitter';

    /**
     * Debug Modus aktiv
     * 
     * @var Boolean 
     */
    protected $debug = false;

    /**
     * fuehrt das Kommando aus
     */
    public function executeCommand() {

        global $argv;

        //max_execution_time auser kraft setzen
        set_time_limit(0);
        //direkte ausgabe der Daten
        ob_implicit_flush();

        //Sprache einbinden
        RWF::getLanguage()->loadModul('SensorTransmitter');

        //pruefen on Server aktiviert
        if (!RWF::getSetting('shc.sensorTransmitter.active')) {

            throw new Exception('Der Sensor Transmitter wurde deaktiviert', 1600);
        }

        //Debug aktivieren
        if (in_array('-d', $argv) || in_array('--debug', $argv)) {

            $this->debug = true;
        }

        //Hilfe anzeigen
        if (in_array('-h', $argv) || in_array('--help', $argv)) {

            $this->writeHelp();
            return;
        }

        //Konfiguration
        if (in_array('-c', $argv) || in_array('--config', $argv)) {

            $this->config();
            return;
        }

        $this->executeCliCommand();
    }
    
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
        
        $sensorTransmitter = new SensorDataTransmitter();
        $sensorTransmitter->transmitSensorData($this->debug);
    }
}
