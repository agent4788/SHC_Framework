<?php

namespace SHC\Command\CLI;

//Imports
use RWF\Request\Commands\CliCommand;
use RWF\Util\CliUtil;
use RWF\Util\String;
use RWF\XML\XmlFileManager;
use SHC\Core\SHC;
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
     * XML Objekt
     *
     * @var \RWF\Xml\XmlEditor
     */
    protected $xml = null;

    /**
     * Einstellungen
     *
     * @var array
     */
    protected $settings = array();

    public function __construct() {

        $this->xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_SENSOR_TRANSMITTER);
    }

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
        RWF::getLanguage()->loadModul('SensorTransmitterDaemon');

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

                $this->response->writeLnColored('Falsches Format, verwende folgendes Format:', 'red');
                $this->response->writeLnColored('php index.php app=shc -addDHT <id (0-999)> <typ (11|22|2302)> <pin (BCMP Pin Nummer)>', 'yellow');
                return;
            }

            //Daten einlesen
            $id = intval($argv[4]);
            $type = intval($argv[5]);
            $pin = intval($argv[6]);

            if($id < 0 || $id > 999 || !in_array($type, array(11, 22, 2302)) || $pin < 0 || $pin > 50) {

                $this->response->writeLnColored('Fasches Format, verwende folgendes Format:', 'red');
                $this->response->writeLnColored('php index.php app=shc -addDHT <id (0-999)> <typ (11|22|2302)> <pin (BCMP Pin Nummer)>', 'yellow');
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
        $r->writeLn("\t\t" . '<GPIO-PIN> BCMP Pin Nummer');
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

        $cli = new CliUtil();
        $response = $this->response;

        //Dienst aktiv
        $n = 0;
        $valid = true;
        $valid_active = false;
        $active_not_change = false;
        while ($n < 5) {

            $sender = $cli->input(RWF::getLanguage()->get('sensorTransmitter.input.active', ($this->getSetting('shc.sensorTransmitter.active') == true ? RWF::getLanguage()->get('global.yes') : RWF::getLanguage()->get('global.no'))));

            //Port nicht aendern
            if (String::length($sender) == 0) {

                $active_not_change = true;
                $valid = true;
                break;
            }

            if (!preg_match('#^('. RWF::getLanguage()->get('global.yes') .')|('. RWF::getLanguage()->get('global.yes.short') .')|('. RWF::getLanguage()->get('global.no') .')|('. RWF::getLanguage()->get('global.no.short') .')$#i', $sender)) {

                $response->writeLnColored(RWF::getLanguage()->get('sensorTransmitter.input.active.invalid'), 'red');
                $n++;
                $valid = false;
                continue;
            }

            if ($valid === true && preg_match('#^('. RWF::getLanguage()->get('global.yes') .')|('. RWF::getLanguage()->get('global.yes.short') .')$#i', $sender)) {

                $valid_active = true;
                break;
            } elseif ($valid === true && preg_match('#^('. RWF::getLanguage()->get('global.no') .')|('. RWF::getLanguage()->get('global.no.short') .')$#i', $sender)) {

                $valid_active = false;
                break;
            }
        }

        if ($valid === false) {

            $response->writeLnColored(RWF::getLanguage()->get('sensorTransmitter.input.active.invalid.repeated'), 'red');
            exit(1);
        }

        //IP Adresse
        $n = 0;
        $valid = true;
        $valid_address = '';
        $address_not_change = false;
        while ($n < 5) {

            $address = $cli->input(RWF::getLanguage()->get('sensorTransmitter.input.ip', $this->getSetting('shc.sensorTransmitter.ip')));

            //Adresse nicht aendern
            if (String::length($address) == 0) {

                $address_not_change = true;
                $valid = true;
                break;
            }

            //Adresse pruefen
            $parts = explode('.', $address);
            for ($i = 0; $i < 3; $i++) {

                if (isset($parts[$i]) && (int) $parts[$i] >= 0 && (int) $parts[$i] <= 255) {

                    continue;
                }

                $response->writeLnColored(RWF::getLanguage()->get('sensorTransmitter.input.ip.invalid'), 'red');
                $n++;
                $valid = false;
                break;
            }

            if ($valid === true) {

                $valid_address = $address;
                break;
            }
        }

        if ($valid === false) {

            $response->writeLnColored(RWF::getLanguage()->get('sensorTransmitter.input.ip.invalid.repeated'), 'red');
            exit(1);
        }

        //Port
        $n = 0;
        $valid = true;
        $valid_port = '';
        $port_not_change = false;
        while ($n < 5) {

            $port = $cli->input(RWF::getLanguage()->get('sensorTransmitter.input.port', $this->getSetting('shc.sensorTransmitter.port')));

            //Port nicht aendern
            if (String::length($port) == 0) {

                $port_not_change = true;
                $valid = true;
                break;
            }

            if (!preg_match('#^[0-9]{1,5}$#', $port) || (int) $port <= 0 || (int) $port >= 65000) {

                $response->writeLnColored(RWF::getLanguage()->get('sensorTransmitter.input.port.invalid'), 'red');
                $n++;
                $valid = false;
                continue;
            }

            if ($valid === true) {

                $valid_port = $port;
                break;
            }
        }

        if ($valid === false) {

            $response->writeLnColored(RWF::getLanguage()->get('sensorTransmitter.input.port.invalid.repeated'), 'red');
            exit(1);
        }

        //Sensorpunkt ID
        $n = 0;
        $valid = true;
        $valid_sp = '';
        $sp_not_change = false;
        while ($n < 5) {

            $response->writeLnColored(RWF::getLanguage()->get('sensorTransmitter.input.sensorPointId.info'), 'yellow');
            $id = $cli->input(RWF::getLanguage()->get('sensorTransmitter.input.sensorPointId', $this->getSetting('shc.sensorTransmitter.pointId')));

            //Port nicht aendern
            if (String::length($id) == 0) {

                $sp_not_change = true;
                $valid = true;
                break;
            }

            if ((int) $id <= 0 || (int) $id >= 998) {

                $response->writeLnColored(RWF::getLanguage()->get('sensorTransmitter.input.sensorPointId.invalid'), 'red');
                $n++;
                $valid = false;
                continue;
            } else {

                $n++;
                $valid = true;
            }

            if ($valid === true) {

                $valid_sp = $id;
                break;
            }
        }

        if ($valid === false) {

            $response->writeLnColored(RWF::getLanguage()->get('sensorTransmitter.input.sensorPointId.invalid.repeated'), 'red');
            exit(1);
        }

        //Status LED
        $n = 0;
        $valid = true;
        $valid_pin = '';
        $pin_not_change = false;
        while ($n < 5) {

            $pin = $cli->input(RWF::getLanguage()->get('sensorTransmitter.input.blinkPin', $this->getSetting('shc.sensorTransmitter.blinkPin')));

            //Pin nicht aendern
            if (String::length($pin) == 0) {

                $pin_not_change = true;
                $valid = true;
                break;
            }

            if ((int) $pin < -1 || (int) $pin > 40) {

                $response->writeLnColored(RWF::getLanguage()->get('sensorTransmitter.input.blinkPin.invalid'), 'red');
                $n++;
                $valid = false;
                continue;
            } else {

                $valid = true;
                $valid_pin = $pin;
                break;
            }
        }

        if ($valid === false) {

            $response->writeLnColored(RWF::getLanguage()->get('sensorTransmitter.input.blinkPin.invalid.repeated'), 'red');
            exit(1);
        }

        //Speichern
        if($active_not_change === false) {

            $this->editSetting('shc.sensorTransmitter.active', $valid_active);
        }
        if($address_not_change === false) {

            $this->editSetting('shc.sensorTransmitter.ip', $valid_address);
        }
        if($port_not_change === false) {

            $this->editSetting('shc.sensorTransmitter.port', $valid_port);
        }
        if($sp_not_change === false) {

            $this->editSetting('shc.sensorTransmitter.pointId', $valid_sp);
        }
        if($pin_not_change === false) {

            $this->editSetting('shc.sensorTransmitter.blinkPin', $valid_pin);
        }

        try {

            $this->saveSettings();
            $response->writeLnColored(RWF::getLanguage()->get('sensorTransmitter.input.save.success'), 'green');
        } catch(\Exception $e) {

            $response->writeLnColored(RWF::getLanguage()->get('sensorTransmitter.input.save.error'), 'red');
        }
    }

    /**
     * fuehrt das CLI Kommando aus
     */
    protected function executeCliCommand() {

        //pruefen on Server aktiviert
        if (!$this->getSetting('shc.sensorTransmitter.active')) {

            throw new \Exception('Der Sensor Transmitter wurde deaktiviert', 1600);
        }
        
        $sensorTransmitter = new SensorDataTransmitter($this);
        $sensorTransmitter->transmitSensorData($this->debug);
    }

    /**
     * gibt den Wert einer Einstellung zurueck
     *
     * @param  string $name Name der Einstellung
     * @return mixed
     */
    public function getSetting($name) {

        if(count($this->settings) == 0) {

            foreach ($this->xml->settings->setting as $setting) {

                $attributes = $setting->attributes();
                switch ($attributes->type) {

                    case 'string':

                        $this->settings[(string) $attributes->name] = rawurldecode((string) $attributes->value);

                        break;
                    case 'bool':

                        $this->settings[(string) $attributes->name] = ((string) $attributes->value === 'true' ? true : false);

                        break;
                    case 'int':

                        $this->settings[(string) $attributes->name] = (int) $attributes->value;

                        break;
                    default:

                        $this->settings[(string) $attributes->name] = (string) $attributes->value;
                }
            }
        }

        if (isset($this->settings[$name])) {

            return $this->settings[$name];
        }
        return null;
    }

    /**
     * setzt den Wert einer Einstellung
     *
     * @param  string $settingName Name der Einstellung
     * @param  mixed  $value       Wert
     * @return bool
     * @throws \Exception
     */
    protected function editSetting($settingName, $value) {

        foreach ($this->xml->settings->setting as $setting) {

            $attributes = $setting->attributes();

            if ($attributes->name == $settingName) {

                switch ($attributes->type) {

                    case 'string':

                        $attributes->value = rawurlencode($value);

                        $this->chanched = true;
                        $this->saved = false;
                        return true;
                    case 'bool':

                        if ($value === true || $value === false || $value === 1 || $value === 0 || $value === '1' || $value === '0') {

                            $attributes->value = (($value === true || $value === 1 || $value === '1') ? 'true' : 'false');
                        } else {

                            throw new \Exception('Ungültiger Wert', 1120);
                        }

                        $this->chanched = true;
                        $this->saved = false;
                        return true;
                    case 'int':

                        if ((int) $value == $value) {

                            $attributes->value = (int) $value;
                        } else {

                            throw new \Exception('Ungültiger Wert', 1120);
                        }

                        $this->chanched = true;
                        $this->saved = false;
                        return true;
                    case 'float':

                        if ((float) $value == $value) {

                            $attributes->value = (float) $value;
                        } else {

                            throw new \Exception('Ungültiger Wert', 1120);
                        }

                        $this->chanched = true;
                        $this->saved = false;
                        return true;
                }
            }
        }
        return false;
    }

    /**
     * Speichert die EInstellungen
     *
     * @return bool
     * @throws \RWF\XML\Exception\XmlException
     */
    protected function saveSettings() {

        if ($this->xml->save(false)) {

            return true;
        }
        return false;
    }
}
