<?php

namespace SHC\Command\CLI;

//Imports
use RWF\Request\Commands\CliCommand;
use SHC\Sensor\SensorDataTransmitter;
use RWF\Core\RWF;
use SHC\Sensor\SensorEditor;

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

        //DHT Sensoren auflisten
        if (in_array('-listDHT', $argv)) {

            $dhts = SensorEditor::getInstance()->listDHT();
            foreach ($dhts as $dht) {

                $this->response->writeLn('DHT' . $dht['type'] . '; ID: ' . $dht['id'] . '; Pin: ' . $dht['pin'] . '; Name: "' . $dht['name'] . '"');
            }
            return;
        }

        //DHT hinzufuegen
        if (in_array('-addDHT', $argv)) {

            if(!isset($argv[4]) || !isset($argv[5]) || !isset($argv[6])) {

                $this->response->writeLnColored('Fasches Format, verwende folgendes Format:', 'red');
                $this->response->writeLnColored('php index.php app=shc -addDHT <id (0-999)> <typ (11|22|2302)> <pin (wiringpi)>', 'yellow');
                return;
            }

            //Daten einlesen
            $id = intval($argv[4]);
            $type = intval($argv[5]);
            $pin = intval($argv[6]);

            if($id < 0 || $id > 999 || !in_array($type, array(11, 22, 2302)) || $pin < 0 || $pin > 20) {

                $this->response->writeLnColored('Fasches Format, verwende folgendes Format:', 'red');
                $this->response->writeLnColored('php index.php app=shc -addDHT <id (0-999)> <typ (11|22|2302)> <pin (wiringpi)>', 'yellow');
                return;
            }

            try {

                SensorEditor::getInstance()->addDHT($id, $type, $pin, (isset($argv[7]) ? $argv[7] : ''));
                $this->response->writeLnColored('DHT erfolgreich erstellt', 'green');
            } catch(\Exception $e) {

                $this->response->writeLnColored('DHT konnte nicht erstellt werden', 'red');
            }
            return;
        }

        //DHT loeschen
        if (in_array('-removeDHT', $argv)) {

            if(!isset($argv[4])) {

                $this->response->writeLnColored('Fasches Format, verwende folgendes Format:', 'red');
                $this->response->writeLnColored('php index.php app=shc -removeDHT <id (0-999)>', 'yellow');
                return;
            }

            //Daten einlesen
            $id = intval($argv[4]);

            if($id < 0 || $id > 999) {

                $this->response->writeLnColored('Fasches Format, verwende folgendes Format:', 'red');
                $this->response->writeLnColored('php index.php app=shc -removeDHT <id (0-999)>', 'yellow');
                return;
            }

            try {

                SensorEditor::getInstance()->removeDHT($id);
                $this->response->writeLnColored('DHT erfolgreich gelöscht', 'green');
            } catch(\Exception $e) {

                $this->response->writeLnColored('DHT konnte nicht gelöscht werden', 'red');
            }
            return;
        }

        //BMP aktivieren
        if (in_array('-enableBMP', $argv)) {

            if(!isset($argv[4])) {

                $this->response->writeLnColored('Fasches Format, verwende folgendes Format:', 'red');
                $this->response->writeLnColored('php index.php app=shc -enableBMP <id (0-999)>', 'yellow');
                return;
            }

            //Daten einlesen
            $id = intval($argv[4]);

            if($id < 0 || $id > 999) {

                $this->response->writeLnColored('Fasches Format, verwende folgendes Format:', 'red');
                $this->response->writeLnColored('php index.php app=shc -enableBMP <id (0-999)>', 'yellow');
                return;
            }

            try {

                SensorEditor::getInstance()->enableBMP($id);
                $this->response->writeLnColored('BMP Sensor erfolgreich aktiviert', 'green');
            } catch(\Exception $e) {

                $this->response->writeLnColored('BMP Sensor konnte nicht aktiviert werden', 'red');
            }
            return;
        }

        //BMP Sensor deaktivieren
        if (in_array('-disableBMP', $argv)) {

            try {

                SensorEditor::getInstance()->disableBMP();
                $this->response->writeLnColored('BMP Sensor erfolgreich deaktiviert', 'green');
            } catch(\Exception $e) {

                $this->response->writeLnColored('BMP Sensor konnte nicht deaktiviert werden', 'red');
            }
            return;
        }

        //BMP Sensor ID
        if (in_array('-getBMPid', $argv)) {

            if(SensorEditor::getInstance()->isBMPenabled()) {

                $this->response->writeLn('ID: '. SensorEditor::getInstance()->getBMPsensorId());
            }
            $this->response->writeLn('deaktiviert');
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
        $r->writeLnColored("\t" . '-addDHT <Sensor-ID> <Typ> <GPIO-PIN> <Name>', 'yellow');
        $r->writeLn("\t\t" . 'Registrier einen neuen DHT22 Sensor');
        $r->writeLn("\t\t" . '<Sensor-ID> Eindeutige Sensor ID');
        $r->writeLn("\t\t" . '<Typ> 11 für DHT11 und 22 für DHT22');
        $r->writeLn("\t\t" . '<GPIO-PIN> Wiring Pi Pin [0 - 20]');
        $r->writeLn("\t\t" . '<Name> Optionale Name des Sensors');
        $r->writeLnColored("\t" . '-removeDHT <Sensor-ID>', 'yellow');
        $r->writeLn("\t\t" . 'löscht einen DHT Sensor');
        $r->writeLn("\t\t" . '<Sensor-ID> Eindeutige Sensor ID');
        $r->writeLnColored("\t" . '-getBMPid', 'yellow');
        $r->writeLn("\t\t" . 'gibt die BMP Sensor ID aus');
        $r->writeLnColored("\t" . '-enableBMP <Sensor-ID>', 'yellow');
        $r->writeLn("\t\t" . 'aktiviert den BMP Sensor');
        $r->writeLn("\t\t" . '<Sensor-ID> Eindeutige Sensor ID');
        $r->writeLnColored("\t" . '-disableBMP', 'yellow');
        $r->writeLn("\t\t" . 'deaktiviert den BMP Sensor');
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
