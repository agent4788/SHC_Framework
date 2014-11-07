<?php

namespace SHC\Command\CLI;

//Imports
use RWF\Request\Commands\CliCommand;
use SHC\Arduino\ArduinoSensorReciver;
use RWF\Core\RWF;

/**
 * Sensordaten vom Arduino Lesen
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ArduinoSensorReciverDeamonCli extends CliCommand {

    /**
     * kurzer Kommandozeilen Parameter
     * 
     * @var String 
     */
    protected $shortParam = '-ar';

    /**
     * voller Kommandozeilen Parameter
     * 
     * @var String 
     */
    protected $fullParam = '--arduinoreciver';

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
        RWF::getLanguage()->loadModul('ArduinoReciver');

        //pruefen on Server aktiviert
        if (!RWF::getSetting('shc.arduinoReciver.active')) {

            throw new Exception('Der Arduino Reciver wurde deaktiviert', 1600);
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

        $r = RWF::getResponse();
        $r->writeLnColored('-ar oder --arduinoreciver startet den Arduino Reciver Daemon', 'green_u');
        $r->writeLn('');
        $r->writeLn('Der Arduino Reciver list die Sensordaten die von einem Arduino auf die Serielle Schnittstelle ausgegeben werden');
        $r->writeLn('Dieser Dienst wird nur in Verbindung mit dem Sensornetzwerk benötigt und ist in den Standardeinstellungen daher deaktiviert.');
        $r->writeLn('');

        $r->writeLnColored('Zusätzliche Optionen:', 'yellow_u');
        $r->writeLnColored("\t" . '-c oder --config', 'yellow');
        $r->writeLn("\t\t" . 'Hier kann die Serielle Schnittstelle gewählt werden über die der Arduino und RPi kommunizieren');
        $r->writeLnColored("\t" . '-d oder --debug', 'yellow');
        $r->writeLn("\t\t" . 'Gibt die eingehenden Daten auf der Standartausgabe aus.');
        $r->writeLn('');
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
        
        $arduinoReciver = new ArduinoSensorReciver(RWF::getSetting('shc.arduinoReciver.interface'), RWF::getSetting('shc.arduinoReciver.baudRate'));
        $arduinoReciver->readDataEndless($this->debug);
        $arduinoReciver->close();
    }

}
