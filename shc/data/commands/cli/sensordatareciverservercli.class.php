<?php

namespace SHC\Command\CLI;

//Imports
use RWF\Request\Commands\CliCommand;
use SHC\Sensor\SensorDataReciverSocket;
use RWF\Core\RWF;

/**
 * Sensordaten aus dem Netzwerk empfangen
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SensorDataReciverServerCli extends CliCommand {
    
    /**
     * kurzer Kommandozeilen Parameter
     * 
     * @var String 
     */
    protected $shortParam = '-sr';

    /**
     * voller Kommandozeilen Parameter
     * 
     * @var String 
     */
    protected $fullParam = '--sensorreciver';

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
        RWF::getLanguage()->loadModul('SensorReciver');

        //pruefen on Server aktiviert
        if (!RWF::getSetting('shc.sensorReciver.active')) {

            throw new Exception('Der Sensor Reciver wurde deaktiviert', 1600);
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

        //Server Deamon stoppen
        if (in_array('-s', $argv) || in_array('--stop', $argv)) {

            $this->stop();
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
     * stoppt den Server
     */
    protected function stop() {

        $socket = new Socket(RWF::getSetting('shc.switchServer.ip'), RWF::getSetting('shc.switchServer.port'), 2);
        $socket->open();
        $socket->write(base64_encode(json_encode(array(array('stop' => 1)))));
        $socket->close();
    }
    
    /**
     * fuehrt das CLI Kommando aus
     */
    protected function executeCliCommand() {

        $sensorReciver = new SensorDataReciverSocket();
        $sensorReciver->run($this->response, $this->debug);
    }
}
