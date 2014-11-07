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

        $r = RWF::getResponse();
        $r->writeLnColored('-st oder --sensortransmitter startet den Sensor Transmitter Daemon', 'green_u');
        $r->writeLn('');
        $r->writeLn('Der Sensor Transmitter sendet die Sensordaten der am RPi angeschlossenen Sensoren aun den Sensor Rceiver.');
        $r->writeLn('Dieser Dienst wird nur benötigt wenn Sensoren die an einem RPi angeschlossen sind verwendet werden.');
        $r->writeLn('In den Standardeinstellungen ist dieser Dienst deaktiviert.');
        $r->writeLn('');

        $r->writeLnColored('Zusätzliche Optionen:', 'yellow_u');
        $r->writeLnColored("\t" . '-d oder --debug', 'yellow');
        $r->writeLn("\t\t" . 'Startet den Debug Modus, alle eingehenden Befehle werden auf der Kommandozeile ausgegeben.');
        $r->writeLnColored("\t" . '-c oder --config', 'yellow');
        $r->writeLn("\t\t" . 'Hier kann die IP-Adresse und der Port des RPi Reader Servers sowie die Sensor Punkt ID, mit der sich der Writer am Reader meldet, festgelegt werden.');
        $r->writeLn("\t\t" . 'DS18x20 Sensoren werden automatisch erkannt, DHT und BMP Sensoren müssen mit den folgenden Optionen registriert werden.');
        $r->writeLnColored("\t" . '-listDHT', 'yellow');
        $r->writeLn("\t\t" . 'gibt eine Liste mit allen registrierten DHT Sensoren aus.');
        $r->writeLnColored("\t" . '-addDHT <Sensor-ID> <Typ> <GPIO-PIN>', 'yellow');
        $r->writeLn("\t\t" . 'Registrier einen neuen DHT22 Sensor');
        $r->writeLn("\t\t" . '<Sensor-ID> Eindeutige Sensor ID');
        $r->writeLn("\t\t" . '<Typ> 11 für DHT11 und 22 für DHT22');
        $r->writeLn("\t\t" . '<GPIO-PIN> Wiring Pi Pin [0 - 20]');
        $r->writeLnColored("\t" . '-removeDHT <Sensor-ID>', 'yellow');
        $r->writeLn("\t\t" . 'löscht einen DHT Sensor');
        $r->writeLn("\t\t" . '<Sensor-ID> Eindeutige Sensor ID');
        $r->writeLnColored("\t" . '-listBMP', 'yellow');
        $r->writeLn("\t\t" . 'gibt eine Liste mit allen registrierten BMP Sensoren aus.');
        $r->writeLnColored("\t" . '-addBMP <Sensor-ID>', 'yellow');
        $r->writeLn("\t\t" . 'Registrier einen neuen BMP Sensor');
        $r->writeLn("\t\t" . '<Sensor-ID> Eindeutige Sensor ID');
        $r->writeLnColored("\t" . '-removeBMP <Sensor-ID>', 'yellow');
        $r->writeLn("\t\t" . 'löscht einen DHT Sensor');
        $r->writeLn("\t\t" . '<Sensor-ID> Eindeutige Sensor ID');
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
        
        $sensorTransmitter = new SensorDataTransmitter();
        $sensorTransmitter->transmitSensorData($this->debug);
    }
}
