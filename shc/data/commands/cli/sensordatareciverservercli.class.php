<?php

namespace SHC\Command\CLI;

//Imports
use RWF\IO\UDPSocket;
use RWF\Request\Commands\CliCommand;
use RWF\Util\CliUtil;
use RWF\Util\String;
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
    protected $fullParam = '--sensorreceiver';

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
        RWF::getLanguage()->loadModul('SensorReceiver');

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
        $r->writeLnColored('-sr oder --sensorreceiver startet den Sensor Receiver Server', 'green_u');
        $r->writeLn('');
        $r->writeLn('Der Sensor Receiver Server empfängt die Sensordaten von Sensoren die am gleichen oder an anderen RPi angeschlossen sind.');
        $r->writeLn('Dieser Dienst wird nur benötigt wenn Sensoren die an einem RPi angeschlossen sind verwendet werden.');
        $r->writeLn('In den Standardeinstellungen ist dieser Dienst deaktiviert.');
        $r->writeLn('');

        $r->writeLnColored('Zusätzliche Optionen:', 'yellow_u');
        $r->writeLnColored("\t" . '-c oder --config', 'yellow');
        $r->writeLn("\t\t" . 'Hier kann die IP-Adresse und der Port des Servers festgelegt werden.');
        $r->writeLnColored("\t" . '-s oder --stop', 'yellow');
        $r->writeLn("\t\t" . 'Mit dieser Option kann ein Laufender Schaltserver gestoppt werden.');
        $r->writeLnColored("\t" . '-d oder --debug', 'yellow');
        $r->writeLn("\t\t" . 'Startet den Debug Modus, alle eingehenden Befehle werden auf der Kommandozeile ausgegeben.');
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

            $sender = $cli->input(RWF::getLanguage()->get('sensorReciver.input.active', (RWF::getSetting('shc.sensorReciver.active') == true ? RWF::getLanguage()->get('global.yes') : RWF::getLanguage()->get('global.no'))));

            //Port nicht aendern
            if (String::length($sender) == 0) {

                $active_not_change = true;
                $valid = true;
                break;
            }

            if (!preg_match('#^('. RWF::getLanguage()->get('global.yes') .')|('. RWF::getLanguage()->get('global.yes.short') .')|('. RWF::getLanguage()->get('global.no') .')|('. RWF::getLanguage()->get('global.no.short') .')$#i', $sender)) {

                $response->writeLnColored(RWF::getLanguage()->get('sensorReciver.input.active.invalid'), 'red');
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

            $response->writeLnColored(RWF::getLanguage()->get('sensorReciver.input.active.invalid.repeated'), 'red');
            exit(1);
        }

        //IP Adresse
        $n = 0;
        $valid = true;
        $valid_address = '';
        $address_not_change = false;
        while ($n < 5) {

            $address = $cli->input(RWF::getLanguage()->get('sensorReciver.input.ip', RWF::getSetting('shc.sensorReciver.ip')));

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

                $response->writeLnColored(RWF::getLanguage()->get('sensorReciver.input.ip.invalid'), 'red');
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

            $response->writeLnColored(RWF::getLanguage()->get('sensorReciver.input.ip.invalid.repeated'), 'red');
            exit(1);
        }

        //Port
        $n = 0;
        $valid = true;
        $valid_port = '';
        $port_not_change = false;
        while ($n < 5) {

            $port = $cli->input(RWF::getLanguage()->get('sensorReciver.input.port', RWF::getSetting('shc.sensorReciver.port')));

            //Port nicht aendern
            if (String::length($port) == 0) {

                $port_not_change = true;
                $valid = true;
                break;
            }

            if (!preg_match('#^[0-9]{1,5}$#', $port) || (int) $port <= 0 || (int) $port >= 65000) {

                $response->writeLnColored(RWF::getLanguage()->get('sensorReciver.input.port.invalid'), 'red');
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

            $response->writeLnColored(RWF::getLanguage()->get('sensorReciver.input.port.invalid.repeated'), 'red');
            exit(1);
        }

        //Speichern
        if($active_not_change === false) {

            RWF::getSettings()->editSetting('shc.sensorReciver.active', $valid_active);
        }
        if($address_not_change === false) {

            RWF::getSettings()->editSetting('shc.sensorReciver.ip', $valid_address);
        }
        if($port_not_change === false) {

            RWF::getSettings()->editSetting('shc.sensorReciver.port', $valid_port);
        }

        try {

            RWF::getSettings()->saveAndReload();
            $response->writeLnColored(RWF::getLanguage()->get('sensorReciver.input.save.success'), 'green');
        } catch(\Exception $e) {

            $response->writeLnColored(RWF::getLanguage()->get('sensorReciver.input.save.error'), 'red');
        }
    }

    /**
     * stoppt den Server
     */
    protected function stop() {

        $socket = new UDPSocket(RWF::getSetting('shc.sensorReciver.ip'), RWF::getSetting('shc.sensorReciver.port'), 2);
        $socket->open();
        $socket->write(base64_encode(json_encode(array(array('stop' => 1)))));
        $socket->close();
    }
    
    /**
     * fuehrt das CLI Kommando aus
     */
    protected function executeCliCommand() {

        //pruefen on Server aktiviert
        if (!RWF::getSetting('shc.sensorReciver.active')) {

            throw new \Exception('Der Sensor Receiver wurde deaktiviert', 1600);
        }

        $sensorReciver = new SensorDataReciverSocket();
        $sensorReciver->run($this->response, $this->debug);
    }
}
