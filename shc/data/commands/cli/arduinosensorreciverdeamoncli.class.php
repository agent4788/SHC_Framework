<?php

namespace SHC\Command\CLI;

//Imports
use RWF\Request\Commands\CliCommand;
use RWF\Util\CliUtil;
use RWF\Util\String;
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

        $cli = new CliUtil();
        $response = $this->response;

        //Dienst aktiv
        $n = 0;
        $valid = true;
        $valid_active = false;
        $active_not_change = false;
        while ($n < 5) {

            $sender = $cli->input(RWF::getLanguage()->get('arduinoReciver.input.active', (RWF::getSetting('shc.arduinoReciver.active') == true ? RWF::getLanguage()->get('global.yes') : RWF::getLanguage()->get('global.no'))));

            //Port nicht aendern
            if (String::length($sender) == 0) {

                $active_not_change = true;
                $valid = true;
                break;
            }

            if (!preg_match('#^('. RWF::getLanguage()->get('global.yes') .')|('. RWF::getLanguage()->get('global.yes.short') .')|('. RWF::getLanguage()->get('global.no') .')|('. RWF::getLanguage()->get('global.no.short') .')$#i', $sender)) {

                $response->writeLnColored(RWF::getLanguage()->get('arduinoReciver.input.active.invalid'), 'red');
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

            $response->writeLnColored(RWF::getLanguage()->get('arduinoReciver.input.active.invalid.repeated'), 'red');
            exit(1);
        }

        //IP Adresse
        $n = 0;
        $valid = true;
        $valid_address = '';
        $address_not_change = false;
        while ($n < 5) {

            $address = $cli->input(RWF::getLanguage()->get('arduinoReciver.input.ip', RWF::getSetting('shc.arduinoReciver.ip')));

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

                $response->writeLnColored(RWF::getLanguage()->get('arduinoReciver.input.ip.invalid'), 'red');
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

            $response->writeLnColored(RWF::getLanguage()->get('arduinoReciver.input.ip.invalid.repeated'), 'red');
            exit(1);
        }

        //Port
        $n = 0;
        $valid = true;
        $valid_port = '';
        $port_not_change = false;
        while ($n < 5) {

            $port = $cli->input(RWF::getLanguage()->get('arduinoReciver.input.port', RWF::getSetting('shc.arduinoReciver.port')));

            //Port nicht aendern
            if (String::length($port) == 0) {

                $port_not_change = true;
                $valid = true;
                break;
            }

            if (!preg_match('#^[0-9]{1,5}$#', $port) || (int) $port <= 0 || (int) $port >= 65000) {

                $response->writeLnColored(RWF::getLanguage()->get('arduinoReciver.input.port.invalid'), 'red');
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

            $response->writeLnColored(RWF::getLanguage()->get('arduinoReciver.input.port.invalid.repeated'), 'red');
            exit(1);
        }

        //Serielle Schnittstelle
        $i = 1;
        $found = false;
        $interfaces = array();
        $valid = true;
        $valid_interface = '';
        $interface_not_change = false;
        $currentInterfache = RWF::getSetting('shc.arduinoReciver.interface');
        $dirH = opendir('/dev');
        while ($dir = readdir($dirH)) {

            if (preg_match('#ttyUSB\d+#i', $dir) || preg_match('#ttyAMA\d+#i', $dir)) {

                $cli->writeLineColored('[' . $i . '] /dev/' . $dir . ('/dev/' . $dir == $currentInterfache ? ' -> '. RWF::getLanguage()->get('arduinoReciver.input.interface.active') : ''), ('/dev/' . $dir == $currentInterfache ? 'green' : 'white'));
                $interfaces[$i] = '/dev/' . $dir;

                if ('/dev/' . $dir == $currentInterfache) {

                    $found = true;
                }

                $i++;
            }
        }
        closedir($dirH);

        //Wenn die eingestellte Schnittstelle nicht mit gefunden wurde
        if ($found == false) {

            $cli->writeLineColored('[' . $i . '] ' . $currentInterfache . ' -> '. RWF::getLanguage()->get('arduinoReciver.input.interface.active.notAvailable'), 'yellow');
        }

        //Stittstelle auswaehlen und Speichern
        $i = 0;
        while (true) {

            $in = intval($cli->input(RWF::getLanguage()->get('arduinoReciver.input.interface')));

            //Port nicht aendern
            if (String::length($port) == 0) {

                $interface_not_change = true;
                $valid = true;
                break;
            }

            if (array_key_exists($in, $interfaces)) {

                //Schnittstelle speichern
                $valid_interface = $interfaces[$in];
                break;
            } elseif($i < 5) {

                $cli->writeLineColored(RWF::getLanguage()->get('arduinoReciver.input.interface.invalid'), 'red');
            } elseif ($i == 5) {

                $cli->writeLineColored(RWF::getLanguage()->get('arduinoReciver.input.interface.invalid.repeated'), 'red');
                exit(1);
            }
        }

        if ($valid === false) {

            $response->writeLnColored(RWF::getLanguage()->get('arduinoReciver.input.interface.invalid.repeated'), 'red');
            exit(1);
        }

        //Speichern
        if($active_not_change === false) {

            RWF::getSettings()->editSetting('shc.arduinoReciver.active', $valid_active);
        }
        if($address_not_change === false) {

            RWF::getSettings()->editSetting('shc.arduinoReciver.ip', $valid_address);
        }
        if($port_not_change === false) {

            RWF::getSettings()->editSetting('shc.arduinoReciver.port', $valid_port);
        }
        if($interface_not_change === false) {

            RWF::getSettings()->editSetting('shc.arduinoReciver.interface', $valid_interface);
        }

        try {

            RWF::getSettings()->saveAndReload();
            $response->writeLnColored(RWF::getLanguage()->get('arduinoReciver.input.save.success'), 'green');
        } catch(\Exception $e) {

            $response->writeLnColored(RWF::getLanguage()->get('arduinoReciver.input.save.error'), 'red');
        }

    }

    /**
     * fuehrt das CLI Kommando aus
     */
    protected function executeCliCommand() {

        //pruefen on Server aktiviert
        if (!RWF::getSetting('shc.arduinoReciver.active')) {

            throw new \Exception('Der Arduino Reciver wurde deaktiviert', 1600);
        }

        $arduinoReciver = new ArduinoSensorReciver(RWF::getSetting('shc.arduinoReciver.interface'), RWF::getSetting('shc.arduinoReciver.baudRate'));
        $arduinoReciver->readDataEndless($this->debug);
        $arduinoReciver->close();
    }

}
